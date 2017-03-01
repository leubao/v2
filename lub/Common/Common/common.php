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
 */
function isModuleInstall($moduleName) {
    $appCache = cache('Module');
    if (isset($appCache[$moduleName])) {
        return true;
    }
    return false;
}

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
 * 返回附件类型图标
 * @param $file 附件名称
 * @param $type png为大图标，gif为小图标
 */
function file_icon($file, $type = 'png') {
    $ext_arr = array('doc', 'docx', 'ppt', 'xls', 'txt', 'pdf', 'mdb', 'jpg', 'gif', 'png', 'bmp', 'jpeg', 'rar', 'zip', 'swf', 'flv');
    $ext = fileext($file);
    if ($type == 'png') {
        if ($ext == 'zip' || $ext == 'rar')
            $ext = 'rar';
        elseif ($ext == 'doc' || $ext == 'docx')
            $ext = 'doc';
        elseif ($ext == 'xls' || $ext == 'xlsx')
            $ext = 'xls';
        elseif ($ext == 'ppt' || $ext == 'pptx')
            $ext = 'ppt';
        elseif ($ext == 'flv' || $ext == 'swf' || $ext == 'rm' || $ext == 'rmvb')
            $ext = 'flv';
        else
            $ext = 'do';
    }
    $config = cache('Config');
    if (in_array($ext, $ext_arr)) {
        return $config['siteurl'] . 'statics/images/ext/' . $ext . '.' . $type;
    } else {
        return $config['siteurl'] . 'statics/images/ext/blank.' . $type;
    }
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
 * 获取模版文件 格式 主题://模块/控制器/方法
 * @param type $templateFile
 * @return boolean|string 
 */
function parseTemplateFile($templateFile = '') {
    static $TemplateFileCache = array();
    //模板路径
    $TemplatePath = TEMPLATE_PATH;
    //模板主题
    $Theme = empty(\Common\Controller\LubTMP::$Cache["Config"]['theme']) ? 'Default' : \Common\Controller\LubTMP::$Cache["Config"]['theme'];
    //如果有指定 GROUP_MODULE 则模块名直接是GROUP_MODULE，否则使用 MODULE_NAME，这样做的目的是防止其他模块需要生成
    $group = defined('GROUP_MODULE') ? GROUP_MODULE : MODULE_NAME;
    //兼容 Add:ss 这种写法
    if (!empty($templateFile) && strpos($templateFile, ':') && false === strpos($templateFile, C('TMPL_TEMPLATE_SUFFIX'))) {
        if (strpos($templateFile, '://')) {
            $temp = explode('://', $templateFile);
            $fxg = str_replace(':', '/', $temp[1]);
            $templateFile = $temp[0] . $fxg;
        } else {
            $templateFile = str_replace(':', '/', $templateFile);
        }
    }
    if ($templateFile != '' && strpos($templateFile, '://')) {
        $exp = explode('://', $templateFile);
        $Theme = $exp[0];
        $templateFile = $exp[1];
    }
    // 分析模板文件规则
    $depr = C('TMPL_FILE_DEPR');
    //模板标识
    if ('' == $templateFile) {
        $templateFile = $TemplatePath . $Theme . '/' . $group . '/' . CONTROLLER_NAME . '/' . ACTION_NAME . C('TMPL_TEMPLATE_SUFFIX');
    }
    $key = md5($templateFile);
    if (isset($TemplateFileCache[$key])) {
        return $TemplateFileCache[$key];
    }
    if (false === strpos($templateFile, '/') && false === strpos($templateFile, C('TMPL_TEMPLATE_SUFFIX'))) {
        $templateFile = $TemplatePath . $Theme . '/' . $group . '/' . CONTROLLER_NAME . '/' . $templateFile . C('TMPL_TEMPLATE_SUFFIX');
    } else if (false === strpos($templateFile, C('TMPL_TEMPLATE_SUFFIX'))) {
        $path = explode('/', $templateFile);
        $action = array_pop($path);
        $controller = !empty($path) ? array_pop($path) : CONTROLLER_NAME;
        if (!empty($path)) {
            $group = array_pop($path)? : $group;
        }
        $depr = defined('MODULE_NAME') ? C('TMPL_FILE_DEPR') : '/';
        $templateFile = $TemplatePath . $Theme . '/' . $group . '/' . $controller . $depr . $action . C('TMPL_TEMPLATE_SUFFIX');
    }
    //区分大小写的文件判断，如果不存在，尝试一次使用默认主题
    if (!file_exists_case($templateFile)) {
        $log = '模板:[' . $templateFile . '] 不存在！';
        \Think\Log::record($log);
        //启用默认主题模板
        $templateFile = str_replace($TemplatePath . $Theme, $TemplatePath . 'Default', $templateFile);
        //判断默认主题是否存在，不存在直接报错提示
        if (!file_exists_case($templateFile)) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                E($log);
            }
            $TemplateFileCache[$key] = false;
            return false;
        }
    }
    $TemplateFileCache[$key] = $templateFile;
    return $TemplateFileCache[$key];
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
function timediff($begin_time,$end_time) {
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
    $days = intval($timediff/86400); 
    $remain = $timediff%86400; 
    $hours = intval($remain/3600); 
    $remain = $remain%3600; 
    $mins = intval($remain/60); 
    $secs = $remain%60; 
    $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs); 
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
 * 异或加密
 * @param  string $data 字符串
 * @param  string $key  加密key
 * @return string 
 */
function xorcrypt($data, $key){
    $key_len = strlen($key);
    $data_len = strlen($data);
    for($i=0;$i<$data_len;$i++){
        $data[$i] = $data[$i]^$key[$i%$key_len];
    }
    return $data;
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
 * 获取支付操作对象
 * @param $pay string wxpay || alipay
 */
function load_payment($pay = '',$product_id = ''){
    static $payment = array();
    //根据产品读取配置信息
    if(empty($product_id)){return false;}
    dump($product_id);
    $proconf = cache('ProConfig_'.$product_id);
    dump($proconf);
    if($pay == 'alipay'){
        /*
        $options = array(
            // 老版本参数，当使用新版本时，不需要传入
            'partner'   => '',// 请填写自己的支付宝账号信息
            'md5_key'   => 'xxxxxx',// 此密码无效，请填写自己对应设置的值
            // 转款接口，必须配置以下两项
            'account'   => 'xxxxx@126.com',
            'account_name' => 'xxxxx',
            'sign_type' => 'RSA',// 默认方式    目前支持:RSA   MD5`
            // 如果没有设置以下内容，则默认使用老版本
            // 支付宝2.0 接口  如果使用支付宝 新版 接口，请设置该参数，并且必须为 1.0。否则将默认使用支付宝老版接口
            'ali_version'   => '1.0',// 调用的接口版本，固定为：1.0
            'app_id'        => '2016073100130857',// 支付宝分配给开发者的应用ID
            'use_sandbox'   => true,//  新版支付，支持沙箱调试
            'ali_public_key'    => SITE_PATH . 'pay/alipay/' . 'alipay_public_key.pem',// 支付宝新版本，每个应用对应的公钥都不一样了

            // 新版与老版支付  共同参数，
            'rsa_private_key'   => SITE_PATH . 'pay/alipay/' . 'rsa_private_key.pem',
            'notify_url'        => 'https://helei112g.github.io/',
            'return_url'        => 'https://helei112g.github.io/',// 我的博客地址
            'time_expire'       => '15',// 取值为分钟
        );*/
        $options = array(
            // 老版本参数，当使用新版本时，不需要传入
            'partner'   => '2088102176680186',// 请填写自己的支付宝账号信息
            'md5_key'   => 'xxxxxx',// 此密码无效，请填写自己对应设置的值
            // 转款接口，必须配置以下两项
            'account'   => 'sxw1988@126.com',
            'account_name' => '沙箱测试应用',
            'sign_type' => 'RSA',// 默认方式    目前支持:RSA   MD5`
            // 如果没有设置以下内容，则默认使用老版本
            // 支付宝2.0 接口  如果使用支付宝 新版 接口，请设置该参数，并且必须为 1.0。否则将默认使用支付宝老版接口
            'ali_version'   => '1.0',// 调用的接口版本，固定为：1.0
            'app_id'        => '2016092100565723',// 支付宝分配给开发者的应用ID
            'use_sandbox'   => true,//  新版支付，支持沙箱调试
            'ali_public_key'    => SITE_PATH . 'pay/alipay/'. $product_id . '/alipay_public_key.pem',// 支付宝新版本，每个应用对应的公钥都不一样了

            // 新版与老版支付  共同参数，
            'rsa_private_key'   => SITE_PATH . 'pay/alipay/' . $product_id . '/rsa_private_key.pem',
            'notify_url'        => 'https://helei112g.github.io/',
            'return_url'        => 'https://helei112g.github.io/',// 我的博客地址
            'time_expire'       => '15',// 取值为分钟
        );
    }else{
        $options = array(
            'app_id'    => 'wxxxxx',  // 公众账号ID
            'mch_id'    => 'xxxxx',// 商户id
            'md5_key'   => 'xxxxxx',// md5 秘钥

            'notify_url'    => 'https://helei112g.github.io/',
            'time_expire'   => '14',

            // 涉及资金流动时 退款  转款，需要提供该文件
            //'cert_path' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wx' . DIRECTORY_SEPARATOR . 'apiclient_cert.pem',
            //'key_path'  => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wx' . DIRECTORY_SEPARATOR . 'apiclient_key.pem',
            'cert_path' => SITE_PATH . 'pay/wxpay/' . $product_id . '/apiclient_cert.pem',
            'key_path'  => SITE_PATH . 'pay/wxpay/' . $product_id . '/apiclient_key.pem',
        );
    }
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