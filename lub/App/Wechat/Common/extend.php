<?php
// +----------------------------------------------------------------------
// | LubTMP  系统扩展函数  微信模块
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
/*微信类型*/
function wechat_type($param,$type = '1'){
	switch ($param) {
		case '1':
			$return = "普通订阅号";
			break;
		case '2':
			$return = "认证订阅号/普通服务号";
			break;
		case '3':
			$return = "认证服务号";
			break;
		case '4':
			$return = "企业号";
			break;
	}
	if($type == '1'){
		echo $return;
	}else{
		return $return;
	}
}

/*判断用户是否已经授权*/
function if_auth($openid = null){
	if(empty($open_id)){
        return false;
    }
	if(M('WxMember')->where(array('openid'=>$openid))->find() != false){
		return true;
	}else{
		return false;
	}
}
/**
 * url参数加密解密
 * @param  string $param 加密或解密参数
 * @param  string $type  DECODE 解密 ENCODE 加密
 * @return [type]        [description]
 */
function url_param($param, $type = 'DECODE'){
	$data = \Libs\Util\Encrypt::authcode($param,$type);
	return $data;
}
//判断当前是否是微信打开
function is_weixin() { 
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { 
        return true; 
    } return false; 
}
?>