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
        $status = I('status');
        $this->assign('starttime',$start_time)->assign('endtime',$end_time)->assign('status', $status);
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }else{
        	$starttime = strtotime(date("Ymd"));
            $endtime = $starttime + 86399;
        	$map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
        }
        if($phone != ''){
            $where['phone'] = $phone;
        }
        if($sn != ''){
            $where['order_sn'] = $sn;
        }
        if(!empty($status)){
        	if(in_array($status, ['0','DELIVRD'])){
        		$where['status'] = $status;
        	}else{
        		$where['status'] = ['in', ['0','DELIVRD']];
        	}
        }
		$this->basePage('SmsLog',$where,'createtime DESC');
		$this->assign('where',$where)->display();
	}
	//统计条数
	//短信重发
	function resend()
	{
		if(IS_POST){
			$pinfo = I('post.');
			if(empty($pinfo['sn']) || empty($pinfo['content']) || empty($pinfo['phone'])){
				$this->erun('参数有误~');
			}
			
			\Libs\Service\Sms::customSms($pinfo['sn'], $pinfo['content'], $pinfo['phone']);
			$this->srun('推送成功!',array('tabid'=>$this->menuid,'closeCurrent'=>true,'divid'=>$this->menuid));
		}else{

			$ginfo = I('get.');
			if(!isset($ginfo['id']) || empty($ginfo['id'])){

			}
			$info = D('SmsLog')->where(['id'=>$ginfo['id']])->find();
			$this->assign('data', $info);
			$this->display();
		}
	}
	//短信模板
	function gateway(){
		$this->display();
	}
	//账户详情 从远端服务器获取短信余额详情
	function account(){
		$num = D('Config')->where(['varname'=>'sms'])->getField('value');
		$date = date('Y-m-d'.'00:00:00',time());
        //获取昨天00:00
		$timestart = strtotime($date) - 86399;
		//获取今天00:00
		$timeend = strtotime($date);
		$where['createtime'] = array(array('EGT', $timestart), array('ELT', $timeend), 'AND');
		$day = D('SmsLog')->where($where)->sum('num');

        $end_time = strtotime($date) + 86399;
        var_dump(date('Y-m-d H:i:s', $timestart), date('Y-m-d H:i:s', $timeend), date('Y-m-d H:i:s', $end_time));
		$map['createtime'] = array(array('EGT', $timeend), array('ELT', $end_time), 'AND');
		$today = D('SmsLog')->where($map)->sum('num');var_dump($day, $today);
		$this->assign('num', $num)->assign('day', $day)->assign('today', $today)->display();
	}
}