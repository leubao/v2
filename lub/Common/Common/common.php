<?php
// +----------------------------------------------------------------------
// | LubTMP
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

/**
 * 系统缓存缓存管理
 * @param mixed $name 缓存名称
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
function cache($name, $value = '', $options = null) {
    static $cache = '';
    if (empty($cache)) {
        $cache = \Libs\System\Cache::getInstance();
    }
    // 获取缓存
    if ('' === $value) {
        if (false !== strpos($name, '.')) {
            $vars = explode('.', $name);
            $data = $cache->get($vars[0]);
            return is_array($data) ? $data[$vars[1]] : $data;
        } else {
            return $cache->get($name);
        }
    } elseif (is_null($value)) {//删除缓存
        return $cache->remove($name);
    } else {//缓存数据
        if (is_array($options)) {
            $expire = isset($options['expire']) ? $options['expire'] : NULL;
        } else {
            $expire = is_numeric($options) ? $options : NULL;
        }
        return $cache->set($name, $value, $expire);
    }
}

/**
 * 调试，用于保存数组到txt文件 正式生产删除
 * 用法：array2file($info, SITE_PATH.'post.txt');
 * @param type $array
 * @param type $filename
 */
function array2file($array, $filename) {
    if (defined("APP_DEBUG") && APP_DEBUG) {
        //修改文件时间
        file_exists($filename) or touch($filename);
        if (is_array($array)) {
            $str = var_export($array, TRUE);
        } else {
            $str = $array;
        }
        return file_put_contents($filename, $str);
    }
    return false;
}

/**
 * 返回LubTMP对象
 * @return Object
 */
function LubTMP() {
    return \Common\Controller\LubTMP::app();
}

/**
 * 快捷方法取得服务
 * @param type $name 服务类型
 * @param type $params 参数
 * @return type
 */
function service($name, $params = array()) {
    return \Libs\System\Service::getInstance($name, $params);
}

/**
 * 生成上传附件验证
 * @param $args   参数
 */
function upload_key($args) {
    return md5($args . md5(C("AUTHCODE") . $_SERVER['HTTP_USER_AGENT']));
}

/**
 * 检查模块是否已经安装
 * @param type $moduleName 模块名称
 * @return boolean
 
function isModuleInstall($moduleName) {
    $appCache = cache('Module');
    if (isset($appCache[$moduleName])) {
        return true;
    }
    return false;
}
*/
/**
 * 产生一个指定长度的随机字符串,并返回给用户 
 * @param type $len 产生字符串的长度
 * @param $type int 生成类型
 * @return string 随机字符串
 */
function genRandomString($len = 6,$type = null) {
    if($type == '1'){
        $chars = array("0", "1", "2","3", "4", "5", "6", "7", "8", "9");
    }else{
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
    }  
    $charsLen = count($chars) - 1;
    // 将数组打乱 
    shuffle($chars);
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

/**
 * 获取模型数据
 * @param type $modelid 模型ID
 * @param type $field 返回的字段，默认返回全部，数组
 * @return boolean
 */
function getModel($modelid, $field = '') {
    if (empty($modelid)) {
        return false;
    }
    $key = 'getModel_' . $modelid;
    $cache = S($key);
    if ($cache === 'false') {
        return false;
    }
    if (empty($cache)) {
        //读取数据
        $cache = M('Model')->where(array('modelid' => $modelid))->find();
        if (empty($cache)) {
            S($key, 'false', 60);
            return false;
        } else {
            S($key, $cache, 3600);
        }
    }
    if ($field) {
        return $cache[$field];
    } else {
        return $cache;
    }
}

/**
 * 检测一个数据长度是否超过最小值
 * @param type $value 数据
 * @param type $length 最小长度
 * @return type 
 */
function isMin($value, $length) {
    return mb_strlen($value, 'utf-8') >= (int) $length ? true : false;
}

/**
 * 检测一个数据长度是否超过最大值
 * @param type $value 数据
 * @param type $length 最大长度
 * @return type 
 */
function isMax($value, $length) {
    return mb_strlen($value, 'utf-8') <= (int) $length ? true : false;
}

/**
 * 取得文件扩展
 * @param type $filename 文件名
 * @return type 后缀
 */
function fileext($filename) {
    $pathinfo = pathinfo($filename);
    return $pathinfo['extension'];
}

/**
 * 对 javascript escape 解码
 * @param type $str 
 * @return type
 */
function unescape($str) {
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        if ($str[$i] == '%' && $str[$i + 1] == 'u') {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f)
                $ret .= chr($val);
            else
            if ($val < 0x800)
                $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
            else
                $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
            $i += 5;
        } else
        if ($str[$i] == '%') {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        } else
            $ret .= $str[$i];
    }
    return $ret;
}

/**
 * 字符截取
 * @param $string 需要截取的字符串
 * @param $length 长度
 * @param $dot
 */
function str_cut($sourcestr, $length, $dot = '...') {
    $returnstr = '';
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr); //字符串的字节数 
    while (($n < $length) && ($i <= $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = Ord($temp_str); //得到字符串中第$i位字符的ascii码 
        if ($ascnum >= 224) {//如果ASCII位高与224，
            $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符         
            $i = $i + 3; //实际Byte计为3
            $n++; //字串长度计1
        } elseif ($ascnum >= 192) { //如果ASCII位高与192，
            $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符 
            $i = $i + 2; //实际Byte计为2
            $n++; //字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90) { //如果是大写字母，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，大写字母计成一个高位字符
        } else {//其他情况下，包括小写字母和半角标点符号，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1;            //实际的Byte数计1个
            $n = $n + 0.5;        //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length > strlen($returnstr)) {
        $returnstr = $returnstr . $dot; //超过长度时在尾处加上省略号
    }
    return $returnstr;
}

/**
 * 取得URL地址中域名部分
 * @param type $url 
 * @return \url 返回域名
 */
function urlDomain($url) {
    if ($url) {
        $pathinfo = parse_url($url);
        return $pathinfo['scheme'] . "://" . $pathinfo['host'] . "/";
    }
    return false;
}

/**
 * 获取当前页面完整URL地址
 * @return type 地址
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}

/**
 * 根据文件扩展名来判断是否为图片类型
 * @param type $file 文件名
 * @return type 是图片类型返回 true，否则返回 false
 */
function isImage($file) {
    $ext_arr = array('jpg', 'gif', 'png', 'bmp', 'jpeg', 'tiff');
    //取得扩展名
    $ext = fileext($file);
    return in_array($ext, $ext_arr) ? true : false;
}

/**
 * 对URL中有中文的部分进行编码处理
 * @param type $url 地址 http://www.chengde360.com/s?wd=博客
 * @return type ur;编码后的地址 http://www.chengde360.com/s?wd=%E5%8D%9A%20%E5%AE%A2
 */
function cn_urlencode($url) {
    $pregstr = "/[\x{4e00}-\x{9fa5}]+/u"; //UTF-8中文正则
    if (preg_match_all($pregstr, $url, $matchArray)) {//匹配中文，返回数组
        foreach ($matchArray[0] as $key => $val) {
            $url = str_replace($val, urlencode($val), $url); //将转译替换中文
        }
        if (strpos($url, ' ')) {//若存在空格
            $url = str_replace(' ', '%20', $url);
        }
    }
    return $url;
}

/**
 * 邮件发送
 * @param type $address 接收人 单个直接邮箱地址，多个可以使用数组
 * @param type $title 邮件标题
 * @param type $message 邮件内容
 */
function SendMail($address, $title, $message) {
    $config = cache('Config');
    import('PHPMailer');
    try {
        $mail = new \PHPMailer();
        $mail->IsSMTP();
        // 设置邮件的字符编码，若不指定，则为'UTF-8'
        $mail->CharSet = C("DEFAULT_CHARSET");
        $mail->IsHTML(true);
        // 添加收件人地址，可以多次使用来添加多个收件人
        if (is_array($address)) {
            foreach ($address as $k => $v) {
                if (is_array($v)) {
                    $mail->AddAddress($v[0], $v[1]);
                } else {
                    $mail->AddAddress($v);
                }
            }
        } else {
            $mail->AddAddress($address);
        }
        // 设置邮件正文
        $mail->Body = $message;
        // 设置邮件头的From字段。
        $mail->From = $config['mail_from'];
        // 设置发件人名字
        $mail->FromName = $config['mail_fname'];
        // 设置邮件标题
        $mail->Subject = $title;
        // 设置SMTP服务器。
        $mail->Host = $config['mail_server'];
        // 设置为“需要验证”
        if ($config['mail_auth']) {
            $mail->SMTPAuth = true;
        } else {
            $mail->SMTPAuth = false;
        }
        // 设置用户名和密码。
        $mail->Username = $config['mail_user'];
        $mail->Password = $config['mail_password'];
        return $mail->Send();
    } catch (phpmailerException $e) {
        return $e->errorMessage();
    } 
}
// +----------------------------------------------------------------------
// | LubTMP 汉字转拼音
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------
define('CODETABLEDIR', COMMON_PATH . 'Data/');

/**
 * gbk转拼音
 * @param $txt
 */
function gbk_to_pinyin($txt) {
    $l = strlen($txt);
    $i = 0;
    $pyarr = array();
    $py = array();
    $filename = CODETABLEDIR . 'gb-pinyin.table';
    $fp = fopen($filename, 'r');
    while (!feof($fp)) {
        $p = explode("-", fgets($fp, 32));
        $pyarr[intval($p[1])] = trim($p[0]);
    }
    fclose($fp);
    ksort($pyarr);
    while ($i < $l) {
        $tmp = ord($txt[$i]);
        if ($tmp >= 128) {
            $asc = abs($tmp * 256 + ord($txt[$i + 1]) - 65536);
            $i = $i + 1;
        } else
            $asc = $tmp;
        $py[] = asc_to_pinyin($asc, $pyarr);
        $i++;
    }
    return $py;
}

/**
 * Ascii转拼音
 * @param $asc
 * @param $pyarr
 */
function asc_to_pinyin($asc, &$pyarr) {
    if ($asc < 128)
        return chr($asc);
    elseif (isset($pyarr[$asc]))
        return $pyarr[$asc];
    else {
        foreach ($pyarr as $id => $p) {
            if ($id >= $asc)
                return $p;
        }
    }
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
 /*计算时间差并返回差多少天、时、分、秒 
  * @param string $begin_time
  * @param string $end_time
  * @return string
  */
function timediff($begin_time,$end_time,$type = 'all') {
    $begin_time = strtotime($begin_time);
    $end_time = strtotime($end_time);  
    if($begin_time < $end_time){ 
        $starttime = $begin_time; 
        $endtime = $end_time; 
    }else{ 
        $starttime = $end_time; 
        $endtime = $begin_time; 
    }
    $timediff = $endtime - $starttime;
    switch ($type) {
        case 'day':
            $days = intval($timediff/86400);
            $res = array("day" => $days); 
            break;
        case 'hour':
            $hours = intval($timediff/3600);
            $res = array("hour" => $hours); 
            break;
        case 'min':
            $mins = intval($timediff/60);
            $res = array("min" => $mins); 
            break;
        case 'all':
            $days = intval($timediff/86400); 
            $remain = $timediff%86400; 
            $hours = intval($remain/3600); 
            $remain = $remain%3600; 
            $mins = intval($remain/60); 
            $secs = $remain%60;
            $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs); 
            break;
    }
    return $res; 
}
/*二维数组转字符串
* @param array $arr 待处理的数组
* @param string $field 字段
* @param string $seg 字符串分隔符,默认','分割
*/
function arr2string($arr,$field,$seg = ','){
    $array = array_unique(array_column($arr,$field));
    $return = implode($seg,$array);
    return $return;
}
/*身份证号码校验
*@param $idcard 身份证号码验证
*/
function checkIdCard($idcard){
    // 只能是18位
    if(strlen($idcard)!=18){
        return false;
    }
    // 取出本体码
    $idcard_base = substr($idcard, 0, 17);
    // 取出校验码
    $verify_code = substr($idcard, 17, 1);
    // 加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    // 校验码对应值
    $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    // 根据前17位计算校验码
    $total = 0;
    for($i=0; $i<17; $i++){
        $total += substr($idcard_base, $i, 1)*$factor[$i];
    }
    // 取模
    $mod = $total % 11;
    // 比较校验码
    if($verify_code == $verify_code_list[$mod]){
        return true;
    }else{
        return false;
    }
}
/*返回去除敏感信息的客户信息
*@param $uinfo 包含敏感信息的
*/
function senuInfo($uinfo){
    $unset = array(
        'id'=>'',
        'username'=>'',
        'password'=>'',
        'last_login_time'=>'',
        'last_login_ip'=>'',
        'verify'=>'',
        'email'=>'', 
        'remark'=>'',
        'create_time'=>'',
        'update_time'=>'',
        'status'=>'',
        'is_scene'=>'',
        'role_id'=>'',
        'info'=>'',
        'rpassword'=>'',
        );
    $return = array_diff_key($uinfo,$unset);
    return $return;
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
/*判断终端访问类型*/
function is_mobile() { 
    $user_agent = $_SERVER['HTTP_USER_AGENT']; 
    $mobile_agents = array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi", 
    "android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio", 
    "au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu", 
    "cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ", 
    "fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi", 
    "htc","huawei","hutchison","inno","ipad","ipaq","iphone","ipod","jbrowser","kddi", 
    "kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo", 
    "mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-", 
    "moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia", 
    "nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-", 
    "playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo", 
    "samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank", 
    "sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit", 
    "tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin", 
    "vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce", 
    "wireless","xda","xde","zte"); 
    $is_mobile = false; 
    foreach ($mobile_agents as $device) { 
        if (stristr($user_agent, $device)) { 
            $is_mobile = true; 
            break; 
        } 
    } 
    return $is_mobile; 
}
/**
*数字金额转换成中文大写金额的函数
*String Int  $num  要转换的小写数字或小写字符串
*return 大写字母
*小数位为两位
**/
function num_to_rmb($num){
    $c1 = "零壹贰叁肆伍陆柒捌玖";
    $c2 = "分角元拾佰仟万拾佰仟亿";
    //精确到分后面就不要了，所以只留两个小数位
    $num = round($num, 2); 
    //将数字转化为整数
    $num = $num * 100;
    if (strlen($num) > 10) {
            return "金额太大，请检查";
    } 
    $i = 0;
    $c = "";
    while (1) {
        if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num)-1, 1);
        } else {
                $n = $num % 10;
        }
        //每次将最后一位数字转化为中文
        $p1 = substr($c1, 3 * $n, 3);
        $p2 = substr($c2, 3 * $i, 3);
        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
        } else {
                $c = $p1 . $c;
        }
        $i = $i + 1;
        //去掉数字最后一位了
        $num = $num / 10;
        $num = (int)$num;
        //结束循环
        if ($num == 0) {
                break;
        } 
    }
    $j = 0;
    $slen = strlen($c);
    while ($j < $slen) {
        //utf8一个汉字相当3个字符
        $m = substr($c, $j, 6);
        //处理数字中很多0的情况,每次循环去掉一个汉字“零”
        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j-3;
                $slen = $slen-3;
        } 
        $j = $j + 3;
    } 
    //这个是为了去掉类似23.0中最后一个“零”字
    if (substr($c, strlen($c)-3, 3) == '零') {
            $c = substr($c, 0, strlen($c)-3);
    }
    //将处理的汉字加上“整”
    if (empty($c)) {
        return "零元整";
    }else{
        return $c . "整";
    }
}
/**
 * 获取微信操作对象
 * @staticvar array $wechat
 * @param  type $type
 * @param  string $scene      场景
 * @param  string $product_id 产品id
 * @return WechatReceive
 */
function & load_wechat($type = '',$product_id = '',$submch = '') {
    !class_exists('Wechat\Loader', FALSE) && Vendor('Wechat.Loader'); 
    static $wechat = array();
    $index = md5(strtolower($type));
    if (!isset($wechat[$index])) {
        if(!empty($product_id)){
            $proconf = cache('ProConfig');
            $proconf = $proconf[$product_id][2];
            //定义微信公众号配置参数（这里是可以从数据库读取的哦）
            $options = array(
                'token'           => $proconf['wx_token'], // 填写你设定的key
                'appid'           => $proconf['wx_appid'], // 填写高级调用功能的app id, 请在微信开发模式后台查询
                'appsecret'       => $proconf['appsecret'], // 填写高级调用功能的密钥
                'encodingaeskey'  => $proconf['wx_encoding'], // 填写加密用的EncodingAESKey（可选，接口传输选择加密时必需）
                'mch_id'          => $proconf['wx_mchid'], // 微信支付，商户ID（可选）
                'partnerkey'      => $proconf['wx_mchkey'], // 微信支付，密钥（可选）
                'sub_appid'       => $proconf['wx_sub_appid'], //子APPiD
                'sub_mch_id'      => $proconf['wx_sub_mch_id'], //子商户ID
                'ssl_cer'         => SITE_PATH.'pay/wxpay/'.$product_id.'/apiclient_cert.pem', // 微信支付，双向证书（可选，操作退款或打款时必需）
                'ssl_key'         => SITE_PATH.'pay/wxpay/'.$product_id.'/apiclient_key.pem', // 微信支付，双向证书（可选，操作退款或打款时必需）
                //'cachepath'       => '', // 设置SDK缓存目录（可选，默认位置在Wechat/Cache下，请保证写权限）
            );
            if($submch == '1'){
                //页面注册等使用子商户id  作为主要ID
                $options = array(
                    'appid'           => $proconf['wx_sub_appid'],
                    'mch_id'          => $proconf['wx_sub_mch_id'], // 微信支付，商户ID（可选）
                    'token'           => $proconf['wx_token'], // 填写你设定的key
                    'appsecret'       => $proconf['wx_appsecret'], // 填写高级调用功能的密钥
                    'encodingaeskey'  => $proconf['wx_encoding'], // 
                    'partnerkey'      => $proconf['wx_sub_mchkey'], // 微信支付，密钥（可选）
                );
            }
            // 设置SDK的缓存路径
            $options['cachepath'] = SITE_PATH . 'paylog/';
        }else{
            $proconf = cache('Config');
            $options = array(
                'appid'           => $proconf['wx_appid'], // 填写高级调用功能的app id, 请在微信开发模式后台查询
                'mch_id'          => $proconf['wx_mchid'], // 微信支付，商户ID（可选）
                'partnerkey'      => $proconf['appsecret'], // 微信支付，密钥（可选）
            );
        }
        $wechat[$index] = & \Wechat\Loader::get_instance($type, $options);
    }
    return $wechat[$index];
}
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
    //服务商模式,支持服务商模式与不支持
    $payment = array('wx_transfer','wx_red','wx_refund','ali_transfer','ali_red','ali_refund');
    //判断收款还是付款
    //收款
    $collection = array();
    //付款
    $payment = array('wx_transfer','wx_red','wx_refund','ali_transfer','ali_red','ali_refund');
    if(in_array($pay,$b)){
        //付款
        if (stripos($pay, 'ali') !== false) {

        }else{
            $basedata = [
                'app_id'        => $proconf['wx_mch_appid'],  // 公众账号ID
                'mch_id'        => $proconf['wx_payment_mch_id'],// 商户id
                'md5_key'       => $proconf['wx_payment_mchkey'],// md5 秘钥
            ];
        }
    }else{
        //收款
        if($proconf['wx_submch'] == '1'){
            $basedata = [
                'app_id'        => $proconf['wx_fw_appid'],  // 公众账号ID
                'mch_id'        => $proconf['wx_fw_mchid'],// 商户id
                'sub_appid'     => $proconf['wx_sub_appid'], //子APPiD
                'sub_mch_id'    => $proconf['wx_sub_mchid'],
                'md5_key'       => $proconf['wx_sub_mchkey'],// md5 秘钥
            ];
        }else{
            $basedata = [
                'app_id'        => $proconf['wx_sub_appid'], //子APPiD
                'mch_id'        => $proconf['wx_sub_mchid'],
                'md5_key'       => $proconf['wx_sub_mchkey'],// md5 秘钥
            ];
        }
    }
    //判断是否是企业付款
    if (stripos($pay, 'ali') !== false) {

    }else{
        $notify_url = U('Api/PayNotify/wxnotify');
        $options = array(
            'app_cert_pem'  => SITE_PATH.'pay/wxpay/'.$product_id.'/apiclient_cert.pem',
            'app_key_pem'   => SITE_PATH.'pay/wxpay/'.$product_id.'/apiclient_key.pem',
            'sign_type'     => 'MD5',// MD5  HMAC-SHA256
            'limit_pay'     => [/*'no_credit', */],// 指定不能使用信用卡支付   不传入，则均可使用
            'fee_type' => 'CNY',// 货币类型  当前仅支持该字段
            'notify_url'    => $notify_url,
            'redirect_url' => 'http://ticket.leuao.com',// 如果是h5支付，可以设置该值，返回到指定页面
            'return_raw'   => true,// 在处理回调时，是否直接返回原始数据，默认为false
        );
    }
    $options = array_merge($basedata,$options);
    //根据支付类型选择驱动
    return $options;
}
/**
 * lubTicket redis 操作API
 * @param  string $apiport 要操作的接口
 * @param  string $key     键名
 * @param  string $value   键值
 * @param  string $time    有效时间
 * @return true|false  
 */
function load_redis($apiport,$key,$value = '',$time = ''){
    $redis = new \Redis();
    $redis->connect(C('REDIS_HOST'),C('REDIS_PORT'));
    //$redis->auth(C('REDIS_AUTH'));
    $redis->select(C('REDIS_DATABASE'));
    switch ($apiport) {
        case 'lsize':
            //判断列表中元素个数
            $return = $redis->lsize($key);
            break;
        case 'rPop':
            //获取队列中最后一个元素，且移除
            if((int)$redis->lsize($key) > 0){
                $return = $redis->rPop($key);
            }else{
                $return = false;
            }
            break;
        case 'lpush':
            //写入带处理队列，若存在则不再写入
            $return = $redis->lPush($key,$value);
            break;
        case 'set':
            $return = $redis->set($key,$value);
            break;
        case 'setex':
            /**
             * 设置有效期
             */
            $return = $redis->setex($key, $time, $value);
            break;
        case 'get':
            $return = $redis->get($key);
            break;
        case 'lrange':
            //返回list 中的元素 返回名称为key的list中start至end之间的元素（end为 -1 ，返回所有） value 为开始位置 $time 为结束位置
            $return = $redis->lrange($key,$value,$time);
            break;
        case 'delete':
            //删除指定key
            $return = $redis->delete($key);
            break;
    }
    return $return;
}
/**
 * 解锁
 */
function un_action_lock($sn='')
{
    load_redis('delete','lock_'.$sn);
}
/**
 * 判断是否可打印门票
 * 计划ID必须
 */
function if_plan_print($plan_id){
    $plan = F('Plan_'.$plan_id);
    if(empty($plan)){
        $plan = M('Plan')->where(array('id'=>$plan_id))->find();
    }
    return true;
}
/**
 * 对明文密码，进行加密，返回加密后的密文密码
 * @param string $password 明文密码
 * @param string $verify 认证码
 * @return string 密文密码
 */
function hashPassword($password, $verify = "") {
    return md5($password . md5($verify));
}
/**
 * 获取产品配置信息
 * @param  int $product_id 产品ID
 * @param  int $type       配置信息类型
 * @return array
 */
function get_proconf($product_id,$type){
    $proconf = cache('ProConfig');
    return $proconf[$product_id][$type];
}