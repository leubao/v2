<?php
// +----------------------------------------------------------------------
// | LubTMP  自动检查渠道商KPI考核
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\Kpi;
class LubTMPKpi {
	/*计划任务错误代码
	*130001 订单查询失败
	*/
    //任务主体
    public function run($cronId) {
    	//获取所有一级代理商的当前余额
    	$where = ['status'=>1,'level'=>16];
    	$list = D('Crm/Crm')->where($where)->field('id,cash,product_id')->select();
    	foreach ($list as $k => $v) {
    		Kpi::if_money_low($v['cash'],$v['product_id'],$v['id']);
    	}
    }
}