<?php
namespace Juhelib;
class file
{
    private static $config = [];

    /**
     * 读取数据配置
     * @param string $name
     * @return mixed|string
     */
    private static function getConfig($name) {
        return empty(self::$config[$name]) ? null : self::$config[$name];
    }

    /**
     * 设置数据配置
     * @param array $config
     */
    public static function setConfig($config) {
        foreach ($config as $key => $value) {
            self::$config[$key] = empty($value) ? !empty(self::$config[$key]) ? self::$config[$key] : null : $value;
        }
    }

    /**
     * 文件后缀名白名单过滤
     * @param string $filename
     * @return mixed|null
     */
    private static function whiteList($filename)
    {
        $ext = array('gif', 'png', 'bmp', 'jpg', 'jpeg', 'psd', '7z', 'zip', 'rar', 'tar', 'gz', 'doc', 'docx', 'ppt', 'pptx'
        , 'xls', 'xlsx', 'mp3', 'mp4', 'avi', 'mpg', 'swf', 'fla', 'apk', 'pdf', 'rm', 'ra', 'rmvb', 'mov', 'wmv', 'wma', 'svg'
        , 'svgz', 'rtf', 'wps', 'deb', 'sit', 'rpm', 'bz2' ,'xsl', 'chm', 'txt', 'torrent');
        foreach ($ext as $k => $v) {
            // 检测后缀名是否在白名单
            $radom_txt = substr(md5(mt_rand(100000,999999)), 8, 16);
            if(strstr($filename.'*'.$radom_txt, $v.'*'.$radom_txt) !== false) {
                // 检测文件名是否包含 php
                return (strstr($filename, 'php') !== false) ? null : $v ;
            }
        }
        return null;
    }

    /**
     * 文件过滤筛选器
     * @param array $fileinfo 文件信息
     * @param int $filesize 限制文件大小
     * @return array|bool
     */
    private static function filter($fileinfo, $filesize)
    {
        if ($fileinfo["error"] > 0) { return false; }
        // 文件大小过滤，单位：KB
        if(($fileinfo["size"] / 1024) <= $filesize) {
            // 开始后缀名白名单过滤
            $ext = self::whiteList($fileinfo['name']);
            if(empty($ext)) { return false; }
            // 压缩原图片，防止图片马
            if($ext == 'gif' || $ext == 'png' || $ext == 'bmp' || $ext == 'jpg' || $ext == 'jpeg') {
                image::compressedPictures($fileinfo['tmp_name'], $fileinfo['tmp_name']); // 压缩图片后放回原处
            }
            return ['ext' => $ext];
        }
        return false;
    }

    /**
     * 云空间文件上传逻辑封装（上传完成后返回图片地址）
     * @param string $upload_file_name 云空间文件存放路径
     * @param string $upload_file_path 本地文件上传路径
     * @return bool|string
     * @throws \Exception
     */
    private static function uploadPackage($upload_file_name, $upload_file_path)
    {
        if(self::getConfig('engine') == 'qiniu') {
            $upManager = new \Qiniu\Storage\UploadManager();
            $auth = new \Qiniu\Auth(self::getConfig('qiniu_key_access'), self::getConfig('qiniu_key_secret'));
            $token = $auth->uploadToken(self::getConfig('qiniu_bucket_name'));
            list($ret, $error) = $upManager->putFile($token, $upload_file_name, $upload_file_path);
            return ($error !== null) ? false : self::getConfig('qiniu_url_agree').'://'.self::getConfig('qiniu_domain').'/'.$upload_file_name;
        }else if(self::getConfig('engine') == 'alioss') {
            try{
                $ossClient = new \OSS\OssClient(self::getConfig('alioss_key_access'), self::getConfig('alioss_key_secret'), self::getConfig('alioss_endpoint'));
                $ossClient->uploadFile(self::getConfig('alioss_bucket_name'), $upload_file_name, $upload_file_path);
                $ossClient = (array)$ossClient;
                return array_values($ossClient)[4];
            } catch(\OSS\Core\OssException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * 文件上传核心处理函数
     * @param string $upload_folder 本地存放路径
     * @param int $file_size 限制文件大小
     * @param array $file_info 上传文件信息
     * @param string $cloud_directory 云空间存放路径
     * @return bool|null|string
     * @throws \Exception
     */
    private static function uploadHandle($upload_folder, $file_size, $file_info, $cloud_directory = '')
    {
        $filter_info = self::filter($file_info, $file_size); if(empty($filter_info)) { return null; }
        $upload_file_path = date('Y')."/".date('m')."/".date('d')."/";
        $upload_file_name = md5(time().mt_rand(10000000, 99999999)).".".$filter_info['ext'];
        $directory_pach = $upload_folder."/".$upload_file_path;
        if(self::getConfig('engine') == 'local') {
            if(!is_dir($directory_pach)) { mkdir(iconv("UTF-8", "GBK", $directory_pach), 0777, true); }
            if(move_uploaded_file($file_info['tmp_name'], $directory_pach.$upload_file_name)) { return $directory_pach.$upload_file_name; }else{ return null; }
        }else {
            return self::uploadPackage($cloud_directory.$upload_file_path.$upload_file_name, $file_info['tmp_name']);
        }
    }

    /**
     * 文件上传（上传完成后返回图片地址）
     * @param string $upload_folder 本地存放路径
     * @param int $file_size 限制文件大小
     * @param string $cloud_directory 云空间存放路径
     * @return array
     * @throws \Exception
     */
    public static function upload($upload_folder, $file_size = 4096, $cloud_directory = '')
    {
        foreach ($_FILES as $key => $value) {
            if (count($value) == count($value, 1)) { // name 不同名文件上传处理
                $file_array[$key] = self::uploadHandle($upload_folder, $file_size, $value, $cloud_directory);
            } else if (count($value) == count($value, 2)) { // name 同名数组多文件上传处理
                // 数组处理
                foreach ($value as $k1 => $v1) { foreach ($v1 as $k2 => $v2) { $array_info[$k2][$k1] = $v2; } }
                // 数据组装
                foreach ($array_info as $k => $v) {
                    $file_array[$key][$k] = self::uploadHandle($upload_folder, $file_size, $v, $cloud_directory);
                }
            }
        }
        return empty($file_array) ? [] : $file_array;
    }
}
?>