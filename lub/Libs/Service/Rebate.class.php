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

	/**
	 * 异步构造订单返利
	 * 计划任务  每隔5分钟同步异步构造一次
	 */
	function ajax_rebate_order(){
		//读取队列中未处理订单
		$redis = new \Redis();
	    $redis->connect('127.0.0.1',6379);
	    //判断列表中元素个数
	    $len = $redis->lsize('test');
		if($len > 0){
			//获取队列中最后一个元素，且移除
			$sn = $redis->rPop('test');
		}
		//写入带处理队列，若存在则不再写入
		$redis->lPush('test','1212211212');
		//读取当前在五分钟内所有团队订单   团队订单分为渠道订单和分销订单
		$map = array(
			'order_sn' => $order_sn,
        	'status' => array('in','1,6,7,9'),
        	'type'  => array('in','2,4,8,9'),
      	);
      	$list = D('Item/Order')->where($map)->relation(true)->find();
		//判断系统设置是否有存在返利
		$proconf = cache('ProductConfig'.);
		//是否开启分销
		//拆解订单
		//构造返利数据
		$rebate += $ticketType[$v['priceid']]['rebate'];
		//团队ALTER TABLE `lub_team_order` ADD `number` INT(3) NOT NULL COMMENT '数量' AFTER `order_sn`;
		//个人不允许底价结算
		$crmInfo = google_crm($plan['product_id'],$info['crm'][0]['qditem']);
		//严格验证渠道订单写入返利状态
		if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
			error_insert('400018');
			$model->rollback();
			return false;
		}
		//判断是否是底价结算
		if($crmInfo['group']['settlement'] == '1'){
			$teamData = array(
				'order_sn' 		=> $sn,
				'plan_id' 		=> $plan['id'],
				'product_type'	=> $info['product_type'],//产品类型
				'product_type'	=> $plan['product_type'],//产品类型
				'product_id' 	=> $plan['product_id'],
				'user_id' 		=> \Libs\Util\Encrypt::authcode($_SESSION['lub_userid'], 'DECODE'),
				'money'			=> $rebate,
				'guide_id'		=> $info['crm'][0]['guide'],
				'qd_id'			=> $info['crm'][0]['qditem'],
				'status'		=> '1',
				'number' 		=> $count,
				'type'			=> $info['type'] == '2' ? $info['sub_type'] : '2',//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
				'createtime'	=> $createtime,
				'uptime'		=> $createtime,
			);
			$in_team = $model->table(C('DB_PREFIX'). 'team_order')->add($teamData);
			//窗口团队时判断是否是底价结算
			if($proconf[$plan['product_id']]['1']['settlement'] == '2'){
				$in_team = true;
			}else{
				$in_team = $model->table(C('DB_PREFIX'). 'team_order')->add($teamData);
				if(!$in_team){error_insert('400017');$model->rollback();return false;}
			}
		}else{
			$in_team = true;
		}
	}
	function full(){

	}
	/**
	 * 三级分销
	 */
	function level3($product_id,$userid,$oinfo){
		//判断下单用户级别，拉取订单关联用户
		//订单数据
		$ticketType = F("TicketType".$product_id);
		//计算返佣
		foreach ($seat as $k => $v) {
			
		}
		$teamData[] = array(
			'order_sn' 		=> $info['order_sn'],
			'plan_id' 		=> $info['plan_id'],
			'subtype'		=> '0',
			'product_type'	=> $info['product_type'],//产品类型
			'product_id' 	=> $info['product_id'],
			'user_id' 		=> $info['user_id'],
			'money'			=> $rebate,
			'number'		=> $info['number'],
			'guide_id'		=> $oinfo['crm'][0]['guide'],
			'qd_id'			=> $oinfo['crm'][0]['qditem'],
			'status'		=> '1',
			'type'			=> $type,//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
			'userid'		=> '0',
			'createtime'	=> $createtime,
			'uptime'		=> $createtime,
		);
	}
	/**
	 * 拉取订单关联用户
	 * @param  int $userid 下单人ID
	 */
	function get_user_list($userid){
		$fid2 = M('User')->where(array('id'=>$userid))->getField('salesman');
		if(!empty($fid)){
			$fid3 = M('User')->where(array('id'=>$userid))->getField('salesman');
		}else{
			return $userid;
		}
	}
	//计算补贴金额
    function rebate($seat,$product_id){
      $ticketType = F("TicketType".$product_id);
      foreach ($seat as $k=>$v){
        //计算订单返佣金额
        $rebate += $ticketType[$v['priceid']]['rebate'];
      }
      return $rebate;
    }
	/*渠道返利
	@param $info array 团队订单信息
	@param $user_id int 操作员id 计划任务执行 时是 1 admin
	return true|false   
	*/
	function rebate($info, $user_id = 1){
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