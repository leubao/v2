<?php

/**
 * @Author: IT Work
 * @Date:   2019-03-10 02:10:28
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-03-27 02:51:56
 */
namespace Item\Model;

use Common\Model\Model;

class BookingModel extends Model {

	/**
	 * 预约订单
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-03-26T23:00:01+0800
	 * @return   [type]                   [description]
	 */
	public function pre_order($data, $pinfo)
	{
		//读取订单
		foreach ($pinfo['priceid'] as $ke => $va) {
			$new[$va] = (int)$pinfo['price_num'][$ke];
		}
		$info = $data['info'];
		//更新数量
		foreach ($info['data'] as $k => $v) {
			$newArea[$v['priceid']] = [
				'areaId'	=>	$v['areaId'],
				'priceid'	=>	$v['priceid'],
				'price'		=>	$v['price'],
				'num'		=>	$new[$v['priceid']],
				'idcard'	=>	isset($v['idcard']) ? $v['idcard'] : 0
			];
		}

		$order = new \Libs\Service\Order();
		$areaSeat = $order->area_group($newArea,$data['product_id'],$info['info']['param'][0]['settlement'],1,'');
		//更新订单
		$newInfo = [
			'order_sn'		=> $data['order_sn'],
			'subtotal'		=> $areaSeat['money'],
			'plan_id'		=> $pinfo['plan'],
			'checkin'		=> $info['info']['checkin'],
			'data'			=> $newArea,
			'crm'			=> $info['info']['crm'],				
			'param'			=> $info['info']['param'],	
		];
		//暂存订单
		$param = [
			'info'	=>	$newInfo,
			'uinfo'	=>	$info['uinfo'],
			'act'	=>	$info['act']
		];
		$newData = [
			'id'			=> $data['id'],
			'plan_id'		=> $pinfo['plan'],
			'number'		=> $areaSeat['num'],
			'money' 		=> $areaSeat['money'],
			'info'			=> json_encode($param)
		];
		if($pinfo['model'] == 'staging'){

			if($this->create($newData)){
			    $result = $this->save(); // 写入数据到数据库 
			    if($result){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
		if($pinfo['model'] == 'push'){
			$newData['status'] = 1;
			
			//推送订单
			if($this->create($newData)){
			    $result = $this->save(); // 写入数据到数据库 
			    if($result){
			    	$status = $order->channel(json_encode($newInfo),27,$info['uinfo'],8,$pinfo['pay']);
			    	
			    	if($status){
			    		return true;
			    	}else{
			    		$this->where(['id'=>$data['id']])->setField('status', '5');
			    		$this->error = '订单推送失败:'.$order->error;
			    		return false;
			    	}
				}else{
					return false;
				}

			}else{
				$this->error = '订单推送失败:'.$order->error;
				return false;
			}	
		}
	}
	/**
	 * 预售登记
	 */
	public function pre_booking(array $data, array $pinfo)
	{
		//$data['info']['info']['plantime'] = $pinfo['datetime'];//"plantime":"2020-03-28",
    	$data['info']['info']['number'] = $pinfo['number'];
    	$data['info']['info']['contact'] = $pinfo['contact'];
    	$data['info']['info']['mobile'] = $pinfo['mobile'];
		//暂存
		if($pinfo['model'] == 'staging'){
			//只修改booking 
			//$data['info']['info']['plantime'] = $pinfo['datetime'];//"plantime":"2020-03-28",
        	$upData = [
        		'id'		=> $data['id'],
        		'pay'		=> $pinfo['pay'],
        		'info'  	=> json_encode($data['info']),
        		'number'	=> $pinfo['number'],
        		'admin_id'	=> get_user_id(),
        		'uptime'	=> time()
        	];
        	if($this->save($upData)){
        		return true;
        	}else{
        		return false;
        	}
		}
		//推送
		if($pinfo['model'] == 'push'){
			$pta = explode('|', $pinfo['plan']);
			$info = $data['info'];
			$ticket = F('TicketType'.$data['product_id'])[$pta[2]];
			$newArea[$pta[2]] = [
				'areaId'	=>	$pta[1],
				'priceid'	=>	$pta[2],
				'price'		=>	(int)$info['uinfo']['group']['settlement'] === 1 ? $ticket['price'] : $ticket['discount'],
				'num'		=>	$pinfo['number'],
				'idcard'	=>	isset($v['idcard']) ? $v['idcard'] : 0
			];
			$order = new \Libs\Service\Order();
			$areaSeat = $order->area_group($newArea,$data['product_id'],$info['uinfo']['group']['settlement'],1,'');
			if(!$areaSeat){
				$this->error = '订单推送失败:'.$order->error;
				return false;
			}
			$newInfo = [
				'order_sn'		=> $data['order_sn'],
				'subtotal'		=> $areaSeat['money'],
				'plan_id'		=> $pta[0],
				'checkin'		=> 1,
				'data'			=> $newArea,
				'crm'			=> [['contact'=>$info['info']['contact'],'phone'=>$info['info']['mobile'],'guide'=>$info['uinfo']['id'],'qditem'=>$info['uinfo']['cid']]],				
				'param'			=> [[
					'pre'		=>	1,
					'remark' 	=> $pinfo['remark']??'无...',
					'settlement'=> $info['uinfo']['group']['settlement']

				]],	
			];

			$upData = [
				'id'			=> $data['id'],
				'plan_id'		=> $pta[0],
				'number'		=> $areaSeat['num'],
				'money' 		=> $areaSeat['money'],
				'info'			=> json_encode($data['info']),
				'status'		=> 1
			];
			$model = new Model();
			$model->startTrans();
			if($model->table(C('DB_PREFIX').'booking')->save($upData)){
        		//创建订单，并排座
				$status = $order->channel(json_encode($newInfo),27,$info['uinfo'],8,$pinfo['pay']);
				    	
		    	if($status){
		    		$model->commit();
		    		return true;
		    	}else{
		    		$this->error = '订单推送失败:'.$order->error;
		    		$model->rollback();
		    		return false;
		    	}
        	}else{
        		$model->rollback();
        		return false;
        	}
			
		}
	}
}