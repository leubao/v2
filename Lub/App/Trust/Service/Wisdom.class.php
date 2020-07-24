<?php
namespace Trust\Service;
/**
 * 
 * @Author: IT Work
 * @Date:   2020-07-13 10:26:44
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-07-22 13:12:47
 */
class Wisdom
{
	const domain = 'https://wisdom.xzusoft.com';
	/**
	 * 订单确认
	 */
	static public function confirm_order($sn, $status, $remark = '')
	{
		//获取订单
		$postData = [
			"order_sn"	=>	$sn,
 			"type"		=>	'order_confirm',
 			"status"	=>	$status,
  			"remark"	=>	$remark
		];

		$url = self::domain.'/api/order_notice';
		$state = getHttpContent($url, 'POST', $postData);
		dump($state);
		if($state){

		}
	}
	/**
	 * 退单确认
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-13T10:28:30+0800
	 * @return   [type]                   [description]
	 */
	public function refund_confirm($sn, $status, $remark = '')
	{
		//获取退单结果
		//获取订单
		$postData = [
			"order_sn"	=>	$sn,
 			"type"		=>	'order_refund',//1新订单提醒//2订单审核提醒3//预约审核提醒//4待处理订单
 			"status"	=>	$status,
  			"remark"	=>	$remark
		];
		$url = $domain.'/api/order_notice';
		$state = getHttpContent($url, 'POST', $postData);
		if($state){

		}
	}
	/**
	 * 待处理订单提醒
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-15T15:15:41+0800
	 */
	public function Notification()
	{
		$postData = [
			"order_sn"	=>	$sn,
 			"type"		=>	'manage_del',//1新订单提醒//2订单审核提醒3//预约审核提醒//4待处理订单
 			"status"	=>	$status,
  			"remark"	=>	$remark
		];
		$url = $domain.'/api/order_notice';
		$state = getHttpContent($url, 'POST', $postData);
		if($state){

		}
	}
}