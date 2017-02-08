<?php
// +----------------------------------------------------------------------
// | LubTMP 销售计划
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

namespace Item\Service;
class Plan {

	/**
	 * 拉取当前日期内可用的活动
	 * @param int $plantime 计划时间
	 */
	function get_activity($plantime){
		//构造查选条件
		$map['starttime'] = array('ELT', $plantime);
		$map['endtime'] = array('EGT', $plantime);
		$map['status'] = '1';
		$list = M('Activity')->where($map)->field('id,title')->select();
		return $list;
	}

	/**
	 * 更新活动的销售计划
	 * @param  array $activity 活动集
	 * @param int $plan_id 销售计划
	 * @return return true
	 */
	function up_activity_plan($activity,$plan_id){
		$db = M('Activity');
		$dbs = M('ActivityPlan');
		if(empty($activity)){
			//停用所有
			$dbs->where(array('plan_id'=>$plan_id))->setField('status','0');
		}else{
			foreach ($activity as $value) {
				//查询是否已经写入活动计划对应表
				if($dbs->where(array('plan_id'=>$plan_id,'activity_id'=>$value))->find()){
					$dbs->where(array('plan_id'=>$plan_id,'activity_id'=>$value))->setField('status','1');
				}else{
					if(!$dbs->add(array('plan_id'=>$plan_id,'activity_id'=>$value,'status'=>1))){
						error_insert('400015');
					}
				}
				$activity = $db->where(array('id'=>$value))->field('param,product_id')->find();
				//写入销售配额
				Plan::insert_quota($value,$plan_id,$activity['param'],$activity['product_id']);
			}
		}
		return true;
	}
	/**
	 * 写入销售配额
	 * @param  int $activity_id 活动ID
	 * @param  int $plan_id  销售计划
	 * @param  string $param   销售参数
	 * @param  int $product_id  产品id
	 * @return true      
	 */
	function insert_quota($activity_id,$plan_id,$param,$product_id){
		$param = unserialize($param);
		//构造配额注册数据
		foreach ($param['info'] as $key => $value) {
			$data[] = array(
				'number'	=>	'0',
				'channel_id'=>	$activity_id,
				'product_id'=>	$product_id,
				'type'		=>	'2',
				'plan_id'	=>	$plan_id,
				'area_id'	=>	$value['area'],
			);
		}
		if(!M('QuotaUse')->addAll($data)){
			error_insert('400016');
		}
	}
}