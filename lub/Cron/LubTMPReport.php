<?php

// +----------------------------------------------------------------------
// | LubTMP  报表计划生成
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\Report;
class LubTMPReport {
	/*计划任务错误代码
	*120001 订单拆解失败，原因：此天已拆解，抹去旧数据失败
	*
	*
	*/
    //任务主体
    public function run($cronId) {
    	//默认计算当天的订单
        $datetime= date('Ymd',strtotime("-1 day"));
        //$datetime= date('Ymd');
        Report::report($datetime);  
    }

}
