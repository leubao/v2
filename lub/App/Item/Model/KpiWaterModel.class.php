<?php
// +----------------------------------------------------------------------
// | LubTMP 考核流水模型
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;
class KpiWaterModel extends Model{
	public function insert($data='',$product_id = '',$channel_id = '', $fill = '2')
	{
		if(empty($data) || empty($product_id) || empty($channel_id)){
			return false;
		}

		//1、新增记录  2、改变分值
		/*多表事务*/
		$model = new Model();
		$model->startTrans();
		$add = array(
			'product_id'=>	$product_id,
			'score'		=>	$data['score'],
			'crm_id'	=>	$channel_id,
			'fill'		=>	$fill,
			'type'		=>	$data['type'],
			'remark'	=>	$data['remark'],
			'status'	=>	'1',
			'user_id'	=>	get_user_id(),
			'create_time'=>	time(),
			'update_time'=> time(),
		);
		$water = $model->table(C('DB_PREFIX').'kpi_water')->add($add);
		$updata = array('score' => array('exp','score-'.$data['score']),'update_time' => time());
		$kpi = $model->table(C('DB_PREFIX')."kpi_channel")->where(array('crm_id'=>$channel_id))->setField($updata);
		if($water && $kpi){
			$model->commit();//提交事务
			return true;
		}else{
			error_insert('410008');
			$model->rollback();//事务回滚
			return false;
		}
	}
}