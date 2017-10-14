<?php
// +----------------------------------------------------------------------
// | LubTMP 智游宝操作类
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Org\Util\Date;
use Common\Model\Model;
class Zyb extends \Libs\System\Service {
	function __construct()
	{
		//测试账号：admin 测试企业码：TESTFX  私钥：TESTFX 
	//测试用票票型编码：PST20160918013085备注：测试环境用上述参数
	//信任地址验证
		//接口配置信息
		$this->config = [
			'API_BASE_URL_PREFIX' => 'http://ds-zff.sendinfo.com.cn/boss/service/code.htm',
			'CORP_CODE'		=>	'TESTFX',
			'PRIVATE_KEY'	=>	'TESTFX',
			'ACCOUNT'		=>	'admin',
			'PRODUCT_NO'	=>	'201704180000001893'
		];
	}
	//生成签名
	function get_sign($string = '')
	{
		$str = "xmlMsg=".$string.$this->config['PRIVATE_KEY'];
		return md5($str);
	}
	//发起请求
	function postServer($pdata = ''){
		$baseData = [
			'header'		  =>	[
				'application'	  =>	'SendCode',//固定值
				'requestTime'	  =>	date('Y-m-d'),//日期
			],
			'identityInfo'	  =>	[
				'corpCode'		  =>	$this->config['CORP_CODE'],
				'userName'		  =>	$this->config['ACCOUNT'],//订单创建人
			]
		];
		$data = array_merge($baseData,$pdata);
		//数组转XML
		$postXML = xml_encode($data,'PWBRequest');
		//dump($postXML);
		$postData = [
			'xmlMsg'	=>	$postXML,
			'sign'		=>	$this->get_sign($postXML),
		];
		//发起请求获取返回结果
		$return = getHttpContent($this->config['API_BASE_URL_PREFIX'],'POST',$postData);
		//处理返回结果
		$arr = xmlToArray($return);
		if($arr['code'] != 0){
			//接口返回错误
		}
		//记录接口与日志
		return $arr;
	}
}