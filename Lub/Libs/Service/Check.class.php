<?php
// +----------------------------------------------------------------------
// | LubTMP  系统校验类
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Common\Model\Model;
class Check{
	/**
	 * 补贴报表
	 * @param  自定义条件 $map  array  日期范围必须
	 * @param  访问类型 $type 1常规轮询2自定义轮询
	 * @return [type]       [description]
	 */
	function check_rebate($map = '',$type = '1'){
		if($type == '1'){
			$list = M('Order')->where(array('type'=>array('in','2,4,8,9'),'status'=>array('in','1,9,7,8')))->limit('1,50')->field('order_sn')->order('id DESC')->select();
		}else{
			$list = M('Order')->where($map)->field('order_sn')->order('id DESC')->select();
		}
		if(!empty($list)){
			$count = 0;
			//判断是否在队列中
			$queue = load_redis('lrange','PreOrder',0,-1);
			//匹配返佣订单
			foreach ($list as $k => $v) {
				if(!in_array($v['order_sn'], $queue)){
					$status = M('TeamOrder')->where(array('order_sn' => $v['order_sn']))->find();
					if(!$status){
					  $count++;
					  load_redis('lpush','PreOrder',$v['order_sn']);
					}
					//未返利的程序
					if($status['status'] == '1'){
						load_redis('lpush','PreOrder_bak',$v['order_sn']);
					}
				}
			}
		}
		load_redis('set','check_rebate',date('Y/m/d H:i:s')."||".$count);
	}
	//退票订单 返款情况、补贴情况
	function check_refund(){

	}
	//过期补贴未补的补贴报表
	//网银支付  排座情况
	function check_pay_order_seat(){
		//读取订单列表，查询本地排座情况，若排座则不存入队列，若没有排座，查询微信支付支付情况，支付完成的调用排座程序
		$order_list = load_redis('lrange','WechatPayOrder',0,-1);
		$ln = load_redis('lsize','WechatPayOrder');
		if($ln > 0){
			$model = D('Item/Order');
			for ($i=0; $i < $ln; $i++) { 
				$sn = load_redis('rPop','WechatPayOrder');
				$info = $model->where(array('order_sn'=>$sn))->relation(true)->find();
				if(!in_array($info['status'],array('1,9'))){
					//查询微信支付情况
					$pay = & load_wechat('Pay',$info['product_id']);
					$query = $pay->queryOrder($info['order_sn']);
					if ($query['result_code'] == 'SUCCESS' && $query['return_code'] == 'SUCCESS' && $query['trade_state'] == 'SUCCESS') {
		                $param = array(
		                  'seat_type' => '1',
		                  'pay_type' =>   '5'
		                );
		                $status = \Libs\Service\Order::mobile_seat($param,$info);
		                if(!$status){
		                	//排座失败
		                	load_redis('lpush','WechatPayOrder',$sn);
		                }else{
		                	//排座成功
		                }
					}else{
						error_insert($pay->errMsg.[$pay->errCode]);
					}
				}
			}
		}
	}
}
	