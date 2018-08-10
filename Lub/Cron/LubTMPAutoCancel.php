<?php
// +----------------------------------------------------------------------
// | LubTMP  自动同意退票申请
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
class LubTMPAutoCancel {
	/*计划任务错误代码
	*130001 订单查询失败
	*/
    //任务主体
    public function run($cronId) {
    	//读取今日未核销的订单
        $datetime = strtotime(date('Ymd'));
        $where = ['plantime'=>$datetime];
        $plan = M('Plan')->where($where)->field('id')->select();
        if(!empty($plan)){
            $plan_id = array('in',implode(',',array_column($plan,'id')));
            $map = [
                'plan_id' => ['in',$plan_id],
                'status'  => 1
            ];
            $orderList = D('Order')->where($map)->field('order_sn as sn')->select();
            if(!empty($orderList)){
                foreach ($orderList as $key => $v) {
                    $count = D('Scenic')->where(['order_sn'=>$v['sn'],'status'=>2])->count();
                    if($count == 0){
                        $updata = [
                            'status' => 9,
                            'uptime' => time()
                        ];
                        D('Order')->where(['order_sn'=>$v['sn']])->setField($updata);
                    }
                }
                
            }

        }

    }


    function autorefund(){
        //读取当天所有销售计划，查询出未取票的订单，开始退票
        $datetime = strtotime(date('Ymd'));
        $where = ['plantime'=>$datetime];
        $plan = M('Plan')->where($where)->field('id')->select();
        if(!empty($plan)){
            $plan_id = array('in',implode(',',array_column($plan,'id')));
            $map = [
                'plan_id' => ['in',$plan_id],
                'status'  => 1
            ];
            $orderList = D('Order')->where($map)->field('order_sn as sn')->select();
            if(!empty($orderList)){
                foreach ($orderList as $key => $v) {
                    //整单退票
                    $status = Refund::refund($v,1,'','',1,3);
                }
                
            }

        }
        
    }
}