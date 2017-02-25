<?php
// +----------------------------------------------------------------------
// | LubTMP  周期返利计划
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\Rebate;
use Libs\Service\Report;
class LubTMPRebate {
	/*计划任务错误代码
	*130001 订单查询失败
	*/
    //任务主体
    public function run($cronId) {
    	//默认计算当天的订单
        $plantime = strtotime(date('Ymd',strtotime("-1 day")));
        //查询当天的计划
        $plan = M('Plan')->where(array('plantime'=>$plantime))->field('id')->select();
        $plan_id = array('in',implode(',',array_column($plan,'id')));
        $map = array('plan_id' => $plan_id,
            'status'=>array('neq','4'),
        );
        $list = M('TeamOrder')->where($map)->select();
        //按订单返佣
        foreach ($list as $key => $value) {
           $info[$key] = Rebate::rebate($value,1);
           $info[$key] = array(
                'type'=>1,
                'performtime'=>time(),
                'cron_id'=>0,
            );
        }
        
        /*
        $log_1 = M('Cronlog')->addAll($info);
        //返佣结束后生成渠道商授信余额快报
        Report::daily($plantime,'41');
        $log_2 = M('Cronlog')->add(array(
            'order_sn'=>'0',
            'user_id'=>'1',
            'status'=>'1',
            'msg'=>date('Ymd',$plantime)."渠道商授信日报!",
            'type'=>'7',
            'performtime'=>time(),
            'cron_id'=>0));
       // return true;*/
    }
}
