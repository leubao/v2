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
		$map = [];
		$rest = $this->get_order_map();
		$map = array_merge($rest['map'],$map);
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
	//订单确认
	function confirm_order(){
		$ginfo = I('get.');
		if(empty($ginfo['sn'])){
			$this->erun("参数错误");
		}
		$map = [
			'order_sn'	=>	$ginfo['sn'],
			'status'	=>	'1'
		];
		$data = [
			'status'	=>	'9',
			'uptime'	=>	time()
		];
		$status = D('Item/Order')->where($map)->save($data);
		if($status){
			$this->srun('订单确认成功...',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		}else{
			$this->erun('订单确认失败...');
		}
	}
	/**
	 * 团队订单
	 */
	public function team()
	{
		$map = [];
        $map['type'] = ['in','2,4,6'];
        $map['status'] = ['in','1,9'];
		$rest = $this->get_order_map();
		$map = array_merge($rest['map'],$map);
		$this->basePage('Order',$map,array('createtime'=>'DESC'));	
		$this->assign('map',$map)->assign('export_map',$rest['export'])->display();
	}
	/**
	 * 获取订单列表条件
	 * @Company  承德乐游宝软件开发有限公司
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2017-12-18
	 * @return   array
	 */
	function get_order_map()
	{
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
		            $map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
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
        $map['product_id'] = get_product('id');
        $return = [
        	'map'	=>	$map,
        	'export'=>	$export_map
        ];
        return $return;
	}
	/*打印团队接待单*/
	function team_report()
	{
		$ginfo = I('get.');
		if(empty($ginfo['sn'])){
			$this->erun("参数错误");
		}
		//根据单号获取订单信息
		$model= D('Order');
		$map = [
			'order_sn'	=>	$ginfo['sn'],
			'type'  =>	['in','2,4,6'],
			'status'=>	['in','1,9']
		];
		$info = $model->where($map)->field('id,order_sn,user_id,plan_id,product_id,type,number,money,guide_id,channel_id,phone,take,status,createtime')->relation(true)->find();
		$detail = unserialize($info['info']);
		foreach ($detail['data'] as $k => $v) {
			$tic[$v['areaId']][$v['priceid']]['areaId']	=	$v['areaId'];
			$tic[$v['areaId']][$v['priceid']]['priceid'] = $v['priceid'];
			$tic[$v['areaId']][$v['priceid']]['price'] = $v['price'];
			$tic[$v['areaId']][$v['priceid']]['number'] += 1;
		}
		foreach ($tic as $key => $value) {
			foreach ($value as $ke => $va) {
				$ticket[] = $va;
			}
		}
		$this->assign('data',$info)->assign('ticket',$ticket)->assign('param',$detail['param'])->assign('crm',$detail['crm'])->display();
	}
	//按场次加载座位销售情况
	public function plan_sales_seat()
	{
		$pinfo = I('post.');
		switch ($pinfo['type']) {
			case '1':
				$map = [];
				break;
			case '2':
				$map = ['status' =>	0];
				break;
			case '3':
				$map = ['status' =>	['in','2,99']];
				break;
			case '4':
				$map = ['idcard' => ['neq','']];
				break;
			case '5':
				$map = ['status' =>	99];
				break;
			case '6':
				$map = ['status' =>	2];
				break;
		}
		if(!empty($pinfo['plan'])){
			$plan_info = F('Plan_'.$pinfo['plan']);
			if(empty($plan_info)){
				$plan_info = M('Plan')->where(array('id'=>$pinfo['plan']))->find();
			}
			$table = ucwords($plan_info['seat_table']);
			$this->basePage($table, $map, '', 25, 'id,order_sn,area,seat,idcard,status,checktime');
		}
		$plantime = strtotime(" -2 day ",strtotime(date('Y-m-d')));
		$plan = M('Plan')->where(array('plantime'=>array('egt',$plantime)))->field('plantime,games,id,starttime')->order('plantime ASC')->select();
		$this->assign('plan',$plan)
			->assign('pinfo',$pinfo)
			->display();
	}
}