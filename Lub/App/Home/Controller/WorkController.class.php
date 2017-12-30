<?php
// +----------------------------------------------------------------------
// | LubTMP  渠道商账户管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use Libs\Service\Operate;
use Home\Service\Partner;
use Home\Controller\ProductController;
class WorkController extends Base{
	
	function _initialize(){
		 parent::_initialize();
	}
	/**
	 * 预约订单
	 * @company  承德乐游宝软件开发有限公司
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2017-12-18
	 * @return   [type]        [description]
	 */
	function pre_order()
	{
		$ginfo = I('get.');
		$ginfo = [
			'type'	=>	'1',
			'productid' => '41'
		];
		//if(empty($ginfo['productid'])){$this->error('参数错误!');}
		//默认日期
		$plantime = date("Y-m-d",strtotime("+1 day"));
		//选择计划
		//加载票型
		$this->public_info_conf();
		$data = [
			'order_sn'	=> 	'get_order_sn()',
			'user_id'	=>	'',
			'datetime'	=>	$info['datetime'],
			'number'	=>	$info['number'],
			'phone'		=>	$info['phone'],
			'channel_id'=>	'',
			'type'		=>	'1',
		];
		$this->assign('plantime',$plantime)->assign('info',$ginfo)->assign('data',$data)->display();
	}
}