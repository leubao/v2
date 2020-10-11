<?php
/**
 * 上传数据至山上饶市局
 * @Author: IT Work
 * @Date:   2019-11-27 17:13:54
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-10-01 23:45:42
 */
namespace CronScript;
class LubTMPJxCity {
    //任务主体
    public function run($cronId) {
    	$jxcity = new \Libs\Service\Jxcity;
		$jxcity->upRealData(time(), 0);
		$jxcity->upExitData(time(), 0);


		//自动出园
        //读取未入园的门票
        // $plan = get_today_plan();
        // $ktime = mt_rand(30,70);
        // $map = [
        //     'product_id'    =>  44,
        //     'status'        =>  1,
        //     'plan_id'		=>	['in', array_column($plan, 'id')],
        //     'create_time'	=>	strtotime("-$ktime minute")
        // ];
        // $updata = [
        // 	'status'	=>	9,
        // 	'uptime'	=>	time()
        // ];
        // $list = D('Order')->where($map)->save($updata);


		return;
    }
}