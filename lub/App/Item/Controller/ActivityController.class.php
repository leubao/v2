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
	 		//TODO  活动类型多样化之后.....  买赠
	 		if($pinfo['type'] == '1'){
	 			foreach ($pinfo['area'] as $key => $value) {
		 			$info[$value] = array(
		 				'area'=>$value,
						'num'=>$pinfo['num'][$value],
						'nums'=>$pinfo['nums'][$value],
						'price'=>$pinfo['ticket_num_'.$value.'_id'],
						'prices'=>$pinfo['ticket_nums_'.$value.'_id'],
						//'quota'=>$pinfo['quota'][$value],
						//'seat'=>$pinfo['seat'][$value]
					);
		 		}
	 		}
	 		//首单免
	 		if($pinfo['type'] == '2'){
	 			foreach ($pinfo['area'] as $key => $value) {
		 			$info[$value] = array(
		 				'area'=>$value,
						'num'=>$pinfo['num'][$value],
						'nums'=>$pinfo['nums'][$value],
						'price'=>$pinfo['ticket_num_'.$value.'_id'],
						'prices'=>$pinfo['ticket_nums_'.$value.'_id'],
						'quota'=>$pinfo['quota'][$value],
						'seat'=>$pinfo['seat'][$value]
					);
		 		}
	 		}
	 		//限定区域销售
	 		if($pinfo['type'] == '3'){
	 			$card = explode('|',trim($pinfo['card']));
	 			$info['card'] = $card;
	 			$info['voucher'] = $pinfo['voucher'];
	 			$info['ticket'] = $pinfo['ticket_id'];
	 		}
	 		//组团销售
	 		if($pinfo['type'] == '4'){
	 			$info['number'] = $pinfo['number'];
	 			$info['ticket'] = $pinfo['ticket_id'];
	 		}
	 		//多产品套票
	 		if($pinfo['type'] == '5'){
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
	 		}
	 		$param = array(
	 			'info' =>  $info,
	 		);
	 		$data = array(
	 			'title'	=>	$pinfo['title'],
	 			'type'	=>	$pinfo['type'],
	 			'scope'	=>	$pinfo['scope'],
	 			'real'	=>	$pinfo['real'],
	 			'product_id' => $pinfo['product_id'],
	 			'starttime' => strtotime($pinfo['starttime']),
	 			'endtime'	=> strtotime($pinfo['endtime']),
	 			'status'	=> $pinfo['status'],
	 			'is_scene'	=> implode(',',$pinfo['scene']),
	 			'param'		=> json_encode($param),
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
				if($pinfo['type'] == '1'){
					//剧院 座椅区域信息
					$seat = D('Area')->where(array('template_id'=>$pinfo['template_id'],'status'=>1))->field('id,name,template_id,num')->select();
					$this->assign('seat',$seat);
				}
				$prolist = M('Product')->where(['status'=>1])->field('id,name')->select();
				$printer = D('Printer')->where(['status'=>1,'product'=>$this->pid])->field('id,title')->select();
				$this->assign('printer',$printer);
				$this->assign('prolist',$prolist);
				$this->assign('product_id',$product_id)
				     ->assign('pinfo',$pinfo)
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
	 		if($pinfo['type'] == '3'){
	 			$info['card'] = $actinfo['param']['info']['card'];
	 			$info['voucher'] = $actinfo['param']['info']['voucher'];
	 			$info['ticket'] = $actinfo['param']['info']['ticket'];
	 		}
	 		//组团销售
	 		if($pinfo['type'] == '4'){
	 			$info['number'] = $pinfo['number'];
	 			$info['ticket'] = $pinfo['ticket_id'];
	 			
	 		}
	 		$info['scope'] = $scope;//参与范围
	 		$param = array(
	 			'info' =>  $info,
	 		);
	 		if(D('Item/Activity')->where(['id'=>$pinfo['id']])->setField('param',json_encode($param))){
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
	 		if($pinfo['type'] == '3'){
	 			$card = explode('|',trim($pinfo['card']));
	 			$info['card'] = $card;
	 			$info['voucher'] = $pinfo['voucher'];
	 			$info['ticket'] = $pinfo['ticket_id'];
	 		}
	 		//组团销售
	 		if($pinfo['type'] == '4'){

	 			$info['number'] = $pinfo['number'];
	 			$info['ticket'] = $pinfo['ticket_id'];
	 		}
	 		//开启范围
	 		if($pinfo['scope']){
	 			$actinfo = $this->get_activity($pinfo['id']);
	 			$info['scope'] = $actinfo['param']['info']['scope'];
	 		}
	 		$param = array(
	 			'info' =>  $info,
	 		);
	 		
	 		$data = array(
	 			'id'	=>	$pinfo['id'],
	 			'title'	=>	$pinfo['title'],
	 			'scope'	=>	$pinfo['scope'],
	 			'real'	=>	$pinfo['real'],
	 			'starttime' => strtotime($pinfo['starttime']),
	 			'endtime'	=> strtotime($pinfo['endtime']),
	 			'status'	=> $pinfo['status'],
	 			'is_scene'	=> implode(',',$pinfo['scene']),
	 			'param'		=> json_encode($param),
	 			'remark'	=> $pinfo['remark'],
	 		);
	 		if(D('Item/Activity')->save($data)){
	 			$this->srun("更新成功!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
	 		}else{
	 			$this->erun("更新失败!");
	 		}
	 	}else{
	 		$ginfo = I('get.');
	 		$info = $this->get_activity($ginfo['id']);
	 		//限制区域销售
	 		if($info['type'] == '3'){
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
	 		if($info['type'] == '4'){
	 			$ticket = explode(',',$info['param']['info']['ticket']);
	 			foreach ($ticket as $k => $v) {
	 				$name[] = ticketName($v,1);
	 			}
	 			$ticket_name = implode(',',$name);//dump($ticket);
	 			$this->assign('ticket_name',$ticket_name);

	 			//$info['number'] = $pinfo['number'];
	 			//$info['ticket'] = $pinfo['ticket_id'];

	 		}//dump($info);
	 		$this->assign('data',$info)->display();
	 	}
	 }
	 
	 //编辑活动
	 //删除活动  订单表中增加活动标记
	function delete(){

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
	 //优惠券
	 //新增优惠券
	 //编辑优惠券
	 //删除优惠券
}