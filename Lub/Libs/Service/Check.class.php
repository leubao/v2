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
	//补贴报表
	function check_rebate(){
		$list = M('Order')->where(array('type'=>array('in','2,4,8,9'),'status'=>array('in','1,9,7,8')))->limit('1,100')->field('order_sn')->order('id DESC')->select();
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
				}
			}
		}
		load_redis('set','check_rebate',date('Y/m/d H:i:s')."||".$count);
	}
	//退票订单 返款情况、补贴情况
	function check_refund(){

	}
	//过期补贴未补的补贴报表
	function check_rebate(){

	}
}
	