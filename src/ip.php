<?php
namespace Juhelib;
class ip
{
    /**
     * ip138 地址库（线上）
     * @param $ip
     * @return array
     */
    private static function ip138($ip)
    {
        $data = httpsGet("https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query=" . $ip . "&co=&resource_id=6006&t=1492224411362&ie=utf8&oe=gbk&cb=op_aladdin_callback&format=json&tn=baidu&cb=jQuery1102032941802880745374_1492224400641&_=1492224400643");
        $data = mb_convert_encoding($data, "utf-8", "gbk");
        $data = json_decode(getSubstr($data, "/**/jQuery1102032941802880745374_1492224400641(", ");"), true);
        return [
            "ip" => empty($data["data"][0]) ? $data["data"][0]["origip"] : "",
            "address" => empty($data["data"][0]) ? $data["data"][0]["location"] : "",
        ];
    }

    /**
     * taobao 地址库（线上）
     * @param $ip
     * @return array
     */
    private static function taobao($ip)
    {
        $data = json_decode(httpGet("http://ip.taobao.com/service/getIpInfo.php?ip=".$ip),true);
        if($data["data"]["region"] != ""){
            return ["ip" => $ip, "address" => $data["data"]["country"]."-".$data["data"]["area"]."地区 ".$data["data"]["region"].$data["data"]["city"]." ".$data["data"]["isp"]];
        }else{
            return ["ip" => $ip, "address" => $data["data"]["country"]];
        }
    }

    /**
     * qqwry 地址库（本地）
     * 版本：2020-04-30
     * @param $ip
     * @return array
     */
    private static function qqwry($ip)
    {
        $data = (new \Juhelib\extend\ip\qqwry)->getlocation($ip);
        return ["ip" => $data["ip"], "address" => str_replace_once('CZ88.NET', '', $data["country"]." ".$data["area"])];
    }

    /**
     * 获取 IP 地址
     * @return mixed|string
     */
    public static function getIP()
    {
        $unknown = "unknown";
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"] && strcasecmp($_SERVER["HTTP_X_FORWARDED_FOR"], $unknown)) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["REMOTE_ADDR"]) && $_SERVER["REMOTE_ADDR"] && strcasecmp($_SERVER["REMOTE_ADDR"], $unknown)) {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        $ip = (false !== strpos($ip, ",")) ? reset(explode(",", $ip)) : $ip;
        return ($ip == "::1") ? "127.0.0.1" : $ip;
    }

    /**
     * 获取 IP 详细数据
     * @param string $type IP 识别库
     * @return array
     */
    public static function getData($type = "qqwry")
    {
        $ip = self::getIP();
        if($type == "taobao") {
            return self::taobao($ip);
        }elseif($type == "ip138") {
            return self::ip138($ip);
        }elseif ($type == "qqwry") {
            return self::qqwry($ip);
        }else{
            return ['ip' => '', 'address' => ''];
        }
    }
}