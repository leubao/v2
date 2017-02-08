<?php
// +----------------------------------------------------------------------
// | LubTMP 返佣
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Libs\Service\Rebate;
class Rebate extends \Libs\System\Service {
	/*渠道返利
	@param $info array 团队订单信息
	@param $user_id int 操作员id 计划任务执行 时是 1 admin
	return true|false   
	*/
	function rebate($info, $user_id = 1)
	{
		if(empty($info)){return array('order_sn'=>$sn,'user_id'=>$user_id,'status'=>'0','msg'=>"未找订单");}
		//dump($info);
		$model = new \Think\Model();
		$model->startTrans();
		//先充值  后标记.
		$crmData = array('cash' => array('exp','cash+'.$info['money']),'uptime' => time());
		//判断是返给个人还是商户
		if($info['type'] == '1'){
			$top_up = $model->table(C('DB_PREFIX')."user")->where(array('id'=>$info['guide_id']))->setField($crmData);
			$recharge = true;
		}else{
			//查询渠道商信息
			$cid = money_map($info['qd_id']);
			$top_up = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$cid))->setField($crmData);
			//充值成功后，添加一条充值记录
			$data = array(
					'type'=> 3,
					'cash'=> $info['money'],
					'user_id'  => $user_id,
					'crm_id'   => $info['qd_id'],//售出信息 票型  单价
					'createtime' =>time(),
					'balance'	=>	balance($cid),
					//'balance'	=>	0,
					'order_sn'	=> $info['order_sn']
			);			
			$recharge = $model->table(C('DB_PREFIX')."crm_recharge")->add($data);
		}
		//更新返利状态
		$up = $model->table(C('DB_PREFIX')."team_order")->where(array('id'=>$info['id']))->save(array('status'=>'4','userid'=>$user_id));
		if($top_up && $recharge && $up){
			$model->commit();//成功则提交
			return array('order_sn'=>$sn,'user_id'=>$user_id,'status'=>'1','msg'=>"返利成功!");
		}else{
			$model->rollback();//不成功，则回滚
			return array('order_sn'=>$sn,'user_id'=>$user_id,'status'=>'0','msg'=>"返利失败!");
		}
	}
}