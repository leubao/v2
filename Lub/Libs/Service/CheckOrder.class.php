<?php
// +----------------------------------------------------------------------
// | LubTMP 订单扣费校验程序
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Service;
use Api\Service\Checkin;
class CheckOrder extends \Libs\System\Service {

	public function count($map)
	{
		$list = D('Order')->where($map)->field('id,order_sn,status,channel_id,money')->select();
		foreach ($list as $k => $v) {
			switch ($v['status']) {
				case '1':
					
					break;
				
				default:
					# code...
					break;
			}
		}
	}
	/**
	 * 校验扣费主体是否一致
	 * 校验返还主体是否一致
	 * @param  int $channel_id 订单中的渠道商
	 * @param  int $crm_id     扣费中的渠道商
	 * @return [type]             [description]
	 */
	public function deduction_main($channel_id,$crm_id)
	{
		//获取扣费主体
		$cid = ;
		if($cid <> $crm_id){
			return false;
		}
		return true;
	}
	/**
	 * 校验扣费金额是否一致
	 * @param  int $omoney 订单金额
	 * @param  int $money  扣费金额
	 * @return [type]         [description]
	 */
	public function deduction_money($omoney,$money)
	{
		if($omoney <> $money){
			//不一致时判断订单类型是否有退单 如果有  将订单金额+退单金额  再判断是否一致
			return false;
		}
		return true;
	}
	/**
	 * 已作废的订单是否已经返款
	 * 返款金额与扣费金额是否一致 
	 * 退票日志是否已经记录
	 * @param  string $order_sn 订单号
	 * @return [type]           [description]
	 */
	public function refund_order($order_sn = '')
	{
		//查询是否已经返款
		$db = M('CrmRecharge');
		$refund_cahe = $db->where(array('order_sn'=>$v['order_sn'],'type'=>4))->select();
		if(empty($refund_cahe)){
			//未返款
		}else{
			//已经返款的进一步调查返款金额是否与扣款金额一致
			foreach ($refund_cahe as $k => $v) {
				$money += $v['cash']; 
			}
			$deduction = $db->where(array('order_sn'=>$v['order_sn'],'type'=>2))->find();
			if(!$this->deduction_money($money,$deduction['cash'])){
				//记录
			}
		}
		//查询退票日志是否已记录
		$refund_log = M('TicketRefund')->where(['order_sn'=>$v['order_sn']])->find();
		if(!$refund_log){
			//记录
		}
	}
	/**
	 * 渠道待支付的订单是否扣款
	 * @param  string $order_sn [description]
	 * @return [type]           [description]
	 */
	public function stay_order($order_sn = '')
	{
		$db = M('CrmRecharge');
		$rech = $db->where(array('order_sn'=>$v['order_sn']))->select();
		if(!empty($rech)){
			//记录
		}
		return true;
	}
	/**
	 * 完结或预定成功的订单是否存在未扣款或者多扣款的情况
	 * @param  string $order_sn 订单号
	 * @param string $money 订单金额
	 * @return [type]           [description]
	 */
	public function ok_order($order_sn = '',$money = '')
	{
		//查询是否已经扣款
		$db = M('CrmRecharge');
		$rech = $db->where(array('order_sn'=>$v['order_sn'],'type' => 2))->select();
		//判断是否多扣
		//判断扣款金额是否正取
		//判断扣款主体是否正确
	}
}