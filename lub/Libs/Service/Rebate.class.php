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
	public static function ajax_rebate_order(){
		//读取队列中未处理订单
		//判断队列是否存在数据
		$ln = load_redis('lsize','PreOrder');
		load_redis('set','ajax_rebate_order_time',date('Y-m-d H:i:s').'&&'.$ln);
		if($ln > 0){
			$fenrun = false;
			//获取队列中最后一个元素，且移除
			$sn = load_redis('rPop','PreOrder');

			$map = array(
				'order_sn' => $sn,
	        	'status' => array('in','1,6,7,9'),
	        	'type'  => array('in','2,4,8,9'),
	      	);
	      	$datatime = time();
	      	$info = D('Item/Order')->where($map)->relation(true)->find();
	      	//判断系统设置是否有存在返利
			$proconf = cache('ProConfig');
			$proconf = $proconf[$info['product_id']][1];
			$model = D('Item/TeamOrder');
			//判断返利是否存在
			if($model->where(array('order_sn'=>$sn))->getField('id')){
				load_redis('lpush','Error_PreOrder',$info['order_sn'].'E2');
				return false;
			}
			$map = array(
				'order_sn' => $sn,
				'status' => array('in','1,6,7,9'),
				'type'  => array('in','2,4,8,9'),
			);
			$info = D('Item/Order')->where($map)->relation(true)->find();
			 //判断系统设置是否有存在返利
			$info['info'] = unserialize($info['info']);
			//个人允许底价结算,且有返佣
			$crmInfo = google_crm($info['product_id'],$info['info']['crm'][0]['qditem'],$info['info']['crm'][0]['guide']);
			//load_redis('set','crmifo',serialize($crmInfo).'1212');
			//严格验证渠道订单写入返利状态
			if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
				error_insert('400018');
				return false;
			}
			//判断是否是底价结算
			if($crmInfo['group']['settlement'] == '1' || $crmInfo['group']['settlement'] == '3'){
				if($crmInfo['group']['type'] == '4'){
				  //当所属分组为个人时，补贴到个人
				  $type = '1';
				}else{
				  $type = '2';
				}
				$ticketType = F('TicketType'.$info['product_id']);
				//判断订单类型1、渠道版订单
				//查询应得返利人,根据导游导游员ID
				switch ($info['type']) {
					case '2':
						//窗口渠道
						$get_user_list['ul1'] =  $info['info']['crm'][0]['guide'];
						break;
					case '4':
						//渠道版
						$get_user_list['ul1'] =  $info['info']['crm'][0]['guide'];
						break;
					case '8':
						//全员销售
						$get_user_list['ul1'] =  $info['info']['crm'][0]['guide'];
						break;
					case '9':
						//三级分销
						$guide = $info['info']['crm'][0]['guide'];
						//拉取订单相关人，判断下单人的级别，若是1级直接拿取全部奖金 2级拿取2级和三级的奖金 3级拿取三级奖金
						$fenrun = true;
						$get_user_list = Rebate::get_user_list($guide);
						break;
				}
				//计算返利金额
				foreach ($info['info']['data'] as $k => $v) {
				  $rebate += $ticketType[$v['priceid']]['rebate'];
				  if($fenrun){
				  	//分润
				  	$param = $ticketType[$v['priceid']]['param']['level3'];
				  	$level1 += $param['l1'];
				  	$level2 += $param['l2'];
				  	$level3 += $param['l3'];
				  }
				}
				//组合数据
				if($fenrun){
					$get_user_list = Rebate::get_user_list($guide,$level1,$level2,$level3);
				}
				load_redis('set','get_user_list',serialize($get_user_list).'===21');
				$count = count($get_user_list);
				foreach ($get_user_list as $key => $value) {
					//组装写入数据
					if($fenrun){
						$changeData = array(
						  'money'     => $value['ul'.$k]['money'],
						  'guide_id'  => $value['ul'.$k]['guide'],
						);
					}else{
						$changeData = array(
						  'money'     => $rebate,
						  'guide_id'  => $info['info']['crm'][0]['guide'],
						);
					}
					$baseData = array(
					  'order_sn'    => $info['order_sn'],
					  'plan_id'     => $info['plan_id'],
					  'subtype'   	=> '0',
					  'product_type'=> $info['product_type'],//产品类型
					  'product_id'  => $info['product_id'],
					  'user_id'     => $info['user_id'],
					  'number'    	=> $info['number'],
					  'qd_id'     	=> $info['info']['crm'][0]['qditem'],
					  'status'    	=> '1',
					  'type'      	=> $type,//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
					  'userid'    	=> '0',
					  'uptime'    	=> $datatime,
					  'createtime'	=> $datatime
					);
					if($changeData['money'] > 0){
						$teamData[] = array_merge($baseData,$changeData);
					}
					load_redis('set','Dadddata',serialize($changeData));
					load_redis('set','adddata',serialize($teamData));
				}
				load_redis('set','adddata',serialize($teamData));
				$status = $model->addAll($teamData);
				if($status){
					return $status;
				}else{
					load_redis('lpush','Error_PreOrder',$info['order_sn'].'E1');
					return false;
				}
				
			}
			//读取当前在五分钟内所有团队订单   团队订单分为渠道订单和分销订单
		}
		//是否开启分销
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
	 * 写入补贴记录
	 */
	function insert_team_data(){

	}
	/**
	 * 拉取订单关联用户
	 * @param  int $userid 下单人ID
	 */
	function get_user_list($userid,$level1 = '',$level2 = '',$level3 = ''){
		//基于微信
		$db = D('WxMember');
		$fid2 = $db->where(array('user_id'=>$userid))->getField('promote');
		if(!empty($fid)){
			$fid1 = $db->where(array('user_id'=>$fid2))->getField('promote');
		}
		if(!empty($fid1)){
			$return['ul1']['guide'] = $fid1;
			$return['ul1']['money'] = $level1;
			$return['ul2']['guide'] = $userid;
			$return['ul2']['money'] = $level2+$level3;
		}elseif(!empty($fid2)){
			$return['ul1']['guide'] = $fid1;
			$return['ul1']['money'] = $level1;
			$return['ul2']['guide'] = $fid2;
			$return['ul2']['money'] = $level2;
			$return['ul3']['guide'] = $userid;
			$return['ul3']['money'] = $level3;
		}else{
			$return['ul1']['guide'] = $userid;
			$return['ul1']['money'] = $level1+$level2+$level3;
		}
		return $return;
	}

	/*渠道返利
	@param $info array 团队订单信息
	@param $user_id int 操作员id 计划任务执行 时是 1 admin
	return true|false   
	*/
	function rebate($info, $user_id = 1){
		if(empty($info)){return array('order_sn'=>$sn,'user_id'=>$user_id,'status'=>'0','msg'=>"未找订单");}
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