<?php
/**
 * 上传数据至山上饶市局
 * @Author: IT Work
 * @Date:   2019-11-27 17:13:54
 * @Last Modified by:   IT Work
 * @Last Modified time: 2019-12-20 16:30:03
 */
namespace CronScript;
class LubTMPJxCity {
    //任务主体
    public function run($cronId) {
    	$jxcity = new \Libs\Service\Jxcity;
		$jxcity->upRealData(time(), 0);
		$jxcity->upExitData(time(), 0);
		return;
    }
}