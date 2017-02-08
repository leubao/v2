<?php
// +----------------------------------------------------------------------
// | LubTMP  报表处理类 分日期按场次分渠道商按票型汇总
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Common\Model\Model;
class ReportSum{
	/**
	 * 汇总处理
	 * @param  datetime $value 要处理日期
	 * @return [type]        [description]
	 */
	function summary($datetime='')
	{
		if(empty($datetime)){
			return false;
		}
		//获取数据源
		$map =  array(
			'datetime' => $datetime,
		);

		$model = D('ReportData');
		$field = "id,datetime,product_id,plan_id,price_id,sum(number),guide_id,channel_id,type";
		$list = $model->where($map)->field($field)->group('product_id,plan_id')->select();

		echo $model->_sql();
		return $list;
		//
	}
	/**
	 * 产品汇总
	 */
	function sum_product($list){
		if(!is_array($list)){
			return false;
		}
		foreach ($list as $key => $value) {
			$data[$value['product_id']][] = $value;
		}
		return $data;
	}
	/**
	 * 计划汇总
	 */
	function sum_plan($list){
		if(!is_array($list)){
			return false;
		}
	}
	/**
	 * 渠道商汇总
	 */
	function sum_channel(){
		if(!is_array($list)){
			return false;
		}
	}
	/**
	 * 票型汇总
	 */
	function sum_price(){
		if(!is_array($list)){
			return false;
		}
	}
	/**
	 * 景区日销售汇总
	 */
	function sum_day($datetime = '')
	{
		# code...
	}
}