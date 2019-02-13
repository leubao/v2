<?php
namespace Payment\Utils;

/**
 * Class StrUtil
 * @dec 字符串处理类
 * @package Payment\Utils
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
class StrUtil
{
    /**
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return string 产生的随机字符串
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * 转码字符集转码  仅支持 转码 到 UTF-8
     * @param string $str
     * @param string $targetCharset
     * @return mixed|string
     */
    public static function characet($str, $targetCharset)
    {
        if (empty($str)) {
            return $str;
        }

        if (strcasecmp('UTF-8', $targetCharset) != 0) {
            $str = mb_convert_encoding($str, $targetCharset, 'UTF-8');
        }

        return $str;
    }
    /** 
     * js escape php 实现 
     * @param $string           the sting want to be escaped 
     * @param $in_encoding       
     * @param $out_encoding 
     * Author zhoujing     
     */ 
    public static function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') { 
        $return = ''; 
        if (function_exists('mb_get_info')) { 
            for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) { 
                $str = mb_substr ( $string, $x, 1, $in_encoding ); 
                if (strlen ( $str ) > 1) { // 多字节字符 
                    $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) ); 
                } else { 
                    $return .= '%' . strtoupper ( bin2hex ( $str ) ); 
                } 
            } 
        } 
        return $return; 
    }
    /**
     * 转成16进制
     * @param string $string
     * @return string
     */
    public static function String2Hex($string)
    {
        $hex = '';
        $len = strlen($string);
        for ($i=0; $i < $len; $i++) {
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    /**
     * 获取rsa密钥内容
     * @param string $key 传入的密钥信息， 可能是文件或者字符串
     * @param string $type
     *
     * @return string
     */
    public static function getRsaKeyValue($key, $type = 'private')
    {
        if (is_file($key)) {// 是文件
            $keyStr = @file_get_contents($key);
        } else {
            $keyStr = $key;
        }
        if (empty($keyStr)) {
            return null;
        }

        $keyStr = str_replace(PHP_EOL, '', $keyStr);
        // 为了解决用户传入的密钥格式，这里进行统一处理
        if ($type === 'private') {
            $beginStr = '-----BEGIN RSA PRIVATE KEY-----';
            $endStr = '-----END RSA PRIVATE KEY-----';
        } else {
            $beginStr = '-----BEGIN PUBLIC KEY-----';
            $endStr = '-----END PUBLIC KEY-----';
        }
        $rsaKey = chunk_split(base64_encode($keyStr), 64, "\n");
        $rsaKey = $beginStr . PHP_EOL . $keyStr . PHP_EOL . $endStr;

        return $rsaKey;
    }
}
