<?php
namespace Juhelib;
class check
{
    /**
     * 验证银行卡号是否有效（前提为16位或19位数字组合）
     * @param string $cardNum 银行卡号
     * @return bool 有效返回true, 否则返回false
     */
    public static function isBankCard($cardNum)
    {
        // 第一步,反转银行卡号
        $cardNum = strrev($cardNum);
        // 第二步,计算各位数字之和
        $sum = 0;
        for ($i = 0; $i < strlen($cardNum); ++$i) {
            $item = substr($cardNum, $i, 1);
            //
            if ($i % 2 == 0) {
                $sum += $item;
            } else {
                $item *= 2;
                $item = $item > 9 ? $item - 9 : $item;
                $sum += $item;
            }
        }
        // 第三步,判断数字之和余数是否为0
        if ($sum % 10 == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证身份证号是否正确
     * @param int $number
     * @return bool
     */
    public static function isIdentityCard($number)
    {
        // 验证长度
        if (strlen($number) != 18) {
            return false;
        }
        // 验证是否符合规则
        if (!preg_match("/\d{17}[0-9Xx]|\d{15}/i", $number)) {
            return false;
        }
        // 每位数对应的乘数因子
        $factors = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        // 计算身份证号前17位的和
        $sum = 0;
        for ($index = 0; $index < 17; ++$index) {
            $num = substr($number, $index, 1);
            $sum += $num * $factors[$index];
        }
        // 将和对11取余
        $mod = $sum % 11;
        // 根据获得的余数，获取验证码
        $verifyCode = "";
        switch ($mod) {
            case 0:
                $verifyCode = "1";
                break;
            case 1:
                $verifyCode = "0";
                break;
            case 2:
                $verifyCode = "X";
                break;
            default:
                $verifyCode = 12 - $mod;
                break;
        }
        // 核对校验码和身份证最后一位
        if ($verifyCode == substr($number, -1, 1)) {
            return true;
        }
        return false;
    }

    /**
     * 验证url地址
     * @param string $str 传递值
     * @param string $agreement 协议
     * @return bool
     */
    public static function isUrl($str, $agreement = "http"){
        return (preg_match("/^".$agreement.":\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/", $str)) ? true : false;
    }

    /**
     * 验证电话号码
     * @param string $str
     * @return bool
     */
    public static function isPhone($str){
        return (preg_match("/^1[3456789]\d{9}$/", $str)) ? true : false;
    }

    /**
     * 验证邮箱
     * @param string $str
     * @return bool
     */
    public static function isEmail($str){
        return (preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $str)) ? true : false;
    }

    /**
     * 验证邮编
     * @param string $str
     * @return bool
     */
    public static function isZip($str){
        return (preg_match("/^[1-9]\d{5}$/", $str)) ? true : false;
    }

    /**
     * 是否是数字
     * @param string $val
     * @return bool
     */
    public static function isNumeric($val){
        return (preg_match('/^[-+]?[0-9]*.?[0-9]+$/', $val)) ? true : false;
    }

    /**
     * 是否超过最大长度
     * @param string $val
     * @param int $max
     * @return bool
     */
    public static function isMaxLength($val, $max){
        return (strlen($val) <= (int)$max);
    }

    /**
     * 超过最大数
     * @param int $number
     * @param int $max
     * @return bool
     */
    public static function isMaxValue($number, $max){
        return ($number > $max);
    }

    /**
     * 验证用户名（只允许下划线+汉字+英文+数字（不支持其它特殊字符））
     * @param string $value 传递值
     * @param int $minLen 最小长度
     * @param int $maxLen 最大长度
     * @return bool|false|int
     */
    public static function isUsername($value, $minLen = 2, $maxLen = 30){
        if (!$value) return false;
        return preg_match('/^[_wdx{4e00}-x{9fa5}]{' . $minLen . ',' . $maxLen . '}$/iu', $value);
    }

    /**
     * 是否为空值
     * @param string $str
     * @return bool
     */
    public static function isEmpty($str){
        $str = trim($str);
        return empty($str) ? true : false;
    }

    /**
     * 数字验证
     * @param string $str
     * @return bool
     */
    public static function isNum($str){
        return (preg_match("/^[1-9][0-9]*$/", $str)) ? true : false;
    }

    /**
     * 是否是数字或英文组合
     * @param string $val
     * @return bool
     */
    public static function isNumLetter($val){
        return (preg_match('/[A-Za-z0-9]+$/', $val)) ? true : false;
    }

    /**
     * 是否同时包含数字和英文，必须同时包含数字和英文
     * @param string $val
     * @return bool
     */
    public static function isNumAndLetter($val){
        return (preg_match("/[A-Za-z]/",$val)&& preg_match("/\d/",$val)) ? true : false;
    }

    /**
     * 微信号验证
     * @param string $val
     * @return bool
     */
    public static function isWebchat($val){
        return (preg_match("/^[-_a-zA-Z0-9]{5,19}$/",$val)) ? true : false;
    }

    /**
     * 是否是 IP
     * @param string $val
     * @return bool
     */
    public static function isIP($val){
        return (preg_match('/^\d{0,3}\.\d{0,3}\.\d{0,3}\.\d{0,3}$/', $val)) ? true : false;
    }
}