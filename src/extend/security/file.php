<?php
namespace Juhelib\extend\security;
class file
{
    private $file_iter = 32; // 加密位数 32 | 64（2019年12月2日 经检测 64 位加密不可用）
    private $file_key = '293b2b89fab1238f472e229f95709411';

    public static function file_encrypt($data, $key = '')
    {
        $object = new self();
        $object->encrypt($data, $key);
    }

    public static function file_decrypt($data, $key = '')
    {
        $object = new self();
        $object->decrypt($data, $key);
    }
    
    /**
     * 加密函数 
     * @access public 
     * @param  mixed $data 要加密的数据 
     * @param  mixed $key  加密秘钥
     * @return string 加密后的数据
     **/
    public function encrypt($data, $key = '')
    {
        if (!$data) {
            return '';
        }
        $n = $this->_resize($data, 4);
        $data_long[0] = $n;
        $n_data_long = $this->_str2long(1, $data, $data_long);
        $n = count($data_long);
		// 2019年12月2日 注释。原因：数组多一个 string，导致 php waring 
        /*if (($n & 1) == 1) {
            $data_long[$n] = chr(0);
            $n_data_long++;
        }*/
        $this->_resize($key, 16, true);
        if ('' == $key) {
            $key = $this->file_key;
        }
        $n_key_long = $this->_str2long(0, $key, $key_long);
        $enc_data = '';
        $w = array(0, 0);
        $j = 0;
        $k = array(0, 0, 0, 0);
        for ($i = 0; $i < $n_data_long; ++$i) {
            if ($j + 4 <= $n_key_long) {
                $k[0] = $key_long[$j];
                $k[1] = $key_long[$j + 1];
                $k[2] = $key_long[$j + 2];
                $k[3] = $key_long[$j + 3];
            } else {
                $k[0] = $key_long[$j % $n_key_long];
                $k[1] = $key_long[($j + 1) % $n_key_long];
                $k[2] = $key_long[($j + 2) % $n_key_long];
                $k[3] = $key_long[($j + 3) % $n_key_long];
            }
            $j = ($j + 4) % $n_key_long;
            $this->_encipherLong($data_long[$i], $data_long[++$i], $w, $k);
            $enc_data .= $this->_long2str($w[0]);
            $enc_data .= $this->_long2str($w[1]);
        }
        return $enc_data;
    }

    /**
     * 解密函数  
     * @access public 
     * @param  string  $enc_data 加密过得数据 
     * @param  string  $key      解密秘钥 
     * @return string  原始数据
     **/
    public function decrypt($enc_data, $key = '')
    {
        if (!$enc_data) {
            return '';
        }
        $n_enc_data_long = $this->_str2long(0, $enc_data, $enc_data_long);
        $this->_resize($key, 16, true);
        if ('' == $key) {
            $key = $this->file_key;
        }
        $n_key_long = $this->_str2long(0, $key, $key_long);
        $data = '';
        $w = array(0, 0);
        $j = 0;
        $len = 0;
        $k = array(0, 0, 0, 0);
        $pos = 0;
        for ($i = 0; $i < $n_enc_data_long; $i += 2) {
            if ($j + 4 <= $n_key_long) {
                $k[0] = $key_long[$j];
                $k[1] = $key_long[$j + 1];
                $k[2] = $key_long[$j + 2];
                $k[3] = $key_long[$j + 3];
            } else {
                $k[0] = $key_long[$j % $n_key_long];
                $k[1] = $key_long[($j + 1) % $n_key_long];
                $k[2] = $key_long[($j + 2) % $n_key_long];
                $k[3] = $key_long[($j + 3) % $n_key_long];
            }
            $j = ($j + 4) % $n_key_long;
            $this->_decipherLong($enc_data_long[$i], $enc_data_long[$i + 1], $w, $k);
            if (0 == $i) {
                $len = $w[0];
                if (4 <= $len) {
                    $data .= $this->_long2str($w[1]);
                } else {
                    $data .= substr($this->_long2str($w[1]), 0, $len % 4);
                }
            } else {
                $pos = ($i - 1) * 4;
                if ($pos + 4 <= $len) {
                    $data .= $this->_long2str($w[0]);
                    if ($pos + 8 <= $len) {
                        $data .= $this->_long2str($w[1]);
                    } elseif ($pos + 4 < $len) {
                        $data .= substr($this->_long2str($w[1]), 0, $len % 4);
                    }
                } else {
                    $data .= substr($this->_long2str($w[0]), 0, $len % 4);
                }
            }
        }
        return $data;
    }
    
    private function _encipherLong($y, $z, &$w, &$k)
    {
        $sum = (int) 0;
        $delta = 0x9e3779b9;
        $n = (int) $this->n_iter;
        while ($n-- > 0) {
            $y = $this->_add($y, $this->_add($z << 4 ^ $this->_rshift($z, 5), $z) ^ $this->_add($sum, $k[$sum & 3]));
            $sum = $this->_add($sum, $delta);
            $z = $this->_add($z, $this->_add($y << 4 ^ $this->_rshift($y, 5), $y) ^ $this->_add($sum, $k[$this->_rshift($sum, 11) & 3]));
        }
        $w[0] = $y;
        $w[1] = $z;
    }

    private function _decipherLong($y, $z, &$w, &$k)
    {
        $sum = 0xc6ef3720;
        $delta = 0x9e3779b9;
        $n = (int) $this->file_iter;
        while ($n-- > 0) {
            $z = $this->_add($z, -($this->_add($y << 4 ^ $this->_rshift($y, 5), $y) ^ $this->_add($sum, $k[$this->_rshift($sum, 11) & 3])));
            $sum = $this->_add($sum, -$delta);
            $y = $this->_add($y, -($this->_add($z << 4 ^ $this->_rshift($z, 5), $z) ^ $this->_add($sum, $k[$sum & 3])));
        }
        $w[0] = $y;
        $w[1] = $z;
    }

    private function _resize(&$data, $size, $nonull = false)
    {
        $n = strlen($data);
        $nmod = $n % $size;
        if (0 == $nmod) {
            $nmod = $size;
        }
        if ($nmod > 0) {
            if ($nonull) {
                for ($i = $n; $i < $n - $nmod + $size; ++$i) {
                    $data[$i] = $data[$i % $n];
                }
            } else {
                for ($i = $n; $i < $n - $nmod + $size; ++$i) {
                    $data[$i] = chr(0);
                }
            }
        }
        return $n;
    }

    private function _hex2bin($str)
    {
        $len = strlen($str);
        return pack('H' . $len, $str);
    }

    private function _str2long($start, &$data, &$data_long)
    {
        $n = strlen($data);
        $tmp = unpack('N*', $data);
        $j = $start;
        foreach ($tmp as $value) {
            $data_long[$j++] = $value;
        }
        return $j;
    }

    private function _long2str($l)
    {
        return pack('N', $l);
    }

    private function _rshift($integer, $n)
    {
        if (0xffffffff < $integer || -0xffffffff > $integer) {
            $integer = fmod($integer, 0xffffffff + 1);
        }
        if (0x7fffffff < $integer) {
            $integer -= 0xffffffff + 1.0;
        } elseif (-0x80000000 > $integer) {
            $integer += 0xffffffff + 1.0;
        }
        if (0 > $integer) {
            $integer &= 0x7fffffff;
            $integer >>= $n;
            $integer |= 1 << 31 - $n;
        } else {
            $integer >>= $n;
        }
        return $integer;
    }

    private function _add($i1, $i2)
    {
        $result = 0.0;
        foreach (func_get_args() as $value) {
            if (0.0 > $value) {
                $value -= 1.0 + 0xffffffff;
            }
            $result += $value;
        }
        if (0xffffffff < $result || -0xffffffff > $result) {
            $result = fmod($result, 0xffffffff + 1);
        }
        if (0x7fffffff < $result) {
            $result -= 0xffffffff + 1.0;
        } elseif (-0x80000000 > $result) {
            $result += 0xffffffff + 1.0;
        }
        return $result;
    }
}