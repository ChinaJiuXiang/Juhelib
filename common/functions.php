<?php
/**
 * 批量文本替换
 * @param string $context 源文本
 * @param array $keyword 需要替换的文本（数组）
 * @param array $keyword_replace 替换后的内容（数组）
 * @return mixed
 */
function batch_str_replace($context, $keyword, $keyword_replace)
{
    if(sizeof($keyword) == 1 && sizeof($keyword_replace) == 1) {
        if(strpos($context, $keyword[0]) !== false) {
            $context = str_replace($keyword[0], $keyword_replace[0], $context);
        }else{
            $context = $keyword_replace[0];
        }
    }elseif(sizeof($keyword) > 1 && sizeof($keyword_replace) > 1 && sizeof($keyword) == sizeof($keyword_replace)) {
        foreach ($keyword as $key => $value) {
            if(strpos($context, $keyword[$key]) !== false) { $context = str_replace($keyword[$key], $keyword_replace[$key], $context);}
        }
    }
    return $context;
}

/**
 * 只替换一次字符串
 * @param string $needle 需要替换的文本
 * @param string $replace 替换后的内容
 * @param string $haystack 源字符串
 * @return mixed
 */
function str_replace_once($needle, $replace, $haystack) {
    $pos = strpos($haystack, $needle);
    if ($pos === false) return $haystack;
    return substr_replace($haystack, $replace, $pos, strlen($needle));
}

/**
 * curl http get 方式访问
 * @param string $url 网址
 * @return mixed 网页源代码
 */
function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_REFERER, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}

/**
 * curl http post 方式访问
 * @param string $url 网址
 * @param array $data 提交数据
 * @return mixed 网页源代码
 */
function httpPost($url, $data) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}

/**
 * curl https get 方式访问
 * @param string $url 网址
 * @return mixed
 */
function httpsGet($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 500);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

/**
 * 取文本中间
 * @param string $str 文本内容
 * @param string $leftStr 左边文本
 * @param string $rightStr 右边文本
 * @return bool|string
 */
function getSubstr($str, $leftStr, $rightStr) {
    $left = strpos($str, $leftStr);
    $right = strpos($str, $rightStr, $left);
    if ($left < 0 or $right < $left) {
        return '';
    }
    return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}

/**
 * 对象转数组
 * @param object $object 对象
 * @return mixed 数组
 */
function object2Array($object) {
    return json_decode(json_encode($object, JSON_UNESCAPED_UNICODE), true);
}

/**
 * 获取汉字拼音、单词的第一个字母
 * @param $str
 * @return string
 */
function getinitial($str)
{
    $asc = ord(substr($str, 0, 1));
    if ($asc < 160) {
        // 非中文
        if ($asc >= 48 && $asc <= 57) {
            return ''; // 数字
        } elseif ($asc >= 65 && $asc <= 90) {
            return chr($asc); // A--Z
        } elseif ($asc >= 97 && $asc <= 122) {
            return strtoupper(chr($asc - 32)); // a--z 强制转换为大写
        } else {
            return ''; // 其他
        }
    } else {
        // 中文
        $s = iconv("UTF-8", "gb2312", $str);
        $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
        if ($asc >= -20319 and $asc <= -20284) {return "A";}
        if ($asc >= -20283 and $asc <= -19776) {return "B";}
        if ($asc >= -19775 and $asc <= -19219) {return "C";}
        if ($asc >= -19218 and $asc <= -18711) {return "D";}
        if ($asc >= -18710 and $asc <= -18527) {return "E";}
        if ($asc >= -18526 and $asc <= -18240) {return "F";}
        if ($asc >= -18239 and $asc <= -17923) {return "G";}
        if ($asc >= -17922 and $asc <= -17418) {return "H";}
        if ($asc >= -17417 and $asc <= -16475) {return "J";}
        if ($asc >= -16474 and $asc <= -16213) {return "K";}
        if ($asc >= -16212 and $asc <= -15641) {return "L";}
        if ($asc >= -15640 and $asc <= -15166) {return "M";}
        if ($asc >= -15165 and $asc <= -14923) {return "N";}
        if ($asc >= -14922 and $asc <= -14915) {return "O";}
        if ($asc >= -14914 and $asc <= -14631) {return "P";}
        if ($asc >= -14630 and $asc <= -14150) {return "Q";}
        if ($asc >= -14149 and $asc <= -14091) {return "R";}
        if ($asc >= -14090 and $asc <= -13319) {return "S";}
        if ($asc >= -13318 and $asc <= -12839) {return "T";}
        if ($asc >= -12838 and $asc <= -12557) {return "W";}
        if ($asc >= -12556 and $asc <= -11848) {return "X";}
        if ($asc >= -11847 and $asc <= -11056) {return "Y";}
        if ($asc >= -11055 and $asc <= -10247) {return "Z";}
        return '';
    }
}

// 获取毫秒时间
function microsecond()
{
    $t = explode(" ", microtime());
    $microsecond = round(round($t[1].substr($t[0],2,3)));
    return $microsecond;
}