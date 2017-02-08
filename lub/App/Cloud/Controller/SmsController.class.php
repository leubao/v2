<?php
// +----------------------------------------------------------------------
// | LubTMP 云平台--短信网关
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Manage\Controller;
use Common\Controller\ManageBase;
class SmsController extends ManageBase{
	//短信发送记录
	function index(){
		$this->basePage();
		$this->display();
	}
	//应用详情
	function appinfo(){
		
	}
	//演示
}