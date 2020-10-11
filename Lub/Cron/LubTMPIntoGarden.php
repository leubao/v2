<?php
/**
 * 自动完结订单
 * @Author: IT Work
 * @Date:   2019-11-27 17:13:54
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-10-01 23:33:28
 */
namespace CronScript;
class LubTMPIntoGarden {
    //任务主体
    public function run($cronId) {
		//自动出园
        //读取未入园的门票
        $plan = get_today_plan();
        if(!empty($plan)){
			$ktime = mt_rand(20,50);
	        $map = [
	        	'product_id'	=>	44,
	            'plan_id'		=>	['in', array_column($plan, 'id')],
	            'create_time'	=>	strtotime("-$ktime minute"),
	            'status'		=>	1
	        ];
	        $updata = [
	        	'status'	=>	9,
	        	'uptime'	=>	time()
	        ];
	        $list = D('Order')->where($map)->field('order_sn')->select();
	        D('Order')->where($map)->save($updata);
	        $sns = array_column($list, 'order_sn');
	        D('Scenic')->where(['order_sn'=>['in', $sns]])->setField(['status' => 99, 'checktime' => time()]);
	        var_dump($list);
        }
        

		return;
    }
}