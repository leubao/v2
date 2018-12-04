<?php
// +----------------------------------------------------------------------
// | LubTMP 商户工作面板
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Libs\System\Cache;
use Common\Controller\ManageBase;
use Item\Service\Partner;
use Libs\Service\Operate;
use Libs\Service\Refund;
use Libs\Service\Sms;
use Libs\Service\CheckStatus;
use Common\Model\Model;
class WorkController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
	}
	/*加载初始售票框架*/
	function index(){
		//售票类型
		$pinfo = I('get.');
		//产品类型
		switch ($this->product['type']) {
			case '1':
				//今天时间戳
				$today = strtotime(date('Y-m-d'))."-1";
				$where = [
					'product_id'=>get_product('id'),
					'status'=>2
				];
				$plan = D('Plan')->where($where)->order('plantime ASC,games ASC')->select();
				//剧场
				$template = 'index';
				break;
			case '2':
				//景区
				$today = date('Y-m-d');
				$template = 'drifting';
				$type = $pinfo['type'] ? $pinfo['type'] : '1';
				break;
			case '3':
				//漂流
				$today = date('Y-m-d');
				$template = 'drifting';
				$type = $pinfo['type'] ? $pinfo['type'] : '1';
				break;
		}
		$this->assign('plan',$plan)
		     ->assign('today',$today)
		     ->assign('type',$type)
			 ->assign('product',$this->product)
		     ->display($template);
	}
	/*日期查场次漂流项目*/ 
	function public_get_date_plan(){
		$pinfo = json_decode($_POST['info'],true);
		$plantime = strtotime($pinfo['plantime']);
		$plan = M('Plan')->where(array('plantime'=>$plantime,'status'=>2,'product_id'=>$this->pid))->field('id,starttime,endtime,games,param,product_type')->select();
		foreach ($plan as $k => $v) {
			$param = unserialize($v['param']);
			if($v['product_type'] == '1'){
				$data[] = array(
					'id'	=> $v['id'],
					'pid'   => '1',
					'pId'	=>	'1',
					'plan' 	=>	$v['id'],
					'type'	=>	$pinfo['type'],
					'name'  => '[第'.$v['games'].'场] '. date('H:i',$v['starttime']) .'-'. date('H:i',$v['endtime']),
				);
			}
			if($v['product_type'] == '2'){
				$data[] = array(
					'id'	=>  $v['id'],
					'pid'   =>  '1',
					'pId'	=>	'1',
					'plan' 	=>	$v['id'],
					'type'	=>	$pinfo['type'],
					'name'  =>  date('H:i',$v['starttime']).'-'.date("H:i",$v['endtime']),
				);
			}
			if($v['product_type'] == '3'){
				$data[] = array(
					'id'	=> $v['id'],
					'pid'   => '1',
					'pId'	=>	'1',
					'plan' 	=>	$v['id'],
					'type'	=>	$pinfo['type'],
					'tooltype' => tooltype($param['tooltype'],1),
					'name'  => '[第'.$v['games'].'趟] '. date('H:i',$v['starttime']),
				);
			}
		}
		if(!empty($data)){
			$str = array(
				'id' => '1',
				'pid'=> '0',
				'pId'=>'0',
				'open'=>true,
				'name'=>$pinfo['plantime'].'趟次',
				'children'=>$data,
			);
		}
		$return = array(
			'statusCode'=>'200',
			'plan' => $str,
		);
		die(json_encode($return));
	}
	/*设置门票销售session*/
	function set_session_plan(){
		if(IS_POST){
			$pinfo = json_decode($_POST['info'],true);
			$ginfo = I('get.param',0,intval) ? I('get.param',0,intval) : '1';
			$product_id = get_product('id');
			$return = \Libs\Service\Api::get_plan($product_id,$pinfo,$ginfo);
			die(json_encode($return));
		}
	}
	/**
	 * 选座位门票销售
	 */
	function sales(){
		$area = I('get.area',0,intval);
		$plan = I('get.plan',0,intval);
		$type = I('get.type',1,intval);
		if(!empty($plan)){
			$this->available($plan);
			$this->assign('type',$type)->assign('area',$area)->assign('plan',$plan)->display();
		}else{
			$this->erun('参数有误!');
		}
	}
	/*窗口门票预定*/
	function pre(){
		if(IS_POST){
			$plan = I('post.plan');
			if(!empty($plan)){
				$this->srun('加载成功!','quick');
			}else{
				$this->erun('加载失败!');
			}
		}else {
			$this->available();
			$this->display();
		}
	}
	/**
	 * 根据区域加载座位信息   区域页面打开时  先加载座椅模板   然后加载售出情况   页面打开时  每个10分无刷新更新页面
	 * 散客售票 type 1 团队售票 2
	 */
	function seat(){
		$area = I('get.area',0,intval);
		$planid = I('get.plan',0,intval);
		$type = I('get.type',1,intval);
		$plan = session('plan');
		if(empty($plan) || $plan['id'] <> $planid){
			$this->erun("参数错误!");
		}else{
			//加载座椅
			$info = Operate::do_read('Area',0,array('id'=>$area,'status'=>1),'','id,name,face,is_mono,seats,num,template_id');
			$info['seats'] = unserialize($info['seats']);
			//选中区域价格和座椅信息
			$tictype = pullprice($plan['id'],$type,$area,1,1);
			$this->assign('price',$tictype);
			$this->assign('data',$info)
				->assign('area',$area)
				->assign('plan',$plan)
				->assign('type',$type)
				->display();
		}
	}
	/**
	 * 加载座椅状态 $area 区域 $plan_id 销售计划 $type 请求类型1窗口选座售票2演出控座3web座位图
	 */
	function seats(){
		$area = I('get.area',0,intval);
		$plan_id = I('get.plan',0,intval);
		$type = I('get.type',1,intval);
		if(empty($area)){
			$this->erun('加载错误!');
		}
		$plan = F('Plan_'.$plan_id);
		if(empty($plan)){
			$plan = Operate::do_read('Plan',0,array('id'=>$plan_id));
		}
		$table=ucwords($plan['seat_table']);
		//只查询已售出的座位
		$info = M($table)->where(array('area'=>$area,'status'=>array('NEQ',0)))->field(array('seat','status'))->select();
		foreach ($info as $key => $value) {
			if($value['status'] == '2'){
				$work_seat[] = $value['seat'];
			}elseif($value['status'] == '66'){
				$work_pre_seat[] = $value['seat'];//预定登其它状态
			}elseif($value['status'] == '99'){
				$work_end_seat[] = $value['seat'];
			}else{
				$nwork_seat[] = $value['seat'];
			}
		}
		//控座请求状态
		if($type == 2){
			$pre_count = M($table)->where(array('area'=>$area,'status'=>'66'))->count();
		}
		$return = array(
			'statusCode' => '200',
			'message' => '区域加载成功!',
			'work_seat'	=> $work_seat,
			'work_pre_seat'	=> $work_pre_seat,
			'work_end_seat'	=> $work_end_seat,
			'nwork_seat'=> $nwork_seat,
			'pre_count'=>$pre_count,
		);
		die(json_encode($return));
		return true;
	}
	/**
	 * 快捷售票 type 1 散客快捷售票 2 团队售票
	 */
	function quick(){
		$plan = I('get.plan',0,intval);
		$type = I('get.type',1,intval);
		if(!empty($plan)){
			$this->available($plan);
			$this->assign('type',$type)->display();
		}else{
			$this->erun('参数有误!');
		}
	}
	/*团队售票*/
	function team(){
		$plan = I('get.plan',0,intval);
		if(!empty($plan)){
			$this->available($plan);
			$this->display();
		}else{
			$this->erun('参数有误!');
		}
	}
	//价格政策
	function prePrice(){
		$aid = I('get.aid',0,intval);
		if(empty($aid)){
			$this->erun('未知区域!');
		}
		$tictype = $this->getPrice(9,$this->product['type'],$aid);
		$this->assign('tictype',$tictype)
			->assign('aid',$aid)
			->display();
	}


	/**
	 * 订单出票
	 */
	function orderticket(){
		$sn = trim(I('sn'));
		$phone = trim(I('phone'));
		$plan = I('plan_id');
		$plan_name = I('plan_name');
		$map = array(
			'product_id'=>	get_product('id'),
			'status'	=>	'1',
			'createtime'=>	array('GT', strtotime("-30 day")),
		);
		if(!empty($sn)){
			$map['order_sn'] = array('like','%'.$sn.'%');
		}
		if(!empty($phone)){
			$map['phone'] = $phone;
		}
		//获取销售计划
		if(!empty($plan)){
			$map['plan_id'] = $plan;
		}else{
			$map['plan_id'] = array('in',normal_plan());
		}
		$this->basePage('Order',$map,'createtime DESC');	
		$this->assign('plan',$plan)->assign('planname',$plan_name)->display();
	}
	/**
	 * 预定单管理
	 */
	function bookTicket(){
		if(IS_POST){
			$sn = trim(I('sn'));
			$plan = I('plan_id');
			$planname = I('plan_name');
			$channel = I('channel_id');
			$channelname = I('channel_name');
			$phone = I('phone');
			$map = array(
				'product_id'=>get_product('id'),
				'status'=>array('in','5,6'),
			);	
			if(!empty($plan)){
				$map['plan_id'] = $plan;
			}
			if(!empty($channel)){
				$map['channel_id'] = $channel;
			}
			if(!empty($sn)){
				$map['order_sn'] = $sn;
			}
			if(!empty($phone)){
				$map['phone'] = $phone;
			}
			//$map['createtime'] = array('GT', strtotime(date("Ym",time())));//过滤已过期的订单
		}else{
			$map = array(
				'product_id'=>get_product('id'),
				'status'=>array('in','5,6')
				//'createtime'=>array('GT', strtotime(date("Ymd",time()))),//过滤已过期的订单
			);
		}
		$map['plan_id']	=	['in',normal_plan()];
		$this->basePage('Order',$map, 'createtime DESC');
		$this->assign('map',$map)->assign('planname',$planname)->assign('channel',$channel)->assign('channelname',$channelname)->display();
	}
	/*编辑预订单数量*/
	public function edit_pre_order()
	{
		if(IS_POST){
			//修正数量
			$pinfo = I('post.');
			//读取订单
			foreach ($pinfo['priceid'] as $ke => $va) {
				$new[$va] = (int)$pinfo['price_num'][$ke];
			}
			$data = $this->getOrder($pinfo['sn']);
			if($data == false){
				$this->erun("未找到相应订单...");
			}
			//dump($data);
			//更新数量
			$oinfo = $data['info'];
			$info = $oinfo['info'];
			foreach ($info['data']['area'] as $k => $v) {
				$newArea[$v['priceid']] = [
					'areaId'	=>	$v['areaId'],
					'priceid'	=>	$v['priceid'],
					'price'		=>	$v['price'],
					'num'		=>	$new[$v['priceid']],
					'idcard'	=>	$v['idcard']
				];
			}
			$order = new \Libs\Service\Order();
			$areaSeat = $order->area_group($newArea,$oinfo['product_id'],$info['param'][0]['settlement'],$oinfo['product_type'],$info['child_ticket']);
			//更新订单
			$newInfo = [
				'plan_id'		=> $pinfo['plan'],
				'subtotal'		=> $areaSeat['moneys'],
				'checkin'		=> $info['checkin'],
				'data'			=> $areaSeat,
				'child_ticket'	=> $info['child_ticket'],
				'crm'			=> $info['crm'],
				'pay'			=> $info['pay'],				
				'param'			=> $info['param'],	
			];
			$newData = [
				'number'		=> $areaSeat['num'],
				'money' 		=> $areaSeat['money'],
			];
			$model = new Model();
			$model->startTrans();

			$up_order = $model->table(C('DB_PREFIX').'order')->where(['order_sn' => $pinfo['sn']])->save($newData);
			$states = $model->table(C('DB_PREFIX').'order_data')->where(['order_sn' => $pinfo['sn']])->setField('info',serialize($newInfo));
			if($up_order && $states){
				$model->commit();//提交事务
				$this->srun("核准成功",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$model->rollback();//事务回滚
				$this->erun('核准失败!');
				return false;
			}
			//dump($newArea);
			//修改日志
			$editOrderLog = [
				'user_id'	=>	get_user_id(),
				'order_sn'	=>	$pinfo['sn'],
				'createtime'=>	time(),
				'status'	=>	''
			];
			//M('OrderData')->where(array('order_sn'=>$sn))->setField('number',$number);
		}else{
			$sn = I('id');
			if(empty($sn)){
				$this->erun('参数错误!');
			}
			$data = $this->getOrder($sn);
			if($data == false){
				$this->erun("未找到相应订单...");
			}else{
				//获取当前所有可售计划
				$plan = D('Plan')->where(['status'=>2,'product_id'=>get_product('id')])->field('id,plantime,starttime,games')->select();
				$this->assign('data',$data['info'])
					->assign('plan',$plan)
					->assign('type',$data['info']['product_type'])
					->assign('area',$data['area'])
					->assign('ticket',$data['ticket'])
					->display();
			}
		}
	}
	/**
	 *订单详情
	 */
	function orderinfo(){
		if(IS_POST){
			$sn = trim(I('sn'));
			if(empty($sn)){
				$this->erun('参数错误!');
			} 
			//提交备注
			$remark = I('win_rem');
			if(M('OrderData')->where(array('order_sn'=>$sn))->setField('win_rem',$remark)){
				$this->srun("备注成功",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("备注失败");
			}
		}else{
			$sn = I('sn');
			if(empty($sn)){
				$this->erun('参数错误!');
			}
			$data = $this->getOrder($sn);
			if($data == false){
				$this->erun("未找到相应订单...");
			}else{
				
				$info = $data['info'];
				//生成打印URl
				$prshow  = print_buttn_show($info['type'],$info['pay'],$info['order_sn'],$info['plan_id'],$info['money'],2,$info['activity'],1);
				$this->assign('data',$info)
					->assign('type',$info['product_type'])
					->assign('area',$data['area'])
					->assign('ticket',$data['ticket'])
					->assign('prshow',$prshow)
					->display();
			}
		}
	}
	/**
	 * @Company  承德乐游宝软件开发有限公司
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2017-12-15
	 * @param    string        $sn                   订单号
	 */
	function getOrder($sn = '')
	{
		$info = D('Order')->where(array('order_sn'=>$sn))->relation(true)->find();
		if(empty($info)){
			return false;
		}
		$info['info']=unserialize($info['info']);
		//当前产品类型
		if($info['product_type'] == '1'){
			//区域分类
			foreach ($info['info']['data'] as $key => $value) {
				$area[$value['areaId']]['area'] = $value['areaId'];
				$area[$value['areaId']]['num'] = $area[$value['areaId']]['num']+1;
			}
		}else{
			//区域分类
			foreach ($info['info']['data'] as $key => $value) {
				$area[$value['priceid']]['area'] = $value['priceid'];
				$area[$value['priceid']]['num'] = $area[$value['priceid']]['num']+1;
			}
			if($info['product_type'] == '2'){
				$table = 'Scenic';
			}else{
				$table = 'Drifting';
			}
			$ticket = M($table)->where(array('order_sn'=>$sn))->field('id,price_id,ciphertext,status,checktime')->select();
		}
		$return = [
			'info' 		=> $info,
			'ticket'	=> $ticket,
			'area'		=> $area
		];
		return $return;
	}
	/**
	 * 获取当前区域价格与座椅信息 景区产品根据销售计划获取价格信息
	 * @param $pinfo['area'] int 当前区域ID
	 */
	function getprice(){
		if(IS_POST){
			$pinfo = json_decode($_POST['info'],true);
			//常规根据计划、区域、产品类型获取销售价格
			if($pinfo['method'] == 'general'){
				$price = pullprice($pinfo['plan'],$pinfo['type'],$pinfo['area'],1,1);
			}
			//根据销售计划和产品类型以及可售的票型获取整体销售票型
			if($pinfo['method'] == 'activity'){
				//读取当前活动绑定的票型
				$where = [
					'status'	=>	'1',
					'_string'   =>  "FIND_IN_SET(1,is_scene)",
					'id'		=>	$pinfo['actid']
				];
				$param = D('Activity')->where($where)->getField('param');
				$param = json_decode($param,true);
				//dump($param['info']['ticket']);
				$ticket = explode(',',$param['info']['ticket']);
				$price = pullprice($pinfo['plan'],$pinfo['type'],$pinfo['area'],1,1,$pinfo['seale'],$ticket);
			}
			$return =  array(
				'statusCode' => 200,
				'price' =>$price,
			);
			die(json_encode($return));
		}else{
			$this->erun("未允许次类操作");
		}
	}
	/**
	 * 根据销售计划加载价格加载
	 */
	/**
	 * 获取联票票型
	 */
	function get_child_ticket(){
		if(IS_POST){
			$pinfo = json_decode($_POST['info'],true);
			$price = pullprice($pinfo['plan'],$pinfo['type'],$pinfo['area'],1,1);
			$return =  array(
				'statusCode' => 200,
				'price' =>$price,
			);
			die(json_encode($return));
		}else{
			$this->erun("未允许次类操作");
		}
	}
	/**
	 * 退票管理
	 */
	function refund(){
		//获取当前产品ID
		$product_id = $this->pid;
		//获取模板ID
		$product = Operate::do_read('Product',0,array('id'=>$product_id));
		$area = Operate::do_read('Area',0,array('template_id'=>$product['template_id']),'',array('id','name'));
		if(IS_POST){
			$pinfo = I('post.');		
		}		
		$this->assign('data',$data)
			->assign('area',$area)
			->display();
	}
	/** 
	 *  渠道取消订单申请列表
	 */	
	function channel_refund(){
		$starttime = I('starttime');
	    $endtime = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    //传递条件
	    $this->assign('starttime',$starttime);
        $this->assign('endtime',$endtime);
        if (!empty($starttime) && !empty($endtime)) {
            $starttime = strtotime($starttime);
            $endtime = strtotime($endtime) + 86399;
            $where['createtime'] = array(array('GT', $starttime), array('LT', $endtime), 'AND');
        }else{
        	//默认显示当天的订单
        	$starttime = strtotime(date("Ymd"));
            $endtime = $starttime + 86399;
        	$where['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
        }
		$where['launch'] = '2';
		$this->basePage('TicketRefund',$where);
		$this->display();
	}
	/**
	 * 窗口退票
	 */
	function window_refund(){
		$sn = I('sn');
		if(!empty($sn)){
			$info = D('Item/Order')->where(array('order_sn'=>$sn))->relation(true)->find();
			//取消中的订单不允许窗口退票
			$no_refund =  array('0','2','3','7','11');
			if(in_array($info['status'],$no_refund)){
				$this->assign("error","该订单状态,不允许此项操作..");
			}else{
				$info['info'] = unserialize($info['info']);
				if($info['info'] == false){
					$this->assign("error","未找到相应数据!");
				}else{
					$this->assign('data',$info)
						->assign('pinfo',$pinfo);
				}
			}
		}
		$this->assign('sn',$sn)->display();
	}
	/**
	 * 一卡通 售卡
	 */
	function sale_card()
	{
		$this->display();
	}
	/**
	 * 退票
	 */
	function refunds(){
		$ginfo = I('get.');
		if(empty($ginfo)){$this->erun("参数错误!");}
		try{
			$refund = new Refund;
			switch ((int)$ginfo['order']) {
				case 1:
					//退订单所有门票
					$ret = $refund->refund($ginfo,1,'','',1,1);
					break;
				case 3:
					//退单个座位
					$ret = $refund->refund($ginfo,2,$ginfo['area'],$ginfo['seatid'],1,1);
					break;
				case 5:
					//退子票 TODO  子票只能单张退
					$ret = $refund->refund($ginfo,5,$ginfo['area'],$ginfo['seatid'],1,1);
					break;

				default:
					$ret = false;//['status'=>false,'msg'=>'未知退票类型']
					break;
			}
			if($ret){
				if(!empty($ginfo['reason'])){
					D('TicketRefund')->where(['order_sn'=>$ginfo['sn']])->setField('reason',trim($ginfo['reason']));	
				}
				
				$this->srun('退票成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				$this->erun('退票失败:'.$refund->error);
			}
		}catch (PayException $e) {
		    $this->erun($e->errorMessage());
		    exit;
		}
	}
	/**
	 * 同意【取消订单】申请1 驳回【取消订单】申请2
	 */	
	function agree(){
		$pinfo = I("post.");
		if(empty($pinfo['sn'])){
			$this->erun("参数有误!");
		}
		/** 订单状态校验 */
		$checkOrder = new CheckStatus();
		if(!$checkOrder->OrderCheckStatus($pinfo['sn'],2103)){
			$this->erun($checkOrder->error,array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		}
		// 判断当前订单状态
		if(M('Order')->where(array('order_sn'=>$pinfo['sn']))->getField('status') <> '9'){
			if($pinfo['type'] == '1'){
				//同意申请
				$status = Refund::refund($pinfo,1,'','',$pinfo['poundage'],1);
				$checkOrder->delMarking($pinfo['sn']);
				if($status){
					$this->srun('退款成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun("退票失败!");
				}
			}else{
				//驳回申请
				$data = array(
					"id" => $pinfo["id"],
					"against_reason" => $pinfo["against_reason"],
					"status" => 2,
					"user_id" => get_user_id(),
				);
				//改变订单状态 事务处理
				$model = new \Common\Model\Model();
				$model->startTrans();
				$order_up = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$pinfo['sn'],'status'=>7))->setField('status',1);
				$up = $model->table(C('DB_PREFIX').'ticket_refund')->save($data);
				if($up && $order_up){
					$model->commit();
					$checkOrder->delMarking($pinfo['sn']);
					$this->srun('退款申请驳回成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$model->rollback();
					$checkOrder->delMarking($pinfo['sn']);
					$this->erun("退款申请驳回失败!");
				}
			}
		}else{
			$data = array(
				"id" => $pinfo["id"],
				"against_reason" => '',
				"status" => 0,
				"user_id" => get_user_id(),
			);
			$model->table(C('DB_PREFIX').'ticket_refund')->save($data);
			$checkOrder->delMarking($pinfo['sn']);
			$this->srun('当前订单已完结,退票申请将作废',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		}	
	}
	/**
	 * @印象大红袍
	 * 核减
	 */
	function subtract(){
		if(IS_POST){
			$pinfo = I('post.');
			$number = 0;
			foreach ($pinfo['area'] as $k=>$v){
				$area[$v]=array(
					'area'=>$v,
					'num'=>$pinfo['seat_num'][$k],
				);
				$number += $pinfo['seat_num'][$k];
			}
			//开始核减
			$subNum =  (int)$pinfo['num'];
			if((int)$number > $subNum ){
				//超出最大核减数 
				$this->erun("超出核减总数!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}
			//得到订单
			$info = Operate::do_read('Order',0,array('order_sn'=>$pinfo['sn']),'','',true);
			//查看核减标示
			if(!empty($info['subtract'])){
				$this->erun("该订单已完成此项操作!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}
			//获取所属计划
			$plan = F('Plan_'.$info['plan_id']);
			if(empty($plan)){
				$this->erun("场次已停止，不能进行此项操作!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				/** 订单状态校验 */
				$checkOrder = new CheckStatus();
				if(!$checkOrder->OrderCheckStatus($pinfo['sn'],2103)){
					$this->erun($checkOrder->error,array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}
				//按区域核减
				foreach ($area as $ka=>$ve){
					if($plan['product_type'] == '1'){
						$map = array('order_sn'=>$pinfo['sn'],'area'=>$ve['area']);
					}else{
						$map = array('order_sn'=>$pinfo['sn'],'price_id'=>$ve['area']);
					}
					$table=ucwords($plan['seat_table']);
					$seat = D($table)->where($map)->limit($ve['num'])->select();
					//按座位核减 
					foreach ($seat as $k=>$v){
						if($plan['product_type'] == '1'){
							$status[$k] = Refund::refund($pinfo,3,$ve['area'],$v['seat'],1,1);
						}else{
							$status[$k] = Refund::refund($pinfo,3,$ve['area'],$v['id'],1,1);
						}
						if($status[$k] == false){
							$checkOrder->delMarking($pinfo['sn']);
							$this->erun("核减失败!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
						}
					}
				}
				$checkOrder->delMarking($pinfo['sn']);
				$this->srun("核减成功!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}
		}else{
			$ginfo  =  I('get.');
			if(empty($ginfo)){$this->erun("参数错误!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));}
			$oinfo = Operate::do_read('Order',0,array('order_sn'=>$ginfo['id'],'status'=>'1','type'=>array('in','2,4,7')),'','',true);
			if(empty($oinfo)){$this->erun("未找到订单或订单不能满足核减条件!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));}
			//判断订单类型
			if($oinfo['status'] == '6'){
				//政府订单
				$subNum = $oinfo['number']-1;
			}else{
				//渠道订单
				$subNum = $oinfo['number']-1;//最大可核减数
				$subNum = (int)$subNum;
				if($subNum < 1 || !empty($oinfo['subtract'])){
					$this->erun("订单未能满足核减要求!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}
			}
			$seat = unserialize($oinfo['info']);
			foreach ($seat['data'] as $k=>$v){
				if($oinfo['product_type'] == '1'){
					$area[$v['areaId']][$k] = $v;
					$area[$v['areaId']]['areaname'] = areaName($v['areaId'],1);
					$area[$v['areaId']]['area']	=	$v['areaId'];
					$area[$v['areaId']]['num'] = count($area[$v['areaId']])-4;	
				}else{
					$area[$v['priceid']][$k] = $v;
					$area[$v['priceid']]['areaname'] = ticketName($v['priceid'],1);
					$area[$v['priceid']]['area']	=	$v['priceid'];
					$area[$v['priceid']]['num'] = count($area[$v['priceid']])-4;
				}
			}
			$this->assign('num',$subNum)
				->assign('sn',$ginfo['id'])
				->assign('area',$area);
			$this->display();
		}
	}
	/*
	* 整场退票
	*/
	function refund_entire(){
		$ginfo = I('get.');
		//TODO 整场退票时先解除该场次所有的锁 退票之前检查返利池是否存在待返利订单 
		switch ($ginfo['type']) {
			case '1':
				//只能退当天的场次
				$today = strtotime(date('Ymd'));
				$plan = M('Plan')->where(array('plantime'=>$today,'status'=>2))->select();
				break;
			case '2':
				//显示当前要退的场次
				$return = array(
					'statusCode' => '200',
					'data'	=>	$ginfo['plan'],
					'stop'	=>	'3',
					'urls'	=>	U('Item/Work/refund_entire',array('type'=>3,'plan'=>$ginfo['plan'])),
					'msg'	=>	planShow($ginfo['plan'],1,1),
				);
				echo json_encode($return);
				break;
			case '3':
				//返回当前场次可退的订单
				if($ginfo['sn']){
					//存储取消失败的单子
					$sns = session('sns');
					$sns = $sns ? $sns.','.$ginfo['sn'] : $ginfo['sn'];
					session('sns',$sns);
					$map = array('plan_id'=>$ginfo['plan'],'status'=>array('in','1,7,9'),'order_sn'=>array('not in',$sns));
				}else{
					$map = array('plan_id'=>$ginfo['plan'],'status'=>array('in','1,7,9'));
				}
				$order = D('Order')->where($map)->field('order_sn')->find();
				if(empty($order['order_sn'])){
					$return = array(
						'statusCode' => '300',
						'stop'	=>	'5',
						'urls'	=>	U('Item/Work/refund_entire',array('type'=>5,'plan'=>$ginfo['plan'])),
						'msg'	=>	"所有订单取消完成...",
					);
				}else{
					$return = array(
						'statusCode' => '200',
						'data'	=>	$order['order_sn'],
						'stop'	=>	'4',
						'urls'	=>	U('Item/Work/refund_entire',array('type'=>4,'plan'=>$ginfo['plan'],'sn'=>$order['order_sn'])),
						'msg'	=>	"订单".$order['order_sn']."读取成功...开始取消...",
						);
				}
				die(json_encode($return));
				break;
			case '4':
				//逐个订单处理退单
				$info = array('sn'=>$ginfo['sn']);
				$status = Refund::refund($info,1,'','',1,2);
				if(!$status){
					//短信通知 TODO
					$return = array(
						'statusCode' => '300',
						'type'	=>	'3',
						'stop'	=>	'3',
						'urls'	=>	U('Item/Work/refund_entire',array('type'=>3,'plan'=>$ginfo['plan'],'sn'=>$ginfo['sn'])),
						'msg'	=>	"订单".$ginfo['sn']."取消失败....",
					);
				}else{
					$return = array(
						'statusCode' => '200',
						'type'	=>	'3',
						'stop'	=>	'3',
						'urls'	=>	U('Item/Work/refund_entire',array('type'=>3,'plan'=>$ginfo['plan'])),
						'msg'	=>	"订单".$ginfo['sn']."取消成功....",
					);
				}
				echo json_encode($return);
				break;
			case '5':
				//退单结束，作废场次
				$sn_s = session('sns');
				if(!empty($sn_s)){
					$return = array(
						'statusCode' => '300',
						'stop'	=>	'0',
						'sns'	=>	$sn_s,
						'msg'	=>	"演出取消失败,存在未取消订单...",
					);
				}else{
					$plans = M('Plan')->where(array('id'=>$ginfo['plan']))->setField('status',0);
					if(!$plans){
						//短信通知 TODO
						$return = array(
							'statusCode' => '300',
							'stop'	=>	'0',
							'msg'	=>	"演出场次禁用失败",
						);
					}else{
						$return = array(
							'statusCode' => '200',
							'stop'	=>	'0',
							'msg'	=>	"演出取消成功",
						);
					}
					
				}
				session('sns',null);
				echo json_encode($return);
				break;
		}
		if($ginfo['type'] == '1'){
			$this->assign('plan',$plan)
			     ->assign('today',$todaya)
			     ->display();
		}	
	}
	//座位号反查订单 二维码反查订单
	function check_oreder(){
		$pinfo = I('post.');
		if(IS_POST){
			if(empty($pinfo['sn'])){
			 $this->erun('查询条件不能为空');
			}
		}
		$product_id = get_product('id');
		if($pinfo['type'] == '1'){
			//座位号查订单
			$map = array(
				'seat' => $pinfo['sn'],
			);
			$plan_info = F('Plan_'.$pinfo['plan']);
			if(empty($plan_info)){
				$plan_info = M('Plan')->where(array('id'=>$pinfo['plan'],'product_id'=>$product_id))->find();
			}
			$info = M(ucwords($plan_info['seat_table']))->where($map)->find();
		}
		if($pinfo['type'] == '2'){
			//二维码查订单
			$info = \Libs\Service\Checkin::prison($pinfo['sn']);
		}
		if($pinfo['type'] == '3'){
			//身份证号查询
			$map = array(
				'idcard' => $pinfo['sn'],
			);
			$plan_info = F('Plan_'.$pinfo['plan']);
			if(empty($plan_info)){
				$plan_info = M('Plan')->where(array('id'=>$pinfo['plan'],'product_id'=>$product_id))->find();
			}
			$info = M(ucwords($plan_info['seat_table']))->where($map)->find();
		}
		$data = D('Order')->where(array('order_sn'=>$info['order_sn']))->relation(true)->find();
		if(!empty($data)){
			$data['info']=unserialize($data['info']);
			//区域分类
			foreach ($data['info']['data'] as $key => $value) {
				$area[$value['areaId']]['area'] = $value['areaId'];
				$area[$value['areaId']]['num'] = $area[$value['areaId']]['num']+1;
			}
		}else{
			$data = "404";
		}
		//读取销售计划 过期的两天 和当前未过期的全部
		$plantime = strtotime(" -2 day ",strtotime(date('Y-m-d')));
		$plan = M('Plan')->where(array('plantime'=>array('egt',$plantime),'product_id'=>$product_id))->order('plantime ASC')->select();
		$this->assign('data',$data)
			->assign('plan',$plan)
			->assign('area',$area)
			->assign('pinfo',$pinfo)
			->display();
	}
	/**
	 * 订单详情
	 */
	function detail(){
		$id   = I("get.id");
		$map  = array("id"=>$id);
		$data = Operate::do_read('TicketRefund',0,$map);
		$this->assign("data",$data);
		$this->display();
	}
	//查看座位详情
	function seat_info(){
		$info = I('get.');
		$plan = F('Plan_'.$info['plan_id']);
		$table = $plan['seat_table'];
		$seat = M(ucwords($plan['seat_table']))->where(array('seat'=>$info['seat']))->find();
		$this->assign('data',$seat)
			->assign('sale',unserialize($seat['sale']))
			->display();
	}
	/*景区门票改签*/
	public function endorse()
	{
		//改签日志
		if(IS_POST){
			$pinfo = I('post.');
			$product = get_product();
			//获取新的单号
			$sn = get_order_sn($pinfo['plan']);
			//新的计划ID
			//读取原有门票
			if($product['type'] == 2){
				$table = 'Scenic';
			}elseif ($product['type'] == 3) {
				$table = 'Drifting';
			}
			$count = D($table)->where(['order_sn'=>$pinfo['sns'],'status'=>2])->count();
			if((int)$count > 0){
				$model = new Model();
				$model->startTrans();
				$updata = ['plan_id'=>(int)$pinfo['plan'],'order_sn'=>$sn];

				$scenic = $model->table(C('DB_PREFIX').$table)->where(['order_sn'=>$pinfo['sns'],'status'=>2])->setField($updata);
				//记录改签日志
				$log = [
					'sns'	=>	$pinfo['sns'],
					'sn'	=>	$pinfo['sn'],
					'plans' =>  $pinfo['plans'],//历史
					'plan'	=>	$pinfo['plan'],
					'number'=>  $pinfo['number'],//原订单门票数
					'count'	=>	$pinfo['count'],//改签数
					'user_id' => get_user_id('id'),
					'createtime'=>time()
				];
				$endorse = $model->table(C('DB_PREFIX').'endorse')->add($log);
				if($scenic && $endorse){
					$model->commit();//提交事务
					//是否改变受让人
					$phone = (int)$pinfo['is_party'] === 2 ? $pinfo['phone'] : D('Order')->where(['order_sn'=>$pinfo['sns']])->getField('phone');
					
					$msgs = array(
						'phone'	 =>$phone,
						'title'	 =>planShow($pinfo['plan'],1,2),
						'remark' =>$msg,
						'num'	 =>$pinfo['count'],
						'sn'	 =>$sn,
						'product'=>$product['title']
					);
					\Libs\Service\Sms::order_msg($msgs,1);
					$this->srun('改签成功...');
				}else{
					$model->rollback();//事务回滚
					$this->erun('改签失败: 改签数据写入失败...');
				}
			}else{
				$this->erun('改签失败: 不存在符合改签条件的门票...');
			}
			
			
		}else{
			$ginfo = I('get.');
			$oinfo = D('Order')->where(['id'=>$ginfo['sn'],'status'=>1])->field('order_sn,number,product_type')->find();

			if(empty($oinfo)){
				$this->erun('订单状态不允许此项操作....');
			}
			if($oinfo['product_type'] == 2){
				$table = 'Scenic';
			}elseif ($oinfo['product_type'] == 3) {
				$table = 'Drifting';
			}

			$count = D($table)->where(['order_sn'=>$ginfo['sn'],'status'=>2])->count();
			if((int)$count === 0){
				D('Order')->where(['id'=>$ginfo['sn']])->setField(['status'=>9,'uptime'=>time()]);
				$this->erun('订单状态不允许此项操作....');
			}
			$this->assign('order',$oinfo)->assign('count',$count)->display();
		}
	}

	/**
	 * @DateTime 2018-05-31
	 * 转单，景区综合套票时，遇到退单边的情况，需要手动补充差价，按单门票转换
	 */
	function turn_single(){
		if(IS_POST){
			$pinfo = I('post.');
			$priceid = I('ticket_id');
			//读取要转的票型，结合销售计划转换
			$plan = F('Plan_'.$pinfo['plan']);
			if(empty($plan)){
				$this->erun("销售计划");
			}
			if((int)$priceid === (int)$pinfo['old_ticket']){
				$this->erun("不能转换为相同票型");
			}
			
			try{ 
				$model = new Model();
				$model->startTrans();
				$createtime = time();
				$ticketType = F('TicketType'.$plan['product_id']);
				//读取订单历史内容
				$oinfo = D('OrderData')->where(['order_sn'=>$pinfo['sn']])->getField('info');
				$oinfo = unserialize($oinfo);
				$map = [
					'plan_id'  => $pinfo['plan'],
					'order_sn' => $pinfo['sn'],
					'price_id' => $pinfo['old_ticket']	
				];
				//获取列表
				$list = D($plan['seat_table'])->where($map)->limit($pinfo['number'])->field('id,price_id,sale')->select();
				$hTicket = $ticketType[$pinfo['old_ticket']];
				$nTicket = $ticketType[$priceid];
				if($hTicket['product_id'] <> $nTicket['product_id']){
					$model->rollback();
					$this->erun("暂不支持不同票型间的装换");
					return false;
				}
				//读取活动
				$ainfo = D('Activity')->where(['id'=>$pinfo['act']])->field('id,type,param')->find();
				$aparam = json_decode($ainfo['param'],true);

				foreach ($list as $k => $v) {
					$hSale = unserialize($v['sale']);
					$remark = print_remark($nTicket['remark'],$plan['product_id']);
					$sale = [
						'plantime'	=>	$hSale['plantime'],
						'games'		=>	$hSale['games'],
						'product_name'=>$aparam['info']['price']['name'],
						'priceid'=>$priceid,
						/*
						'priceName' =>	$aparam['info']['price']['name'],
						'price'		=>	$aparam['info']['price']['price'],
						'discount'	=>	$aparam['info']['price']['discount'],*/

						'priceName' =>	$aparam['info']['price']['name'],
						'price'		=>	$nTicket['price'],
						'discount'	=>	$nTicket['discount'],
						'remark_type' => $remark['remark_type'],
						'remark'=>$remark['remark'],
					];
					$updata[$v['id']] = [
						'id'	=>	$v['id'],
						'sale'	=>	$sale
					];
					$up = $model->table(C('DB_PREFIX').$plan['seat_table'])->where(['id'=>$v['id']])->setField(['sale'=>serialize($sale),'price_id'=>$priceid]);
					if(!$up){
						$model->rollback();//事务回滚
						$this->erun("票型更新失败");
						return false;
					}
				}
				//计算新金额
				$nMoney = $nTicket['discount']*$pinfo['number'];
				$hMoney = $hTicket['discount']*$pinfo['number'];

				$cMoney = abs((int)$nMoney - (int)$hMoney);
				if($cMoney <> 0){
					//读取该笔订单扣款记录，主要是获取扣款条件
					$oPayMap = D('CrmRecharge')->where(['order_sn'=>$pinfo['sn'],'type'=>2])->field('crm_id')->select();
					$oPayMapId = array_unique(array_column($oPayMap,'crm_id'));
					if($nMoney > $hMoney){
						//扣款操作
						$crmData = array('cash' => array('exp','cash-'.$cMoney),'uptime' => $createtime);
						$money = $oinfo['subtotal']+$cMoney;
						$payType = 2;
					}else{
						//返款操作
						$crmData = array('cash' => array('exp','cash+'.$cMoney),'uptime' => $createtime);
						$money = $oinfo['subtotal']-$cMoney;
						$payType = 4;
					}
					
					$c_pay = $model->table(C('DB_PREFIX')."crm")->where(['id'=>['in',implode(',',$oPayMapId)]])->setField($crmData);
					//TODO 不同级别扣款金额不同
					foreach ($oPayMapId as $p => $l) {
						$data[] = array(
							'cash'		=>	$cMoney,
							'user_id'	=>	get_user_id('id'),
							'guide_id'	=>	$l,//TODO  这个貌似没什么意义
							'addsid'	=>	1,
							'crm_id'	=>	$l,
							'createtime'=>	$createtime,
							'type'		=>	$payType,
							'order_sn'	=>	$pinfo['sn'],
							'balance'	=>  balance($l,1),
							'tyint'		=>	1,//客户类型1企业4个人
							'remark'	=> '换票差价'
						);
					}
					$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->addAll($data);
					if($c_pay == false || $c_pay2 == false){
						$model->rollback();//事务回滚
						$this->erun("资金操作失败");
						return false;
					}
				}
				foreach ($oinfo['data'] as $ke => $va) {
					$upTicket = $updata[$va['id']];
					if($upTicket){
						//dump($upTicket);
						$dataList[] = [
							'ciphertext' => $va['ciphertext'],
					        'priceid' => $priceid,
					        'price' => $upTicket['sale']['price'],
					        'discount' => $upTicket['sale']['discount'],
					        'id' => $va['id'],
					        'idcard' => $va['idcard'],
					        'plan_id' => $va['plan_id'],
					        'child_ticket' => $va['child_ticket']
						]; 

					}else{
						$dataList[] = $va;
					}
					$upTicket = '';
				}
				//更新订单列表的内容
				$newData = [
					'subtotal'	=> $money,
					'checkin'	=> $oinfo['checkin'],
					'data' 		=> $dataList,
					'crm' 		=> $oinfo['crm'],
					'pay' => $oinfo['pay'],
					'param'	=> $oinfo['param'],
					'child_ticket'=>$oinfo['child_ticket']
				];
				$o_status = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn'=>$pinfo['sn']))->setField('info',serialize($newData));
				//改变订单状态
				$status = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$pinfo['sn']))->setField(['money'=>$money,'uptime'=>$createtime]);
				
				if($o_status && $status){
					$model->commit();
					$this->srun('办理成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun("转换失败!");
				}
			}catch(Exception $e){ 
				$this->erun("转换失败:".$e);
			}
			
		}else{
			//列出订单门票详情
			$ginfo = I('get.');
			if(!empty($ginfo['sn'])){
				$oinfo = D('OrderData')->where(['order_sn'=>$ginfo['sn']])->field('order_sn,info')->find();
				$oinfo['info'] = unserialize($oinfo['info']);
				foreach ($oinfo['info']['data'] as $k => $v) {
					$v['title'] = ticketName($v['priceid'],1);
					$ticket[$v['priceid']] = $v;
				}
				$this->assign('oinfo',$oinfo)->assign('sn',$ginfo['sn'])->assign('ticket',$ticket)->assign('activity',$oinfo['info']['param'][0]['activity']);
			}
			
			$this->display();
		}
	}

	//年卡办理
	function year_card(){
		if(IS_POST){
			$pinfo = I('post.');
			$model = D('Member');
			//判断身份证号是否唯一
			if($model->where(['idcard'=>$pinfo['idcard']])->field('id')->find()){
				$this->erun("添加失败,该身份证已注册");
				return false;
			}
			$data = [
				'source'	=>  '1',
				'no_number' =>  date('YmdH').genRandomString(6,1),
				'idcard'	=>	strtolower(trim($pinfo['idcard'])),
				'nickname'	=>	$pinfo['content'],
				'phone'		=>	$pinfo['phone'],
				'group_id'	=>	(int)$pinfo['group'],
				'user_id'	=>	get_user_id(),//窗口时写入办理人
				'thetype'	=>	$pinfo['type'], //凭证类型
				'remark'	=>	$pinfo['remark'],//备注
				'create_time'=> time(),
				'update_time'=> time(),
				'verify'	=>	genRandomString(),
				'status'	=>	'1',
			];
			if($model->token(false)->create($data)){
				$result = $model->add();
				if($result){
					$this->srun('办理成功',array('tabid'=>$this->menuid,'closeCurrent'=>true));
				}else{
					$this->erun("添加失败:");
				}
			}else{
				$this->erun("添加失败tokern".$e);
			}
			
		}else{
			$type = F('MemGroup');
			if(empty($type)){
				D('Crm/MemberType')->mem_group_cache();
			}
			$this->assign('type',$type)->display();
		}
	}
	//年卡临时凭证
	function year_ticket(){
		if(IS_POST){
			//根据身份证或手机号 查询年卡然后打印  打印日期 人数
			$pinfo = I('post.');
			if(empty($pinfo['phone']) && empty($pinfo['card'])){
				$this->erun("请输入查询条件...");
			}
			if(!empty($pinfo['phone'])){
				$where['phone'] = $pinfo['phone'];
			}
			if(!empty($pinfo['card'])){
				$where['idcard'] = $pinfo['card'];
			}
			$list = D('Crm/Member')->where($where)->field('openid,password,verify,thetype,remark,user_id,create_time',true)->select();
			$this->assign("data",$list);
			//打印凭证
			//录入年卡记录
		}
		$this->display();
	}

	//打开订单详情,直接编辑区域数量
}