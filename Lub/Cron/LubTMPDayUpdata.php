<?php

/**
 * 上传数据至省局 单天上传
 * @Author: IT Work
 * @Date:   2019-11-27 17:13:54
 * @Last Modified by:   IT Work
 * @Last Modified time: 2019-12-30 16:56:46
 */
namespace CronScript;
class LubTMPDayUpdata {
    //任务主体
    public function run($cronId) {
    	$datetime = date('Y-m-d',strtotime("-1 day"));
  //   	$provincial = new \Libs\Service\Provincial;
		// $provincial->upTodayData($datetime, 0);

		$jxcity = new \Libs\Service\Jxcity;
		$jxcity->upTodayData($datetime, 0);

		return;
    }
    
}