<?php
// +----------------------------------------------------------------------
// | LubTMP 信任接口管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

namespace Common\Controller;
use \Libs\Service\ArrayUtil;
class TrustBase extends LubTMP {

	protected function _initialize() {
	{
		parent::_initialize();
		//判断请求方式
		if(!IS_POST){
			return showReturnCode(false,1000,'','','不被支持的请求方式');
		}
		//验证来路域名
		$url = $_SERVER['HTTP_REFERER'];
		$trustUrl = [
			'api.alizhiyou.com',
			'api.leubao.com'
		];
		if(!in_array($url, $trustUrl)){
			return showReturnCode(false,1005);
		}
		//验证签名
		if(!$this->checkSign()){
			return showReturnCode(false,1009);
		}
		
		
	}
	//验证签名
	public function checkSign()
	{
		$pinfo = I('post.');
		//获取请求数据中的签名
		//生成新的签名
		$signData = ArrayUtil::removeKeys($pinfo);
		$signData = ArrayUtil::arraySort($signData);
        $signStr = ArrayUtil::createLinkstring($signData);
        if($pinfo['sign'] === $signStr){
        	if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
        		return false;
        	}
        	return true;
        }
        return false;
	}

}