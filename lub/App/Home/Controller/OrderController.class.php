<?php
// +----------------------------------------------------------------------
// | LubTMP  渠道商订单管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Libs\Service\Sms;

use Libs\Service\Operate;
use Common\Controller\Base;
use Libs\Service\Order;
use Home\Service\Partner;
use Libs\Service\Refund;
use Common\Model\Model;
class OrderController extends Base{
	function _initialize(){
		parent::_initialize();
	}
	/**
	 * 订单列表
	 */
	function index(){
		if (IS_POST) {
            $this->redirect('home.php?g=home&m=order&a=index', $_POST);
        }
		$db = D('Order');
		$where = array();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $user_id = I('user');
        $product_id = I('product');
        $datetype = I('datetype') ? I('datetype') : '1';
        $status = I('status');
        $sn = I('sn');
        //传递查询时间
        $this->assign('start_time',$start_time)
        	->assign('end_time',$end_time)->assign('datetype',$datetype);
		$uinfo = Partner::getInstance()->getInfo();
        if($uinfo['groupid'] == '3'){
        	$where =  ['channel_id' =>	$uinfo['cid']];
        }else{
        	$where = array(
				'channel_id' =>	array(in,$this->get_channel()),
			);
        }
        if(!empty($user_id)){
        	$where['user_id'] = $user_id;
        }
        if($datetype == '1'){
        	if (!empty($start_time) && !empty($end_time)) {
	            $start_time = strtotime($start_time);
	            $end_time = strtotime($end_time) + 86399;
	            $where['createtime'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
	        }else{
	        	//查询时间段为空时默认查询未过期的订单
	        	$where['plan_id'] = array('in',implode(',',array_column(get_today_plan(),'id')));
	        }
	        $order = 'createtime DESC';
        }elseif($datetype == '2' && !empty($start_time) && !empty($end_time)){
        	//查询一段时间内所有演出计划,时间段 不超过三十天
        	$day = timediff($start_time,$end_time);
        	if($day['day'] < 0 || $day['day'] > 60){
        		$this->error("根据演出日期查询时最多可查询60天的数据...");
        	}
        	$start_time = strtotime($start_time);
	        $end_time = strtotime($end_time);
	        $plantime = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        	$planlist = M('Plan')->where(['plantime'=>$plantime,'status'=>['in','2,3,4']])->field('id')->select();
        	$where['plan_id'] = array('in',implode(',',array_column($planlist,'id')));
        	$order = 'plan_id DESC';
        }
        if ($status != '') {
            $where['status'] = $status;
        }
		if($sn != ''){
			$where['order_sn'] = $sn;
		}
		if(!empty($product_id)){
			$where['product_id'] = $product_id;
		}
		$user = M('User')->where(array('status'=>'1','cid'=>$uinfo['cid']))->field('id,nickname')->select();
		$product = M('Product')->field('id,name')->select();
		$count = $db->where($where)->count();
		$Page  = new \Home\Service\Page($count,25);
		$show  = $Page->show();
		$list = $db->where($where)->limit($Page->firstRow.','.$Page->listRows)->order($order)->select();
		//统计数量和金额
		$info['num'] = $db->where($where)->sum('number');
		$info['money'] = $db->where($where)->sum('money');
		$this->assign('data',$list)
			->assign('page',$show)
			->assign('where',$where)
			->assign('user',$user)
			->assign('info',$info)
			->assign('product',$product)
			->display();
	}
	/**
	 * 订单座位图
	 */
	function seat(){
		$ginfo = I('get.');
		if(empty($ginfo)){
			$this->error("参数错误!");
		}
		$info = Operate::do_read('Order',0,array('order_sn'=>$ginfo['sn'],'status'=>'1'),'','',true);
		$info['info']=unserialize($info['info']);
		foreach ($info['info']['data'] as $k=>$v){
			$area[$v['areaId']][$k] = array(
				'areaId'	=> $v['areaId'],
				'seatid'	=> $v['seatid'],
			);
			$area[$v['areaId']] = $v['areaId']; 
		}
		$this->assign('area',$area);
		$this->assign('data',$info['info']['data']);
		$this->display();
	}
	/**
	 *加载座位
	 */
	function seats(){
		$ginfo = I('get.');
		//加载座椅
		$info = Operate::do_read('Area',0,array('id'=>$ginfo['area'],'status'=>1));
		$seat = unserialize($info['seat']);
		$return = array(
			'status' => '1',
			'message' => '区域加载成功!',
			'seat'	=> $info ? $info : 0,
		);
		echo json_encode($return);
		$this->assign('data',$info)
			 ->assign('seat',$seat);
	}
	/**
	 * 创建订单
	 */
	function channelPost(){
		$info = $_POST['info'];
		$uInfo = \Home\Service\Partner::getInstance()->getInfo();
		//根据当前用户所属分组类型进行区分是政企还是企业或个人
		if($uInfo['group']['type'] == '3'){
			//政企
			$sn = Order::channel($info,26,$uInfo);
		}else{
			//个人或企业
			$sn = Order::channel($info,22,$uInfo);
		}
		if($sn != false){
			$return = array('statusCode' => '200','sn'=>$sn);
		}else{
			$return = array('statusCode' => '300','sn'=>$sn);
		}
		//记录售票员日报表
		echo json_encode($return);
		return true;
	}
	/*
	 *账户余额支付
	 */
	function pay(){
		//记录充值消费记录
		if(IS_POST){
			$pinfo = $_POST['info'];
			$info = json_decode($pinfo,true);
			$oinfo = Operate::do_read('Order',0,array('order_sn'=>$info['sn']),'','',true);
			if(empty($info) || empty($oinfo)){echo json_encode(array('statusCode' => '300'));}
			//@印象大红袍
			/*if($oinfo['status'] == '5'){
				$status = Order::pay_no_seat($info,$oinfo);
			}elseif($oinfo['status'] == '6'){
				$status = Order::seat_no_pay($info,$oinfo);
			}else{
				$status = Order::channel_seat($info,$oinfo);
			}*/
			$status = Order::channel_seat($info,$oinfo);
			//返回结果
			if($status != false){
				$return = array('statusCode' => '200','sn'=>$info['sn'],);
			}else{
				$return = array('statusCode' => '300','sn'=>$info['sn'],);
			}
			//记录售票员日报表
			echo json_encode($return);
			return true;
		}
	}
	/*通过网银支付
	@param $info 参数
	*/
	public function web_pay($info){
		$oinfo = Operate::do_read('Order',0,array('order_sn'=>$info['sn']),'','',true);
		if(empty($info) || empty($oinfo)){return false;}
		if($oinfo['status'] == '5'){
			$status = Order::pay_no_seat($info,$oinfo);
		}else{
			$status = Order::channel_seat($info,$oinfo,'4');
		}
		//返回结果
		if($status != false){
			return true;
		}else{
			return false;
		}
	}
	/**
	*订单详情
	*/
	function orderinfo(){
		$ginfo = I('get.');
		if(empty($ginfo)){
			$this->error("参数错误!");
		}
		$info = Operate::do_read('Order',0,array('order_sn'=>$ginfo['sn']),'','',true);
		$info['info']=unserialize($info['info']);
		//当前产品类型
		if($info['product_type'] == '1'){
			//区域分类
			foreach ($info['info']['data'] as $key => $value) {
				$area[$value['areaId']]['area'] = $value['areaId'];
				$area[$value['priceid']]['areaname'] = areaName($value['areaId'],1);
				$area[$value['areaId']]['num'] = $area[$value['areaId']]['num']+1;
			}
		}else{
			foreach ($info['info']['data'] as $key => $value) {
				$area[$value['priceid']]['area'] = $value['priceid'];
				$area[$value['priceid']]['areaname'] = ticketName($value['priceid'],1);
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
		//取消订单
		if($info['status'] == '1' || $info['status'] == '5' || $info['status'] == '6'){
			$info['cancel'] = '1';
		}else{
			$info['cancel'] = '0';
		}
		$this->assign('data',$info)->assign('area',$area)->assign('type',$info['product_type']);
		switch ($info['product_type']) {
			case '1':
				$template = 'orderinfo';
				break;
			case '2':
				//$template = 'order_scenic';
				$template = 'orderinfo';
				break;
			case '3':
				$template = 'orderinfo';
				//$template = 'order_scenic';
				break;
		}
		if($ginfo['type'] == 1){
			$this->display($template);
		}else{
			$this->display('order_info');
		}	
	}
	/**
	 * 取消订单
	 */
	function cancel_order(){
		if(IS_POST){
			$ginfo = I('post.');
			if(empty($ginfo)){
				$this->error("参数错误!");
			}
			$info = Operate::do_read('Order',0,array('order_sn'=>$ginfo['sn'],'status'=>1),'','',true);
			//增加场次时间判断 开演后就不让提交退单申请
			if(if_plan($info['plan_id']) == false){
				$this->error('抱歉，该场次或该订单状态不允许此项操作!');
			}else{
				$model = new \Think\Model();
				$model->startTrans();
				if($info["status"] == '1' || $info["status"] == '5'){
					//在lub_ticket_refund表中添加一条数据,记录申请
					$data = array(
						"createtime" => time(),
						"order_sn"   => $ginfo["sn"],
						"applicant"  => \Home\Service\Partner::getInstance()->id,
						"crm_id"     => $info["channel_id"],
						"plan_id"    => $info["plan_id"],
						"reason"     => $ginfo["reason"],
						"re_type"    => $ginfo["re_type"],
						"status"     => 1,
						"money"      => $ginfo["money"],
						"launch"     => 2,
						"order_status" => $ginfo["order_status"]
					);
					$add = $model->table(C('DB_PREFIX')."ticket_refund")->add($data);
					//修改lub_order表的status字段
					$order_info = array(
						"id" => $info["id"],
						"status"   => $info['status'] == '1' ? '7' : '8',
					);
					$up = $model->table(C('DB_PREFIX')."order")->save($order_info);
					
					if($add && $up){
						$model->commit();//成功则提交
						$this->success("取消订单申请成功！");
					}else{
						$model->rollback();//不成功，则回滚
						$this->error("取消订单申请失败!");			
					}				
				}else{
					/*修改lub_order表的status字段
					$order_info = array(
						"id" => $info["id"],
						"status"   => 3
					);
					$up = $model->table(C('DB_PREFIX')."order")->save($order_info);
					if($up){
						$this->success("订单取消中，等待管理员处理...");
					}else{
						$this->error("取消订单失败！");
					}*/
					$this->error('抱歉，该场次或该订单状态不允许此项操作!');				
				}
			}
			
		}
	} 	
	/**
	 * 订单核减
	 * @印象大红袍
	 */
	function subtracts(){
		if(IS_POST){
			$pinfo = I('post.');
			$number = 0;
			foreach ($pinfo['area'] as $k=>$v){
				$area[$v]=array(
					'area'=>$v,
					'num'=>$pinfo['seat_num'][$k],
				);
				$number = $number + $pinfo['seat_num'][$k];
			}
			if($number  == '0' || $number ==' '){
				$this->success("未找到核减数量!");
			}else{
				//开始核减
				$subNum =  (int)$pinfo['nums'];
				if((int)$number > $subNum ){//超出最大核减数 
					$this->error("超出核减总数!");
				}
				//得到订单
				$info = Operate::do_read('Order',0,array('order_sn'=>$pinfo['sn']),'','',true);
				//查看核减标示
				if(!empty($info['subtract'])){
					$this->error("该订单已完成此项操作!");
				}
				//获取所属计划
				$plan = M('Plan')->where(array('id'=>$info['plan_id']))->find();
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
							$this->error("核减失败!");
						}
					}
				}
				$this->success("核减成功!");
			}
		}
	}
	/**
	*订单核减
	*/
	function subtract(){
		if(IS_POST){
			$ginfo = $_POST['info'];
			$info = json_decode($ginfo,true);
			if(empty($info)){$data = array('statusCode' => 300,'msg' => "参数错误!");}
			$oinfo = Operate::do_read('Order',0,array('order_sn'=>$info['sn'],'status'=>'1'),'','',true);
			//渠道版限定核减时间 TODO
			$plan = M('Plan')->where(array('id'=>$oinfo['plan_id'],'status'=>'1'))->getField('plantime');
			$subtract_time = $this->pro_conf[$oinfo['product_id']]['subtract_time'];
			if(date('Ymd',$plan) == date('Ymd') && date('H:i') >=  $subtract_time || empty($oinfo)){
				$data = array('statusCode' => 300,'msg' => "超出核减时间，不能进行核减！");
			}else{
				//判断订单类型
				if($oinfo['status'] == '6'){
					//政府订单
					$subNum = $oinfo['number'];
				}else{
					//渠道订单
					$subNum = $oinfo['number']*self::$Cache['Config']['subtract'];//最大可核减数
					$subNum = (int)$subNum;
					if($subNum < 1 || !empty($oinfo['subtract'])){
						$data = array('statusCode' => 300,'msg' => "订单未能满足核减要求!");
					}
				}
				$seat = unserialize($oinfo['info']);
			
				foreach ($seat['data'] as $k=>$v){
					if($oinfo['product_type'] == '1'){
						$area[$v['areaId']][$k] = $v;
						$area[$v['areaId']]['areaname'] = areaName($v['areaId'],1);
						$area[$v['areaId']]['area']	=	$v['areaId'];
						$area[$v['areaId']]['num'] = count($area[$v['areaId']])-4 == 0 ? 1 : count($area[$v['areaId']])-4;	
					}else{
						$area[$v['priceid']][$k] = $v;
						$area[$v['priceid']]['areaname'] = ticketName($v['priceid'],1);
						$area[$v['priceid']]['area']	=	$v['priceid'];
						$area[$v['priceid']]['num'] = count($area[$v['priceid']])-4 == 0 ? 1 : count($area[$v['priceid']])-4;
					}			
						
				}
				if(empty($data)){
					$this->assign('sn',$info['sn']);
					$data  = array('statusCode' => 200,'sn' => $info['sn'], 'area' => $area, 'num' => $subNum, 'msg' => "OK" );
				}
			}
			die(json_encode($data));
		}
	}
	/**
	 * 出票方式  订单出票
	 */
	function drawer(){
		if(IS_POST){
			$map = json_decode($_POST['info'],true);
			$info = M('Order')->where(array('order_sn'=>$map['sn']))->getField('status,plan_id');
			if($info['status'] == '9'){//已打印
				$return = array(
					'statusCode' => '200',
					'forwardUrl' => U('Home/Order/to_print',array('sn'=>$map['sn'],'plan_id'=>$info['plan_id'],'type'=>'2')),
				);
			}else{//未打印
				$return = array(
					'statusCode' => '300',
					'forwardUrl' => U('Home/Order/drawer',array('sn'=>$map['sn'],'plan_id'=>$info['plan_id'],'type'=>'1')),
				);
			}
			echo json_encode($return);
		}else{
			$ginfo = I('get.');//获取订单号
			if(empty($ginfo)){
				$this->erun('参数错误!');
			}// 检测订单是否过期
			if(check_sn($ginfo['sn'])){
				$this->assign('data',$ginfo);//传递参数
				$this->display();
			}else{
				$this->erun("订单已过期，无法出票!");
			}
		}	
				
	}
	
	/**
	 * 打印纸质门票
	 */
	function printTicket(){
		$ginfo = I('get.');
		//订单状态校验
		$order_type = order_type($ginfo['sn']);
		$user = $ginfo['user'] ? $ginfo['user'] : 0;
		//判断订单状态是否可执行此项操作
		if(in_array($order_type['status'], array('0','2','3','7','8','11'))){
			$return = array(
				'status' => '2',
				'message' => '订单状态不允许此项操作!'
			);
			die(json_encode($return));
		}
		//判断是否是二次打印
		if($order_type['status'] == '9' && empty($user)){
			$return = array(
				'status' => '2',
				'message' => '订单已打印!',
				'info'	=>  $ginfo,
			);
			die(json_encode($return));
		}
		$plan = F('Plan_'.$ginfo['plan_id']);
		if(empty($plan)){
			$return = array(
				'status' => '0',
				'message' => '订单读取失败!',
				'info'	=>  0,
			);
			die(json_encode($return));
		}
		//更新门票打印状态
		$model = new Model();
		$model->startTrans();
		switch ($plan['product_type']) {
			case '1':
				$table = $plan['seat_table'];
				break;
			case '2':
				$table = 'scenic';
				break;
			case '3':
				$table = 'drifting';
				break;
		}
		//读取门票列表
		$list = M(ucwords($table))->where(array('order_sn'=>$ginfo['sn']))->select();
		if($ginfo['type'] == '1'){
			//一人一票
			//读取门票列表
			foreach ($list as $k=>$v){
				$info[] = re_print($plan['id'],$plan['encry'],$v,$plan['product_id']);
			}
		}else{
			//一单一票
			//读取订单信息  日期时间  人数  单价 10元/人
			$map['order_sn'] = $ginfo['sn'];
			$oinfo = D('Item/Order')->where($map)->relation(true)->find();
			//$code = \Libs\Service\Encry::encryption($plan_id,$data['order_sn'],$encry,$data['area'],$data['seat'],$print,$data['id']);
        	//$sn = $code."^#";
			//$info[0] = ;
			//打票员名称
	        if($this->procof['print_user'] == '1'){
	            $info_user = \Manage\Service\User::getInstance()->username; 
	        }
	        //入场时间
	        if($this->procof['print_field'] == '1'){
	            $end = date('H:i',$plan['starttime']);
	            $start = date('H:i',strtotime("$end -30 minute"));
	            $info_field = $start .'-'. $end;
	        }
			//判断是否是单一票型 
			foreach ($list as $k=>$v){
				$num[$v['price_id']]['number'] += 1;
				$sale = unserialize($v['sale']);//dump($sale);
				$sn = \Libs\Service\Encry::encryption($plan['id'],$ginfo['sn'],$plan['encry'],$v['area'],$v['seat'],'1',$v['id'])."^2017^#";
				$info[$v['price_id']] = array(
					'discount'		=>	$sale['discount'],
					'field'			=>	$info_field,
					'games'			=>	$sale['games'],
					'plantime'		=>	planShow($ginfo['plan_id'],1,2),
					'price'			=>	$sale['price'],
					'product_name' 	=>	$sale['product_name'],
					'remark'		=>	$sale['remark'],
					'remark_type'	=>	$sale['remark_type'],
					'sn'			=>	$sn,
					'sns'			=>	$ginfo['sn'],
					'user'			=>	$info_user,
					'number'		=>	$num[$v['price_id']]['number'],
				);
			}
		}
		//更新门票打印状态
		$up_print = $model->table(C('DB_PREFIX'). $table)->where(array('order_sn'=>$ginfo['sn']))->setInc('print',1);	
		//判断订单类型
		$order_type = order_type($ginfo['sn']);
		//判断订单状态
		if($order_type['status'] == '9'){
			//二次打印处理
			$up_order = true;
			$type = '2';
		}else{
			//更新订单状态
			$up_order = $model->table(C('DB_PREFIX'). order)->where(array('order_sn'=>$ginfo['sn']))->setField('status',9);
			//渠道订单发送取票短信
			$type = '1';
		}
		if($up_print && $up_order){
			//记录打印日志
			print_log($ginfo['sn'],$user,$type,$order_type['channel_id'],'',count($list),3);
			$model->commit();//提交事务
			$return = array(
				'status' => '1',
				'message' => '订单读取成功!',
				'info'	=> $info ? $info : 0,
			);
		}else{
			$model->rollback();//事务回滚
			$return = array(
				'status' => '0',
				'message' => '订单读取失败!',
				'info'	=>  0,
			);
		}
		die(json_encode($return));
	}
	/**
	 * 二次打印密码确认
	 */
	function to_print(){
		if(IS_POST){
			$pinfo = I('post.');
		}else{
			$ginfo = I('get.');
			if(empty($ginfo)){
				$this->erun('参数错误!');
			}
			$this->assign('sn',$ginfo['sn'])
				->display();
		}
	}
	//订单取票
	function up_tickets(){
		//$pinfo = I('post.');
		$pinfo = I('get.');//dump($pinfo);
		if(empty($pinfo['sn']) && empty($pinfo['phone'])){
			$data = "404";
		}else{
			$data = D('Order')->where(array('order_sn'=>$pinfo['sn']))->relation(true)->find();
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
		}
		$this->assign('data',$data)->assign('area',$area)->assign('pinfo',$pinfo)->display();
	}
}