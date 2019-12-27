<?php
namespace Juhelib;
class safe
{
    /**
     * 字符串加密模块
     * @param string $string 源字符串
     * @param string $key 密钥
     * @param int $expiry 到期时间（秒）
     * @return bool|string
     */
    public static function str_encrypt($string, $key = '', $expiry = 0)
    {
        return call_user_func_array(
            array("\\Juhelib\\extend\\security\\strings", 'string_handle'), array($string, 'encode', $key, $expiry)
        );
    }

    /**
     * 字符串解密模块
     * @param string $string 加密过的字符串
     * @param string $key 密钥
     * @param int $expiry 到期时间（秒）
     * @return bool|string
     */
    public static function str_decrypt($string, $key = '', $expiry = 0)
    {
        return call_user_func_array(
            array("\\Juhelib\\extend\\security\\strings", 'string_handle'), array($string, 'decode', $key, $expiry)
        );
    }

    /**
     * 文件加密模块
     * @param string $source_path 源地址
     * @param string $encrypt_path 加密后的文件地址
     * @param string $key 密钥
     */
    public static function file_encrypt($source_path, $encrypt_path, $key = '')
    {
        file_put_contents($encrypt_path, call_user_func_array(
            array("\\Juhelib\\extend\\security\\file", 'file_encrypt'), array(@file_get_contents($source_path), $key)
        ));
    }

    /**
     * 文件解密模块
     * @param string $encrypt_path 加密过的文件地址
     * @param string $decrypt_path 解密后的文件地址
     * @param string $key 密钥
     */
    public static function file_decrypt($encrypt_path, $decrypt_path, $key = '')
    {
        file_put_contents($decrypt_path, call_user_func_array(
            array("\\Juhelib\\extend\\security\\file", 'file_decrypt'), array(@file_get_contents($encrypt_path), $key)
        ));
    }
}