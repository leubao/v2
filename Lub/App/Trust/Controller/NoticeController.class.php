<?php
namespace Trust\Controller;
/**
 * @Author: IT Work
 * @Date:   2020-06-24 17:25:21
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-06-24 17:40:55
 */
use Libs\Service\ArrayUtil;
class NoticeController
{
	public function push_plan_insert()
	{
		# code...
	}
	public function push_plan_update()
	{
		# code...
	}
	/**
	 * 异步推送订单状态
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-06-24T17:17:39+0800
	 * @return   [type]                   [description]
	 */
	public function push_order_state()
	{
		$data = [
			'order_sn'	=>	'1',
			'incode'	=>	'122112',
			'status'	=>	1,
			'timestamp'	=>	time(),	
		];
		$data['sign'] = ArrayUtil::setSign($data);
		$url = 'http://39.99.140.42:9501/order_notice';
		$res = getHttpContent($url, 'POST', $data);
		//$res = json_decode($res, true);
		var_dump($res);
		if($res['status']){
		// 记录日志
			return showReturnCode(true,200,'','','推送成功');
		}else{
			//记录状态，随后推送
		}
	}
}