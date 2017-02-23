<?php
// +----------------------------------------------------------------------
// | LubTMP 订单处理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Libs\Service\Operate;
use Common\Controller\ManageBase;
use Libs\Service\Order;
use Common\Model\Model;
class OrderController extends ManageBase{	
	function _initialize(){
	 	parent::_initialize();
	}
/*==================================华丽分割线  1 添加窗口订单====================================*/
	/*选座提交*/
	function seatPost(){
		if(IS_POST){
			$info = $_POST['info'];
			$plan = I('get.plan',0,intval);
			$type = I('get.type',1,intval);
			//更新座位状态信息
			$sn = Order::rowSeat($info);
			if($sn != false){
				$return = array(
					'statusCode' => '200',
					'refresh'	 => 'work_seat',
					'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn,'plan_id'=>$plan)),
				);
				$message = "下单成功!单号".$sn;
				D('Item/Operationlog')->record($message, 200);//记录售票员日报表
			}else{
				$return = array(
					'statusCode' => '300',
					'forwardUrl' => '',
				);
				$message = "下单失败!";
				D('Item/Operationlog')->record($message, 300);//记录售票员日报表
			}
			//记录订单信息
			die(json_encode($return));
		}
	}
	
	/**
	 * 出票方式  订单出票
	 */
	function drawer(){
		if(IS_POST){
			$pinfo = I('post.');
			$this->assign('data',$pinfo);//传递参数
			$this->display();
		}else{
			$ginfo = I('get.');//获取订单号
			if(empty($ginfo)){
				$this->erun('参数错误!');
			}
			// 检测订单是否过期
			if(check_sn($ginfo['sn'])){
				$order_type = order_type($ginfo['sn']);
				//判断订单状态是否可执行此项操作
				if(in_array($order_type['status'], array('0','2','3','7','8','11'))){
					$this->erun("订单状态不允许此项操作!");
				}else{
					//传递参数
					$this->assign('data',$ginfo);
					if($order_type['status'] == '9'){
						if(empty($ginfo['user'])){
							$user = M('Pwd')->where(array('status'=>1))->select();
							$this->assign('user',$user)->display('to_print');
						}else{
							$this->display();
							//session('author',null);
						}
					}else{
						$this->display();
					}
				}
			}else{
				$this->erun("订单已过期，无法出票!");
			}
		}	
	}
	//门票打印
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
				$sn = \Libs\Service\Encry::encryption($plan['id'],$ginfo['sn'],$v['encry'],$v['area'],$v['seat'],'1',$v['id'])."^2016^#";
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
		//$oinfo['info'] = unserialize($oinfo['info']);
		//dump($info);

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
			if($order_type['type'] == '2' || $order_type['type'] == '4'){
				$this->to_sms($order_type['user_id'],$ginfo['sn'],$order_type['plan_id']);
			}
			$type = '1';
		}
		if($up_print && $up_order){
			//记录打印日志
			print_log($ginfo['sn'],$user,$type,$order_type['channel_id'],'',count($list),1);
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

		/*
		//判断产品类型
		if($plan['product_type'] == '1'){
		}elseif($plan['product_type'] == '2'){
			$data = Operate::do_read('ScenicTicket',1,array('order_sn'=>$ginfo['sn'],'status'=>'2'));
			foreach ($data as $k=>$v){
				$info[$k] = array_merge(array('sn' => $v['order_sn'],'print'=>$v['print']+1,),unserialize($v['sale']));
			}
			//更新门票打印状态
			$up_print = M('ScenicTicket')->where(array('order_sn'=>$ginfo['sn']))->setInc('print',1);
			if($up_print){
				$return = array(
					'status' => '1',
					'message' => '订单读取成功!',
					'info'	=> $info ? $info : 0,
				);
				echo json_encode($return);
			}	
		}else{
			if($up_print){
				$return = array(
					'status' => '1',
					'message' => '订单读取成功!',
					'info'	=> $info ? $info : 0,
				);
			}else{
				$return = array(
					'status' => '0',
					'message' => '订单打印失败!',
				);
			}
			die(json_encode($return));
		}*/
	}
	/*
	 * 打印纸质门票
	function printTicket(){
		$ginfo = I('get.');
		$plan = F('Plan_'.$ginfo['plan_id']);
		if(empty($plan)){
			$return = array(
				'status' => '0',
				'message' => '订单读取失败1!',
				'info'	=>  0,
			);
			echo json_encode($return);
			return false;
		}
		//判断产品类型
		if($plan['product_type'] == '1'){
			$list = M(ucwords($plan['seat_table']))->where(array('order_sn'=>$ginfo['sn']))->select();
			foreach ($list as $k=>$v){
				$info[] = $this->re_print($plan['id'],$plan['encry'],$v);
			}
			//更新门票打印状态
			$model = new Model();
			$model->startTrans();
			$up_print = $model->table(C('DB_PREFIX'). $plan['seat_table'])->where(array('order_sn'=>$ginfo['sn']))->setInc('print',1);
			
			
			//判断订单类型
			$order_type = $this->order_type($ginfo['sn']);

			//判断订单状态
			if($order_type['status'] == '9'){
				//二次打印处理
				$up_order = true;
				$top_up = true;
				$recharge = true;
				$up = true;
			}else{
				if($order_type['type'] == '2' || $order_type['type'] == '4'){
					$user_id = \Libs\Util\Encrypt::authcode($_SESSION['lub_imuid'], 'DECODE');
					//@大红袍  门票打印完成即返利
					$Tinfo = Operate::do_read('TeamOrder',0,array('order_sn'=>$ginfo['sn']));
					$result = M("Crm")->where(array('id'=>$Tinfo['qd_id']))->find();
					$cid = money_map($result);
					//先充值  后标记.
					$top_up = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$cid))->setInc('cash',$Tinfo['money']);
					//充值成功后，添加一条充值记录
					$data = array(
							'type'=> 3,
							'cash'=> $Tinfo['money'],
							'user_id'  => $user_id,
							'crm_id'   => $Tinfo['qd_id'],//售出信息 票型  单价
							'createtime' =>time(),
							'order_sn'	=> $Tinfo['order_sn']
					);			
					$recharge = $model->table(C('DB_PREFIX')."crm_recharge")->add($data);
					//更新返利状态
					$up = $model->table(C('DB_PREFIX')."team_order")->where(array('id'=>$Tinfo['id']))->save(array('status'=>'4','userid'=>$user_id));
				}else{
					$top_up = true;
					$recharge = true;
					$up = true;
				}
				//更新订单状态
				$up_order = $model->table(C('DB_PREFIX'). order)->where(array('order_sn'=>$ginfo['sn']))->setField('status',9);
			}
			
			if($up_print && $up_order && $top_up && $recharge && $up){
				$model->commit();//提交事务
				$return = array(
					'status' => '1',
					'message' => '订单读取成功!',
					'info'	=> $info ? $info : 0,
				);
				echo json_encode($return);
			}else{
				$model->rollback();//事务回滚
				$return = array(
					'status' => '0',
					'message' => '订单读取失败!',
					'info'	=>  0,
				);
				echo json_encode($return);
			}	
		}else{
			$data = Operate::do_read('ScenicTicket',1,array('order_sn'=>$ginfo['sn'],'status'=>'2'));
			foreach ($data as $k=>$v){
				$info[$k] = array_merge(array('sn' => $v['order_sn'],'print'=>$v['print']+1,),unserialize($v['sale']));
			}//更新门票打印状态
			$up_print = M('ScenicTicket')->where(array('order_sn'=>$ginfo['sn']))->setInc('print',1);
			if($up_print){
				$return = array(
					'status' => '1',
					'message' => '订单读取成功!',
					'info'	=> $info ? $info : 0,
				);
				echo json_encode($return);
			}	
		}
	}
	*/
	/*发送取票短信
	*@param $user_id 下单人id 
	*@param $order_sn 订单号
	*@param $plan_id 销售计划id
	*/
	function to_sms($user_id,$order_sn,$plan_id){
		//根据售票员id获取售票员信息 当前登录用户 渠道订单打印完成后是否给下单人发送短信
		if($this->procof['print_sms'] == '1'){
			$name = \Item\Service\Partner::getInstance()->nickname;
			$user = M('User')->where(array('id'=>$user_id))->field('phone')->find();
			$info = array('phone'=>$user['phone'],'title'=>planShows($plan_id),'sn'=>$order_sn,'user'=>$name);
			\Libs\Service\Sms::order_msg($info,3);
		}
		return true;
	}

	
	/**
	 * 二次打印密码确认
	 */
	function to_print(){
		if(IS_POST){
			//密码验证
			$pinfo = I('post.');
			$user = M('Pwd')->where(array('id'=>$pinfo['user']))->find();
			if($user['password'] == md5($pinfo['password'])){
				session('author',$user['id']);
				$return = array(
					'statusCode' => '200',
					'rel'	 => 'page1',
					'title'	=> '门票打印',
					'popup'	=>	'dialog',
					'forward' => U('Item/Order/drawer',array('sn'=>$pinfo['sn'],'plan_id'=>$pinfo['plan_id'],'user'=>$user['id'])),
				);
				die(json_encode($return));
			}else{
				$this->erun("授权失败!");
			}
		}else{
			$ginfo = I('get.');
			$author = session('author');
			if(empty($author)){
				$user = M('Pwd')->where(array('status'=>1))->select();
				$this->assign('user',$user);
			}
			$this->assign('data',$ginfo)->display();
		}	
	}
	
	/*发送电子门票*/
	function electronicTicket(){
		if(IS_POST){
		
		}else{
			$sn = I('get.sn');
			$info = Operate::do_read('Order',0,array('order_sn'=>$sn),'','',true);
			$this->display();
		}
	}
	/**
	 * 一单一票
	 */
	function single_ticket(){

	}
/*=============================华丽分割线  2 添加快捷订单====================================*/
	/*快捷售票*/
	function quickPost(){
		$pinfo = $_POST['info'];
		$plan = I('get.plan',0,intval);
		$type = I('get.type',1,intval);
		$uInfo = \Manage\Service\User::getInstance()->getInfo();//读取当前登录用户信息
		$type = '6'.$type;
		$run = Order::quick($pinfo,$type,$uInfo);
		if($run != false){
			//支付方式影响返回结果
			if(in_array($run['is_pay'],array('4','5'))){
				$forwardUrl = U('Item/Order/public_payment',array('sn'=>$run['sn'],'plan'=>$plan,'is_pay'=>$run['is_pay'],'money'=>$run['money']));
				$title = "网银支付";
				$width = '600';
				$height = '400';
				$pageId = 'payment';
			}else{
				$forwardUrl = U('Item/Order/drawer',array('sn'=>$run['sn'],'plan_id'=>$plan));
				$title = "门票打印";
				$width = '213';
				$height = '208';
				$pageId = 'print';
			}
			$return = array(
				'statusCode' => '200',
				'title'		 =>	$title,
				'width'		 =>	$width,
				'height'	 =>	$height,
				'pageid' 	 => $pageId,
				'refresh'	 => 'work_quick',
				'forwardUrl' => $forwardUrl,
			);
			$message = "下单成功!单号".$run;
			D('Item/Operationlog')->record($message, 200);//记录售票员日报表
		}else{
			$return = array(
				'statusCode' => '300',
				'forwardUrl' => '',
			);
			$message = "下单失败!";
			D('Item/Operationlog')->record($message, 300);//记录售票员日报表
		}			
		//记录订单信息
		die(json_encode($return));
	}
	//处理刷卡支付
	function public_payment(){
		if(IS_POST){
			$pinfo = $_POST['info'];
			$info = json_decode($pinfo,true);
			//pos 收费 现金支付
			$oinfo = D('Item/Order')->where(array('order_sn'=>$info['sn'],'status'=>array('in','6,11')))->relation(true)->find();
            if(empty($info) || empty($oinfo)){die(json_encode(array('statusCode' => '300','msg' => $oinfo)));}
			if($info['pay_type'] == '1' || $info['pay_type'] == '6'){
				$run = Order::sweep_pay_seat($info,$oinfo);
				if($run != false){
					//支付方式影响返回结果
					$return = array(
						'statusCode' => '200',
						'title'		 =>	"门票打印",
						'width'		 =>	'213',
						'height'	 =>	'208',
						'forwardUrl' => U('Item/Order/drawer',array('sn'=>$run['sn'],'plan_id'=>$plan)),
					);
					$message = "支付成功!单号".$run;
					D('Item/Operationlog')->record($message, 200);//记录售票员日报表
				}else{
					$return = array(
						'statusCode' => '300'
					);
					$message = "支付失败!";
					D('Item/Operationlog')->record($message, 300);//记录售票员日报表
				}
			}
			$product = product_name($oinfo['product_id'],1);
			//构造支付订单数据
			$payData = [
			    "order_no"	=> $info['sn'],
			    "amount"	=> $oinfo['money'],// 单位为元 ,最小为0.01
			    "client_ip"	=> get_client_ip(),
			    "subject"	=> $product."门票",
			    "body"		=> $product."门票",//planShow($oinfo['plan_id'],1,1).$product."门票",
			    "show_url"  => 'http://www.leubao.com/',// 支付宝手机网站支付接口 该参数必须上传 。其他接口忽略
			    "extra_param"	=> '',
			];
			if($info['pay_type'] == '4'){
				//支付宝支付
				$this->alipay_code($payData);
				$return = array(
					'statusCode' => '200'
				);
			}
			if($info['pay_type'] == '5'){
				//微信支付
				$result = $this->weixin_code($oinfo['product_id'],$info['paykey'],$payData);
				if(!empty($result['err_code'])){
					$return = array(
						'statusCode' => '400',
						'message'=>'['.$result['err_code'].']'.$result['err_code_des']
					);
				}else{
					$return = array(
						'statusCode' => '200'
					);
				}
				
			}
			die(json_encode($return));
		}else{
			$ginfo = I('get.');
			$this->assign('ginfo',$ginfo)->display('payment');
		}
	}
	//支付宝扫码支付 当面付
	function alipay_code($odata)
	{
		
	}
	//微信扫码支付
	function weixin_code($product_id,$paykey,$payData)
	{
		$pay = & load_wechat('Pay',$product_id);
		$money = $payData['amount']*100;
		//创建JSAPI签名参数包，这里返回的是数组
		$result = $pay->createMicroPay($paykey,$payData['order_no'],$money,'',$payData['body']);
		dump($result);
		return $result;

	}
	/*轮询支付日志查询支付结果*/
	function public_payment_results(){
		$ginfo = I('get.');
		$status = S('pay'.$ginfo['sn']);
		if($status == '200'){
			$oinfo = D('Item/Order')->where(array('order_sn'=>$ginfo['sn'],'status'=>array('in','6,11')))->relation(true)->find();
            if(empty($oinfo)){die(json_encode(array('statusCode' => '300','msg' => $oinfo)));}
            $info = array(
            	'seat_type' => $ginfo['seat'],
            	'pay_type'  => $ginfo['pay'],
            );
			$run = Order::sweep_pay_seat($info,$oinfo);
			if($run !== false){
				$return = array(
					'statusCode' => '200',
					'title'		 =>	"门票打印",
					'width'		 =>	'213',
					'height'	 =>	'208',
					'forwardUrl' => U('Item/Order/drawer',array('sn'=>$run['sn'],'plan_id'=>$plan)),
				);
			}else{
				//抛出售票失败，同时执行退款程序
				if($ginfo['pay'] == '4'){
					//支付宝
				}
				if($ginfo['pay'] == '5'){
					//微信
					\Libs\Service\Refund::weixin_refund($ginfo['sn'],$oinfo['product_id']);
					$return = array(
						'statusCode' => '400',
						'message' => '订单创建遇到严重错误,无法继续执行,已经执行退款程序,请提醒客户查收',
					);
				}
			}
			S('pay'.$ginfo['sn'],null);
		}else{
			$return = array(
				'statusCode' => '300'
			);
		}
		die(json_encode($return));
	}
		
	/*======================================================================华丽分割线 团队售票订单====================================================*/
/*	function teamPost(){
		$info = $_POST['info'];
		//$sn = Order::team($info,1);
		$uInfo = \Item\Service\Partner::getInstance()->getInfo();//读取当前登录用户信息
		$sn = Order::quick($info,12,$uInfo);
		$plan = session('plan');
		if($sn != false){
				$return = array(
					'statusCode' => '200',
					'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn,'plan_id'=>$plan['id'])),
				);
				$message = "下单成功!单号".$sn;
				D('Item/Operationlog')->record($message, 200);//记录售票员日报表
			}else{
				$return = array(
					'statusCode' => '300',
					'forwardUrl' => '',
				);
				$message = "下单失败!";
				D('Item/Operationlog')->record($message, 300);//记录售票员日报表
			}
			//记录订单信息	
			echo json_encode($return);
			return true;
	}*/
/*======================================================================分割线3 景区门票订单=============================================================*/
	function scenicPost(){
		$info = $_POST['info'];
		$sn = Order::scenic($info);
		$plan = session('plan');
		if($sn != false){
				$return = array(
					'statusCode' => '200',
					'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn,'plan_id'=>$plan['id'])),
				);
				$message = "下单成功!单号".$sn;
				D('Item/Operationlog')->record($message, 200);//记录售票员日报表
			}else{
				$return = array(
					'statusCode' => '300',
					'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn)),
				);
				$message = "下单失败!";
				D('Item/Operationlog')->record($message, 300);//记录售票员日报表
			}
			//记录订单信息
			echo json_encode($return);
	}
/*======================================================================分割线4 @大红袍 =============================================================*/
	/**
	 * 预订单处理 type 1    同意排座 2拒绝 退款
	 */
	function pay_no_seat(){
		$ginfo = I('get.');
		$oinfo = Operate::do_read('Order',0,array('order_sn'=>$ginfo['id']),'','',true);
		if(empty($ginfo) || empty($oinfo)){$this->erun("参数错误!");}
		if($oinfo['type'] == '6'){
			$this->erun("政企订单不支持此项操作!");
		}else{
			if($ginfo['type'] == '1'){
				//同意 排座
				$status = Order::add_seat($oinfo);			
			}else{
				//不同意退款
				$status = \Libs\Service\Refund::arefund($oinfo);			
			}
		}
		//返回结果
		if($status != false){
			$this->srun("操作成功",array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$this->erun("操作失败!");
		}	
	}
	
	/**
	 * 政企订单处理   排座但不付费
	 */
	function gov(){
		if(IS_POST){
			if(I('get.type') == '1'){
				//根据订单号
				$sn = I('post.sn');
				$map = array(
					'product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
					'order_sn' => array('like','%'.$sn.'%'),
				);
			}else{
				//取票人手机
				$map = array(
					'product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
					'phone' =>I('post.phone'),
				);
			}
			$lists = Operate::do_read('Order',1,$map,'createtime desc','',true);
			foreach ($lists as $k=>$v){
				$list[$k]=$v;
				$list[$k]['info']= unserialize($v['info']);
			}
		}else{
			//默认加载当天未出票订单
			//获取销售计划
			$plan_time = strtotime(date('Y-m-d',time()));
			$plan = Operate::do_read('Plan',1,array('plantime'=>$plan_time),'',array('id'));
			$map = array(
				'product_id'	=>	\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
				'plan_id'		=>	array('in',implode(',',array_column($plan['id'], 'id'))),
			);
			$lists = Operate::do_read('Order',1,$map,'createtime desc','',true);
			foreach ($lists as $k=>$v){
				$list[$k]=$v;
				$list[$k]['info']= unserialize($v['info']);
			}
		}//dump($list);
		$this->assign('data',$list)
			->display();
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
				'product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
				//'status'=>6,//只查询未出票的订单
				'order_sn' => $ginfo['id'],
			);
			if($ginfo['plan_id']){
				$data = array(
					'area' => $ginfo['aid'],
					'plan_id' => $ginfo['plan_id'],
					'sn'	=>	$ginfo['id'],
					'num'	=>	$ginfo['num'],
					'statusCode' => '300',
					'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn)),
				);
				echo json_encode($return);
			}else{
				$list = Operate::do_read('Order',0,$map,'','',true);
				$info = unserialize($list['info']);
				foreach ($info['data']['area'] as $key => $value) {
					$area[]=array(
						'area' => $key,
						'num'  => $value['num'],
						'priceid' => $value['seat'][0]['priceid'],
						'price' => $value['seat'][0]['price'],
					);
				}
				//只支持单个区域
				if(count($info['data']['area']) == 1){
					$this->assign('data',$list)
						->assign('area',$area)
						->assign('num',$num)
						->assign('plan',$list['plan_id'])
						->display();
				}else{
					$this->erun("非常抱歉该功能目前只支持单个区域!");
				}
				/*
				foreach ($info['data']['area'] as $key => $value) {
					$area[]=array(
						'area' => $key,
						'num'  => $value['num'],
						'priceid' => $value['seat'][0]['priceid'],
						'price' => $value['seat'][0]['price'],
						);
				}dump($area);
				$this->assign('data',$list)
					->assign('area',$area)
					->assign('num',$num)
					->assign('plan',$list['plan_id'])
					->display();*/
			}
		}
	}
	/**
	 * 加载根据区域加载座位
	 */
	function public_seats(){
		$ginfo = I('get.');
		if(empty($ginfo)){$this->erun('参数错误!');}
		$map = array(
			'product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
			'order_sn' => $ginfo['sn'],
		);
		$list = Operate::do_read('Order',0,$map,'','',true);
		if($list['status'] == '1' || $list['status'] == '9' ){
			$this->erun("该订单已完成排座，或已打印,请从订单管理中查询此订单!");
		}else{
			$info = unserialize($list['info']);
			foreach ($info['data']['area'] as $key => $value) {
				if($ginfo['num'] == $value['num']){
					$ginfo['priceid'] = $value['seat'][0]['priceid'];
					$ginfo['price'] = $value['seat'][0]['price'];
				}
				
			}
			//加载座椅
			$info = Operate::do_read('Area',0,array('id'=>$ginfo['area'],'status'=>1),'','id,name,face,is_mono,seats,num,template_id');
			$info['seats'] = unserialize($info['seats']);
			$this->assign('data',$info)
				->assign('ginfo',$ginfo)
				->assign('area',$area)
				->assign('plan',F('Plan_'.$ginfo['plan']))
				->display('seats');
		}
	}
	/**
	 * 根据区域加载座位信息   区域页面打开时  先加载座椅模板   然后加载售出情况   页面打开时  每个10分无刷新更新页面
	 * 散客售票 type 1 团队售票 2
	 */
	function seatsee(){
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
			$tictype = pullprice($plan['id'],$type,$area,1);
			$this->assign('price',$tictype);
			$this->assign('data',$info)
				->assign('area',$area)
				->assign('plan',$plan)
				->assign('type',$type)
				->display();
		}
	}
}