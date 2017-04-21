<?php
// +----------------------------------------------------------------------
// | LubTMP 订单管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Order\Controller;

use Libs\Service\Operate;

use Common\Controller\ManageBase;
use Libs\Service\Order;

class IndexController extends ManageBase {
	/*订单列表*/
	function index(){
		$starttime = I('starttime');
	    $endtime = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $sn = I('sn');
	    $status = I('status');
	    $channel_id = I('channel_id');
	    $channel_name = I('channel_name');
	    $user_id = I('user_id');
	    $user_name = I('user_name');
	    $plan_id = I('plan_id');
	    $plan_name = I('plan_name');
	    $type = I('type');
	    $phone = I('phone');
	    $pay = I('pay');
	    
	    //传递条件
        $this->assign('channel_id',$channel_id)
	        ->assign('channel_name',$channel_name)
	        ->assign('user_id',$user_id)
	        ->assign('user_name',$user_name)
	        ->assign('starttime',$starttime)
	        ->assign('endtime',$endtime)
	        ->assign('plan_id',$plan_id)
	        ->assign('plan_name',$plan_name)
	        ->assign('status',$status)
	        ->assign('pay',$pay);
	    //导出条件
	    $export_map = array();
        if(!empty($sn)){
        	$map['order_sn'] = array('like','%'.$sn.'%');
        }elseif(!empty($phone)){
        	$map['phone'] = $phone;
        }else{
        	if(!empty($plan_id)){
				$map['plan_id'] = $plan_id;
				$export_map['plan_id'] = $plan_id;
        	}else{
        		if (!empty($starttime) && !empty($endtime)) {
        			$export_map['starttime'] = $starttime;
        			$export_map['endtime'] = $endtime;
		            $starttime = strtotime($starttime);
		            $endtime = strtotime($endtime) + 86399;
		            $map['createtime'] = array(array('GT', $starttime), array('LT', $endtime), 'AND');
		        }else{
		        	//默认显示当天的订单
		        	$starttime = strtotime(date("Ymd"));
		            $endtime = $starttime + 86399;
		        	$map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
		        }
        	}
	        if(!empty($channel_id)){
	        	$export_map['channel_id'] = $channel_id;
	        	$map['channel_id'] = array('in',agent_channel($channel_id,2));

	        }
	        if(!empty($user_id)){
	        	$export_map['user_id'] = $user_id;
	        	$map['user_id'] = $user_id;
	        }
	        if(!empty($status)){$export_map['status'] = $status;$map['status'] = array('in',$status);}
	        if(!empty($type)){$map['type'] = $type;}
	        if(!empty($pay)){$map['pay'] = $pay;}
        }
		$map['product_id'] = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
		$this->basePage('Order',$map,array('createtime'=>'DESC'));	
		$this->assign('map',$map)->assign('export_map',$export_map)->display();
	}
	/*订单导出*/
	function public_export_order(){
		$starttime = I('starttime');
	    $endtime = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $status = I('status');
	    $channel_id = I('channel_id');
	    $user_id = I('user_id');
	    $plan_id = I('plan_id');
	    $type = I('type');
        //限制导出时间范围不能超过60天
        $check_day = timediff($starttime,$endtime);
        if($check_day['day'] > '60'){
        	$this->erun("亲，一次最多只能导出60天的数据....");
        	return false;
        }
	    if(!empty($plan_id)){
				$map['plan_id'] = $plan_id;
    	}else{
    		if (!empty($starttime) && !empty($endtime)) {
	            $starttime = strtotime($starttime);
	            $endtime = strtotime($endtime) + 86399;
	            $map['createtime'] = array(array('GT', $starttime), array('LT', $endtime), 'AND');
	        }else{
	        	//默认显示当天的订单
	        	$starttime = strtotime(date("Ymd"));
	            $endtime = $starttime + 86399;
	        	$map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
	        }
    	}
        if(!empty($channel_id)){
        	$map['channel_id'] = $channel_id;
        }
        if(!empty($user_id)){
        	$map['user_id'] = $user_id;
        }
        if(!empty($status)){$map['status'] = array('in',$status);}
        if(!empty($type)){$map['type'] = $type;}
		$list = M('Order')->where($map)->select();
		foreach ($list as $k => $v) {
   			$data[] = array(
   				'sn'		=>	$v['order_sn'],
   				'scena'		=>	addsid($v['addsid'],1).'('.channel_type($v['type'],1).')',
	   			'number'	=>	$v['number'],
	   			'money'		=>	$v['money'],
	   			'plan'		=>	planShow($v['plan_id'],2,1),
	   			'user'		=>	userName($v['user_id'],1,1),
	   			'status'	=>	order_status($v['status'],1),
	   			'datetime'	=>	date('Y-m-d H:i:s',$v['createtime']),
	   			);
   		}
   		$headArr = array(
   			'sn'		=>	'订单号',
   			'scena'		=>	'场景(类型)',
   			'number'	=>	'数量',
   			'money'		=>	'金额',
   			'plan'		=>	'所属计划',
   			'user'		=>	'下单人',
   			'status'	=>	'状态',
   			'datetime'	=>	'操作时间',
   		);//dump($data);
   		$filename = "订单记录";
   		return \Libs\Service\Exports::getExcel($filename,$headArr,$data);
   		exit;
	}
}