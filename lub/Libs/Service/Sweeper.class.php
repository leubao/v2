<?php
// +----------------------------------------------------------------------
// | LubTMP  系统清道夫
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Common\Model\Model;
class Sweeper{
	//清理过期待支付订单
	function pay_del(){
		//设定日期，当前日期前一个月之前的所有订单
		//保留30天以内的订单数据
		$map = [
			'status' => ['in','11'],
			'create_time' => ['ELT',]
		];
		$model = D('Item/Order');
		$list = $model->where($map)->field('order_sn')->select();
		$sn = array_column($list,'order_sn',',');
		//记录删除日志
		$log = [
			'datetime' => time(),
			'type'	   => '4',//4删除 系统中待支付的订单
			'data'	   => json_decode(['sn'=>$sn]);
		];
		//删除满足条件的记录 关联删除
		$model->where($map)->delete();
		return true;
	}
	//清理过期座位表
	function table_del(){

	}
	//处理提醒
	function remind_del(){
		//读取当前有效的提醒
		$list = D('')->where()->field('order_sn')->select();
		foreach ($list as $k => $v) {
			//读取订单当前状态  是否与提醒状态一至
		}
	}
	//清理日志
	function log_del(){
		//登录日志保留2个月
		//操作日志保留2个月
		//检票日志保留2个月
		//打印日志保留3个月
	}
}