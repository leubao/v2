<?php
// +----------------------------------------------------------------------
// | LubTMP 订单服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Libs\Service\Checkin;
class Ocheck extends \Libs\System\Service {
	//获取订单  场次结束时执行此操作  对未取票订单进行处理  并记录
	function order_check($map)
	{
		$list = M('Order')->where($map)->select();
		if(empty($list)){
			foreach ($list as $key => $value) {
				//改变订单状态
			}
			$status = M('')->addAll($dataList);
		}
		return true;
	}

	//逐个订单校验金额   按场次进行
	function order_money(){

	}
	//按场次查询是否正确
	function plan_check(){

	}
	//查询扣费是否正确  读取扣充  是否正确
	function crm_money(){

	}
	/*扣补程序
	*@param $type int 操作类型 1、充值 退款 2、消费 扣款
	*@param $channel_id 渠道商
	*@param $money 金额
	*/
	function back_fill($money,$channel_id,$type){
		if($type == '1'){

		}else{

		}
		//判断是否开启代理商制度
		
		Ocheck::logs($sn,$type,$money);
		return true;
	}
	/*记录异常日志
	*@param $sn int 订单号
	*@param $type int 类型
	*@param $money 金额
	*/
	function logs($sn,$type,$money){
		M('')->add(
			array(

				));
		return true;
	}
}