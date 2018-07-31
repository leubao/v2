<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务 电子门票
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Libs\Service\Checkin;
class Ticket extends \Libs\System\Service {

	public function createTicket($sn)
	{
		$order = D('Order')->where(['order_sn'=>$sn])->field('plan_id')->find();
		//订单状态校验
		$order_type = order_type($sn);
		
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
				'message' => '订单已核销',
			);
			die(json_encode($return));
		}
		$plan = F('Plan_'.$order['plan_id']);
		if(empty($plan)){
			$plan = D('Plan')->where(['id'=>$order['plan_id']])->field('id,product_type,seat_table,encry,starttime,endtime,product_id')->find();
		}
		if(empty($plan)){
			$return = array(
				'status' => '0',
				'message' => '订单读取失败!',
				'info'	=>  0,
			);
			die(json_encode($return));
		}
		/** 订单状态校验 
		$checkOrder = new CheckStatus();
		if(!$checkOrder->OrderCheckStatus($sn,2103)){
			$this->erun($checkOrder->error,array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		}*/
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
		$list = M(ucwords($table))->where(array('order_sn'=>$sn))->select();
		if($ginfo['type'] == '1'){
			//一人一票
			//读取门票列表
			foreach ($list as $k=>$v){
				$info[] = re_print($plan['id'],$plan['encry'],$v,$plan['product_id']);
			}
		}else{
			//一单一票
			//读取订单信息  日期时间  人数  单价 10元/人
			$map['order_sn'] = $sn;
			$oinfo = D('Item/Order')->where($map)->relation(true)->find();
			//打票员名称
	        if($this->procof['print_user'] == '1'){
	            $info_user = \Manage\Service\User::getInstance()->username; 
	        }
	        //入场时间
	        if($this->procof['print_field'] == '1'){
				$statrtime = $plan['starttime'];
	            $end = date('H:i',strtotime("$statrtime -5 minute"));
	            $start = date('H:i',strtotime("$end -30 minute"));
	            $info_field = $start .'-'. $end;
	        }
	        //dump($oinfo);
	        //打印客源地 TODO
	        if($this->procof['print_to_guest'] == '1'){
	        	
	        	$oParam = unserialize($oinfo['info']);
	        	$guest_area = '';
	        }
			//判断是否是单一票型 
			foreach ($list as $k=>$v){
				$num[$v['price_id']]['number'] += 1;
				$sale = unserialize($v['sale']);
				$sn = \Libs\Service\Encry::encryption($plan['id'],$sn,$plan['encry'],$v['area'],$v['seat'],'1',$v['id'])."&".$oinfo['id']."^#";
				
				$info[$v['price_id']] = array(
					'discount'		=>	$sale['discount'],
					'field'			=>	$info_field,
					'games'			=>	$sale['games'],
					'plantime'		=>	date('Y-m-d',$plan['plantime']),//planShow($order['plan_id'],1,2),
					'starttime'     =>  date('H:i',$plan['starttime']),
					'endtime'		=>	date('H:i',$plan['endtime']),
					'price'			=>	$sale['price'],
					'priceName'		=>	$sale['priceName'],
					'product_name' 	=>	$sale['product_name'],
					'remark'		=>	$sale['remark'],
					'remark_type'	=>	$sale['remark_type'],
					'sn'			=>	$sn,
					'sns'			=>	$sn,
					'user'			=>	$info_user,
					'number'		=>	$num[$v['price_id']]['number'],
					'guest_area'	=>	$guest_area
				);
			}
		}
		//更新门票打印状态
		$up_print = $model->table(C('DB_PREFIX'). $table)->where(array('order_sn'=>$sn))->setInc('print',1);	
		//判断订单类型
		$order_type = order_type($sn);
		//判断订单状态
		if($order_type['status'] == '9'){
			//二次打印处理
			$up_order = true;
			$type = '2';
		}else{
			//更新订单状态
			$up_order = $model->table(C('DB_PREFIX'). order)->where(array('order_sn'=>$sn))->setField('status',9);
			$type = '1';
		}
		if($up_print && $up_order){
			//记录打印日志
			print_log($sn,$user,$type,$order_type['channel_id'],'',count($list),1);
			$model->commit();//提交事务
			//$checkOrder->delMarking($pinfo['sn']);
			$return = array(
				'status' => '1',
				'message' => '订单读取成功!',
				'info'	=> $info ? $info : 0,
			);
		}else{
			$model->rollback();//事务回滚
			//$checkOrder->delMarking($pinfo['sn']);
			$return = array(
				'status' => '0',
				'message' => '订单读取失败',
				'info'	=>  0,
			);
		}
		die(json_encode($return));
	}
}
	
?>