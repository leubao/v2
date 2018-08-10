<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务 电子门票
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Common\Model\Model;
class Ticket extends \Libs\System\Service {

	public function createTicket($sn)
	{
		$map['order_sn'] = $sn;
		$order = D('Item/Order')->where($map)->relation(true)->find();
		//订单状态校验
		$order_type = order_type($sn);
		
		//判断订单状态是否可执行此项操作
		if(in_array($order_type['status'], array('0','2','3','7','8','11'))){
			$return = array(
				'status' => '0',
				'message' => '订单状态不允许此项操作!'
			);
			return $return;
		}
		//判断是否是二次打印
		if($order_type['status'] == '9' && empty($user)){
			$return = array(
				'status' => '0',
				'message' => '订单已核销完成',
			);
			return $return;
		}
		$plan = F('Plan_'.$order['plan_id']);
		if(empty($plan)){
			$plan = D('Plan')->where(['id'=>$order['plan_id']])->field('id,product_type,seat_table,encry,starttime,endtime,product_id')->find();
		}
		if(empty($plan)){
			$return = array(
				'status' => '0',
				'message' => '订单读取失败或已过期!',
				'info'	=>  0,
			);
			return $return;
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
		$list = M(ucwords($table))->where(array('order_sn'=>$sn,'status'=>2))->select();
		$count = count($list);//dump($list);
		//一单一票
		//读取订单信息  日期时间  人数  单价 10元/人  
		if($count > 0){
			
			foreach ($list as $k=>$v){
				$sale = unserialize($v['sale']);
				
				if($v['print'] == 0){
					$print = 1;
				}else{
					$print = $print + 1;
				}
				$sns = \Libs\Service\Encry::encryption($plan['id'],$sn,$plan['encry'],$v['area'],$v['seat'],$print,$v['id'])."&".$order['id']."^#";

				$info[$v['price_id']] = array(
					'discount'		=>	$sale['discount'],
					'field'			=>	$info_field,
					'games'			=>	$sale['games'],
					'plantime'		=>	date('Y-m-d',$plan['plantime']),
					'starttime'     =>  date('H:i',$plan['starttime']),
					'endtime'		=>	date('H:i',$plan['endtime']),
					'price'			=>	$sale['price'],
					'priceName'		=>	$sale['priceName'],
					'product_name' 	=>	$sale['product_name'],
					'remark'		=>	$sale['remark'],
					'remark_type'	=>	$sale['remark_type'],
					'sn'			=>	$sn,
					'sns'			=>	$sns,
					'user'			=>	$info_user,
					'number'		=>	$count,
					'guest_area'	=>	$guest_area
				);
				$price_id = $v['price_id'];
			}	
			$sns = $info[$price_id]['sns'];

			//更新门票打印状态
			$up_print = $model->table(C('DB_PREFIX'). $table)->where($map)->setInc('print',1);
		}else{
			$up_print = true;
			$up_order = $model->table(C('DB_PREFIX'). order)->where(array('order_sn'=>$sn))->setField('status',9);
		}
		
		if($count > 0 && $up_print){
			//记录打印日志
			//print_log($sn,$user,$type,$order_type['channel_id'],'',count($list),1);
			$model->commit();//提交事务
			//$checkOrder->delMarking($pinfo['sn']);
			$return = array(
				'status' => '1',
				'message' => '订单读取成功!',
				'sns'	=> $sns,
				'info'	=> $info ? $info : 0,
			);
		}else{
			$model->rollback();//事务回滚
			//$checkOrder->delMarking($pinfo['sn']);
			$return = array(
				'status' => '0',
				'message' => '订单已核销完成',
				'info'	=>  0,
			);
		}
		return $return;
	}
}
	
?>