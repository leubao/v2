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
		$where = array();
        $start_time = I('starttime');
        $end_time = I('endtime');
        $sn = I('order_sn');
        $phone = I('phone');
        $this->assign('starttime',$start_time)->assign('endtime',$end_time);
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }
        if($phone != ''){
            $where['phone'] = $phone;
        }
        if($sn != ''){
            $where['order_sn'] = $sn;
        } 
		$this->basePage('SmsLog',$where,'createtime DESC');
		$this->assign('where',$where)->display();
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