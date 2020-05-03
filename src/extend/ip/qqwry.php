<?php
namespace Juhelib\extend\ip;
class qqwry
{
    /**
     * qqwry.Dat文件指针
     * @var resource
     */
    private $fp;

    /**
     * 第一条IP记录的偏移地址
     * @var int
     */
    private $firstip;

    /**
     * 最后一条IP记录的偏移地址
     * @var int
     */
    private $lastip;

    /**
     * IP记录的总条数（不包含版本信息记录）
     * @var int
     */
    private $totalip;

    /**
     * 返回结果的编码
     * @var string
     */
    private $charset = 'UTF-8';

    /**
     * 构造函数，打开 QQWry.Dat 文件并初始化类中的信息
     * @param string $filename
     * @return IpLocation
     */
    public function __construct($filename = '')
    {
        if(empty($filename)){
            $filename = __DIR__.DIRECTORY_SEPARATOR.'qqwry.dat';
        }
        $this->fp = null;
        if (($this->fp = fopen($filename, 'r')) !== false) {
            $this->firstip = $this->getlong();
            $this->lastip = $this->getlong();
            $this->totalip = ($this->lastip - $this->firstip) / 7;
        }
    }

    /**
     * 析构函数，用于在页面执行结束后自动关闭打开的文件。
     */
    public function __destruct()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
        $this->fp = null;

    }

    /**
     * 返回读取的长整型数
     * @access private
     * @return int
     */
    private function getlong()
    {
        // 将读取的little-endian编码的4个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 4));
        return $result['long'];
    }

    /**
     * 返回读取的3个字节的长整型数
     * @access private
     * @return int
     */

    private function getlong3()
    {
        // 将读取的little-endian编码的3个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 3) . chr(0));
        return $result['long'];
    }

    /**
     * 返回压缩后可进行比较的IP地址
     * @access private
     * @param string $ip
     * @return string
     */

    private function packip($ip)
    {
        // 将IP地址转化为长整型数，如果在PHP5中，IP地址错误，则返回False，
        // 这时intval将Flase转化为整数-1，之后压缩成big-endian编码的字符串
        return pack('N', intval(ip2long($ip)));
    }

    /**
     * 返回读取的字符串
     * @access private
     * @param string $data
     * @return string
     */
    private function getstring($data = "")
    {
        $char = fread($this->fp, 1);
        while (ord($char) > 0) {        // 字符串按照C格式保存，以结束
            $data .= $char;             // 将读取的字符连接到给定字符串之后
            $char = fread($this->fp, 1);
        }
        return $data;
    }

    /**
     * 返回地区信息
     * @access private
     * @return string
     */

    private function getarea()
    {
        $byte = fread($this->fp, 1);    // 标志字节
        switch (ord($byte)) {
            case 0:                     // 没有区域信息
                $area = "";
                break;
            case 1:
            case 2:                     // 标志字节为1或2，表示区域信息被重定向
                fseek($this->fp, $this->getlong3());
                $area = $this->getstring();
                break;
            default:                    // 否则，表示区域信息没有被重定向
                $area = $this->getstring($byte);
                break;
        }
        return $area;
    }

    /**
     * 从qqwry.dat文件中查找IP的位置（偏移量）
     */
    private function findIp($ip)
    {
        // 对分搜索
        $l = 0;                         // 搜索的下边界
        $u = $this->totalip;            // 搜索的上边界
        $findip = $this->lastip;        // 如果没有找到就返回最后一条IP记录（QQWry.Dat的版本信息）
        while ($l <= $u) {              // 当上边界小于下边界时，查找失败
            $i = floor(($l + $u) / 2); // 计算近似中间记录
            fseek($this->fp, $this->firstip + $i * 7);
            $beginip = strrev(fread($this->fp, 4));     // 获取中间记录的开始IP地址
            // strrev函数在这里的作用是将little-endian的压缩IP地址转化为big-endian的格式
            // 以便用于比较，后面相同。
            if ($ip < $beginip) {       // 用户的IP小于中间记录的开始IP地址时
                $u = $i - 1;            // 将搜索的上边界修改为中间记录减一
            } else {
                fseek($this->fp, $this->getlong3());
                $endip = strrev(fread($this->fp, 4));   // 获取中间记录的结束IP地址
                if ($ip > $endip) {     // 用户的IP大于中间记录的结束IP地址时
                    $l = $i + 1;        // 将搜索的下边界修改为中间记录加一
                } else {                  // 用户的IP在中间记录的IP范围内时
                    $findip = $this->firstip + $i * 7;
                    break;              // 则表示找到结果，退出循环
                }
            }
        }
        return $findip;
    }

    /**
     * 根据所给 IP 地址或域名返回所在地区信息
     * @access public
     * @param string $ip
     * @return array
     */
    public function getlocation($ip)
    {
        if (!$this->fp) return null;            // 如果数据文件没有被正确打开，则直接返回空
        // $location['ip'] = gethostbyname($ip);   // 将输入的域名转化为IP地址
        $location['ip'] = $ip;
        $ip = $this->packip($location['ip']);   // 将输入的IP地址转化为可比较的IP地址
        // 查找IP在文件中的偏移量
        $findip = $this->findIp($ip);
        // 获取查找到的IP地理位置信息
        fseek($this->fp, $findip);
        $location['beginip'] = long2ip($this->getlong());   // 用户IP所在范围的开始地址
        $offset = $this->getlong3();
        fseek($this->fp, $offset);
        $location['endip'] = long2ip($this->getlong());     // 用户IP所在范围的结束地址
        $byte = fread($this->fp, 1);    // 标志字节
        switch (ord($byte)) {
            case 1:                     // 标志字节为1，表示国家和区域信息都被同时重定向
                $countryOffset = $this->getlong3();         // 重定向地址
                fseek($this->fp, $countryOffset);
                $byte = fread($this->fp, 1);    // 标志字节
                switch (ord($byte)) {
                    case 2:             // 标志字节为2，表示国家信息又被重定向
                        fseek($this->fp, $this->getlong3());
                        $location['country'] = $this->getstring();
                        fseek($this->fp, $countryOffset + 4);
                        $location['area'] = $this->getarea();
                        break;
                    default:            // 否则，表示国家信息没有被重定向
                        $location['country'] = $this->getstring($byte);
                        $location['area'] = $this->getarea();
                        break;
                }
                break;
            case 2:                     // 标志字节为2，表示国家信息被重定向
                fseek($this->fp, $this->getlong3());
                $location['country'] = $this->getstring();
                fseek($this->fp, $offset + 8);
                $location['area'] = $this->getarea();
                break;
            default:                    // 否则，表示国家信息没有被重定向
                $location['country'] = $this->getstring($byte);
                $location['area'] = $this->getarea();
                break;
        }
        $location = $this->transcoding($location);
        return $this->otherLocation($location);
    }

    /**
     * 将变为由GBK转换为指定的编码
     * @param  Array $location
     * @return Array
     */
    private function transcoding($location)
    {
        foreach (['country', 'area'] as $key) {
            $location[$key] = iconv("GBK", 'UTF-8', $location[$key]);
        }
        return $location;
    }

    /**
     * 处理其他情况 -- 没有信息或本地，局域网的情况
     * @param $location
     * @internal param String $ip ip地址
     * @return Array
     */
    private function otherLocation($location)
    {
        if ($location['country'] == ' CZ88.NET') { // CZ88.NET表示没有有效信息
            $location['country'] = '未知';
        }
        if ($location['area'] == ' CZ88.NET') {
            $location['area'] = '未知';
        }
        $ip = $location['ip'];
        if (substr($ip, 0, 3) == '127') {
            $location['country'] = '本地网络';
            $location['area'] = '本地保留地址';
        } elseif (substr($ip, 0, 7) == '192.168') {
            $location['country'] = '局域网';
            $location['area'] = '同一网络内';
        }
        return $location;
    }
}