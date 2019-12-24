<?php

/**
 * 上传数据至省局 单天上传
 * @Author: IT Work
 * @Date:   2019-11-27 17:13:54
 * @Last Modified by:   IT Work
 * @Last Modified time: 2019-12-19 14:16:25
 */
namespace CronScript;
class LubTMPRealUpdata {
    //任务主体
    public function run($cronId) {
    	$provincial = new \Libs\Service\Provincial;
		$provincial->upRealData(time(), 0);
		$provincial->upExitData(time(), 0);
		return;
    }
}