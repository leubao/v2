<?php

// +----------------------------------------------------------------------
// | LubTMP  定时提醒
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\Report;
class LubTMPRemind {
    //任务主体
    public function run($cronId) {
    	//印象大红袍当日订单
    	//读取销售计划，读取订单中联系人手机号，去重,发送短信
    	$plan = get_date_plan(date('Y-m-d'));
    	$planIdx = array_column($plan, 'id');
        if(empty($planIdx)){
            return true;
        }
    	$list = D('Order')->where(['plan_id'=>['in', $planIdx],'status'=>['in',['1','9','6']]])->field('order_sn,phone')->select();
       
    	$list = unique_multidim_array($list, 'phone');
       
    	if(!empty($list)){
    		foreach ($list as $k => $v) {
    			\Libs\Service\Sms::remind($v['order_sn'],$v['phone']);
    		}
    	}
    }
}
