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
class TrustBase extends LubTMP{

	protected function _initialize() {
	
		parent::_initialize();
		//判断请求方式
		if(!IS_POST){
			return showReturnCode(false,1000,'','','不被支持的请求方式');
		}
		// $pinfo = I('post.');
		// //校验签名唯一性判断
		// // if(Cache::get($param['sign'])){
		// // 	die(json_encode(['status'=>false,'code'=>'1011','data'=>[],'msg'=>'签名错误,签名无效']));
		// // }
		// // 校验时间 超过10s的为不合法请求
		// if((time() - $pinfo['timestamp']) > 60){
		// 	return showReturnCode(false,1011, [], '签名错误,请求超时');
		// }
		// //验证来路域名
		// // $url = $_SERVER['HTTP_REFERER'];
		// // $trustUrl = [
		// // 	'api.alizhiyou.com',
		// // 	'api.leubao.com',
		// // 	'ticket.leubao.com'
		// // ];
		// // if(!in_array($url, $trustUrl)){
		// // 	return showReturnCode(false,1005);
		// // }
		
		// //验证签名
		// if(!$this->checkSign()){
		// 	return showReturnCode(false,1009);
		// }
		
		
	}
	//获取产品
	public function getProduct($incode)
	{
		$product = D('Product')->where(['idCode' => $incode])->field('id,name,template_id,type,param')->find();
		$product['isArea'] = false;
		if((int)$product['type'] === 1){
			$product['isArea'] = true;
		}

		return $product;
	}
	//验证签名
    function checkSign()
	{
		$pinfo = I('post.');
		//获取请求数据中的签名
		//生成新的签名
        $signStr = ArrayUtil::setSign($pinfo);
        if($pinfo['sign'] === $signStr){
        	// if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
        	// 	return false;
        	// }
        	return true;
        }
        return false;
	}
}