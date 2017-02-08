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
		$this->basePage('SmsLog');
		$this->display();
	}
	//模板管理
	function template(){
		
	}
	//演示
}