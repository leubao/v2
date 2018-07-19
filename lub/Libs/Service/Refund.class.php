<?php
// +----------------------------------------------------------------------
// | LubTMP 退票类
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19 
// +----------------------------------------------------------------------
namespace Libs\Service;
use Common\Model\Model;
use Payment\Common\PayException;
use Payment\Client\Refund as wxRefund;
class Refund extends \Libs\System\Service {
	
	/*退票处理
	 * 
	 * @param $ginfo array 参数包
	 * @param $type int 退票类型    1整个订单退 2 单个座位退3核减
	 * @param $area_id int 区域ID
	 * @param $seat_id string 座位号 TODO多个座位一起退 
	 * @param $poundage int 手续费
	 * @param $scena int 创建场景 1窗口退票 2 整场退票 3自动退单 4批量退指定座位 5 api申请退款 6 系统自动完成订单取消 7多产品退单 
	 * */
	function refund($ginfo, $type = 1, $area_id = null, $seat_id = null, $poundage = null, $scena = '1'){
		$sn = $ginfo['sn'];
		$info = D('Item/Order')->where(['order_sn'=>$sn])->relation(true)->find();
		$plan_id = (int)$ginfo['plan'];
		if(empty($plan_id)){
			$plan_id = $info['plan_id'];
		}
		
		if(!empty($info['activity']) && (int)$type === 1){
			$atype = D('Activity')->where(['id'=>$info['activity']])->getField('type');
			if((int)$atype === 5){
				$type = 7;
			}
		}
		if(empty($sn) || empty($info) || empty($plan_id)){
			error_insert('4004101');return false;
		}
		//获取所属计划
		$plan = F('Plan_'.$plan_id);
		if(empty($plan)){
			$plan = M('Plan')->where(array('id'=>$plan_id))->find();
		}
		$proconf = cache('ProConfig');
		$proconf = $proconf[$plan['product_id']][1];
		//判断演出是否已过期 TODO 场次结束时间不允许退票  动态配置
		if($proconf['plan_refund'] == '1'){
			$datetime = date('Ymd',time());
			$plantime = date('Ymd',$plan['plantime']);
			$starttime = date('H:i',$plan['starttime']);
			$endtime = strtotime("$starttime +40 minute");
			if($datetime > $plantime || empty($plan)){error_insert('400411');return false;}
			if($plantime == $datetime){
				if($createtime >= $endtime){error_insert('400411');return false;}
			}
		}
		//判断订单所属渠道商是个人还是商户 三级分销
		if(in_array($info['type'],array('8','9'))){
			//微信全员销售
			$channel_id = $info['guide_id'];
			$sub_type = '1';
		}else{
			$channel_id = $info['channel_id'];
			$sub_type = $info['sub_type'];
		}
		//判别订单类型
		$if_order_type = Refund::if_order_type($info['type'],$info['status'],$info['product_id'],$channel_id,$sub_type);//dump($type);
		//判断场景
		switch ($scena) {
			case '1':
				//窗口退票 辨别退票类型
				switch ($type) {
					case '1':
						//整单退
						return Refund::single_back($if_order_type,$info,$plan,$poundage,'','','');
						break;
					case '2':
						//单个座位退
						return Refund::single_back($if_order_type,$info,$plan,$poundage,$area_id, $seat_id,'');
						break;
					case '3':
						//订单核减
						return Refund::single_back($if_order_type,$info,$plan,$poundage,$area_id, $seat_id,'',1);
						break;
					case '5':
						return Refund::child_back($if_order_type,$info,$plan,$poundage,$ginfo['fid'],$ginfo['priceid'], '');
						break;
					case '7':
						//多景区整单退票
						return Refund::pack_back($if_order_type,$info,$plan,$poundage,'','','');
						break;
				}
				break;
			case '2':
				//整场退票  辨别退票类型 $type ＝ 1
				return Refund::single_back($if_order_type,$info,$plan,$poundage,'', '',1,2);
				break;
			case '3':
				//自动退单 辨别退票类型 $type ＝ 1
				return Refund::single_back($if_order_type,$info,$plan,$poundage,'', '',1);
				break;
			case '5':
				//api接口退票 
				switch ($type) {
					case '1':
						//整单退
						return Refund::single_back($if_order_type,$info,$plan,$poundage,'','','');
						break;
					case '2':
						//单个座位退
						return Refund::single_back($if_order_type,$info,$plan,$poundage,$area_id, $seat_id,'');
						break;
				}
				break;
		}
	}
	/*套票整单退票*/
	static function pack_back($type,$info,$plan,$poundage = '',$area_id = null, $seat_id = null, $user_id = null,$tract = '')
	{
		//分产品退
		$model = new Model();
		$model->startTrans();
		$createtime = time();
		$seas = unserialize($info['info']);//获取订单内的座椅
		$counts = count($seas['data']);//统计门票张数
		$cost = 0;//手续费
		$unit = 1;//退单类型 1 整单2个别
		$sn = $info['order_sn'];
		$child_moeny = 0;
		$subtotal = $info['money'];
		$sn = $info['order_sn'];
		if(empty($user_id)){
			$user_id = get_user_id();
		}
		if($counts > 1){
			if(!empty($area_id) && !empty($seat_id)){
				$unit = 2;
			}else{
				$unit = 1;
			}
		}else{
			$unit = 1;
		}
		/********处理手续费问题*****/
		if($poundage <> '1'){//有手续费
			$cost = Refund::cost($poundage,$info['money']);
			if($cost > 0){
				//若有手续费，则应在额外收益表中增加一条数据
				$income_info = array(
					"plan_id"	 => $info['plan_id'],
					"money" 	 => $cost,
					"type"  	 => 1,
					"channel_id" => $info['channel_id'],
					'userid' 	 => $info['user_id'],
					"createtime" => $createtime,
					"order_sn" 	 => $info["order_sn"],
					"user_id"    => get_user_id(),
					'product_id' => $info['product_id'],
				);
				$income = $model->table(C('DB_PREFIX')."other_income")->add($income_info);
				if($income == false){echo "15";
					$model->rollback();return false;
				}
			}else{
				$model->rollback();return false;
			}
		}
		//返回金额
		$money_back = $subtotal-$cost;
		//获取销售计划的数据
		foreach ($seas['data'] as $k => $v) {
			$ticket[$v['priceid']] = $v;
		}
		foreach ($ticket as $key => $value) {
			$plan = F('Plan_'.$value['plan_id']);
			if(empty($plan)){
				$plan = M('Plan')->where(array('id'=>$value['plan_id']))->find();
			}
			//有门票
			$data = array('status'=>'0','sale'=> '','idcard'=>'','order_sn'=> '0','price_id'=>'0');
			//整单退 判断是否存在已检票的座位
			if($tract == '2'){
				//整场退票时  已检票的门票也退
				$map = array('order_sn'=>$sn);
			}else{
				if(Refund::check_seat($plan['seat_table'],$sn) != false){
					$map = array('order_sn'=>$sn,'status'=>array('notin','99'));
				}else{
					error_insert('400015');
					$model->rollback();return false;
				}
			}
			$up = $model->table(C('DB_PREFIX').$plan['seat_table'])->where($map)->save($data);
			if($up == false){
				error_insert('400410');
				$model->rollback();return false;
			}
		}
		//获取订单支付方式
		if($info['pay'] == '2'){
			//区分是个人还是企业
			if(in_array($info['type'],['8','9'])){
				$channel = '4';
			}else{
				$channel = '1';
			}
			if($channel == '1'){
				//渠道商客户
				$db = M('Crm');
				//判断是否开启多级扣款
				$crm = F('Crm');
				$crm = $crm[$info['channel_id']];
				$itemConf = cache('ItemConfig');

				if($itemConf[$crm['itemid']]['1']['level_pay']){
					//开启多级扣款
					//获取扣款连条
            		$payLink = crm_level_link($info['channel_id']);
				}else{
					//获取扣费条件
					$payLink = money_map($info['channel_id'],$channel);
				}
			}//dump($payLink);
			if($channel == '4'){
				//个人客户
				$db = M('User');
				$payLink = $info['guide_id'];
			}
			if(is_array($payLink)){
				$backMap = [
					'id'	=>	['in',implode(',',$payLink)]
				];
			}else{
				$backMap = [
					'id'	=> $payLink
				];
			}
			$crmData = array('cash' => array('exp','cash+'.$money_back),'uptime' => time());

			if($channel == '1'){
				//渠道商客户
				$c_pay = $model->table(C('DB_PREFIX')."crm")->where($backMap)->setField($crmData);
			}
			if($channel == '4'){
				//个人客户
				$c_pay = $model->table(C('DB_PREFIX')."user")->where($backMap)->setField($crmData);
			}
			if(is_array($payLink)){
				//TODO 不同级别扣款金额不同
				foreach ($payLink as $p => $l) {
					$recharge[] = array(
						'cash'		=>	$money_back,
						'user_id'	=>	$info['user_id'],
						'guide_id'	=>	$l,//TODO  这个貌似没什么意义
						'addsid'	=>	$info['addsid'],
						'crm_id'	=>	$l,
						'createtime'=>	$createtime,
						'type'		=>	'4',
						'order_sn'	=>	$info['order_sn'],
						'balance'	=>  balance($l,$channel),
						'tyint'		=>	$channel,//客户类型1企业4个人
					);
				}
				$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->addAll($recharge);
			}else{
				$recharge = array(
					'cash'		=>	$money_back,
					'user_id'	=>	$info['user_id'],
					'guide_id'	=>	$payLink,//TODO  这个貌似没什么意义
					'addsid'	=>	$info['addsid'],
					'crm_id'	=>	$payLink,
					'createtime'=>	$createtime,
					'type'		=>	'4',
					'order_sn'	=>	$info['order_sn'],
					'balance'	=>  balance($payLink,$channel),
					'tyint'		=>	$channel,//客户类型1企业4个人
				);
				$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->add($recharge);
			}
			if($c_pay == false || $c_pay2 == false){
				error_insert('400008');
				$model->rollback();//事务回滚
				return false;
			}
			
			
			if($c_pay == false || $c_pay2 == false){echo "st2ing";
				error_insert('400008');
				$model->rollback();//事务回滚
				return false;
			}
		}
		if($info['status'] <> '7'){
			$refund_data = array(
				'createtime'	=>  $createtime,
				'order_sn'		=>	$sn,
				'applicant'		=>	$user_id,
				'crm_id'		=>	$info['channel_id'] ? $info['channel_id'] : '0',
				'plan_id'		=>	$info['plan_id'],
				'param'			=>	'',
				'money'			=>	$info['money'],
				'reason'		=>	$info['status'] == '1' ? "窗口退票" : "渠道取消订单",
				'status'		=>	'3',
				're_money'		=>	$subtotal,
				're_type'		=>	$info['type'] <> 1 ? 1:2,
				'launch'		=>	'1',
				'number'		=>	$unit == 1 ? $counts : 1,
				'updatetime'	=> 	$createtime,
				'poundage'		=> 	'0',
				'poundage_type'	=>	'1',
				'against_reason'=>	'',
				'order_status'	=>	$info['status'],
				'user_id'		=>	$user_id,
			);
			$refund = $model->table(C('DB_PREFIX').'ticket_refund')->add($refund_data);
		}else{
			//渠道取消订单
			$refund_data = array(
				'poundage'		=>	$poundage,
				're_money'		=>	$subtotal,
				'number'		=>	$counts,
				'poundage_type'	=>	'1',
				'updatetime'	=>	$createtime,
				'status'		=>	'3',
				'against_reason'=>  $ginfo['against_reason'],
				'user_id'		=>	$user_id,
			);
			$refund = $model->table(C('DB_PREFIX').'ticket_refund')->where(array('order_sn'=>$sn))->save($refund_data);
		}
		$ordeData =array('uptime'=>$createtime,'status'=>0,'number'=>0,'money'=>'0.00');
		$order = $model->table(C('DB_PREFIX'). 'order')->where(array('order_sn'=>$sn))->save($ordeData);
		$order_data = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn'=>$sn))->save(array('info' => serialize($newData)));
		//dump($up);dump($order);dump($refund);dump($order_data);
		if($order && $refund && $order_data){
			$model->commit();//提交事务
			return true;
		}else{
			$model->rollback();//事务回滚
			return false;
		}
	}
	/**
	 * 退子票型
	 * @return [type] [description]
	 */
	function child_back($type,$info,$plan,$poundage,$fid,$price_id = null, $user_id = null){
		$model = new Model();
		$model->startTrans();
		$createtime = time();
		$ticketType = F("TicketType".$info['product_id']);
		$unit = 1;//退单类型 1 整单2个别
		$seas = unserialize($info['info']);//获取订单内的座椅
		$counts = count($seas['data']);//统计门票张数
		$child_ticket_count = count($seas['child_ticket']);//统计子门票数
		$cost = 0;//手续费
		$sn = $info['order_sn'];
		
		//＝1时  减少金额，并删除该子票票型
		//>1时，减少金额
		//判断订单门票数量，>1 和＝1 的情况
		$child_ticket = $seas['child_ticket'];
		//获取金额
		$unset_ticket = $child_ticket[$price_id];
		if(in_array($type,array('81','83','91'))){
			$unset_moeny = $unset_ticket['discount'];
		}else{
			$unset_moeny = $unset_ticket['price'];
		}
		//重新设置订单金额
		$new_money = $info['money'] - $unset_moeny;
		//删除主票型中的子票集合
		foreach ($seas['data'] as $ke => $va) {
			if($child_ticket_count == '1'){
				if($fid == $va['ciphertext']){
					$new_data[] = array(
						'ciphertext' =>	$va['ciphertext'],
						'priceid'=>	$va['priceid'],
						'price'=>$va['price'],
						'discount'=>$va['discount'],/*结算价格*/
						'id'	=>	$va['id'],
						'plan_id' => $va['plan_id'],
						'child_ticket' => '',
					);
				}else{
					$new_data[] = $va;
				}
			}else{
				if($fid == $va['ciphertext']){
					$child_ticket_array = explode(',',$va['child_ticket']);
					foreach ($child_ticket_array as $ks => $vs) {
						if($price_id == $vs){
							$offsets = $ks;
						}
						//删除子票
						unset($child_ticket_array[$offsets]);
					}
					$new_data[] = array(
						'ciphertext' =>	$va['ciphertext'],
						'priceid'=>	$va['priceid'],
						'price'=>$va['price'],
						'discount'=>$va['discount'],/*结算价格*/
						'id'	=>	$va['id'],
						'plan_id' => $va['plan_id'],
						'child_ticket' => implode(',',$child_ticket_array),
					);
				}else{
					$new_data[] = $va;
				}
			}
		}
		/*
		//不为一时，该子票留存数为一
		foreach ($new_data as $ko => $vo) {
			$child_ticket_array = explode(',',$vo['child_ticket']);
			if(in_array($price_id,$child_ticket_array)){
				$child_ticket_num += '1'; 
			}
		}
		//当订单门票数量为1时或当前删除子票留存为0时，删除子票
		if($counts == '1' || $child_ticket_num == '0'){
			foreach ($child_ticket as $k => $v) {
				if($price_id == $v['priceid']){
					$offset = $k;
				}
			}
			//删除子票
			unset($child_ticket[$offset]);
		}*/
		$child_ticket = Refund::new_child_ticket($counts,$price_id,$new_data,$child_ticket);
		$new_info = array(
			'subtotal'		=>	$new_money,
			'checkin'		=>	$seas['checkin'],
			'data'			=>	$new_data,
			'crm'			=>	$seas['crm'],
			'pay'			=>	$seas['pay'],
			'param'			=>	$seas['param'],
			'child_ticket'	=>	$child_ticket,
		);
		$new_order = array(
			'money'	=>	$new_money,
			'uptime'=>  $createtime,
		);
		$order = $model->table(C('DB_PREFIX'). 'order')->where(array('order_sn'=>$sn))->save($new_order);
		$order_data = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn'=>$sn))->save(array('info' => serialize($new_info)));
		//退票日志
		$refund_data = array(
			'createtime'	=>  $createtime,
			'order_sn'		=>	$sn,
			'applicant'		=>	get_user_id(),
			'crm_id'		=>	$info['channel_id'] ? $info['channel_id'] : '0',
			'plan_id'		=>	$info['plan_id'],
			'param'			=>	'',
			'money'			=>	$info['money'],
			'reason'		=>	$info['status'] == '1' ? "窗口退票" : "渠道取消订单",
			'status'		=>	'3',
			're_money'		=>	$unset_moeny,
			're_type'		=>	$info['type'] <> 1 ? 1:2,
			'launch'		=>	'1',
			'number'		=>	$child_ticket_count,
			'updatetime'	=> 	$createtime,
			'poundage'		=> 	'0',
			'poundage_type'	=>	'1',
			'against_reason'=>	'',
			'order_status'	=>	$info['status'],
			'user_id'		=>	get_user_id(),
		);
		$refund = $model->table(C('DB_PREFIX').'ticket_refund')->add($refund_data);
		if($order && $refund && $order_data){
			$model->commit();//提交事务
			return true;
		}else{
			$model->rollback();//事务回滚
			return false;
		}
	}
	//是否删除子票
	function new_child_ticket($number,$price_id,$main_ticket,$child_ticket){
		$child_ticket_num = '0';
		$child_ticket_array = explode(',', $price_id);
		foreach ($child_ticket_array as $key => $value) {
			//不为一时，该子票留存数为一
			foreach ($main_ticket as $ko => $vo) {
				$child_ticket_array = explode(',',$vo['child_ticket']);
				if(in_array($value,$child_ticket_array)){
					$child_ticket_num += '1'; 
				}
			}
			//当订单门票数量为1时或当前删除子票留存为0时，删除子票
			if($number == '1' || $child_ticket_num == '0'){
				foreach ($child_ticket as $k => $v) {
					if($value == $v['priceid']){
						$offset = $k;
					}
				}
				//删除子票
				unset($child_ticket[$offset]);
			}
		}
		return $child_ticket;
	}
	/*
	* 开始退票
	* @param $type int 退票类型
 	* @param $info array 订单内容
 	* @param $plan array 销售计划
	* @param $poundage int 手续费标识
	* @param $area_id area
	* @param $seat_id string 指定座位退票
	* @param $user_id int 操作员   在系统执行退票时  为系统管理员
	* @param $tract int 1 核减订单  2 整场退票
	*/
	function single_back($type,$info,$plan,$poundage = '',$area_id = null, $seat_id = null, $user_id = null,$tract = ''){
		$model = new Model();
		$model->startTrans();
		$createtime = time();
		$ticketType = F("TicketType".$info['product_id']);
		$unit = 1;//退单类型 1 整单2个别
		$seas = unserialize($info['info']);//获取订单内的座椅
		$counts = count($seas['data']);//统计门票张数
		$cost = 0;//手续费
		$sn = $info['order_sn'];
		$child_moeny = 0;
		if(empty($user_id)){
			$user_id = get_user_id();
		}
		//81 团队无返利结算价格退款有座位  82 团队无返利票面价格退款无座位
		// 83删除返利 按票面价退款有座位 91 按结算价格退款 有座位 92 无座位置
		/*=============判定系统是整单退 还是个别退===============*/
		if($counts > 1){
			if(!empty($area_id) && !empty($seat_id)){
				$unit = 2;
			}else{
				$unit = 1;
			}
		}else{
			$unit = 1;
		}
		/*=============处理座位===============*/
		if(in_array($type,array('81','83','91'))){
			//有座位
			$data = array('status'=>'0','sale'=> '','idcard'=>'','order_sn'=> '0','price_id'=>'0');
			if($unit == '2'){
				//按座位退
				if($plan['product_type'] == '1'){
					$map = array('area'=>$area_id,'order_sn'=>$sn,'seat'=>$seat_id,'status'=>array('notin','99'));
				}else{
					$map = array('order_sn'=>$sn,'status'=>array('notin','99'),'id'=>$seat_id);
				}
			}else{
				//整单退 判断是否存在已检票的座位
				if($tract == '2'){
					//整场退票时  已检票的门票也退
					$map = array('order_sn'=>$sn);
				}else{
					if(Refund::check_seat($plan['seat_table'],$sn) != false){
						$map = array('order_sn'=>$sn,'status'=>array('notin','99'));
					}else{
						$model->rollback();
						$this->error = '400015:';
						return false;
					}
				}
			}
			$up = $model->table(C('DB_PREFIX').$plan['seat_table'])->where($map)->save($data);
			if($up == false){
				error_insert('400410');
				$model->rollback();
				$this->error = '';
				return false;
			}
		}else{
			//无座位
		}
		/*=============处理手续费问题===============*/
		if($poundage <> '1'){//有手续费
			$cost = Refund::cost($poundage,$info['money']);
			if($cost > 0){
				//若有手续费，则应在额外收益表中增加一条数据
				$income_info = array(
					"plan_id"	 => $info['plan_id'],
					"money" 	 => $cost,
					"type"  	 => 1,
					"channel_id" => $info['channel_id'],
					'userid' 	 => $info['user_id'],
					"createtime" => $createtime,
					"order_sn" 	 => $info["order_sn"],
					"user_id"    => get_user_id(),
					'product_id' => $info['product_id'],
				);
				$income = $model->table(C('DB_PREFIX')."other_income")->add($income_info);
				if($income == false){echo "15";
					$model->rollback();return false;
				}
			}else{echo "16";
				$model->rollback();return false;
			}
		}
		/*=============确定金额===============*/
		if(in_array($type,array('81','83','91')) && $unit == '2'){
			if($plan['product_type'] == '1'){
				//存在座位且按照座位退单
				foreach ($seas['data'] as $k=>$v){
					if($v['areaId'] == $area_id && $v['seatid'] == $seat_id){
						$offset = $k;
						if(in_array($type,array('81','91','92'))){
							//结算价
							$subtotal = $v['discount'];
						}else{
							//票面价
							if($ticketType[$v['priceid']]['discount'] == '0.00' || $ticketType[$v['priceid']]['discount'] == '0'){
								//结算价格为0元时，按结算价格计算否则按票面价格计算
								$subtotal = '0';
							}else{
								$subtotal = $v['price'];
							}
							$rebate	= $ticketType[$v['priceid']]['rebate'];
						}
						if(!empty($v['child_ticket'])){
							$child_ticket_array = $v['child_ticket'];
						}
						break;
					}
				}
				unset($seas['data'][$offset]);
			}else{
				//存在座位且按照座位退单
				foreach ($seas['data'] as $k=>$v){
					if($v['priceid'] == $area_id && $v['id'] == $seat_id){
						$offset = $k;
						if(in_array($type,array('81','91','92'))){
							//结算价
							$subtotal = $v['discount'];
						}else{
							//票面价
							if($ticketType[$v['priceid']]['discount'] == '0.00' || $ticketType[$v['priceid']]['discount'] == '0'){
								//结算价格为0元时，按结算价格计算否则按票面价格计算
								$subtotal = '0';
							}else{
								$subtotal = $v['price'];
							}
							$rebate	= $ticketType[$v['priceid']]['rebate'];
						}
						if(!empty($v['child_ticket'])){
							$child_ticket_array = $v['child_ticket'];
						}
						break;
					}
				}//dump($info['money']);
				unset($seas['data'][$offset]);
			}
			//存在子票
			if(!empty($seas['child_ticket'])){
				$child_moeny = Refund::check_child_ticket($child_ticket_array,$seas['child_ticket'],$type);
			}
		}else{
			//不存在座位   或者整单退
			$subtotal = $info['money'];
		}
		//返回金额
		$money_back = $subtotal-$cost+$child_moeny;

		//订单金额
		$money = $info['money'] - $subtotal - $child_moeny;
		if($money_back < 0 || $money < 0){
			$model->rollback();//事务回滚
			$this->error = "退还金额计算有误,请联系管理员";
			return false;
		}
		/*=============处理返利===============*/
		if(in_array($type,array('91','92','82','81'))){
			//无返利
			$in_team = true;
		}else{
			//有返利
			if($unit == '2'){
				//按座位退 更新返利
				$in_team = $model->table(C('DB_PREFIX'). 'team_order')->where(array('order_sn'=>$info['order_sn']))->setDec('money',$rebate);
			}else{
				//整单退 删除返利 判断是否已经补贴
				$in_team = $model->table(C('DB_PREFIX'). 'team_order')->where(array('order_sn'=>$info['order_sn'],'status'=>'1'))->delete();
			}
		}

		/*=============处理退款===============*/
		//获取订单支付方式
		if($info['pay'] == '2'){
			//区分是个人还是企业
			if(in_array($info['type'],['8','9'])){
				$channel = '4';
			}else{
				$channel = '1';
			}
			if($channel == '1'){
				//渠道商客户
				$db = M('Crm');

				//判断是否开启多级扣款
				$crm = F('Crm');
				$crm = $crm[$info['channel_id']];
				$itemConf = cache('ItemConfig');

				if($itemConf[$crm['itemid']]['1']['level_pay']){
					//开启多级扣款
					//获取扣款连条
            		$payLink = crm_level_link($info['channel_id']);
				}else{
					//获取扣费条件
					$payLink = money_map($info['channel_id'],$channel);
				}
			}//dump($payLink);
			if($channel == '4'){
				//个人客户
				$db = M('User');
				$payLink = $info['guide_id'];
			}
			if(is_array($payLink)){
				$backMap = [
					'id'	=>	['in',implode(',',$payLink)]
				];
			}else{
				$backMap = [
					'id'	=> $payLink
				];
			}
			$crmData = array('cash' => array('exp','cash+'.$money_back),'uptime' => time());

			if($channel == '1'){
				//渠道商客户
				$c_pay = $model->table(C('DB_PREFIX')."crm")->where($backMap)->setField($crmData);
			}
			if($channel == '4'){
				//个人客户
				$c_pay = $model->table(C('DB_PREFIX')."user")->where($backMap)->setField($crmData);
			}
			//TODO 不同级别扣款金额不同
			if(is_array($payLink)){
				foreach ($payLink as $p => $l) {
					$recharge[] = array(
						'cash'		=>	$money_back,
						'user_id'	=>	$info['user_id'],
						'guide_id'	=>	$l,//TODO  这个貌似没什么意义
						'addsid'	=>	$info['addsid'],
						'crm_id'	=>	$l,
						'createtime'=>	$createtime,
						'type'		=>	'4',
						'order_sn'	=>	$info['order_sn'],
						'balance'	=>  balance($l,$channel),
						'tyint'		=>	$channel,//客户类型1企业4个人
					);
				}
				$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->addAll($recharge);
			}else{
				$recharge = array(
					'cash'		=>	$money_back,
					'user_id'	=>	$info['user_id'],
					'guide_id'	=>	$payLink,//TODO  这个貌似没什么意义
					'addsid'	=>	$info['addsid'],
					'crm_id'	=>	$payLink,
					'createtime'=>	$createtime,
					'type'		=>	'4',
					'order_sn'	=>	$info['order_sn'],
					'balance'	=>  balance($payLink,$channel),
					'tyint'		=>	$channel,//客户类型1企业4个人
				);
				$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->add($recharge);
			}
			if($c_pay == false || $c_pay2 == false){
				error_insert('400008');
				$model->rollback();//事务回滚
				return false;
			}
		}
		/*微信支付4支付宝5微信支付*/
		if($info['pay'] == '5'){
			$refundPay = Refund::weixin_refund($sn,$info['product_id'],$money_back);
			if(!$refundPay){
				$model->rollback();//事务回滚
				return false;
			}
		}
		if($info['pay'] == '4'){
			$refundPay = Refund::alipay_refund($sn,$info['product_id'],$money_back);
		}
		/*=============处理日志===============*/
		if($info['status'] <> '7'){
			$refund_data = array(
				'createtime'	=>  $createtime,
				'order_sn'		=>	$sn,
				'applicant'		=>	$user_id,
				'crm_id'		=>	$info['channel_id'] ? $info['channel_id'] : '0',
				'plan_id'		=>	$info['plan_id'],
				'param'			=>	'',
				'money'			=>	$info['money'],
				'reason'		=>	$info['status'] == '1' ? "窗口退票" : "渠道取消订单",
				'status'		=>	'3',
				're_money'		=>	$subtotal,
				're_type'		=>	$info['type'] <> 1 ? 1:2,
				'launch'		=>	'1',
				'number'		=>	$unit == 1 ? $counts : 1,
				'updatetime'	=> 	$createtime,
				'poundage'		=> 	'0',
				'poundage_type'	=>	'1',
				'against_reason'=>	'',
				'order_status'	=>	$info['status'],
				'user_id'		=>	$user_id,
			);
			$refund = $model->table(C('DB_PREFIX').'ticket_refund')->add($refund_data);
		}else{
			//渠道取消订单
			$refund_data = array(
				'poundage'		=>	$poundage,
				're_money'		=>	$subtotal,
				'number'		=>	$counts,
				'poundage_type'	=>	'1',
				'updatetime'	=>	$createtime,
				'status'		=>	'3',
				'against_reason'=>  $ginfo['against_reason'],
				'user_id'		=>	$user_id,
			);
			$refund = $model->table(C('DB_PREFIX').'ticket_refund')->where(array('order_sn'=>$sn))->save($refund_data);
		}
		/*=============处理原始订单===============*/
		if($unit == '2'){
			//判断子票型的留存问题
			$new_child_ticket = Refund::new_child_ticket($n_counts,$child_ticket_array,$seas['data'],$seas['child_ticket']);
			//单个座位退单
			$newData = array(
				'subtotal'	=> 	$money,
				'number'	=>	$counts,
				'checkin'	=> 	$info['checkin'],
				'data'		=> 	$seas['data'],
				'crm'		=>	$seas['crm'],
				'pay'		=>	$seas['pay'],				
				'param'		=>	$seas['param'],
				'child_ticket' => $new_child_ticket,
			);
			$n_counts = count($seas['data']);
			$ordeData = array('money'=>$money,'number'=>$n_counts,'uptime'=>$createtime);
		}else{
			//整单退
			$ordeData =array('uptime'=>$createtime,'status'=>0,'number'=>0,'money'=>'0.00');
		}
		//是否是核减
		if($tract == '1'){
			$ordeData = array('money'=>$money,'number'=>$n_counts,'uptime'=>$createtime,'subtract'=>$info['subtract']+1,'subtract_num'=>$info['subtract_num']+1);
		}
		$order = $model->table(C('DB_PREFIX'). 'order')->where(array('order_sn'=>$sn))->save($ordeData);
		$order_data = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn'=>$sn))->save(array('info' => serialize($newData)));
		//dump($up);dump($order);dump($refund);dump($order_data);
		if($up && $order && $refund && $order_data){
			$model->commit();//提交事务
			return true;
		}else{
			$model->rollback();//事务回滚
			return false;
		}	
	}
	/*
	//多级扣款情况下 多级返款 且是渠道订单 且是渠道版订单
	if($itemConfg[$itemid]['1']['level'] && ){
		//判断当前用户
		//判断级别
		//确定扣款金额
	}else{

	}*/
	/*
	* 检测是否存在已检票的座位
	* @param $table string 座位表名称
	* @param $sn 订单号
	* return false true
	*/
	function check_seat($table,$sn){	
		$map = array('order_sn'=>$sn,'status'=>'99');	
		if(M(ucwords($table))->where($map)->find()){
			return false;
		}else{
			return true;
		}
	}
	/*
	*判别订单类型
	*@param $type int 订单类型 1散客订单2团队订单4渠道版定单6政府订单
	*@param $status int 状态1正常5已支付但未排座6政府订单7申请退票中9门票已打印11窗口订单创建成功但未排座
	*@param $product_id 产品id
	*@param $channel_id int 渠道商ID
	*@param $sub_type int 渠道商类型  1个人 其它为企业  根据补贴对象获取
	*/
	function if_order_type($type,$status,$product_id,$channel_id = null,$sub_type = 2){
		//dump($type);dump($status);dump($product_id);dump($channel_id);
		//窗口散客支付但未排座
		if(in_array($type,array('2','4','6','7','8'))){
			//团队
			$pay_type = Refund::pay_type($product_id,$channel_id,$sub_type);
			//dump($pay_type);
			if(in_array($pay_type,array('1','3'))){
				//存在返利
				switch ($status) {
					case '1':
						//支付且排座
						return '83';//删除返利 按票面价退款 
						break;
					case '5':
						return '82';//无返利 按票面价退款
						//支付但未排座
						break;
					case '6':
						return '82';//无返利 按票面价退款
						//支付但未排座
						break;
					case '7':
						return '83';//删除返利 按票面价退款  删除座位
						//支付但未排座
						break;
					case '8':
						return '82';//无返利 按票面价退款
						//支付但未排座
						break;
					case '9':
						return '83';//删除返利 按票面价退款
						//支付且排座
						break;
				}
			}else{
				//底价支付不存在返利
				switch ($status) {
					case '1':
						//支付且排座
						return '81';//无返利结算价格退款 删除已拍座位
						break;
					case '5':
						return '82';//无返利 无座位
						//支付但未排座
						break;
					case '6':
						return '82';//无返利 按结算价退款
						//支付但未排座
						break;
					case '7':
						return '81';//无返利 按结算价退款 删除座位
						//支付已排座
						break;
					case '8':
						return '82';//无返利 按票面价退款
						//支付但未排座
						break;
					case '9':
						return '81';//无返利有座位
						//支付且排座
						break;
				}
			}
		}else{
			//散客  按结算价退款
			switch ($status) {
				case '1':
					//支付且排座
					return '91';
					break;
				case '11':
					return '92';
					//支付但未排座
					break;
				case '9':
					return '91';
					//支付且排座
					break;
			}
		}
		//窗口散客支付且排座
		//窗口团队支付但为排座
		//窗口团队支付且排座
	}
	/*获取团队支付类型 底价支付  票面价格支付
	* @param $product_id int 产品id
	* @param $channel_id int 渠道商id 全员销售时为全员销售的人员id
	* @param $type 渠道商类型 1个人 其它为企业 默认为企业
	* return 1 票面价格结算  存在返利 2 底价结算不存在返利
	*/
	function pay_type($product_id,$channel_id,$type = 2){
		if($type == '1'){
			//个人
			$crmInfo = google_crm($product_id,'',$channel_id);
		}else{
			//企业
			$crmInfo = google_crm($product_id,$channel_id);
		}
		//判断是否是底价结算['group']['settlement']
		return $crmInfo['group']['settlement'];
	}
	/**
	 * 当子票型不为空的时候  轮询子票型号整体价格
	 * @param  array $child_ticket 子票型集合
	 * @param  array $type 结算方式
	 * @return [type]               [description]
	 */
	function check_child_ticket($price_id,$child_ticket,$type){
		$child_ticket_array = explode(',', $price_id);
		foreach ($child_ticket_array as $k => $v) {
			if(in_array($type,array('81','83','91'))){
				$unset_moeny += $child_ticket[$v]['discount'];
			}else{
				$unset_moeny += $child_ticket[$v]['price'];
			}
		}
		return $unset_moeny;
	}
	/*
	*计算手续费
	*@param $poundage int 比率
	*@param $money string 金额
	*/
	function cost($poundage,$money){
		switch ($poundage) {
			case '1':
				$cost = 0*$money; //退款应收手续费
				break;
			case '2':
				$cost = 0.1*$money;  
				break;
			case '3':
				$cost = 0.2*$money;  
				break;
			default:
				$cost = 0*$money;		
				break;
		}
		return $cost;
	}
	//渠道返利
	function rebate($info){
		$model = new \Think\Model();
		$model->startTrans();
		//查询渠道商信息
		if($top_up && $recharge && $up){
			$model->commit();//成功则提交
			$this->srun('返利成功!',$this->navTabId);
		}else{
			$model->rollback();//不成功，则回滚
			$this->erun("返利失败!");
		}
	} 
	//微信退款
	function weixin_refund($sn,$product_id,$money){
		

		$info = D('Item/Pay')->where(array('order_sn'=>$sn))->find();
		
		//$money = $info['money'];//临时
		$rsn = get_order_sn($product_id);
	
		try {
			$config = load_payment('wx_refund');
			$data = [
			    'out_trade_no' => $sn,
			    'total_fee'  => $info['money'],
			    'refund_fee' => $money,
			    'refund_no'  => $rsn,
			    'refund_account' => 'REFUND_UNSETTLED',// REFUND_RECHARGE:可用余额退款  REFUND_UNSETTLED:未结算资金退款（默认）
			    'sub_appid'     =>  $config['sub_appid'],
	            'sub_mch_id'    =>  $config['sub_mch_id']
			];//dump($config);
		    $ret = wxRefund::run('wx_refund', $config, $data);
		    if($ret['return_code'] === 'SUCCESS' && $ret['result_code'] === 'SUCCESS'){
		    	if($money == $info['money']){
		    		//全部退款
				    $param = unserialize($info['param']);
				    $param['refund'] = $result;
				    $uppaylog = array(
				    	'status'		=>	4,
				    	'out_refund_no'	=>	$result['refund_id'],
				    	'refund_sn'		=>	$result['out_refund_no'],
				    	'refund_moeny'	=>	$result['cash_refund_fee'],
				    	'param'			=>  serialize($param),
				    	'user_id'		=>	get_user_id(),
				    	'update_time'	=>	time()
				    );
		            $paylog = D('Item/Pay')->where(array('order_sn'=>$sn))->save($uppaylog);
		    	}else{
		    		//部分退款
		    		$pay_log = array(
				        'out_trade_no' =>   $ret['transaction_id'], 
				        'money'        =>   $money,
				        'order_sn'     =>   $sn,
				        'refund_sn'	   =>	$rsn,
				        'param'        =>   serialize($ret),
				        'status'       =>   4,
				        'type'         =>   2,
				        'pattern'      =>   2,
				        'scene'        =>   1,
				        'create_time'  =>   time(),
				        'update_time'  =>   time()
				    );
    				D('Manage/Pay')->add($pay_log);
		    	}
		    	
	            return true;
		    }else{
		    	$this->error = $ret['err_code_des'];
		    	return false;
		    }
		} catch (PayException $e) {
		    //echo $e->errorMessage();
		    $this->error = $e->errorMessage();
		    return false;
		    exit;
		}
	}
	//支付宝退款
	function alipay_refund($sn,$product_id,$money)
	{
		//读取支付日志
		$info = D('Item/Pay')->where(array('order_sn'=>$sn))->find();
		$pay = & load_wechat('Pay',$product_id);
		if($money > $info['money']){
			return false;
		}
		$money = $money*100;
		$rsn = get_order_sn($product_id);
		$result = $pay->refund($sn,$info['out_trade_no'],$rsn,$money,$money);
		// 处理创建结果
		if($result===FALSE){
		    //TODO  写入紧急处理错误
		    //写入待处理事件
		    load_redis('lpush','WeixinPayRefund',$sn);
		}else{
		    // 接口成功的处理
		    $param = unserialize($info['param']);
		    $param['refund'] = $result;
		    $uppaylog = array(
		    	'status'		=>	4,
		    	'out_refund_no'	=>	$result['refund_id'],
		    	'refund_sn'		=>	$result['out_refund_no'],
		    	'refund_moeny'	=>	$result['cash_refund_fee'],
		    	'param'			=>  serialize($param),
		    	'user_id'		=>	get_user_id(),
		    	'update_time'	=>	time()
		    );
            $paylog = D('Item/Pay')->where(array('order_sn'=>$sn))->save($uppaylog);
		}
		return $result;
	}
	/**
	 * 不同意退款
	 */
	function arefund($oinfo){
		$model = new \Think\Model();
		$model->startTrans();
		$createtime = time();
		//读取支付方式 非授信额不退款 TODO 微信支付宝支付
		if($oinfo['pay'] == 2){
			$cid = money_map($oinfo['channel_id']);
			//先消费后记录
			$crmData = array('cash' => array('exp','cash+'.$oinfo['money']),'uptime' => $createtime);
			$c_pay = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$cid))->setField($crmData);
			$data = array(
				'cash'		=>	$oinfo['money'],
				'user_id'	=>	get_user_id(),
				'crm_id'	=>	$cid,
				'createtime'=>	$createtime,
				'type'		=>	'5',
				'order_sn'	=>	$oinfo['order_sn'],
				'balance'	=>  balance($cid),
			);
			$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->add($data);
		}else{
			$c_pay = true;$c_pay2 = true;
		}
		//修改订单状态
		$up = $model->table(C('DB_PREFIX')."order")->where(array('order_sn'=>$oinfo['order_sn']))->setField('status','3');
		$up2 = $model->table(C('DB_PREFIX')."pre_order")->where(array('order_sn'=>$oinfo['order_sn']))->setField('status','4');
		if($c_pay && $c_pay2 && $up && $up2){
			$model->commit();//成功则提交
			return true;
		}else{
			$model->rollback();//不成功，则回滚
			return false;
		}
		
	}
}