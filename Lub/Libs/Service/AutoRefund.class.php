<?php
// +----------------------------------------------------------------------
// | LubTMP 自动退票
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Libs\Service\Refund;
class AutoRefund extends \Libs\System\Service {
	//自动去退票，自动取消已经提交申请的退票申请
	public function auto_refund()
	{
		//读取当日销售计划
		$datetime = date('Y-m-d');
		$plan = get_date_plan($datetime);

		//读取申请退票列表
		$where = [
			'status'	=>	1,
			'plan_id'	=>	['in', arr2string($plan,'id')]
		];
		$list = D('TicketRefund')->where($where)->field('id,order_sn')->select();
		foreach ($list as $k => $v) {
			//开始退票
			$status = Refund::refund(['sn'=>$v['order_sn']],1,'','',1,6);
			if($status){
				D('TicketRefund')->where(['id'=>$v['id']])->setField('status',3);
			}else{
				//取消错误
			}	
		}
	}
}