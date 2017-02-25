<?php
// +----------------------------------------------------------------------
// | LubTMP  自动结算返利程序
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\Rebate;
class LubTMPAutoRebate {
	/*计划任务错误代码
	*130001 订单查询失败
	*/
    //任务主体
    public function run($cronId) {
    	Rebate::ajax_rebate_order();
    }
}