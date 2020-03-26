<?php
// +----------------------------------------------------------------------
// | LubTMP 向海洋馆下单
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19
// +----------------------------------------------------------------------
namespace Libs\Service;
class Hy{

	public function createOrder($data)
	{
		//选中的票型
		$selected = '';
		if(!in_array($selected,['',''])){
			return false;
		}
		//成人
		if($selected == ''){

		}
		/*儿童
		if($selected == ''){

		}*/
		//销售计划数据转换
		$yxPlan = F('Plan_'.$data['plan_id']);
		$hyPlan = $this->getHyPlan($yxPlan['plantime']);
		//组合数据
		$orderdata = [

		];
		//向海洋馆服务器下单
		$this->setHyOrder($orderdata);
		//向印象大红袍系统中备注里写上海洋馆的单号
	}
	//写入海洋馆服务器
	public function setHyOrder($postData)
	{
		$url = 'http://hy.yx513.net/api.php/trust/alizhiyou_scenic_order';
		$order = getHttpContent($url,'POST',$postData);
		//如果创建失败，直接发起一个退款申请
		if($order['status']){

		}else{
			//海洋馆订单创建失败发起退单申请
		}
		//获取第一条
		return $order;

	}
	//获取海洋馆销售计划
	public function getHyPlan($plantime)
	{
		$where = ['datetime'=>$plantime,'status'=>2,'type'=>2];
		$url = 'http://hy.yx513.net/api.php/trust/get_plan';
		$plan = getHttpContent($url,'POST',$where);
		//获取第一条
		return $plan[0];
	}
}