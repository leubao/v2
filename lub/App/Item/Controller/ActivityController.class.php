<?php
// +----------------------------------------------------------------------
// | LubTMP 活动支持
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;

use Common\Controller\ManageBase;
use Item\Service\Partner;

class ActivityController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
		//取得所有产品信息
		$this->products = cache('Product');
		//取得当前产品信息
		$this->product = $this->products[$this->pid];
	 }

	 //活动列表
	 function index(){
	 	$this->basePage('Activity','','id DESC');
	 	$this->display();
	 }
	 //新建活动
	 function add(){
	 	if(IS_POST){
	 		$pinfo = I('post.');
	 		if(!in_array($pinfo['type'],['1','2','3','4','5','6','7','8','9','10'])){
	 			$this->erun("请选择活动类型~!");
	 		}
	 		//TODO  活动类型多样化之后.....  买赠
	 		if((int)$pinfo['type'] === 1){
	 			foreach ($pinfo['area'] as $key => $value) {
		 			$info[$value] = array(
		 				'area'=>$value,
						'num'=>$pinfo['num'][$value],
						'nums'=>$pinfo['nums'][$value],
						'ticket'=>$pinfo['ticket_num_'.$value.'_id'],
						'tickets'=>$pinfo['ticket_nums_'.$value.'_id'],
						//'quota'=>$pinfo['quota'][$value],
						//'seat'=>$pinfo['seat'][$value]
					);
		 		}
	 		}
	 		//首单免
	 		if((int)$pinfo['type'] === 2){
	 			foreach ($pinfo['area'] as $key => $value) {
		 			$info[$value] = array(
		 				'area'=>$value,
						'num'=>$pinfo['num'][$value],
						'nums'=>$pinfo['nums'][$value],
						'ticket'=>$pinfo['ticket_num_'.$value.'_id'],
						'tickets'=>$pinfo['ticket_nums_'.$value.'_id'],
						'quota'=>$pinfo['quota'][$value],
						'seat'=>$pinfo['seat'][$value]
					);
		 		}
	 		}
	 		//限定区域销售或区域限制场次销售
	 		if(in_array((int)$pinfo['type'],['3','9'])){
	 			$card = explode('|',trim($pinfo['card']));
	 			$info['number'] = $pinfo['number'];
	 			$info['card'] = $card;
	 			$info['voucher'] = $pinfo['voucher'];
	 			$info['ticket'] = $pinfo['ticket_id'];
	 			$info['limit'] = $pinfo['limit'];
	 			$info['is_control'] = $pinfo['is_control'];
	 		}
	 		//组团销售
	 		if((int)$pinfo['type'] === 4){
	 			$info['number'] = $pinfo['number'];
	 			$info['ticket'] = $pinfo['ticket_id'];
	 			$info['limit'] = $pinfo['limit'];
	 			$info['is_control'] = $pinfo['is_control'];
	 		}
	 		//多产品套票
	 		if((int)$pinfo['type'] === 5){
	 			//判断是否选择
	 			foreach ($pinfo['product'] as $k => $v) {
	 				$packages[] = [
	 					'product'	=>	$v,//产品ID
	 					'ticket'	=>	$pinfo['ticket_'.$v],//打包的票型ID
	 				];
	 			}
	 			$info['packages'] = $packages;
	 			$info['price'] = [
	 				'name'	=>	$pinfo['price_name'],
	 				'price' =>  $pinfo['price'],
	 				'discount' => $pinfo['discount']
	 			];
	 			$info['limit'] = $pinfo['limit'];
	 			$info['is_control'] = $pinfo['is_control'];
	 		}
	 		//单场限额
	 		if((int)$pinfo['type'] === 6){
	 			$info['number'] = $pinfo['number'];
	 			$info['ticket'] = $pinfo['ticket_id'];
	 			$info['limit'] = $pinfo['limit'];
	 			$info['is_control'] = $pinfo['is_control'];
	 		}
	 		//限时秒杀
	 		if((int)$pinfo['type'] === 7){
	 			if(empty($pinfo['ticket_id']) || empty($pinfo['kill'])){
	 				$this->erun("新增失败,票型或抢票时间不能为空!");
	 			}
	 			$info['ticket'] = $pinfo['ticket_id'];
	 			$info['number'] = $pinfo['number'];
	 			$info['rule']	= $pinfo['kill'];
	 			$info['limit'] = $pinfo['limit'];
	 			$info['is_control'] = $pinfo['is_control'];
	 		}
	 		//预约销售
	 		if((int)$pinfo['type'] === 8){
	 			if(empty($pinfo['ticket_id']) || empty($pinfo['today'])){
	 				$this->erun("新增失败,票型或预约天数不能为空!");
	 			}
	 			$info['ticket'] = $pinfo['ticket_id'];
	 			$info['number'] = $pinfo['number'];
	 			$info['today']	= $pinfo['today'];
	 			$info['pre_model'] = $pinfo['pre_model'] ? $pinfo['pre_model'] : '2';
	 			$info['limit'] = $pinfo['limit'];
	 			$info['is_control'] = $pinfo['is_control'];
	 		}
	 		//窗口促销
	 		if(in_array((int)$pinfo['type'],['10'])){
	 			$info['ticket'] = $pinfo['ticket_id'];
	 			$info['limit'] = $pinfo['limit'];
	 			$info['is_control'] = $pinfo['is_control'];
	 		}
	 		$param = array(
	 			'info' =>  $info,
	 		);
	 		$data = array(
	 			'title'	=>	$pinfo['title'],
	 			'type'	=>	$pinfo['type'],
	 			'scope'	=>	$pinfo['scope'],
	 			'real'	=>	$pinfo['real'],
	 			'is_team'=>	$pinfo['is_team'],
	 			'is_quota'=> $pinfo['is_quota'],
	 			'product_id' => $pinfo['product_id'],
	 			'starttime' => strtotime($pinfo['starttime']),
	 			'endtime'	=> strtotime($pinfo['endtime']),
	 			'status'	=> $pinfo['status'],
	 			'is_scene'	=> implode(',',$pinfo['scene']),
	 			'param'		=> json_encode($param),
	 			'print_tpl' => $pinfo['print_tpl'],
	 			'remark'	=> $pinfo['remark'],
	 		);
	 		if(D('Item/Activity')->add($data)){
	 			$this->srun("新增成功!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
	 		}else{
	 			$this->erun("新增失败!");
	 		}
	 	}else{
	 		$product_id = (int)$this->pid;
			if(!empty($product_id)){
				//产品信息
				$pinfo = M('Product')->where(array('id'=>$product_id))->find();
				//判断产品类型
				if((int)$pinfo['type'] === 1){
					//剧院 座椅区域信息
					$seat = D('Area')->where(array('template_id'=>$pinfo['template_id'],'status'=>1))->field('id,name,template_id,num')->select();
					$this->assign('seat',$seat);
				}
				$prolist = M('Product')->where(['status'=>1])->field('id,name')->select();
				$printer = D('Printer')->where(['status'=>1,'product'=>$product_id])->field('id,title')->select();
				/*秒杀增加场次选择*/
				$plan = D('Plan')->where(['product_id'=>get_product('id'),'status'=>2])->field('id')->select();
				$list = [];
				if(!empty($plan)){
					foreach ($plan as $k => $v) {
						$list[] = [
							'id'	=>	$v['id'],
							'title'	=>	planShow($v['id'],4,1)
						];
					}
				}
				$this->assign('printer',$printer);
				$this->assign('prolist',$prolist);
				$this->assign('product_id',$product_id)
				     ->assign('pinfo',$pinfo)
				     ->assign('plan',$list)
					 ->display();
			}else{
				$this->erun('参数错误!');
			}
	 	}
	 }
	 //限制参与渠道商
	 public function public_up_scope_channel()
	 {
	 	if(IS_POST){
	 		$pinfo = I('post.');
	 		$actinfo = $this->get_activity($pinfo['id']);
	 		foreach ($pinfo['channel'] as $k => $v) {
 				if(!empty($v['name'])){
	 				if($v['scope']){
	 					$scope['ginseng'][] = $v['name'];
	 				}else{
	 					$scope['dont'][] = $v['name'];
	 				}
 				}
 			}
	 		if(in_array((int)$pinfo['type'],['3','9'])){
	 			$info['card'] = $actinfo['param']['info']['card'];
	 			$info['voucher'] = $actinfo['param']['info']['voucher'];
	 			$info['ticket'] = $actinfo['param']['info']['ticket'];
	 			
	 		}
	 		//组团销售 单场限额
	 		if(in_array($pinfo['type'], ['4','6'])){
	 			
	 			$info['number'] = $actinfo['param']['info']['number'];
	 			$info['ticket'] = $actinfo['param']['info']['ticket'];
	 		}
	 		if((int)$pinfo['type'] === 8){
	 			$info['ticket'] = $actinfo['param']['info']['ticket_id'];
	 			$info['number'] = $actinfo['param']['info']['number'];
	 			$info['today']	= $actinfo['param']['info']['today'];
	 			$info['pre_model'] = $actinfo['param']['info']['pre_model'];
	 		}
	 		$info['limit'] = $actinfo['param']['info']['limit'];
	 		$info['is_control'] = $pinfo['is_control'];
	 		$info['scope'] = $scope;//参与范围
	 		$param = array(
	 			'info' =>  $info,
	 		);
	 		if(D('Item/Activity')->where(['id'=>$pinfo['id']])->setField('param',json_encode($param))){
	 			load_redis('delete','actInfo_'.$pinfo['id']);
	 			$this->srun("更新成功!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
	 		}else{
	 			$this->erun("更新失败!");
	 		}
	 	}else{
	 		//读取渠道商
	 		$ginfo = I('get.');
	 		$info = $this->get_activity($ginfo['id']);
	 		$this->assign('data',$info)->display();
	 	}
	 }
	 //活动购买流程》》》拉取获取页面》》》读取所有活动场次》》加载参与活动的场次和价格》》》客户下单》》按照预定活动规则进行金额计算
	 //支付订单金额 》》排座
	 
	 //特殊订单记录表
	 //使用优惠券
	 public function edit()
	 {
	 	$model = D('Item/Activity');
	 	if(IS_POST){
	 		$pinfo = I('post.');

	 		//限定区域销售
	 		if(in_array((int)$pinfo['type'], ['3','9'])){
	 			$card = explode('|',trim($pinfo['card']));
	 			$info['card'] = $card;
	 			$info['number'] = $pinfo['number'];
	 			$info['voucher'] = $pinfo['voucher'];
	 			$info['ticket'] = $pinfo['ticket_id'];
	 		}
	 		//组团销售
	 		if((int)$pinfo['type'] === 4){

	 			$info['number'] = $pinfo['number'];
	 			$info['ticket'] = $pinfo['ticket_id'];
	 		}
	 		//联票
	 		if((int)$pinfo['type'] === 5){

	 		}
	 		if((int)$pinfo['type'] === 6){
	 			//单场限额
	 			$info['number'] = $pinfo['number'];
	 			$info['ticket'] = $pinfo['ticket_id'];
	 		}
	 		//限时秒杀
	 		if((int)$pinfo['type'] === 7){
	 			if(empty($pinfo['ticket_id']) || empty($pinfo['kill'])){
	 				$this->erun("新增失败,票型或抢票时间不能为空!");
	 			}
	 			$info['tciket'] = $pinfo['ticket_id'];
	 			$info['number'] = $pinfo['number'];
	 			$info['rule']	= $pinfo['kill'];
	 		}
	 		//预售
	 		if((int)$pinfo['type'] === 8){
	 			if(empty($pinfo['ticket_id']) || empty($pinfo['today'])){
	 				$this->erun("新增失败,票型或预约天数不能为空!");
	 			}
	 			$info['ticket'] = $pinfo['ticket_id'];
	 			$info['number'] = $pinfo['number'];
	 			$info['today']	= $pinfo['today'];
	 			$info['pre_model'] = $pinfo['pre_model'];
	 		}
	 		//窗口促销
	 		if((int)$pinfo['type'] === 10){
	 			$info['ticket'] = $pinfo['ticket_id'];
	 		}
	 		//开启范围
	 		if($pinfo['scope']){
	 			$actinfo = $this->get_activity($pinfo['id']);
	 			$info['scope'] = $actinfo['param']['info']['scope'];
	 		}
	 		$info['limit'] = $pinfo['limit'];
	 		$info['is_control'] = $pinfo['is_control'];
	 		$data = array(
	 			'id'	=>	$pinfo['id'],
	 			'title'	=>	$pinfo['title'],
	 			'scope'	=>	$pinfo['scope'],
	 			'real'	=>	$pinfo['real'],
	 			'is_team'=>	$pinfo['is_team'],
	 			'is_quota'=> $pinfo['is_quota'],
	 			'starttime' => strtotime($pinfo['starttime']),
	 			'endtime'	=> strtotime($pinfo['endtime']),
	 			'status'	=> $pinfo['status'],
	 			'is_scene'	=> implode(',',$pinfo['scene']),
	 			'print_tpl' => $pinfo['print_tpl'],
	 			'remark'	=> $pinfo['remark'],
	 			'uptime'	=> time()
	 		);
	 		if(!empty($info)){
	 			$param = array(
		 			'info' =>  $info,
		 		);
		 		$data['param'] = json_encode($param);
	 		}
	 		if(D('Item/Activity')->save($data)){
	 			load_redis('delete','actInfo_'.$pinfo['id']);
	 			$this->srun("更新成功!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
	 		}else{
	 			$this->erun("更新失败!");
	 		}
	 	}else{
	 		$ginfo = I('get.');
	 		$info = $this->get_activity($ginfo['id']);
	 		//限制区域销售
	 		if(in_array($info['type'],['3','9'])){
	 			$ticket = explode(',',$info['param']['info']['ticket']);
	 			foreach ($ticket as $k => $v) {
	 				$name[] = ticketName($v,1);
	 			}
	 			$ticket_name = implode(',',$name);
	 			$card = implode('|',$info['param']['info']['card']);
	 			$this->assign('ticket_name',$ticket_name);
	 			$this->assign('card',$card);
	 		}
	 		//组团销售
	 		if(in_array($info['type'], ['4','6','8','7','10'])){
	 			$ticket = explode(',',$info['param']['info']['ticket']);
	 			foreach ($ticket as $k => $v) {
	 				$name[] = ticketName($v,1);
	 			}
	 			$ticket_name = implode(',',$name);//dump($ticket);
	 			$this->assign('ticket_name',$ticket_name);

	 			//$info['number'] = $pinfo['number'];
	 			//$info['ticket'] = $pinfo['ticket_id'];

	 		}
	 		//限时秒杀
	 		if((int)$pinfo['type'] === 7){
	 			if(empty($pinfo['ticket_id']) || empty($pinfo['kill'])){
	 				$this->erun("新增失败,票型或抢票时间不能为空!");
	 			}
	 			$info['tciket'] = $pinfo['ticket_id'];
	 			$info['number'] = $pinfo['number'];
	 			$info['rule']	= $pinfo['kill'];
	 		}
	 		$printer = D('Printer')->where(['status'=>1,'product'=>$this->pid])->field('id,title')->select();
			$this->assign('printer',$printer);
	 		$this->assign('data',$info)->display();
	 	}
	 }
	 
	 //编辑活动
	 //删除活动  订单表中增加活动标记
	function delete(){
		$id = I('get.id',0,intval);
		if(!empty($id)){
			$count = D('Order')->where(['activity'=>$id])->find();
			if($count){
				$this->erun('该活动已经生效，不能删除!');
			}
			$del = D('Item/Activity')->where(['id'=>$id])->delete();
			if($del){
				$this->srun('删除成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				$this->erun('删除失败!');
			}
		}else{
			$this->erun('参数错误!');
		}
	}
	//编辑活动页面
	function activity_page(){
	 	if(IS_POST){
	 		$pinfo = I('post.');

	 		
	 	}else{
	 		$ginfo = I('get.');
	 		$this->assign('data',$this->get_activity($ginfo['id']))->display();
	 	}
	}
	 //活动详情
	function activity(){
	 	$ginfo = I('get.');
	 	
	 	$this->assign('data',$this->get_activity($ginfo['id']))->display();
	}

	function get_activity($id){
	 	$info = M('Activity')->where(array('id'=>$id))->find();
	 	$info['param'] = json_decode($info['param'],true);
	 	return $info;
	}
	/**
	 * 政企订单排座
	 */
	function row_seat(){
		if(IS_POST){
			$pinfo = $_POST['info'];
			if(Order::govSeat($pinfo)){
				$return = array(
					'statusCode' => '200',
					'msg'	=> 	"排座成功",
				);
				$message = "排座成功!单号";
				D('Item/Operationlog')->record($message, 200);//记录售票员日报表
			}else{
				$return = array(
					'statusCode' => '300',
					'msg'	=> 	"排座失败!",
				);
				$message = "排座失败!";
				D('Item/Operationlog')->record($message, 300);//记录售票员日报表
			}
			echo json_encode($return);
			return true;
		}else{
			$ginfo = I('get.');
			if(empty($ginfo)){$this->erun('参数错误!');}
			$map = array(
				'product_id'=>get_product('id'),
				//'status'=>6,//只查询未出票的订单
				'order_sn' => $ginfo['id'],
			);
			$this->assign('area',$ginfo['area'])->display();
		}
	}
	/**
	 * 加载根据区域加载座位
	*/
	function seats(){
		$ginfo = I('get.');
		if(empty($ginfo)){$this->erun('参数错误!');}
		$map = array(
			'product_id'=>get_product('id')
		);

		$info = M('Area')->where(array('id'=>$ginfo['area'],'status'=>1))->field('id,name,face,is_mono,seats,num,template_id')->find();
			$info['seats'] = unserialize($info['seats']);
			$this->assign('data',$info)
				->assign('ginfo',$ginfo)
				->assign('area',$ginfo['area'])
				->display();
	}
	//获取当前可售场次
	public function public_get_sales_plan()
	{
		$plan = D('Plan')->where(['product_id'=>get_product('id'),'status'=>2])->field('id')->select();
		$list = [];
		if(!empty($plan)){
			foreach ($plan as $k => $v) {
				$list[] = [
					'id'	=>	$v['id'],
					'title'	=>	planShow($v['id'],4,1)
				];
			}
		}
		$return = array(
			'statusCode'=>'200',
			'plan' => $list,
		);
		die(json_encode($return));
	}
	 //优惠券
	 //新增优惠券
	 //编辑优惠券
	 //删除优惠券
}