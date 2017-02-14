<?php
// +----------------------------------------------------------------------
// | LubTMP 销售看板  图表  销售日历
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19 
// +----------------------------------------------------------------------
namespace Report\Controller;
use Common\Controller\ManageBase;
use Libs\Service\Operate;
use Libs\Service\Report;
use Item\Service\Partner;
class SalesController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
	}
	/*
	* 销售日历
	*/
	function calendar(){
		/*SELECT 
			SUM(IF(`type`=1,1,0)) AS `充值`,
			SUM(IF(`type`=2,1,0)) AS `消费`,
			SUM(IF(`type`=3,1,0)) AS `返佣`,
			SUM(IF(`type`=4,1,0)) AS `退款`,
			
			FROM `lub_crm_recharge` GROUP BY `order_sn`
			HAVING SUM(IF(`type`=4 OR `type`=2,1,0))<2 AND SUM(IF(`type`=4 OR `type`=2,1,0))>0 AND SUM(IF(`type`=2,1,0)) = 0
			WHERE NOT IN ('1,2');*/
		$this->display();
	}
	/*
	*/
	function sales_chart(){
		$this->display();
	}
	/*单日销售详情*/
	function day_info(){
		$this->display();
	}
	/*查询未补贴的订单*/
	function query_fill(){

	}
	/*处理这些未补贴的订单*/
	function deal_fill(){
		$sn = "";
		//读取订单
		$info = D('Item/Order')->where(array('order_sn' => $sn))->relation(true)->find();

		if(!empty($info)){
			//组合返佣订单
			$teamData = array(
				'order_sn' 		=> $info['order_sn'],
				'plan_id' 		=> $info['plan_id'],
				'product_type'	=> $info['product_type'],//产品类型
				'product_id' 	=> $info['product_id'],
				'user_id' 		=> $info['user_id'],
				'money'			=> $rebate,
				'guide_id'		=> $oInfo['crm'][0]['guide'],
				'qd_id'			=> $oInfo['crm'][0]['qditem'],
				'status'		=> '1',
				'number' 		=> $counts,
				'type'			=> $info['type'] == '2' ? $sub_type : '2',//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
				'createtime'	=> $createtime,
				'uptime'		=> $createtime,
			);
			//写入返利订单
			$in_team = D('TeamOrder')->add($teamData);
		}
	}
}
