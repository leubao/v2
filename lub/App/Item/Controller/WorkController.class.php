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
				$plan = Operate::do_read('Plan',1,array('product_id'=>get_product('id'),'status'=>2));
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
			switch ($v['product_type']) {
				case '2':
					$tooltype = '00';
					$name = date('H:m',$v['starttime']).'-'.date("H:m",$v['endtime']);
					break;
				case '3':
					$tooltype = tooltype($param['tooltype'],1);
					$name = '[第'.$v['games'].'趟-'.$tooltype.'] '. date('H:m',$v['starttime']).'-'.date("H:m",$v['endtime']);

					break;
			}
			$data[] = array(
				'id'	=> $v['id'],
				'pid'   => '1',
				'pId'	=>	'1',
				'plan' 	=>	$v['id'],
				'type'	=>	$pinfo['type'],
				'tooltype' => $tooltype,
				'name'  => $name,
			);
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
			$info = explode('-', $pinfo['plan']);
			$map = array(
				'product_id'=>$this->pid,
				'status'=>2,//状态必为售票中
				'plantime' => (int)$info[0] ? (int)$info[0] : $today,
				'games' => (int)$info[1] ? (int)$info[1] : 1 ,
			);
			$plan = M('Plan')->where($map)->field('id,param,seat_table,product_type,plantime,starttime,endtime,games')->find();
			$param = unserialize($plan['param']);
			//拉取坐席
			if($ginfo == '1'){
				foreach ($param['seat'] as $k => $v) {
					$area[] = array(
						'id'	=>	$v,
						'name'	=>	areaName($v,1),
						'number'=>  areaSeatCount($v,1),
						'num'	=>  area_count_seat($plan['seat_table'],array('status'=>'0','area'=>$v),1),
						'nums'	=>	area_count_seat($plan['seat_table'],array('status'=>array('in','2,66,99'),'area'=>$v),1),//已售出
						'numb'	=>	area_count_seat($plan['seat_table'],array('status'=>array('in','66'),'area'=>$v),1),//预定数
						'cnum'	=>	area_count_seat($plan['seat_table'],array('status'=>array('in','99'),'area'=>$v),1),//已检票
					); 
				}
				$sale = array(
					'nums'	=>	area_count_seat($plan['seat_table'],array('status'=>array('in','2,66,99')),1),
					'numb'	=>	area_count_seat($plan['seat_table'],array('status'=>array('in','66')),1),
					'money' =>	format_money(M('Order')->where(array('status'=>array('in','1,7,9'),'plan_id'=>$plan['id']))->sum('money')),
				);
				$return = array(
					'statusCode' => '200',
					'info'	=>	'',
					'plan'	=> $plan['id'],
					'area'	=> $area,
					'sale'	=> $sale,
				);
			}
			//拉取小商品
			if($ginfo == '2'){
				foreach ($param['goods'] as $k => $v) {
					$goods[] = array(
						'id'	=>	$v,
						'name'	=>	goodsName($v,1),
						'number'=>  areaSeatCount($v,1),//已售出
						'price'	=>  goodsprice($v,1)
					); 
				}
				$return = array(
					'statusCode' => '200',
					'info'	=>	'',
					'plan'	=> $plan['id'],
					'goods'	=> $goods				
				);
			}
			//设置session
			session('plan',$plan);
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
		echo json_encode($return);
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
	 * 窗口扫码付款
	 * @param  string $type 窗口扫码付款分为支付宝 alipay  微信支付 wxpay
	 */
	function micropay($type = 'wxpay'){
		//微信支付
		if($type == 'wxpay'){
			//加载微信支付
			$pay = & load_wechat('Pay','6',$this->pid);
			//①、提交被扫支付
			$result = $pay->micropay();
			//如果返回成功
			if(!array_key_exists("return_code", $result)
				|| !array_key_exists("out_trade_no", $result)
				|| !array_key_exists("result_code", $result))
			{
				echo "接口调用失败,请确认是否输入是否有误！";
				throw new WxPayException("接口调用失败！");
			}
			
			//签名验证
			$out_trade_no = $microPayInput->GetOut_trade_no();
			
			//②、接口调用成功，明确返回调用失败
			if($result["return_code"] == "SUCCESS" &&
			   $result["result_code"] == "FAIL" && 
			   $result["err_code"] != "USERPAYING" && 
			   $result["err_code"] != "SYSTEMERROR")
			{
				return false;
			}

			//③、确认支付是否成功
			$queryTimes = 10;
			while($queryTimes > 0)
			{
				$succResult = 0;
				$queryResult = $this->query($out_trade_no, $succResult);
				//如果需要等待1s后继续
				if($succResult == 2){
					sleep(2);
					continue;
				} else if($succResult == 1){//查询成功
					return $queryResult;
				} else {//订单交易失败
					return false;
				}
			}
			
			//④、次确认失败，则撤销订单
			if(!$this->cancel($out_trade_no))
			{
				throw new WxpayException("撤销单失败！");
			}
			
			return false;
		}
		//支付宝支付
		if($type == 'alipay'){

		}
	}
	/**
	 * 
	 * 查询订单情况
	 * @param string $out_trade_no  商户订单号
	 * @param int $succCode         查询订单结果
	 * @return 0 订单不成功，1表示订单成功，2表示继续等待
	 */
	public function query($out_trade_no, &$succCode)
	{
		$queryOrderInput = new WxPayOrderQuery();
		$queryOrderInput->SetOut_trade_no($out_trade_no);
		$result = WxPayApi::orderQuery($queryOrderInput);
		
		if($result["return_code"] == "SUCCESS" 
			&& $result["result_code"] == "SUCCESS")
		{
			//支付成功
			if($result["trade_state"] == "SUCCESS"){
				$succCode = 1;
			   	return $result;
			}
			//用户支付中
			else if($result["trade_state"] == "USERPAYING"){
				$succCode = 2;
				return false;
			}
		}
		
		//如果返回错误码为“此交易订单号不存在”则直接认定失败
		if($result["err_code"] == "ORDERNOTEXIST")
		{
			$succCode = 0;
		} else{
			//如果是系统错误，则后续继续
			$succCode = 2;
		}
		return false;
	}
	
	/**
	 * 
	 * 撤销订单，如果失败会重复调用10次
	 * @param string $out_trade_no
	 * @param 调用深度 $depth
	 */
	public function cancel($out_trade_no, $depth = 0)
	{
		if($depth > 10){
			return false;
		}
		
		$clostOrder = new WxPayReverse();
		$clostOrder->SetOut_trade_no($out_trade_no);
		$result = WxPayApi::reverse($clostOrder);
		
		//接口调用失败
		if($result["return_code"] != "SUCCESS"){
			return false;
		}
		
		//如果结果为success且不需要重新调用撤销，则表示撤销成功
		if($result["result_code"] != "SUCCESS" 
			&& $result["recall"] == "N"){
			return true;
		} else if($result["recall"] == "Y") {
			return $this->cancel($out_trade_no, ++$depth);
		}
		return false;
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
			'product_id'=>	\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
			'status'	=>	'1',
			//'createtime'=>	array('GT', strtotime(date("Ymd",time()))),
		);
		if(!empty($sn)){
			//单号长度不小于5
			if(strlen($sn) < '5'){
				$this->erun("您输入的单号太短,请输入单号后五位");
				return false;
			}
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
			$map = array(
				'product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
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
			
		}else{
			$map = array(
				'product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
				'status'=>array('in','5,6'),
				'createtime'=>array('GT', strtotime(date("Ymd",time()))),//过滤已过期的订单
			);
		}
		$this->basePage('Order',$map, 'createtime DESC');
		$this->assign('map',$map)->assign('planname',$planname)->assign('channel',$channel)->assign('channelname',$channelname)->display();
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
			$info = D('Order')->where(array('order_sn'=>$sn))->relation(true)->find();
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
				$this->assign('ticket',$ticket);

			}
			
			$this->assign('data',$info)
				->assign('type',$info['product_type'])
				->assign('area',$area)
				->display();
		}
	}
	
	/**
	 * 获取当前区域价格与座椅信息 景区产品根据销售计划获取价格信息
	 * @param $pinfo['area'] int 当前区域ID
	 */
	function getprice(){
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
        /*
		$user_id = get_user_id();  //当前登录用户id
		$c_data  = Operate::do_read('User',0,array("id"=>$user_id));
		//TODO 限定显示有效数据
		if($c_data["cid"] != ""){
			$where["crm_id"] = $c_data["cid"];
			$this->assign("crm_id",$where["crm_id"]);
		}*/
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
	 * 退票
	 */
	function refunds(){
		$ginfo = I('get.');
		if(empty($ginfo)){$this->erun("参数错误!");}
		//退订单所有门票
		if($ginfo['order'] == '1'){
			//订单内所有门票全退
			if(Refund::refund($ginfo,1,'','',1,1) != false){
				$this->srun('退票成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				$this->erun('退票失败!');
			}
		}
		//退单个座位
		if($ginfo['order'] == '3'){
			//退单个座位
			if(Refund::refund($ginfo,2,$ginfo['area'],$ginfo['seatid'],1,1) != false){
				$this->srun('退票成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				$this->erun('退票失败!');
			}		
		}
		//退子票 TODO   子票只能单张退
		if($ginfo['order'] == '5'){
			if(Refund::refund($ginfo,5,$ginfo['area'],$ginfo['seatid'],1,1) != false){
				$this->srun('退票成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				$this->erun('退票失败!');
			}
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
		if($pinfo['type'] == '1'){
			//同意申请
			if(M('Order')->where(array('order_sn'=>$pinfo['sn']))->getField('status') <> '9'){
				if(Refund::refund($pinfo,1,'','',$pinfo['poundage'],1)){
					$this->srun('退款成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun("退票失败!");
				}
			}else{
				$this->erun("订单已打印，不能完成此项操作!");
			}
		}else{
			//驳回申请
			$data = array(
				"id" => $pinfo["id"],
				"against_reason" => $pinfo["against_reason"],
				"status" => 2,
				"user_id" => \Libs\Util\Encrypt::authcode($_SESSION['lub_imuid'], 'DECODE'),
			);
			//改变订单状态
			$order_up = M('Order')->where(array('order_sn'=>$pinfo['sn']))->setField('status',1);
			$up = M('TicketRefund')->save($data);
			if($up && $order_up){
				$this->srun('退款申请驳回成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("退款申请驳回失败!");
			}
		}
	}
	/**
	 * 订单详情
	 */
	function detail(){
		$id   = I("get.id");          //取消订单id
		$map  = array("id"=>$id);
		$data = Operate::do_read('TicketRefund',0,$map);
		//dump($data);
		$this->assign("data",$data);
		$this->display();
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
				//按区域核减
				foreach ($area as $ka=>$ve){
					if($plan['product_type'] == '1'){
						$map = array('order_sn'=>$pinfo['sn'],'area'=>$ve['area']);
					}else{
						$map = array('order_sn'=>$pinfo['sn'],'price_id'=>$ve['area']);
					}
					
					$table=ucwords($plan['seat_table']);
					$seat =D($table)->where($map)->limit($ve['num'])->select();
					//按座位核减 
					foreach ($seat as $k=>$v){
						if($plan['product_type'] == '1'){
							$status[$k] = Refund::refund($pinfo,3,$ve['area'],$v['seat'],1,1);
						}else{
							$status[$k] = Refund::refund($pinfo,3,$ve['area'],$v['id'],1,1);
						}
						
						if($status[$k] == false){
							$this->erun("核减失败!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
						}
					}
				}
				
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
				//$subNum = $oinfo['number']*self::$Cache['Config']['subtract'];//最大可核减数
				$subNum = $oinfo['number']-1;//最大可核减数
				$subNum = (int)$subNum;
				if($subNum < 1 || !empty($oinfo['subtract'])){
					$this->erun("订单未能满足核减要求!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}
			}
			$seat = unserialize($oinfo['info']);
			foreach ($seat['data'] as $k=>$v){
				if($info['product_type'] == '1'){
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
		switch ($ginfo['type']) {
			case '1':
				//只能退当天的场次
				$today = strtotime(date('Y-m-d'));
				$plan = M('Plan')->where(array('plantime'=>$today,'status'=>'2'))->select();
				break;
			case '2':
				//显示当前要退的场次
				//$plan = M('Plan')->where(array('id'=>$ginfo['plan']))->field('id')->find();
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
				echo json_encode($return);
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
		if($pinfo['type'] == '1'){
			//座位号查订单
			$map = array(
				'seat' => $pinfo['sn'],
			);
			$plan_info = F('Plan_'.$pinfo['plan']);
			if(empty($plan_info)){
				$plan_info = M('Plan')->where(array('id'=>$pinfo['plan']))->find();
			}
			$info = M(ucwords($plan_info['seat_table']))->where($map)->find();
		}
		if($pinfo['type'] == '2'){
			//二维码查订单
			$info = \Libs\Service\Checkin::prison($pinfo['sn']);
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
		$plan = M('Plan')->where(array('plantime'=>array('egt',$plantime)))->order('plantime ASC')->select();
		$this->assign('data',$data)
			->assign('plan',$plan)
			->assign('area',$area)
			->assign('pinfo',$pinfo)
			->display();
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
	/*窗口获取渠道商*/
	function channel(){
		if(IS_POST){
			if($_POST["name"] != ""){
				$map["name"] = array('like','%'.$_POST["name"].'%');
				$map['product_id'] = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
				$this->assign("name",$_POST["name"]);
			}	
		}
		C('VAR_PAGE','pageNum');
		$db = M('Crm');
		$count = $db->where($map)->count();// 查询满足要求的总记录数
		$num = 9;
		$p = new \Item\Service\Page($count,$num);
		$currentPage = !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
		$firstRow = ($currentPage - 1) * $num;
		$listRows = $currentPage * $num;
		$data = $db->where($map)->order("id ASC")->limit($firstRow . ',' . $p->listRows)->select();
		$this->assign ( 'totalCount', $count);
		$this->assign ( 'numPerPage', $p->listRows);
		$this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
		$this->assign("list",$data)
			->display();

	}
	/*窗口获取导游*/
	function guide(){
		if(IS_POST){
			if($_POST["name"] != ""){
				$map["nickname"] = array('like','%'.$_POST["name"].'%');
				$this->assign("name",$_POST["name"]);
			}	
		}
		//TODO 将员工筛选出去
		C('VAR_PAGE','pageNum');
		$db = M('User');
		$count = $db->where($map)->count();// 查询满足要求的总记录数
		$num = 10;
		$p = new \Item\Service\Page($count,$num);
		$currentPage = !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
		$firstRow = ($currentPage - 1) * $num;
		$listRows = $currentPage * $num;
		$data = $db->where($map)->order("id ASC")->limit($firstRow . ',' . $p->listRows)->select();
		$this->assign ( 'totalCount', $count);
		$this->assign ( 'numPerPage', $p->listRows);
		$this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
		$this->assign('type',I('type'));
		$this->assign("list",$data)
			->display();
	}
}