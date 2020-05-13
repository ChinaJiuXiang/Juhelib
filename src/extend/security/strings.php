<?php
namespace Juhelib\extend\security;
class strings
{
    /**
     * 字符串加解密模块（核心）
     * @param string $string 源字符串
     * @param string $operation encode：加密  decode：解密
     * @param string $key 加密用到的 KEY
     * @param int $expiry 到期时间（秒）
     * @return bool|string
     */
    public static function string_handle($string, $operation = 'decode', $key = '', $expiry = 0)
    {
       $ckey_length = 4;
       $key = md5($key ? $key : "293b2b89fab1238f472e229f95709411");
       $keya = md5(substr($key, 0, 16));
       $keyb = md5(substr($key, 16, 16));
       $keyc = $ckey_length ? $operation == 'decode' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length) : '';
       $cryptkey = $keya . md5($keya . $keyc);
       $key_length = strlen($cryptkey);
       $string = $operation == 'decode' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
       $string_length = strlen($string); $result = '';
       $box = range(0, 255); $rndkey = [];
       for ($i = 0; $i <= 255; $i++) { $rndkey[$i] = ord($cryptkey[$i % $key_length]); }
       for ($j = $i = 0; $i < 256; $i++) {
           $j = ($j + $box[$i] + $rndkey[$i]) % 256; $tmp = $box[$i];
           $box[$i] = $box[$j]; $box[$j] = $tmp;
       }
       for ($a = $j = $i = 0; $i < $string_length; $i++) {
           $a = ($a + 1) % 256; $j = ($j + $box[$a]) % 256;
           $tmp = $box[$a]; $box[$a] = $box[$j]; $box[$j] = $tmp;
           $result .= chr(ord($string[$i]) ^ $box[($box[$a] + $box[$j]) % 256]);
       }
       if ($operation == 'decode') {
           if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
               return substr($result, 26); } else { return '';
           }
       } else {
           return $keyc . str_replace('=', '', base64_encode($result));
       }
    }
}