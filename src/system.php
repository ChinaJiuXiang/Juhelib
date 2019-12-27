<?php
namespace Juhelib;
class system
{
    /**
     * 获取物理 MAC 地址
     */
    public static function get_mac_addr()
    {
        $return_array = $temp_array = []; $mac_addr = '';
        if(strtolower(PHP_OS) == 'linux') {
            @exec("ifconfig -a", $return_array);
        }elseif(strtolower(PHP_OS) == 'solaris') {

        }elseif(strtolower(PHP_OS) == 'unix') {
            
        }elseif(strtolower(PHP_OS) == 'aix') {

        }else{
            @exec("ipconfig /all", $return_array); 
            if(!$return_array) {
                $ipconfig = $_SERVER["WINDIR"]."\system32\ipconfig.exe"; 
                if(is_file($ipconfig)) {
                    @exec($ipconfig." /all", $return_array); 
                }else{ 
                    @exec($_SERVER["WINDIR"]."\system\ipconfig.exe /all", $return_array); 
                }
            }
        }
        foreach($return_array as $value) {
            if(preg_match("/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i", $value, $temp_array)) {
                $mac_addr = $temp_array[0];
                unset($temp_array); break;
            }
        }
        return $mac_addr;
    }

    /**
     * 生成机器码
     * @param string $blur_code 混淆码
     */
    public static function machine_code($blur_code = "293b2b89fab1238f472e229f95709411")
    {
        $macmd5str = Get_Current_User().php_uname().self::get_mac_addr();
        // $macmd5str = Get_Current_User().php_sapi_name().PHP_VERSION.php_uname().DEFAULT_INCLUDE_PATH;
        $macmd5str = strtoupper(substr(md5(md5($macmd5str.$blur_code).$blur_code), 0, 30));
        return substr($macmd5str, 0, 6)."-".substr($macmd5str, 6, 6)."-".substr($macmd5str, 12, 6)."-".substr($macmd5str, 18, 6)."-".substr($macmd5str, 24, 6);
    }
}