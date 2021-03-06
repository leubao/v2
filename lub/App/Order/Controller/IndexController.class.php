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
		$map = array_merge($rest['map'], $map);
		$field = [
			'order_sn',
			'addsid',
			'number',
			'createtime',
			'money',
			'plan_id',
			'status',
			'user_id',
			'pay',
			'type',
			'activity'
		];
		//判断是否存在分表
		$table = $this->getTableName();
		$this->basePage($table, $map, array('createtime'=>'DESC'), 25, $field);
		$this->assign('map',$map)->assign('export_map',$export_map)->display();
	}
	
	/*订单导出*/
	function public_export_order(){
		$starttime = I('starttime');
	    $endtime = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    //限制导出时间范围不能超过60天
        $check_day = timediff($starttime,$endtime);
        if($check_day['day'] > '60'){
        	$this->erun("亲，一次最多只能导出60天的数据....");
        	return false;
        }
        $map = [];
        $rest = $this->get_order_map();
		$map = array_merge($rest['map'],$map);
        $table = $this->getTableName();
        
		$list = M($table)->where($map)->field(['order_sn','addsid','type','number','money','plan_id','channel_id','user_id','take','phone','status','createtime'])->select();
		if(count($list) > 10000){
			$this->erun("亲，一次最多只能导出10000条数据,请缩短日期范围后重试....");
        	return false;
		}
		$uIdx = array_column($list, 'user_id');
		$cIdx = array_column($list, 'channel_id');
		//->where(['id'=>['in', $cIdx]])
		$crm = D('Crm')->field('id,name')->select();
		$crm = array_column($crm, 'name', 'id');
		$user = D('User')->where(['id'=>['in', $uIdx]])->field('id,nickname')->select();
		$user = array_column($user, 'nickname', 'id');

		foreach ($list as $k => $v) {
			$cid = money_map($v['channel_id'],1);
   			$data[] = array(
   				'sn'		=>	$v['order_sn'],
   				'scena'		=>	addsid($v['addsid'],1).'('.channel_type($v['type'],1).')',
	   			'number'	=>	$v['number'],
	   			'money'		=>	$v['money'],
	   			'plan'		=>	planShow($v['plan_id'],2,1),
	   			'mpc'		=>	$crm[$cid],
	   			'channel'	=>	$crm[$v['channel_id']],//crmName($v['channel_id'],1),
	   			'user'		=>	$user[$v['user_id']],//userName($v['user_id'],1,1),
	   			'take'		=>	$v['take'],
	   			'phone'		=>	$v['phone'],
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
   			'mpc'		=>	'结算渠道商',
   			'channel'	=>	'渠道商',
   			'user'		=>	'下单人',
   			'take'		=>	'联系人',
   			'phone'		=>	'联系电话',
   			'status'	=>	'状态',
   			'datetime'	=>	'操作时间',
   		);
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
        $map['type'] = ['in','2,4,6,7'];
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
	        
	        $map['product_id'] = get_product('id');
        }
        
        $return = [
        	'map'	=>	$map,
        	'export'=>	$export_map
        ];
        return $return;
	}
	public function pre_order()
	{
		$map = [];
		$rest = $this->get_order_map();
		$map = array_merge($rest['map'],$map);
		unset($map['createtime']);
		$this->basePage('Booking',$map,array('createtime'=>'DESC'));
		$this->display();
	}
	/*打印团队接待单*/
	function team_report()
	{
		$ginfo = I('get.');
		if(empty($ginfo['sn'])){
			$this->erun("参数错误");
		}
		//根据单号获取订单信息
		$model= D('Item/Order');
		$map = [
			'order_sn'	=>	$ginfo['sn'],
			'type'  =>	['in','2,4,6,7'],
			'status'=>	['in','1,9']
		];
		$info = $model->where($map)->field('id,order_sn,user_id,plan_id,product_id,type,number,money,guide_id,channel_id,phone,take,status,createtime')->relation(true)->find();
		$detail = unserialize($info['info']);
		foreach ($detail['data'] as $k => $v) {
			$tic[$v['areaId']][$v['priceid']]['areaId']	= $v['areaId'];
			$tic[$v['areaId']][$v['priceid']]['priceid'] = $v['priceid'];
			$tic[$v['areaId']][$v['priceid']]['price'] = $v['price'];
			$tic[$v['areaId']][$v['priceid']]['number'] += 1;
		}
		foreach ($tic as $key => $value) {
			foreach ($value as $ke => $va) {
				$ticket[] = $va;
			}
		}
		$this->assign('data',$info)->assign('user_id',get_user_id())->assign('ticket',$ticket)->assign('param',$detail['param'])->assign('crm',$detail['crm'])->display();
	}
	//按场次加载座位销售情况
	public function plan_sales_seat()
	{
		$map = [];
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
		if(!empty($pinfo['sn'])){
			$map['order_sn|idcard'] = $pinfo['sn'];
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
	/**
	 * 身份证日志
	 * @Company  承德乐游宝软件开发有限公司
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2018-01-04
	 * @return   [type]        [description]
	 */
	public function idcard_log()
	{
		$pinfo = I('post.');
		//是否是活动
		if(!empty($pinfo['activity'])){
			$map['activity_id'] = $pinfo['activity'];
		}
		if(!empty($pinfo['idcard'])){
			$map['idcard']	= $pinfo['idcard'];
		}
		//获取半年内的活动
		$datetime = strtotime("-0 year -6 month -0 day");
		$where = [
			'product_id'	=>	get_product('id'),
			'endtime'		=>	['gt',$datetime]
		];
		$activity = D('Activity')->where($where)->field('id,title')->select();
		$this->assign('activity',$activity)->assign('pinfo',$pinfo);
		$this->basePage('idcardLog',$map,'id DESC');

		$this->display();
	}
	/**
	 * 删除身份证号
	 * @Company  承德乐游宝软件开发有限公司
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2018-01-04
	 * @return   [type]        [description]
	 */
	public function del_idcard()
	{
		$ginfo = I('get.');
		if(empty($ginfo['id'])){
			$this->erun('参数错误...');
		}
		$status = D('idcardLog')->delete($ginfo['id']);
		if($status){
			$this->srun('删除成功',array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$this->erun('删除失败...');
		}
	}
	/**
	 * @Company  承德乐游宝软件开发有限公司
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2018-09-24
	 * @return   年卡入园记录        [description]
	 */
	public function year_log()
	{
		$starttime = I('starttime');
	    $endtime = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $sn = I('sn');
	    $status = I('status');
	     $this->assign('starttime',$starttime)
	        ->assign('endtime',$endtime)
	        ->assign('status',$status);
	    $this->basePage('MemberLog', $map, 'id DESC', 25, 'id,sn,thetype,password,member_id,status,datetime,update_time');
	    $this->assign('pinfo',$pinfo)
			->display();
	}

	/**
	 * 动态获取表名称
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-27T00:38:45+0800
	 * @return   [type]                   [description]
	 */
	public function getTableName()
	{
		$time = strtotime('2018-12-31');
		$endtime = I('endtime');
		if(empty($endtime)){
			$table = 'Order';
		}else{
			if(strtotime($endtime) > $time){
				$table = 'Order';
			}else{
				$table = 'Order_2018';
			}
		}
		return $table;
	}
}