<?php
// +----------------------------------------------------------------------
// | LubTMP  自动同意退票申请
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\Refund;
class LubTMPAutoRefund {
	/*计划任务错误代码
	*130001 订单查询失败
	*/
    //任务主体
    public function run($cronId) {
    	//读取退票申请
    	$map = [
    		'status'	=>	1,
    		'createtime'=>	['EGT',strtotime('20180401')]
    	];
    	$list = D('ticket_refund')->where($map)->field('id,order_sn as sn,crm_id')->select();
    	$crm = F('Crm');
    	if(empty($crm)){
    		D('Crm/Crm')->crm_cache();
    		$crm = F('Crm');
    	}
    	//dump($list); TODO 单体客户配置缓存无效
    	foreach ($list as $k => $v) {
    		//判断商户是否允许系统自动退票
            /*
    		$thisCrm = $crm[$v['crm_id']];dump($thisCrm);
            dump(D('Crm')->where(['id'=>$v['crm_id']])->find());
    		if($thisCrm['param']['refund']){
    			//开始退票
    			$status = Refund::refund($v,1,'','',1,3);
    			dump($status);
    		}*/
            $status = Refund::refund($v,1,'','',1,3);
    	}
    }


    function autorefund(){

    }
}