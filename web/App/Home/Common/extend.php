<?php
/**
 * 模拟请求
 * @param  string $url  访问地址
 * @param string $method 请求方式
 * @param array  $postData
 *
 * @return mixed|null|string
 */
function getHttpContent($url, $method = 'GET', $postData = array()){
    $data = '';
    if (!empty($url)) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); //30秒超时
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
            if (strtoupper($method) == 'POST') {
                $curlPost = is_array($postData) ? http_build_query($postData) : $postData;
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
            }
            $data = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            $data = null;
        }
    }
    return $data;
}
/**
 * 获取产品名称
 * @param $param int 产品ID
 */
function product_name($param,$type=NULL){
    if(!empty($param)){
         $name = M('Product')->where(array('id'=>$param))->getField('name');
         if($type){
            return $name;
         }else{
            echo $name;
         }
    }else{
        echo "未知";
    }   
}
/**
 * 根据ID显示销售计划信息
 * @param $param 计划ID
 * @param $stype 显示方式
 */
function planShow($param,$stype = 1,$type=NULL){
    if(!empty($param)){
        $info = M('Plan')->where(array('id'=>$param))->field('id,plantime,games,starttime,endtime')->find();
        switch ($stype) {
            case '1':
            //完全展示
                $name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")". "&nbsp;&nbsp;第".$info['games']."场&nbsp;&nbsp;".date('H:i',$info['starttime'])."-".date('H:i',$info['endtime']);
                break;
            case '2':
            //不显示场次
                $name = date('Y-m-d',$info['plantime'])."(".get_chinese_weekday($info['plantime']).")".date('H:i',$info['starttime'])."-".date('H:i',$info['endtime']);
                break;
            case '3':
            //不现实场次且简短日期显示 2014-12-16 19:00
                $name = date('Y-m-d',$info['plantime'])."&nbsp;&nbsp;".date('H:i',$info['starttime']);
                break;
            case '4':
            //不显示场次 和结束时间
                $name = date('Y-m-d',$info['plantime'])."&nbsp;".get_chinese_weekday($info['plantime'])."&nbsp;".date('H:i',$info['starttime']);
                break;
        }
        if($type){
            return $name;
        }else{
            echo $name;
        }
    }else{
        echo "场次未知";
    }
}
/**
 * 汉化星期
 */
function get_chinese_weekday($datetime){
    $weekday  = date('w', $datetime);
    $weeklist = array('日', '一', '二', '三', '四', '五', '六');
    return '周' . $weeklist[$weekday];
}
/*二维数组转字符串
* @param array $arr 待处理的数组
* @param string $field 字段
* @param string $seg 字符串分隔符,默认','分割
*/
function arr2string($arr,$field,$seg = ','){
    $array = array_column($arr,$field);
    $return = implode($seg,$array);
    return $return;
}
/**
 * 二位数组转一维数组
 */
if (!function_exists('array_column')) {
    function array_column($input, $columnKey, $indexKey = null) {
        $columnKeyIsNumber = (is_numeric($columnKey)) ? true : false;
        $indexKeyIsNull = (is_null($indexKey)) ? true : false;
        $indexKeyIsNumber = (is_numeric($indexKey)) ? true : false;
        $result = array();
        foreach ((array) $input as $key => $row) {
            if ($columnKeyIsNumber) {
                $tmp = array_slice($row, $columnKey, 1);
                $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : null;
            } else {
                $tmp = isset($row[$columnKey]) ? $row[$columnKey] : null;
            }
            if (!$indexKeyIsNull) {
                if ($indexKeyIsNumber) {
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && !empty($key)) ? current($key) : null;
                    $key = is_null($key) ? 0 : $key;
                } else {
                    $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    }
}
/**
* 座椅显示处理
* @param $param 座椅iD
*/
function seatShow($param,$type=NULL){
    if(!empty($param)){
        $seta = explode('-', $param);
        $name = $seta['0']."排".$seta['1']."号";
        if($type){
            return $name;
         }else{
            echo $name;
         }
    }else{
        echo "未知";
    }
}
/**
 * 区域名称
 *  @param $param int 区域ID
 *  @param $type int 数据返回方式 
 */
function areaName($param,$type=NULL){
    if(!empty($param)){
        $area = F('Area');
        if(!empty($area)){
            $name = $area[$param]['name'];
        }else{
            $name = M('Area')->where(array('id'=>$param))->getField('name');
        }
        if($type){
          return $name;
        }else{
          echo $name;
        }
    }else{
        echo "区域未知";
    }
}
/**
 * 格式化金额
 *
 * @param int $money
 * @param int $len
 * @param string $sign
 * @return string
 */
function format_money($money, $len=2, $sign='￥'){
    $negative = $money >= 0 ? '' : '-';
    $int_money = intval(abs($money));
    $len = intval(abs($len));
    $decimal = '';//小数
    if ($len > 0) {
        $decimal = '.'.substr(sprintf('%01.'.$len.'f', $money),-$len);
    }
    $tmp_money = strrev($int_money);
    $strlen = strlen($tmp_money);
    for ($i = 3; $i < $strlen; $i += 3) {
        $format_money .= substr($tmp_money,0,3).',';
        $tmp_money = substr($tmp_money,3);
    }
    $format_money .= $tmp_money;
    $format_money = strrev($format_money);
    return $sign.$negative.$format_money.$decimal;
}
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed

function get_client_ip($type = 0, $adv = false) {
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
} */
/**
 * 获取支付操作配置信息
 * @param $pay string ali_app  ali_wap  ali_web  ali_qr  ali_bar 
 * || wx_app    wx_pub   wx_qr   wx_bar  wx_lite   wx_wap
 * @param $product_id 产品ID
 * @param $sub 是否开启子商户
 * @param $define 是否采用默认配置
 */
function load_payment($pay = '',$product_id = ''){
    static $payment = array();
    //根据产品读取配置信息
    if(empty($product_id)){
        $proconf = cache('Config');
        $options = array(
            'app_id'  => $proconf['wx_appid'], // 填写高级调用功能的app id, 请在微信开发模式后台查询
            'mch_id'  => $proconf['wx_mchid'], // 微信支付，商户ID（可选）
        );
        return $options;
    }
    $proconf = cache('ProConfig');
    $proconf = $proconf[$product_id][11];
    $options = [
        'use_sandbox'           => true,
        'partner'               => $proconf['ali_partner_id'],
        'app_id'                => $proconf['ali_app_id'],
        'sign_type'             => 'RSA2',

        'ali_public_key'        => $proconf['ali_public_key'],
        'rsa_private_key'       => SITE_PATH.'pay/alipay/'.$product_id.'/rsa_private_key.pem',
        'limit_pay'             => [
            //'balance',
            //'moneyFund',
            // ... ...
        ],

        'notify_url'            => 'https://ticket.leubao.com/',
        'return_url'            => 'https://ticket.leubao.com/',

        'return_raw'            => false,

    ];
    $options = array_merge($basedata,$options);
    //根据支付类型选择驱动
    return $options;
}