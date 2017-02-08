<?php
// +----------------------------------------------------------------------
// | LubTMP 云平台--短信网关
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Apps\Controller;
use Common\Controller\ManageBase;
class SmsController extends ManageBase{
	//短信发送记录
	function index(){
		$this->basePage('SmsLog','','createtime DESC');
		$this->display();
	}
	//模板管理
	function template(){
		
	}
	//短信模板
	function gateway(){
		$this->display();
	}
	//账户详情 从远端服务器获取短信余额详情
	function account(){
		$url = "http://www.leubao.com/project/index.php";
		$post = array(
			'appid' 	=>	'',
			'appkey'	=>	'',
			'type'		=>	'sms',
		);
		$data = curl_server($url,$post);
		$data = json_decode($data,true);
		$this->display();
	}
}