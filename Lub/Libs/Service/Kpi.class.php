<?php
// +----------------------------------------------------------------------
// | LubTMP  渠道kPI考核类
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Common\Model\Model;
class Kpi{
	//余额报警
	//\Libs\Service\Kpi::if_money_low(,$plan['product_id'],$cid);
	/**
	 * 判断是否处于最低额，发送短信通知
	 * $money 当前余额
	 
	function if_money_low($money = '',$product_id = '', $crm_id = ''){
		//查询返利金额
		//查询账户最低余额
		$kpimoney = F('KpiMoney');
		if(empty($kpimoney)){
			D('Item/KpiChannel')->kpi_channel_cache();
			$kpimoney = F('KpiMoney');
		}
		//获取缓存记录
		$proconf = cache('ProConfig');
		$proconf = $proconf[$product_id][5];
		if($money < $kpimoney['money_low']){
			//查询是否已经有提醒
			$log = load_redis('get','kpimoney_'.$crm_id);
			if(!$log){
				KpiChannel::kpi_to_mgs($crm_id);
				//记录缓存时间
				$log = $crm_id.'|'.date('Y-m-d H:i:s');
				load_redis('set','kpimoney_'.$crm_id,$log);
			}else{
				//判断时间差
				$log = explode('|',$log);
				$starttime = $log[1];
				$res = timediff($starttime,date('Y-m-d H:i:s'),'hour');
				if($res['hour'] > 24){
					//超过24小时提醒
					KpiChannel::kpi_to_mgs($crm_id);
				}
				if($res['hour'] > $proconf['money_low_time']){
					//超过48小时扣分,每隔48小时扣分
					$interval = (int)$res['hour']%$proconf['money_low_time'];
					if($interval == 0){
						$pdata = [
							'score'	=> $proconf['money_low_cycle'],
							'type'	=> '1',
							'remark'=> '余额连续'.$proconf['money_low_time'].'小时,自动减分',
						];
						D('Item/KpiWater')->insert($pdata,$product_id,$crm_id,1,1);
					}
				}
			}
		}
		return true;
	}*/
	function if_money_low($item_id = '', $crm_id = '', $money = ''){
		//判断是否开启余额报警
		$itemConf = cache('ItemConfig');
		if($itemConf[$item_id]['1']['if_money_low']){
			//读取配置
			$money_low = $itemConf[$item_id]['1']['money_low'];
			if($money_low > $money){
				Kpi::kpi_to_mgs($crm_id,$money_low);
			}else{
				return true;
			}
		}else{
			return true;
		}
		
	}
	//发送提醒短信
	function kpi_to_mgs($crm_id,$money)
	{	
		if($crm_id){
			$crminfo = D('Crm')->where(['id'=>$crm_id])->field('name,cash,phone')->find();
			//发送警告,切记录时间
			$info = [
				'title'	 =>	$crminfo['name'],
				'money'	 =>	$money,
				'moneys' => $crminfo['cash'],
				'phone'	 =>	$crminfo['phone'],
			];
			\Libs\Service\Sms::order_msg($info,'11');
		}
		return true;
	}
	//校验是否取消报警
	function check_change_log($crm_id = ''){
		//查询是否存在月报警
		$log = load_redis('get','kpimoney_'.$crm_id);
		if(!$log){
			//查询余额
			$cid = money_map($info['channel_id']);
			$balance = balance($cid);
			$kpimoney = F('KpiMoney');
			//判断该用户最低余额,超过则取消
			if($balance > $kpimoney['money_low']){
				load_redis('delete','kpimoney_'.$crm_id);
			}
		}
	}
	//记录操作日志 TODO 操作日志
	function actionLog(){
		/*
		$data = [
			'time' => time(),
			'action' => ,
			'url'	=>	,
			'user_id'=>,
			'status'=>,
			'remark'=>,
		];*/
	}
}