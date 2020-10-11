<?php
// +----------------------------------------------------------------------
// | LubTMP 阿里智游信任接口
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Trust\Controller;

use Common\Controller\TrustBase;
use Libs\Service\Order;
use Libs\Service\Refund;
use Libs\Service\ArrayUtil;
class MpcController extends TrustBase{

	protected function _initialize() {
		parent::_initialize();
	}
	/**
	 * 获取权限
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-18T21:36:18+0800
	 * @return   [type]                   [description]
	 */
	public function get_auth()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['user_id']) || empty($pinfo['user_id'])){
			return showReturnCode(false,1003);
		}
		//获取权限
		$map = [
			'id' => $pinfo['user_id'],
			'is_scene'  =>  1
		];
		$user = D('User')->where($map)->field('id,nickname,role_id')->find();
		$auth = [
			//订单管理
			'1' =>  ['app'=>'Order', 'controller'=> 'index', 'action' => 'index'],
			//销售简报
			'2' =>  ['app'=>'Report', 'controller'=> 'Financial', 'action' => 'index'],
			//销售计划
			'3' =>  ['app'=>'Item', 'controller'=> 'Product', 'action' => 'plan'],
			//待处理订单
			'4' =>  ['app'=>'Item', 'controller'=> 'Work', 'action' => 'bookticket'],
			//退单管理
			'5' =>  ['app'=>'Item', 'controller'=> 'Work', 'action' => 'channel_refund'],
		];

		if((int)$user['role_id'] === 1){
			$authList = [1,2,3,4,5,6,7];
		}else{
			$authList = [6,7];
			foreach ($auth as $k => $v) {
				$map = [
					'role_id'   =>  $user['role_id'],
					'app'       =>  $v['app'],
					'controller'=>  $v['controller'],
					'action'    =>  $v['action'],
					'status'    =>  1
				];
				$role = D('Access')->where($map)->find();
				if($role){
					array_push($authList, $k);
				}
			} 
		} 
		return showReturnCode(true,0, $authList, 'ok');
	}
	/**
	 * 获取销售计划
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-08T12:28:31+0800
	 * @return   [type]                   [description]
	 */
	public function get_plan_list()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}

		$product = $this->getProduct($pinfo['incode']);
		$model = D('Item/Plan');
		$map = [
			'product_id' => $product['id'],
			'plantime'	 => ['egt', strtotime(date('Y-m-d'))]
		];
		$list = $model->where($map)->field('id,games,plantime,starttime,endtime,product_type,status')->order('plantime DESC')->select();
		return showReturnCode(true,0, $list, 'ok');
	}
	/**
	 * 获取销售详情
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-08T14:25:24+0800
	 * @return   [type]                   [description]
	 */
	public function get_plan_info()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['id']) || empty($pinfo['id'])){
			return showReturnCode(false,1003);
		}
		$product = $this->getProduct($pinfo['incode']);
		$model = D('Item/Plan');
		$map = [
			'product_id' => $product['id'],
			'id'	 => $pinfo['id']
		];
		$info = $model->where($map)->find();
		if(empty($info)){
			return showReturnCode(false,1004, [], '用户名或密码错误~');
		}
		if($info){
			$info['param'] = unserialize($info['param']);
			//票型价格信息
			$ticket = D('Item/TicketGroup')->relation(true)->where(array('product_id'=>$info['product_id'],'status'=>'1'))->select();
			
			//区域
			$area = D('Area')->where(['template_id' => $product['template_id']])->field('id,name')->select();
			$info['ticket'] = $ticket;
			$info['area'] = $area;
		}
		return showReturnCode(true,0, $info, 'ok');
	}
	/**
	 * 新增销售计划初始化数据
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-08T14:36:42+0800
	 * @return   [type]                   [description]
	 */
	public function get_plan_init()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		$product = $this->getProduct($pinfo['incode']);
		$model = D('Item/Plan');
		$init = $model->create_plan_init($product);
		$init['product'] = $product;

		return showReturnCode(true,0, $init, 'ok');
	}
	/**
	 * 新增计划
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-08T12:29:09+0800
	 * @return   [type]                   [description]
	 */
	public function post_plan()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		$field = ['plantime','games','starttime','endtime'];
		
		$product = $this->getProduct($pinfo['incode']);
		//校验票型编码是否都存在
		$ifTicket = false;
		$ticket = D('TicketType')->where(['product_id'=>$product['id'],'status'=>1])->field('id')->select();
		$ticket = array_column($ticket, 'id');
		foreach ($pinfo['ticket'] as $k => $v) {
			if(!in_array($v, $ticket)){
				$ifTicket = true;
				break;
			}
		}
		if($ifTicket){
			return showReturnCode(false,1007, [], '新增失败,存在过期票型,请重新加载~');
		}
		//判断产品类型
		if((int)$product['type'] === 1){
			$ifArea = false;
			$area = D('Area')->where(['status'=>1,'template_id'=>$product['template_id']])->field('id')->select();
			$area = array_column($area, 'id');
			if(empty($area)){
				return showReturnCode(false,1007, [], '新增失败,存在未授权区域,请重新加载~');
			}
			foreach ($pinfo['seat'] as $k => $v) {
				if(!in_array($v, $area)){
					$ifArea = true;
					break;
				}
			}
			if($ifArea){
				return showReturnCode(false,1007, [], '新增失败,存在未授权区域,请重新加载~');
			}
		}
		$data = [
			'batch'			=>	'one',
			'user_id'		=>	$pinfo['user_id'],
			'plantime'		=>	$pinfo['plantime'],
			'games'			=>	$pinfo['games'],
			'starttime'		=>	$pinfo['starttime'],
			'endtime'		=>  $pinfo['endtime'],
			'product_id'	=>	$product['id'],
			'product_type'	=>	$product['type'],
			'template_id'   =>  $product['template_id'],
			'ticket'		=>	$pinfo['ticket'],
			'seat'			=>	$pinfo['seat'],
			'quota'         =>  $pinfo['quota'],
			'quotas'        =>  $pinfo['quotas'],
			'start'         =>  $pinfo['start'],
			'goods'			=>	[]
		];
		if(in_array($product['type'], ['2','3','4'])){
			$data['plan'] = [[
				
				'plantime'      =>  $pinfo['plantime'],
				'games'         =>  $pinfo['games'],
				'starttime'     =>  $pinfo['starttime'],
				'endtime'       =>  $pinfo['endtime'],
				'quota'         =>  $pinfo['quota'],
				'quotas'        =>  $pinfo['quotas']


			]];
		}
		$model = D('Item/Plan');
		$state = $model->add_plan($data);
		if($state){
			return showReturnCode(true,0, $state, 'ok');
		}else{
			return showReturnCode(false,1007, $state, '新增失败~');
		}
	}
	/**
	 * 更新状态
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-08T12:30:04+0800
	 * @return   [type]                   [description]
	 */
	public function up_plan_state()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['id']) || empty($pinfo['id'])){
			return showReturnCode(false,1003);
		}
		$product = $this->getProduct($pinfo['incode']);
		$model = D('Item/Plan');
		$map = [
			'product_id' => $product['id'],
			'id'	 => $pinfo['id']
		];
		$info = $model->where($map)->field('id,product_id,product_type,status')->find();
		if($info['status'] == '3'){
			$procof = cache('ProConfig');
			//判断是否开启配额
			if($procof['quota'] == '1' && $info['product_type'] <> '1'){
				$count = M('QuotaUse')->where(array('plan_id'=>$info['id']))->count();
				if($count == '0'){
					\Libs\Service\Quota::reg_quota($info['id'],$info['product_id']);
				}
			}
			//暂停中开始销售
			$status = '2';
		}elseif($info['status'] == '2'){
			//售票中暂停销售
			$status = '3';
			F('Plan_'.$id,null);
		}else{
			return showReturnCode(false,1003, [], '计划状态不允许此项操作~');
		}
		if($model->where(array('id'=>$info['id']))->setField('status',$status)){
			$model->toAlizhiyouPlan($info['id'], $info['product_id']);
			$model->plan_cache($info['product_id']);
			return showReturnCode(true,0, [], 'ok');
		}else{
			return showReturnCode(false,1001, [], '更新失败~');
		}
	}
	/**
	 * 更新票型
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-08T16:30:56+0800
	 * @return   [type]                   [description]
	 */
	public function up_plan_ticket()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['id']) || empty($pinfo['id'])){
			return showReturnCode(false,1003);
		}
		$product = $this->getProduct($pinfo['incode']);
		$model = D('Item/Plan');
		$map = [
			'product_id' => $product['id'],
			'id'	 	 => $pinfo['id']
		];
		$info = $model->where($map)->find();
		$param = unserialize($info['param']);
		$param['ticket'] = $pinfo['ticket'];
		$state = $model->where(['id'=>$pinfo['id']])->setField('param',serialize($param));
		if($state){
			$model->plan_cache($info['product_id']);
			return showReturnCode(true,0, [], 'ok');
		}else{
			return showReturnCode(false,1001, [], '更新失败~');
		}
	}

	/***********************************订单处理**********************************************/
	public function get_order_list()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		$starttime = strtotime(date("Ymd"));
		$endtime = $starttime + 86399;
		$product = $this->getProduct($pinfo['incode']);
		$model = D('Item/Order');
		$map = [
			'product_id' => $product['id'],
			//'createtime' => array(array('EGT', $starttime), array('ELT', $endtime), 'AND')
		];
		if(!isset($pinfo['page']) || empty($pinfo['page'])){
			$page = 1;
		}else{
			$page = $pinfo['page'];
		}
		$firstRow = ($page - 1) * 20;
		$count = $model->where($map)->count();
		$toal_page = ceil($count/20);
		$list = $model->where($map)->field('id,order_sn,plan_id,number,guide_id,channel_id,money,addsid,type,status,createtime')->order('id DESC')->limit($firstRow, 20)->select();
		if(!empty($list)){
			$idx = array_unique(array_column($list, 'channel_id'));
			$crmList = D('Crm')->where(['id' => ['in', $idx]])->field('id,name')->select();
			$crmList = array_column($crmList, 'name', 'id');

			$uidx = array_unique(array_column($list, 'guide_id'));
			$userList = D('User')->where(['id' => ['in', $uidx]])->field('id,nickname')->select();
			$userList = array_column($userList, 'nickname', 'id');
			foreach ($list as $k => $v) {
				$v['plan']  = planShow($v['plan_id'],2,1);
				$v['guide'] = $userList[$v['guide_id']];
				$v['name']  = $crmList[$v['channel_id']];
				$v['type']  = channel_type($v['type'],1);
				$v['addsid']= scene($v['addsid'],1);
				$v['createtime'] = date('Y-m-d H:i:s', $v['createtime']);
				$v['status_str'] = order_status($v['status'], 1);
				$channel[] = $v;
			}
			$list = $channel;
		}
		
		return showReturnCode(true,0, ['list'=>$list,'total_page'=>$toal_page ? $toal_page : 1], 'ok');
	}
	/**
	 * 待审核订单
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-09T11:38:41+0800
	 */
	public function get_audit_order()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		$product = $this->getProduct($pinfo['incode']);
		$model = D('Item/Order');
		$map = [
			'product_id' => $product['id'],
			'status'     => array('in',['5','6']),
			//'createtime' => array('lt', strtotime(date('Y')))
		];

		if(!isset($pinfo['page']) || empty($pinfo['page'])){
			$page = 1;
		}else{
			$page = $pinfo['page'];
		}
		$firstRow = ($page - 1) * 20;

		$count = $model->where($map)->count();
		$toal_page = ceil($count, 20);
		$list = $model->where($map)->field('id,order_sn,plan_id,number,guide_id,channel_id')->order('id DESC')->limit($firstRow, 20)->select();
		if(!empty($list)){
			$idx = array_unique(array_column($list, 'channel_id'));
			$crmList = D('Crm')->where(['id' => ['in', $idx]])->field('id,name')->select();
			$crmList = array_column($crmList, 'name', 'id');

			$uidx = array_unique(array_column($list, 'guide_id'));
			$userList = D('User')->where(['id' => ['in', $uidx]])->field('id,nickname')->select();
			$userList = array_column($userList, 'nickname', 'id');
			foreach ($list as $k => $v) {
				$v['plan']  = planShow($v['plan_id'],2,1);
				$v['guide'] = $userList[$v['guide_id']];
				$v['name']  = $crmList[$v['channel_id']];
				$channel[] = $v;
			}
			$list = $channel;
		}
		
		return showReturnCode(true,0, ['list'=>$list,'total_page'=>$toal_page ? $toal_page : 1], 'ok');
	}
	public function get_audit_oinfo()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['sn']) || empty($pinfo['sn'])){
			return showReturnCode(false,1003);
		}
		$product = $this->getProduct($pinfo['incode']);
		$model = D('Item/Order');
		$info = $model->where(['order_sn'=>$pinfo['sn']])->relation(true)->find();
		if(empty($info)){
			return showReturnCode(false,1010, '', '未找到有效订单~');
		}
		$plan = F('Plan_'.$info['plan_id']);
		
		if($plan){
			$controls = [];
			//拉取所有特殊控座模板
			$control = D('ControlSeat')->where(['status' => 1,'type'=>2,'product_id'=>$info['product_id']])->field('id,name,num,seat')->select();
			foreach ($control as $k => $v) {
				$seat = unserialize($v['seat']);
				$map = [];
				foreach ($seat as $k => $v) {
					if(isset($v['seat']) && !empty($v['seat'])){
						$seatList = explode(',', $v['seat']);
						if(!empty($seatList)){
							if(empty($map)){
								$map = $seatList;
							}else{
								$map = array_merge($map, $seatList);
							}
						}
					}
				}
				$remain = D($plan['seat_table'])->where(['seat'=>['in', $map],'status'=>0])->count();
				$v['seat'] = $remain;
				$controls[] = $v;
			}
		}
		
		$ifPlan = $plan ? true : false;
		$info['plan']  = planShow($info['plan_id'],2,1);
		$info['guide'] = userName($info['guide_id'], 1, 1);
		$info['crm_name']  = crmName($info['channel_id'],1);
		$info['createtime']  = date('Y-m-d H:i:s', $info['createtime']);
		$return = [
			'order'   => $info,
			'is_audit'=> $ifPlan,
			'control' => $controls
		];
		return showReturnCode(true,0, $return, 'ok');
	}
	/**
	 * 审核订单
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-09T11:39:38+0800
	 * @return   [type]                   [description]
	 */
	public function post_audit_order()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['action']) || empty($pinfo['action'])){
			return showReturnCode(false,1003);
		}
		if(!in_array($pinfo['action'], ['1','2','4'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['sn']) || empty($pinfo['sn'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['user_id']) || empty($pinfo['user_id'])){
			return showReturnCode(false,1003);
		}
		$model = D('Item/Order');
		$info = $model->where(['order_sn'=>$pinfo['sn']])->relation(true)->find();
		if(empty($info)){
			return showReturnCode(false,1010, '', '未找到有效订单~');
		}
		switch ((int)$pinfo['action']) {
			case 1:
				$order = new \Libs\Service\Order();
				$status = $order->add_seat($info);
				break;
			case 2:
				//使用控座模板设置座位
				if(!isset($pinfo['control']) || empty($pinfo['control'])){
					$status = false;
				}else{
					$order = new \Libs\Service\Order();
					$status = $order->up_control_seat($pinfo, $info);
				}
				break;
			case 4:
				//不同意退款
			   // $info['channel_id']
				$status = \Libs\Service\Refund::arefund($info, $pinfo['user_id']);
				break;
		}
		if($status){
			return showReturnCode(true,0, [], '操作成功~');
		}else{
			return showReturnCode(false,1000, [], '操作失败~');
		}
	}
	/**
	 * 退单列表
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-09T11:40:17+0800
	 * @return   [type]                   [description]
	 */
	public function get_refund_order()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		$product = $this->getProduct($pinfo['incode']);
		$model = D('Item/TicketRefund');

		if(!isset($pinfo['page']) || empty($pinfo['page'])){
			$page = 1;
		}else{
			$page = $pinfo['page'];
		}
		$firstRow = ($page - 1) * 20;

		$field = 'param,against_reason';
		$map = ['product_id'=>$product['id'],'status'=>1,'launch'=>2];
		$list = $model->where($map)->field($field, true)->limit($firstRow, 20)->select();
		
		$count = $model->where($map)->count();
		$toal_page = ceil($count, 20);
		foreach ($list as $k => $v) {
			$v['crm_id'] = crmName($v['crm_id'], 1);
			$v['plan'] = planShow($v['plan_id'], 2, 4);
			$v['applicant'] = userName($v['applicant'], 1, 1);
			$v['createtime'] = date('Y-m-d H:i:s', $v['createtime']);
			$lists[] = $v;
		}
		return showReturnCode(true,0, ['list'=>$lists,'total_page'=>$toal_page ? $toal_page : 1], 'ok');
	}
	/**
	 * 退单详情
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-15T14:09:29+0800
	 * @return   [type]                   [description]
	 */
	public function get_refund_info()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['sn']) || empty($pinfo['sn'])){
			return showReturnCode(false,1003);
		}
		$product = $this->getProduct($pinfo['incode']);
		$model = D('Item/TicketRefund');
		$info = $model->where(['product_id'=>$product['id'],'order_sn'=>$pinfo['sn'],'status'=>1])->find();
		$info['applicant'] = userName($info['applicant'],1,1);
		$info['crm_id'] = crmName($info['crm_id'],1);
		$info['plan_id'] = planShow($info['plan_id'], 2, 1);
		return showReturnCode(true,0, $info, 'ok');
	}
	/**
	 * 退单审核
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-09T11:40:26+0800
	 * @return   [type]                   [description]
	 */
	public function post_refund_order()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		if(!in_array($pinfo['action'], ['1','2'])){
			return showReturnCode(false,1003);
		}
		if((int)$pinfo['action'] === 1 && !in_array($pinfo['poundage'], ['1','2','3'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['user_id']) || empty($pinfo['user_id'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['id']) || empty($pinfo['id'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['sn']) || empty($pinfo['sn'])){
			return showReturnCode(false,1003);
		}
		$checkOrder = new \Libs\Service\CheckStatus();
		if(!$checkOrder->OrderCheckStatus($pinfo['sn'], 2103)){
			return showReturnCode(false,1000,'','订单已锁定'.$checkOrder->error);
		}
		if((int)$pinfo['action'] === 1){
			//同意
			$refund = new \Libs\Service\Refund;
			$status = $refund->refund($pinfo,1,'','',$pinfo['poundage'],1);
			$checkOrder->delMarking($pinfo['sn']);
			if($status){
				return showReturnCode(true,0, ['sn'=>$pinfo['sn']], '退款成功');
			}else{
				return showReturnCode(false,1000, ['sn'=>$pinfo['sn']], '退票失败');
			}
		}
		if((int)$pinfo['action'] === 2){
			//驳回申请
			$data = array(
				"id" => $pinfo["id"],
				"against_reason" => $pinfo["remark"],
				"status"  => 2,
				"user_id" => $pinfo['user_id'],
				"updatetime" => date('Y-m-d H:i:s')
			);
			//改变订单状态 事务处理
			$model = new \Common\Model\Model();
			$model->startTrans();
			$order_up = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$pinfo['sn'],'status'=>7))->setField('status',1);
			$up = $model->table(C('DB_PREFIX').'ticket_refund')->save($data);
			if($up && $order_up){
				$model->commit();
				$checkOrder->delMarking($pinfo['sn']);
				return showReturnCode(true,0, ['sn'=>$pinfo['sn']], '退款申请驳回成功');
			}else{
				$model->rollback();
				$checkOrder->delMarking($pinfo['sn']);
				return showReturnCode(false,1000, ['sn'=>$pinfo['sn'],$order_up,$up], '退款申请驳回失败!');
			}
		}
	}
	/*******************************销售简报****************************************/
	/**
	 * 获取当日销售计划
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-17T10:22:44+0800
	 * @return   [type]                   [description]
	 */
	public function get_today_plan()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}

		if(isset($pinfo['datetime']) && !empty($pinfo['datetime'])){
			$day = strtotime($pinfo['datetime']);
		}else{
			$day = strtotime(date('Y-m-d'));
		}
		$product = $this->getProduct($pinfo['incode']);

		$plan = D('Plan')->where(['plantime'=>$day,'product_id'=>$product['id']])->field('id,plantime,starttime,endtime,games,product_type')->order('games ASC')->select();

		foreach ($plan as $k => $v) {
			$v['title'] = planShow($v['id'], 2,4);
			$return[] = $v;
		}

		return showReturnCode(true,0, $return, 'ok');
	}
	/**
	 * 销售简报 日报
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-09T11:43:08+0800
	 * @return   [type]                   [description]
	 */
	public function briefing(){
		//统计报表
		//1、读取当天的场次
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}

		$product = $this->getProduct($pinfo['incode']);
		if(isset($pinfo['datetime']) && !empty($pinfo['datetime'])){
			$day = strtotime($pinfo['datetime']);
		}else{
			$day = strtotime(date('Y-m-d'));
		}

		$plan = D('Plan')->where(['plantime'=>$day, 'product_id'=>$product['id']])->field('id')->order('games ASC')->select();
		foreach ($plan as $k => $v) {
			$plans[] = [
				'value'=>  $v['id'],
				'label'=>  planShow($v['id'], 2,4)
			];
		}

		if(!isset($pinfo['plan']) || empty($pinfo['plan'])){
			$planIdx = ['in', array_column($plan, 'id')];
		}else{
			$planIdx = $pinfo['plan'];
		}

		//2、旅行社排行
		$map = [
			'product_id' => $product['id'],
			'plan_id'    => $planIdx,
			'status'     => ['in', ['1','9']],
			'type'       => ['in', ['2','4','6']]
		];
		$list = M('Order')->where($map)->field(['id','channel_id','sum(number)' => 'total', 'sum(money)' => 'moneys'])->group('channel_id')->order('total DESC')->limit(15)->select();
		$channel = [];
		if(!empty($list)){

			$idx = array_column($list, 'channel_id');
			$crmList = D('Crm')->where(['id' => ['in', $idx]])->field('id,name')->select();

			$crmList = array_column($crmList, 'name', 'id');
			foreach ($list as $k => $v) {
				$v['name']  =   $crmList[$v['channel_id']];
				$channel[] = $v;
			}

		}
		G('begin');
		//3、票型汇总
		$map = array(
			'plan_id'=> $planIdx,
			'status' => array('in','1,7,9'),//订单状态为支付完成和已出票和申请退票中的报表
			'product_id' => $product['id']
		);
		$list = \Libs\Service\ItemReport::strip_order($map, date('Ymd',$day),2);
		
		//构造报表生成数据
		$list = \Libs\Service\ItemReport::operator($list);
		$ticket = \Libs\Service\ItemReport::day_fold($list, 1, $product['id']);
		G('end');
		$return = [
			'plan'      =>  $plans,//销售计划
			'channel'   =>  $channel,
			'ticket'    =>  $ticket,
			'time'		=>G('begin','end').'s'
		];
		return showReturnCode(true,0, $return, 'ok');
	}
	/**
	 * 核验数据
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-16T20:57:28+0800
	 * @return   [type]                   [description]
	 */
	public function nuclear()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['plan_id']) || empty($pinfo['plan_id'])){
			return showReturnCode(false,1003);
		}
		$plan = F('Plan_'.$pinfo['plan_id']);
		if(empty($plan)){
			$plan = D('Plan')->where(['id' => $pinfo['plan_id']])->field('id,seat_table,product_type')->find();
		}
		if((int)$plan['product_type'] === 1){
			$return = [
				'sold'      =>  D($plan['seat_table'])->where(['status' => ['in','2,99']])->count(),//已售出
				'nuclear'   =>  D($plan['seat_table'])->where(['status' => '99'])->count(),//已核销
				'notinto'   =>  D($plan['seat_table'])->where(['status' => '2'])->count(),//未入园
				'drawer'    =>  D('Order')->where(['plan_id' => $pinfo['plan_id'],'status'=>1])->count()
			];
		}else{
			$return = [
				'sold'      =>  D($plan['seat_table'])->where(['status' => ['in','2,99'],'plan_id' => $plan['id']])->count(),//已售出
				'nuclear'   =>  D($plan['seat_table'])->where(['status' => '99','plan_id' => $plan['id']])->count(),//已核销
				'notinto'   =>  D($plan['seat_table'])->where(['status' => '2','plan_id' => $plan['id']])->count(),//未入园
				'drawer'    =>  D('Order')->where(['plan_id' => $pinfo['plan_id'],'status'=>1])->count()
			];
		}
		
		return showReturnCode(true,0, $return, 'ok');
	}
	/**
	 * 场次简报
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-09T11:43:49+0800
	 * @return   [type]                   [description]
	 */
	public function plan_report()
	{
		# code...
	}

	/**
	 * 余票查询
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-17T00:44:33+0800
	 * @return   [type]                   [description]
	 */
	public function more_ticket()
	{
		$pinfo = I('post.');
		if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
			return showReturnCode(false,1003);
		}
		if(!isset($pinfo['plan']) || empty($pinfo['plan'])){
			return showReturnCode(false,1003);
		}
		$product = $this->getProduct($pinfo['incode']);
		if(in_array($product['type'], ['2','3'])){
			$type = 4;
		}else{
			$type = 1;
		}
		$return = \Libs\Service\Api::get_trust_plan($product['id'],$pinfo,$type);
		return showReturnCode(true,0, $return, 'ok');
	}
	
	
}