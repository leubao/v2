<?php
// +----------------------------------------------------------------------
// | LubTMP  自动执行清理
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\Sweeper;
class LubTMPAutoSweeper {
    //任务主体
    public function run($cronId) {
    	$sweeper = new Sweeper();
        //清清楚一个月前待支付的订单
        $sweeper->order_nopay_del();
        //清楚过期座位表
        //$sweeper->table_del();
        //清除过期打印日志
        //$sweeper->print_log_del();

    }
}