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
	function order_nopay_del(){
		//设定日期，当前日期前一个月之前的所有订单
		//保留30天以内的订单数据
		//$datetime = strtotime("-30 day");
		$datetime = strtotime("-1 year");
		$map = [
			'status' => ['in','0,2,11'],
			'createtime' => ['ELT',$datetime]
		];
		$model = D('Item/Order');
		$list = $model->where($map)->field('order_sn')->limit(50)->select();
		//$sn = implode(',',array_column($list,'order_sn'));
		dump($list);
		if(!empty($list)){
			//删除满足条件的记录 关联删除
			
			foreach ($list as $k => $v) {
				$where = [
					'order_sn' => $v['order_sn']
				];
				$model->where($where)->delete();
				D('OrderData')->where($where)->delete();
			}
			
			//记录删除日志
			$log = [
				'title' => '清理过期待支付订单',
				'number'=> count($list),
				'datetime' => time(),
				'type'	   => empty(get_user_id()) ? '1' : '2',//系统自动执行
			];
			D('SweeperLog')->add($log);
		}
		
		return true;
	}
	//清理过期座位表 保留60天
	function table_del(){
		//读取过期的销售计划，
		$datetime = strtotime("-30 day");
		$map = [
			'plantime' => ['ELT',$datetime]
		];
		$model = D('Item/Plan');
		$list = $model->where($map)->field('id,product_id,product_type,seat_table')->select();
		foreach ($list as $key => $value) {
			//判断产品类型
			switch ($value['product_type']) {
				case '1':
					//剧院删除表，景区删除门票数据
					$dbPrefix = C("DB_PREFIX");
					$table = $value['seat_table'];
					$sql = <<<sql
				DROP TABLE {$dbPrefix}{$table};
sql;
    				$res = M()->execute($sql);
					break;
				case '2':
					D('Scenic')->where(['plan_id'=>$id])->delete();
					//剧院删除表，景区删除门票数据
					break;
				case '3':
					D('Drifting')->where(['plan_id'=>$id])->delete();
					//剧院删除表，景区删除门票数据
					break;
			}
		}
		$log = [
			'title' => '清理过期的座位表,以及过期的检票数据',
			'number'=> count($list),
			'datetime' => time(),
			'type'	   => empty(get_user_id()) ? '1' : '2',//系统自动执行
		];
		D('SweeperLog')->add($log);
		return true;
	}
	//删除一年前的打印日志
	public function print_log_del()
	{
		//创建日期是一年前的数据
		$datetime = strtotime("-1 year");
		$count = D('PrintLog')->where(['createtime',['ELT',$datetime]])->count();
		D('PrintLog')->where(['createtime',['ELT',$datetime]])->delete();
		$log = [
			'title' => '清理过期的打印日志',
			'number'=> $count,
			'datetime' => time(),
			'type'	   => empty(get_user_id()) ? '1' : '2',//系统自动执行
		];
		D('SweeperLog')->add($log);
		return true;
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