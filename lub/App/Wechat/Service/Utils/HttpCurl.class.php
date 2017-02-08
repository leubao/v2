<?php
/**
 * HttpCurl Curl模拟Http工具类
 * 
 *
 * @author      gaoming13 <gaoming13@yeah.net>
 * @link        https://github.com/gaoming13/wechat-php-sdk
 * @link        http://me.diary8.com/
 */

namespace Wechat\Service\Utils;

class HttpCurl {

    /**
     * 模拟GET请求
     *
     * @param string $url
     * @param string $data_type     
     *
     * @return mixed
     * 
     * Examples:
     * ```   
     * HttpCurl::get('http://api.example.com/?a=123&b=456', 'json');
     * ```               
     */
    static public function get($url, $data_type='text') {
        $cl = curl_init();
        if(stripos($url, 'https://') !== FALSE) {
            curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($cl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($cl, CURLOPT_URL, $url);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1 );
        $content = curl_exec($cl);
        $status = curl_getinfo($cl);
        curl_close($cl);
        if (isset($status['http_code']) && $status['http_code'] == 200) {
            if ($data_type == 'json') {
                $content = json_decode($content);
            }
            return $content;
        } else {
            return FALSE;
        }        
    }

    /**
     * 模拟POST请求
     *
     * @param string $url
     * @param array $fields
     * @param string $data_type
     * @param int $security 是否安全请求 zj
     * @return mixed
     * 
     * Examples:
     * ```   
     * HttpCurl::post('http://api.example.com/?a=123', array('abc'=>'123', 'efg'=>'567'), 'json');
     * HttpCurl::post('http://api.example.com/', '这是post原始内容', 'json');
     * 文件post上传
     * HttpCurl::post('http://api.example.com/', array('abc'=>'123', 'file1'=>'@/data/1.jpg'), 'json');
     * ```               
     */
    static public function post($url, $fields, $data_type='text', $security = null) {
        $cl = curl_init();
        if(stripos($url, 'https://') !== FALSE) {
            curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($cl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($cl, CURLOPT_URL, $url);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($cl, CURLOPT_POST, true);
 
        // convert @ prefixed file names to CurlFile class
        // since @ prefix is deprecated as of PHP 5.6
        if (class_exists('\CURLFile')) {
            foreach ($fields as $k => $v) {
                if (strpos($v, '@') === 0) {
                    $v = ltrim($v, '@');
                    $fields[$k] = new \CURLFile($v);
                }
            }
        }
        curl_setopt($cl, CURLOPT_POSTFIELDS, $fields);
        $content = curl_exec($cl);
        $status = curl_getinfo($cl);
        curl_close($cl);
        if (isset($status['http_code']) && $status['http_code'] == 200) {
            if ($data_type == 'json') {
                $content = json_decode($content);
            }
            return $content;
        } else {
            return FALSE;
        }
    }

    //提交请求
    function curl_post_ssl($url, $vars, $second = 30, $aHeader = array()) {
        $ch = curl_init ();
        //超时时间
        curl_setopt ( $ch, CURLOPT_TIMEOUT, $second );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        //这里设置代理，如果有的话
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
        
        //cert 与 key 分别属于两个.pem文件
        curl_setopt ( $ch, CURLOPT_SSLCERT, dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'cacert' . DIRECTORY_SEPARATOR . 'apiclient_cert.pem' );
        curl_setopt ( $ch, CURLOPT_SSLKEY, dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'cacert' . DIRECTORY_SEPARATOR . 'apiclient_key.pem' );
        curl_setopt ( $ch, CURLOPT_CAINFO, dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'cacert' . DIRECTORY_SEPARATOR . 'rootca.pem' );
        
        if (count ( $aHeader ) >= 1) {
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $aHeader );
        }
        
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $vars );
        $data = curl_exec ( $ch );
        if ($data) {
            curl_close ( $ch );
            return $data;
        } else {
            $error = curl_errno ( $ch );
            curl_close ( $ch );
            return false;
        }
    }
}