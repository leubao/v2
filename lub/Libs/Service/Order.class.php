<?php
// +----------------------------------------------------------------------
// | LubTMP  订单处理   V 20160419 新增账户余额校验
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Org\Util\Date;
use Libs\Service\Sms;
use Common\Model\Model;
use Libs\Service\Autoseat;
class Order extends \Libs\System\Service {
    //数据
    protected $data = array();
    /** 执行错误消息及代码 */
    public $errMsg;
    public $errCode;
	/**************************************************选座订单****************************************************/
	/**
	 * 选座订单
	 * @param  string $pinfo     请求数据包
	 * @param $scena int 场景 1窗口2渠道版3网站4微信5api
	 * @param $uinfo array 当前用户信息
	 */
	function rowSeat($pinfo,$scena,$uinfo = null){
		$info = json_decode($pinfo,true);
		$plan = F('Plan_'.$info['plan_id']);

		if(empty($plan)){error_insert('400005');return false;}
		$areaId = $info['areaId'];
		$count = count($info['data']);//统计座椅个数
		//获取订单号 1代表检票方式1人一票 一团一票
		$printtype = $info['checkin'] ? $info['checkin'] : 1;
		$sn = get_order_sn($plan['id'],$printtype);
		$createtime = time();
		$ticketType = F("TicketType".$plan['product_id']);
		/*多表事务*/
		$model = new Model();
		$model->startTrans();
		$flag=false;
		$money = 0;
		$num = 0;
		//根据支付方式判断排座方式
		if(in_array($info['param'][0]['is_pay'],array('4','5'))){
			$seat_type = '2';
		}else{
			$seat_type = '1';
		}
		//同步排座
		if($seat_type == '1'){
			//更新座位信息	
			foreach ($info['data'] as $k=>$v){	
				$map = array(
					'area' => $v['areaId'],
					'seat' => $v['seatid'],
					'status' => array('in','0,66'),
				);
				$remark = print_remark($ticketType[$v['priceid']]['remark'],$plan['product_id']);
				$data = array(
					'order_sn' => $sn,
					'soldtime' => $createtime,
					'status'   => '2',
					'price_id' => $v['priceid'],
					'sale'	   => serialize(array('plantime'=>date('Y-m-d ',$plan['plantime']).date(' H:i',$plan['starttime']),
												'area'=>areaName($v['areaId'],1),
												'seat'=>Order::print_seat($v['seatid'],$plan['product_id'],$ticketType[$v['priceid']]['param']['ticket_print'],$ticketType[$v['priceid']]['param']['ticket_print_custom']),
												'games'=>$plan['games'],
												'priceid'=>$v['priceid'],
												'priceName'=>$ticketType[$v['priceid']]['name'],
												'price'=>$ticketType[$v['priceid']]['price'],/*票面价格*/
												'discount'=>$ticketType[$v['priceid']]['discount'],/*结算价格*/
												'remark_type' => $remark['remark_type'],
												'remark'=>$remark['remark'],
										)),//售出信息 票型  单价
				);
				$status[$k]=$model->table(C('DB_PREFIX').$plan['seat_table'])->where($map)->save($data);
				//计算订单返佣金额
				$rebate += $ticketType[$v['priceid']]['rebate'];
				/*以下代码用于校验*/
				if($info['param'][0]['settlement'] == '1'){
					$money = $money+$ticketType[$v['priceid']]['price'];
				}else{
					$money = $money+$ticketType[$v['priceid']]['discount'];
				}
				
				/*重组座位数据*/
				$seatData[$k] = array(
					'areaId' =>	$v['areaId'],
					'priceid'=>$v['priceid'],
					'price'=>$ticketType[$v['priceid']]['price'],/*票面价格*/
					'discount'=>$ticketType[$v['priceid']]['discount'],/*结算价格*/
					'seatid'=>	$v['seatid'],

				);
				$num = $num++;
				if($status[$k] == false){
					//$this->errCode = '400009';
					//$this->errMsg = '座椅信息更新失败';
					//error_insert('400009');
					return false;
					break;
				}
				if($count == $k+1){
					$flag=true;
				}
				//是否为团队订单 
				if($info['type'] == '2' || $info['type'] == '4' || $info['type'] == '8' || $info['type'] == '9'){
					//个人允许底价结算,且有返佣
					$crmInfo = google_crm($plan['product_id'],$info['crm'][0]['qditem'],$info['crm'][0]['guide']);
					//严格验证渠道订单写入返利状态
					if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
						error_insert('400018');
						$model->rollback();
						return false;
					}
					//判断是否是底价结算
					if($crmInfo['group']['settlement'] == '1' || $crmInfo['group']['settlement'] == '3'){
						//存储待处理数据
						load_redis('lpush','PreOrder',$sn);
					}
				}
			}
			/*金额与数量校验*/
			if($money == $info['subtotal']){
				$info['subtotal'] = $money;
			}else{
				//error_insert('400018');
				$model->rollback();//事务回滚
				//$this->errCode = '400018';
				//$this->errMsg = '金额校验失败';
				return false;
			}
			$status = '1';
		}
		//异步排座
		if($seat_type == '2'){
			$seatData = $info['data'];
			load_redis('setex',$sn,serialize($info),3600);
			$status = '11';
			$flag = true;
		}
		//结算方式
		if($info['type'] == '2'){
			//个人不允许底价结算
			$crmInfo = google_crm($plan['product_id'],$info['crm'][0]['qditem']);
			//严格验证渠道订单写入返利状态
			if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
				//error_insert('400018');
				$model->rollback();
				//$this->errCode = '400021';
				//$this->errMsg = '渠道商信息获取失败';
				return false;
			}
			//判断是否是底价结算['group']['settlement']
			if($crmInfo['group']['settlement'] == '1'){
				load_redis('lpush','PreOrder',$info['order_sn']);
			}
		}
		/*写入订单信息*/
		$orderData = array(
			'order_sn' => $sn,
			'plan_id' => $plan['id'],
			'product_type'	=>	$plan['product_type'],//产品类型
			'product_id' => $plan['product_id'],
			'type'	=> $info['type'],
			'user_id' => get_user_id(),
			'addsid' => '1',
			'money' => $info['subtotal'],
			'createtime'=>$createtime,
			'guide_id'		=> $info['crm'][0]['guide'],
			'channel_id'	=> $info['crm'][0]['qditem'],
			'pay'	=> $info['param'][0]['is_pay'] ? $info['param'][0]['is_pay'] : '1',
			'status' => $status,
			'number' =>$count,
			'phone'			=> $info['crm'][0]['phone'],//取票人手机
			'sub_type'		=> $info['sub_type'] ? $info['sub_type'] : 2,//补贴对象
			'take'			=> $info['crm'][0]['contact'],
			'activity'		=> $info['param'][0]['activity'],//活动标记
		);
		$newInfo = array('subtotal'	=> $info['subtotal'],'type'=>$info['type'],'checkin'=> $info['checkin'],'sub_type'=>$info['sub_type'],'num'=>$info['num'],'data' => $seatData,'crm' => $info['crm'],'pay' => $info['param'][0]['is_pay'] ? $info['param'][0]['is_pay'] : '1','param'=> $info['param']);

		$state = $model->table(C('DB_PREFIX').'order')->add($orderData);
		$oinfo = $model->table(C('DB_PREFIX').'order_data')->add(array('oid'=>$state,'order_sn' => $sn,'info' => serialize($newInfo)));
		/*记录售票员操作日志*/
		if($flag && $state && $oinfo){
			$model->commit();//提交事务
			$sn = array('sn' => $sn,'is_pay' => $info['param'][0]['is_pay'],'money'=>$info['subtotal']);
			return $sn;
		}else{
			//error_insert('400006');
			$model->rollback();//事务回滚
			//$this->errCode = '400006';
			//$this->errMsg = '订单写入失败';
			return false;
		}	
	}
	/**
	 * 选座排座
	 */
	function choose_seat($seat, $info, $sub_type = '2', $channel = '0', $is_pay = null){
		$plan = F('Plan_'.$info['plan_id']);
		if(empty($plan)){error_insert('400005');return false;}
		if(empty($info['order_sn'])){
			$errMsg = '未找到有效订单';
			return false;
		}
		$oinfo = $info['info'];
		/*
		$oinfo = load_redis('get',$info['sn']);
		if(empty($oinfo)){
			$oinfo = D('OrderData')->where(array('order_sn'=>$info['sn']))->field('info')->find();
			$oinfo = unserialize($oinfo);
		}*/
		$count = count($seat);//统计座椅个数
		$createtime = time();
		$ticketType = F("TicketType".$plan['product_id']);
		/*多表事务*/
		$model = new Model();
		$model->startTrans();
		$flag=false;
		$money = 0;
		$num = 0;
		//获取订单信息
		
		//更新座位信息	
		foreach ($seat as $k=>$v){	
			$map = array(
				'area' => $v['areaId'],
				'seat' => $v['seatid'],
				'status' => array('in','0,66'),
			);
			$remark = print_remark($ticketType[$v['priceid']]['remark'],$plan['product_id']);
			$data = array(
				'order_sn' => $info['order_sn'],
				'soldtime' => $createtime,
				'status'   => '2',
				'price_id' => $v['priceid'],
				'sale'	   => serialize(array('plantime'=>date('Y-m-d ',$plan['plantime']).date(' H:i',$plan['starttime']),
											'area'=>areaName($v['areaId'],1),
											'seat'=>Order::print_seat($v['seatid'],$plan['product_id'],$ticketType[$v['priceid']]['param']['ticket_print'],$ticketType[$v['priceid']]['param']['ticket_print_custom']),
											'games'=>$plan['games'],
											'priceid'=>$v['priceid'],
											'priceName'=>$ticketType[$v['priceid']]['name'],
											'price'=>$ticketType[$v['priceid']]['price'],/*票面价格*/
											'discount'=>$ticketType[$v['priceid']]['discount'],/*结算价格*/
											'remark_type' => $remark['remark_type'],
											'remark'=>$remark['remark'],
									)),//售出信息 票型  单价
			);
			$status[$k]=$model->table(C('DB_PREFIX').$plan['seat_table'])->where($map)->save($data);
			//计算订单返佣金额
			$rebate += $ticketType[$v['priceid']]['rebate'];
			/*以下代码用于校验*/
			if($oinfo['param'][0]['settlement'] == '1'){
				$money = $money+$ticketType[$v['priceid']]['price'];
			}else{
				$money = $money+$ticketType[$v['priceid']]['discount'];
			}
			
			/*重组座位数据*/
			$seatData[$k] = array(
				'areaId' =>	$v['areaId'],
				'priceid'=>$v['priceid'],
				'price'=>$ticketType[$v['priceid']]['price'],/*票面价格*/
				'discount'=>$ticketType[$v['priceid']]['discount'],/*结算价格*/
				'seatid'=>	$v['seatid'],

			);
			$num = $num++;
			if($status[$k] == false){
				//$this->errCode = '400009';
				//$this->errMsg = '座椅信息更新失败';
				//error_insert('400009');
				return false;
				break;
			}
			if($count == $k+1){
				$flag=true;
			}
		}
		/*金额与数量校验*/
		if($money == $info['money']){
			$oinfo['subtotal'] = $money;
		}else{
			$model->rollback();//事务回滚
			//$this->errCode = '400018';
			//$this->errMsg = '金额校验失败';
			return false;
		}
		/*写入订单信息*/
		$orderData = array(
			'pay'	=> $is_pay ? $is_pay : $oinfo['param'][0]['is_pay'],
			'status' => '1',
		);
		//是否为团队订单 
		if($info['type'] == '2' || $info['type'] == '4' || $info['type'] == '8' || $info['type'] == '9'){
			//个人允许底价结算,且有返佣
			$crmInfo = google_crm($plan['product_id'],$info['crm'][0]['qditem'],$info['crm'][0]['guide']);
			//严格验证渠道订单写入返利状态
			if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
				error_insert('400018');
				$model->rollback();
				return false;
			}
			//判断是否是底价结算
			if($crmInfo['group']['settlement'] == '1' || $crmInfo['group']['settlement'] == '3'){
				//存储待处理数据
				load_redis('lpush','PreOrder',$info['order_sn']);
			}
		}
		$newInfo = array('subtotal'	=> $oinfo['subtotal'],'type'=>$oinfo['type'],'checkin'=> $oinfo['checkin'],'sub_type'=>$oinfo['sub_type'],'num'=>$oinfo['num'],'data' => $seatData,'crm' => $oinfo['crm'],'pay' => $is_pay ? $is_pay : $oinfo['param'][0]['is_pay'],'param'=> $info['param']);

		$state = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$info['order_sn'],'status'=>array('not in','1,9')))->save($orderData);
		$oinfo = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn'=>$info['order_sn']))->save(array('info' => serialize($newInfo)));
		//dump($flag);dump($state);dump($oinfo);
		/*记录售票员操作日志*/
		if($flag && $state && $oinfo){
			$model->commit();//提交事务
			return $info['order_sn'];
		}else{
			//error_insert('400006');
			$model->rollback();//事务回滚
			//$this->errCode = '400006';
			//$this->errMsg = '订单写入失败';
			return false;
		}
	}
	/**************************************************窗口订单****************************************************/
	/*快捷售票
	*数据处理
	*@apram $pinfo array 数据
	*@param $scena int 场景 1窗口2渠道版3网站4微信5api
	*@param $uinfo array 当前用户信息
	*/
	function quick($pinfo,$scena,$uinfo = null){
		if(empty($pinfo) || empty($scena)){error_insert('400001');return false;}
		$info = json_decode($pinfo,true);
		if(empty($info)){error_insert('400002');return false;}
		//获取订单初始数据
		$scena = Order::is_scena($scena,$info['param'][0]['is_pay']);
		//判断是否选择的是微信或支付宝刷卡支付
		//1现金2余额3签单4支付宝5微信支付6划卡
		if(in_array($info['param'][0]['is_pay'],array('4','5'))){$is_seat = '2';}else{$is_seat = '1';}
		$sn = Order::quick_order($info,$scena,$uinfo,$is_seat);
		if($sn != false){
			$sn = array('sn' => $sn,'is_pay' => $info['param'][0]['is_pay'],'money'=>$info['subtotal']);
		}
		return $sn;
	}
	/**
	 * 窗口通过支付宝和微信扫码付款
	 */
	function sweep_pay_seat($info,$oinfo){
		//获取座位区域信息
		$oinfo['info'] = unserialize($oinfo['info']);
		$seat = $oinfo['info']['data'];
		//判断支付提交场景，窗口自动排座，窗口选座
		//选座售票 
		if($info['order_type'] == '1'){
			$status = Order::choose_seat($seat,$oinfo,'',1,$info['pay_type']);
		}
		//快捷售票
		if($info['order_type'] == '2'){
			$status = Order::quickSeat($seat,$oinfo,'',1,$info['seat_type'],$info['pay_type']);
		}
		return $status;
	}
	/**************************************************微信、网站**************************************************/
	/*
	* 微信散客 网站散客  自助机散客
	* @param $info array 客户端提交数据
	* 用户表中默认新增 微信 官网 自助机
	*/
	function mobile($pinfo,$scena = null,$uinfo = null){//dump($pinfo);
		if(empty($pinfo) || empty($scena)){error_insert('400001');return false;}
		$info = json_decode($pinfo,true);
		if(empty($info)){error_insert('400002');return false;}
		/*$seat = Order::area_group($info['data']);//dump($seat);
		//TODO   临时写法
        $proconf = cache('ProConfig');
        $plan = F('Plan_'.$info['plan_id']);
		//判断是否写入配额
        //检测是否开启配额个人不检测配额
        if($proconf['quota']){
            check_quota($info['plan_id'],$plan['product_id'],$uinfo['qditem']);
        }*/
        $scena = Order::is_scena($scena);
		return Order::quick_order($info,$scena,$uinfo,2); 
	}
	/*微信/官网网页订单支付 
	* @param $info array 客户端提交数据
	* @param $oinfo array 订单数据
	*/
	function mobile_seat($info,$oinfo){
		//获取座位区域信息
		$param = unserialize($oinfo['info']);
		$seat = $param['data'];
		//判断订单类型 pay_type seat_type
		//是否是预订单  政府的单子可选择手动排座还是自动排座 其它预定的单子默认手动排座
		if($param['param'][0]['pre'] == '1' && $param['param'][0]['gov'] == '1'){
			$info['seat_type'] = $info['seat_type'];
		}elseif($param['param'][0]['pre'] == '1'){
			$info['seat_type'] = '2';
		}
		$status = Order::quickSeat($seat,$oinfo,'',1,$info['seat_type'],$info['pay_type']);
		return $status;
	}
	/**************************************************渠道订单****************************************************/
	function channel($pinfo,$scena,$uinfo = null){
		if(empty($pinfo) || empty($scena)){error_insert('400001');return false;}
		$info = json_decode($pinfo,true);
		if(empty($info) || if_plan($info['plan_id']) == false){error_insert('400002');return false;}
		//获取订单初始数据
		$scena = Order::is_scena($scena);
		return Order::quick_order($info,$scena,$uinfo,2); 
	}
	/*渠道订单支付
	* @param $info array 客户端提交数据
	* @param $oinfo array 订单数据
	*/
	function channel_seat($info,$oinfo){
		//获取座位区域信息
		$param = unserialize($oinfo['info']);
		$seat = $param['data'];
		//判断订单类型 pay_type seat_type
		//是否是预订单  政府的单子可选择手动排座还是自动排座 其它预定的单子默认手动排座
		if($param['param'][0]['pre'] == '1' && $param['param'][0]['gov'] == '1'){
			$info['seat_type'] = $info['seat_type'];
		}elseif($param['param'][0]['pre'] == '1'){
			$info['seat_type'] = '2';
		}
		if($oinfo['product_type'] == '1'){
			//剧场
			$status = Order::quickSeat($seat,$oinfo,'',1,$info['seat_type'],$info['pay_type']);
		}else{
			//景区漂流
			$status = Order::quickScenic($oinfo,'','',1,$info['pay_type']);
		}
		return $status;
	}
	/**************************************************API订单****************************************************/
	/*
	*API订单处理
	*@param $info array 接口提交数据
	*@param $uinfo array 接口APP数据
	*/
	function orderApi($info,$scena,$uinfo){
		//获取订单初始数据
		$scena = Order::is_scena($scena);
		$channel = $uinfo['is_pay'] == 2 ? 1 : 2;
		$return = Order::quick_order($info,$scena,$uinfo,1,$channel); 
		if($return != false){
			M('ApiOrder')->add(array('app_sn'=>$info['app_sn'],'order_sn'=>$return));
		}
		return $return;
	}
	/**************************************************通用处理****************************************************/
	
	/*生成订单号
	* @param $info 订单信息
	* @param $uinfo 当前用户信息
	* @param $is_seat 是否立即排座 1 立即排座 2不排做 只返回订单号
	* @param $channel 是否是团队使用授信额支付 后期窗口出票也可以使用授信额度
	*/
	function quick_order($info, $scena, $uinfo, $is_seat = '1',$channel = null){
		//获取销售计划
		$plan = F('Plan_'.$info['plan_id']);
		if(empty($plan)){error_insert('400005');return false;}
		
		$seat = Order::area_group($info['data'],$plan['product_id'],$info['param'][0]['settlement'],$plan['product_type'],$info['child_ticket']);
		/*景区*/
		if($plan['product_type'] <> '1'){
			if(Order::check_salse_num($info['plan_id'],$plan['quotas'],$plan['seat_table'],$seat['num']) == '400'){
				error_insert('400031');
				return false;
			}
		}
		if($seat == false){
			error_insert('400003');
			return false;
		}
		//订单金额校验
		if(bccomp((float)$info['subtotal'],(float)$seat['money'],2) <> 0){
          	error_insert('418'.$seat['money']);
			return false;
        }
		
		//获取订单号 1代表检票方式1人一票2 一团一票
		$printtype = $info['checkin'] ? $info['checkin'] : 1;
		$sn = get_order_sn($plan['id'],$printtype);

		$model = new Model();
		$model->startTrans();
		//写入订单 窗口订单直接写入并完成排座   微信等其它场景只写入订单  支付完成后排座
		//重新组合订单详情  增加座位信息  与选做订单详情一至
		$newData = array(
			'subtotal'		=> $info['subtotal'],
			'checkin'		=> $info['checkin'],
			'data'			=> $seat,
			'child_ticket'	=> $info['child_ticket'],
			'crm'			=> $info['crm'],
			'pay'			=> $scena['pay'],				
			'param'			=> $info['param'],	
		);
		//添加联系人信息 TODO
		if($info['crm'][0]['phone']){
			Order::is_tourists($info['crm'][0]['contact'],$info['crm'][0]['phone'],$info['crm'][0]['qditem'],$plan['product_id'],$plan['id']);
		}
		/*写入订单信息*/
		$orderData = array(
			'order_sn' 		=> $sn,
			'plan_id' 		=> $plan['id'],
			'product_type'	=> $plan['product_type'],//产品类型
			'product_id' 	=> $plan['product_id'],
			'user_id' 		=> $uinfo['id'],
			'id_card'		=> $info['param'][0]['id_card'],
			'channel_id' 	=> $info['crm'][0]['qditem'],
			'guide_id'		=> $info['crm'][0]['guide'],
			'number'		=> $seat['num'],
			'money' 		=> $info['subtotal'],
			'phone'			=> $info['crm'][0]['phone'],//取票人手机
			'sub_type'		=> $info['sub_type'] ? $info['sub_type'] : 2,//补贴对象
			'take'			=> $info['crm'][0]['contact'],
			'activity'		=> $info['param'][0]['activity'],//活动标记	
		);
		$orderData = array_merge($orderData,$scena);
		$state = $model->table(C('DB_PREFIX').'order')->add($orderData);
		$oinfo = $model->table(C('DB_PREFIX').'order_data')->add(array('oid'=>$state,'order_sn' => $sn,'info' => serialize($newData),'remark'=>$info['param'][0]['remark']));
		if($state && $oinfo){
			$model->commit();//提交事务
			if($is_seat == '1'){
				//窗口订单 开始排座 剧场
				$order_info = D('Item/Order')->where(array('order_sn'=>$sn))->relation(true)->find();
				if($plan['product_type'] == '1'){
					$status = Order::quickSeat($seat,$order_info,$info['sub_type'],$channel,'1',$scena['pay']);
				}else{
					$status = Order::quickScenic($order_info,$plan);
				}
				return $status;
			}else{
				return $sn;
			}
		}else{
			error_insert('400006');
			$model->rollback();//事务回滚
			return false;
		}
	}
	/**
	 * 景区快捷售票
	 * 批量写入检票数据
	 * @param $sub_type int 0散客或不存在返佣 1返给导游 2返给旅行社
	 * @param $is_pay int 是否改变支付方式 支付方式0未知1现金2余额3签单4支付宝5微信支付6划卡
	 */
	function quickScenic($info,$plan = null,$sub_type = '2',$channel = '0',$is_pay = null){
		$info['info'] = unserialize($info['info']);
		$createtime = time();
		$model = new Model();
		$model->startTrans();
		if(empty($plan)){
			$plan = F('Plan_'.$info['plan_id']);
		}
		$proconf = cache('ProConfig');
		$proconf = $proconf[$plan['product_id']]['1'];
		//扣除授信额度
		if($channel == '1' && $info['pay'] == '2'){
			//获取扣费条件
			$cid = money_map($info['channel_id']);
			//先消费后记录
			//$c_pay = $model->table(C('DB_PREFIX').'crm')->where(array('id'=>$cid))->setDec('cash',$info['money']);
			//验证客户余额是否够用
			$balance = M('Crm')->where(array('id'=>$cid,'cash'=>array('EGT',$info['money'])))->field('id')->find();
			if($balance){
				$crmData = array('cash' => array('exp','cash-'.$info['money']),'uptime' => time());
				$c_pay = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$cid))->setField($crmData);
				$data = array(
					'cash'		=>	$info['money'],
					'user_id'	=>	$info['user_id'],
					'crm_id'	=>	$cid,
					'createtime'=>	$createtime,
					'type'		=>	'2',
					'order_sn'	=>	$info['order_sn'],
					'balance'	=>  balance($cid),
				);
				$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->add($data);
				if($c_pay == false || $c_pay2 == false){
					error_insert('400008');
					$model->rollback();//事务回滚
					return false;
				}
			}else{
				error_insert('400008');
				$model->rollback();//事务回滚
				return false;
			}
		}else{
			//网银支付记录支付记录
		}
		$ticketType = F('TicketType'.$plan['product_id']);
		//构造打印数据
		//$dataList = Order::create_print($info['order_sn'],$info['info']['data']['area'],$plan);
		if($plan['product_type'] == '2'){
			$table = 'scenic';
		}else{
			$table = 'drifting';
		}
		$map = array('plan_id' => $info['plan_id'],'status'=>'0');//dump($info);
		foreach ($info['info']['data']['area'] as $key => $value) {
			/*判断是否联合售票*/
			if(!empty($info['info']['child_ticket'])){
				foreach ($info['info']['child_ticket'] as $ck => $cv) {
					$child_t[$cv['priceid']] = array(
						'priceid'	=>	$cv['priceid'],
						'priceName' =>	ticket_single($ticketType[$cv['priceid']]['single_id'],1),
						'price'     =>	$ticketType[$cv['priceid']]['price'],/*票面价格*/
						'discount'  =>	$ticketType[$cv['priceid']]['discount'],/*结算价格*/
					);
				}
				$remark = array(
					'remark_type' => '99',
					'remark'	=>	$child_t,
				);
			}else{
				$remark = print_remark($ticketType[$value['priceid']]['remark'],$plan['product_id']);
			}
			//获取票型数据
			$param = array(
				'plantime'	=>  date('Y-m-d ',$plan['plantime']),
				'games'	   	=>  $plan['games'],
				'product_name' => $plan['product_name'],
				'priceid'   =>	$value['priceid'],
				'priceName' =>	$ticketType[$value['priceid']]['name'],
				'price'     =>	$ticketType[$value['priceid']]['price'],/*票面价格*/
				'discount'  =>	$ticketType[$value['priceid']]['discount'],/*结算价格*/
				'remark_type'=>	$remark['remark_type'],
				'remark'	=>	$remark['remark'],
			);
			//计算消耗配额的票型 只有团队订单时才执行此项操作 21060118
			if($info['type'] == '2' || $info['type'] == '4' && $ticketType[$value['priceid']]['param']['quota'] <> '1'){
				$quota_num += $value['num'];
			}
			//计算补贴
			$rebate += $ticketType[$value['priceid']]['rebate']*$value['num'];
			$printList = array(
				'order_sn' => $info['order_sn'],
				'price_id'   =>	$value['priceid'],
				'sale' => serialize($param),
				'status' => '2',
				'createtime' => $createtime,
			);
			$state = $model->table(C('DB_PREFIX').$table)->where($map)->limit($value['num'])->lock(true)->save($printList);
			if($proconf['ticket_sms'] == '1'){
				$msg = $msg.$ticketType[$value['priceid']]['name'].$value['num']."张";
			}else{
				$msg = $info['number']."张";
			}
		}
		//获取售票信息
		$saleList = $model->table(C('DB_PREFIX').$table)->where(array('order_sn'=>$info['order_sn']))->field('id,ciphertext,sale,price_id')->select();
		foreach ($saleList as $ks => $vs) {
			$sale[$ks]=unserialize($vs['sale']);
			$dataList[] = array(
					'ciphertext' =>	$vs['ciphertext'],
					'priceid'=>	$sale[$ks]['priceid'],
					'price'=>$ticketType[$sale[$ks]['priceid']]['price'],
					'discount'=>$ticketType[$sale[$ks]['priceid']]['discount'],/*结算价格*/
					'id'	=>	$vs['id'],
					'plan_id' => $info['plan_id'],
					'child_ticket' => arr2string($child_t,'priceid'),
				);
		}
		//是否为团队订单 
		if($info['type'] == '2' || $info['type'] == '4' || $info['type'] == '8'){
			/*查询是否开启配额 读取是否存在不消耗配额的票型*/
			if($proconf['quota'] == '1'){
				if(in_array($info['type'],array('2','4'))){
					$up_quota = \Libs\Service\Quota::update_quota($quota_num,$info['info']['crm'][0]['qditem'],$info['plan_id']);
				}else{
					//TODO  全员营销的配额
					//$up_quota = \Libs\Service\Quota::up_full_quota($quota_num,$oInfo['crm'][0]['qditem'],$info['plan_id'],$oInfo['param'][0]['area']);
				}
				if($up_quota == '400'){
					error_insert('400012');
					$model->rollback();
					return false;
				}
			}
			//个人允许底价结算,且有返佣
			$crmInfo = google_crm($plan['product_id'],$info['info']['crm'][0]['qditem'],$info['info']['crm'][0]['guide']);
			//严格验证渠道订单写入返利状态
			if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
				error_insert('400018');
				$model->rollback();
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
				$teamData = array(
					'order_sn' 		=> $info['order_sn'],
					'plan_id' 		=> $info['plan_id'],
					'subtype'		=> '0',
					'product_type'	=> $info['product_type'],//产品类型
					'product_id' 	=> $info['product_id'],
					'user_id' 		=> $info['user_id'],
					'money'			=> $rebate,
					'number'		=> $info['number'],
					'guide_id'		=> $info['info']['crm'][0]['guide'],
					'qd_id'			=> $info['info']['crm'][0]['qditem'],
					'status'		=> '1',
					'type'			=> $type,//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
					'userid'		=> '0',
					'createtime'	=> $createtime,
					'uptime'		=> $createtime,
				);
				//窗口团队时判断是否是底价结算
				if($info['type'] == '2' && $proconf['settlement'] == '2'){
					$in_team = true;
				}else{
					$in_team = $model->table(C('DB_PREFIX'). 'team_order')->addAll($teamData);
					if(!$in_team){error_insert('400017');$model->rollback();return false;}
				}
			}
		}
		
		//更新订单详情
		//重新组合订单详情  增加座位信息  与选做订单详情一至
		$newData = array('subtotal'	=> $info['info']['subtotal'],'checkin'	=> $info['info']['checkin'],'data' => $dataList,'crm' => $info['info']['crm'],'pay' => $is_pay ? $is_pay : $info['info']['pay'],'param'	=> $info['info']['param'],'child_ticket'=>$child_t);
		$o_status = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn'=>$info['order_sn']))->setField('info',serialize($newData));
		//改变订单状态
		$status = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$info['order_sn']))->setField('status','1');
		if($state && $status){
			$model->commit();//提交事务
			if(!in_array($info['addsid'],array('1','6')) && $no_sms <> '1'){
			    //发送成功短信
				if($proconf['crm_sms']){$crminfo = Order::crminfo($plan['product_id'],$param['crm'][0]['qditem']);}	
				$msgs = array('phone'=>$info['info']['crm'][0]['phone'],'title'=>planShow($plan['id'],1,2),'remark'=>$msg,'num'=>$info['number'],'sn'=>$info['order_sn'],'crminfo'=>$crminfo,'product'=>$plan['product_name']);
				if($info['pay'] == '1' || $info['pay'] == '3'){
					Sms::order_msg($msgs,6);
				}else{
					Sms::order_msg($msgs,1);
				}
			}
			return $info['order_sn'];
		}else{
			error_insert('400006');
			$model->rollback();//事务回滚
			return false;
		}	
	}
	/*
	* 快捷 团队(非排座)  售票  排座
	* @param $seat array 票型及座位数量
	* @param $info string 客户端传递数据
	* @param $sub_type int 0散客或不存在返佣 1返给导游 2返给旅行社
	* @param $channel int 0 窗口自动排座 1渠道自动排座 2个人渠道商扣费 （进行相应的扣费操作）
	* @param $is_seat int 是否排座 1排座 2不排座
	* @param $is_pay int 是否改变支付方式 支付方式0未知1现金2余额3签单4支付宝5微信支付6划卡
	*/
	function quickSeat($seat, $info, $sub_type = '2', $channel = '0', $is_seat = '1', $is_pay = null){
		$plan = F('Plan_'.$info['plan_id']);
		$plan_param = unserialize($plan['param']);
		$createtime = time();
		$ticketType = F("TicketType".$plan['product_id']);
		$proconf = cache('ProConfig');
		$proconf = $proconf[$plan['product_id']]['1'];
		if(empty($ticketType) || empty($seat)){error_insert('4000076');return false;}
		/*多表事务*/
		$model = new Model();
		$model->startTrans();
		$flags = false;
		$money = 0;
		$rebate	= 0;
		/*==============================渠道版扣费 start===============================================*/
		if(in_array($channel,'1,4') && $info['pay'] == '2'){
			//获取扣费条件
			$cid = money_map($info['channel_id'],$channel);

			if($channel == '1'){
				//渠道商客户
				$db = M('Crm');
			}
			if($channel == '4'){
				//个人客户
				$db = M('User');
			}
			//先消费后记录验证客户余额是否够用
			$balance = $db->where(array('id'=>$cid,'cash'=>array('EGT',$info['money'])))->field('id')->find();
			if($balance){
				$crmData = array('cash' => array('exp','cash-'.$info['money']),'uptime' => time());
				if($channel == '1'){
					//渠道商客户
					$c_pay = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$cid))->setField($crmData);
				}
				if($channel == '4'){
					//个人客户
					$c_pay = $model->table(C('DB_PREFIX')."user")->where(array('id'=>$cid))->setField($crmData);
				}				
				$data = array(
					'cash'		=>	$info['money'],
					'user_id'	=>	$info['user_id'],
					'guide_id'	=>	$cid,//TODO  这个貌似没什么意义
					'addsid'	=>	$info['addsid'],
					'crm_id'	=>	$cid,
					'createtime'=>	$createtime,
					'type'		=>	'2',
					'order_sn'	=>	$info['order_sn'],
					'balance'	=>  balance($cid,$channel),
					'tyint'		=>	$channel,//客户类型1企业4个人
				);
				$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->add($data);
				if($c_pay == false || $c_pay2 == false){
					error_insert('400008');
					$model->rollback();//事务回滚
					return false;
				}
			}else{
				error_insert('400008');
				$model->rollback();//事务回滚
				return false;
			}
			//个人授信额支付
			/**
			 * 1、判断当前用户是企业员工 还是个人客户
			 * 2、调用支付
			 */
		}
		/*==============================渠道版扣费 end=================================================*/
		/*==============================自动排座开始 start =============================================*/
		if($is_seat == '1'){
			foreach ($seat['area'] as $k=>$v){
				//循环区域
				$count[$k] = count($v['seat']);//统计座椅个数
				foreach($v['seat'] as $ke=>$va){
					//检测是否有足够的座位 TODO   智能排座
					if(!empty($plan_param['auto_group'])){
						$auto[$k] = Autoseat::auto_group($plan_param['auto_group'],$k,$va['num'],$plan['product_id'],$plan['seat_table']);
					}else{
						$auto[$k] = "0";
					}
					//写入数据
					$map = array(
						'area' => $k,
						'group' => $auto[$k] ?  array('in',$auto[$k]) : '0',
						'status' => array('eq',0),
					);
					$data = array(
						'order_sn'=> $info['order_sn'],
						'soldtime'=> $createtime,
						'status'  => '2',
						'price_id'=> $va['priceid'],
						'sale'    => serialize(array('priceid'=>$va['priceid'],'price'=>$va['price'])),//售出信息 票型  单价
					);
					//计算消耗配额的票型 只有团队订单时才执行此项操作 21060118
					if($info['type'] == '2' || $info['type'] == '4' && $ticketType[$va['priceid']]['param']['quota'] <> '1'){
						$quota_num += $va['num'];
					}
					$status[$ke] = $model->table(C('DB_PREFIX').$plan['seat_table'])->where($map)->limit($va['num'])->lock(true)->save($data);
					/*以下代码用于校验*/
					$money = $money+$ticketType[$va['priceid']]['discount']*$va['num'];
					if(empty($status[$ke])){
						error_insert('400009');
						$model->rollback();//事务回滚
						return false;
						break;
					}
					if($count[$k] == $ke+1){
						$flag=true;
					}
					//统计订单座椅个数
					$number = (int)$number+$va['num'];
					if($proconf['ticket_sms'] == '1'){$msg = $msg.$ticketType[$va['priceid']]['name'].$va['num']."张";}
				}
				//按区域发送短信
				if($proconf['area_sms'] == '1'){$msg = $msg.areaName($k,1).$v['num']."张";}
			}
			/*座椅信息*/
			$seatInfo = $model->table(C('DB_PREFIX').$plan['seat_table'])->where(array('order_sn'=>$info['order_sn']))->field('order_sn,area,seat,sale')->select();
			/*更新售出信息*/
			$counts = count($seatInfo);//统计座椅个数
			//校验已排座位数与实际座位数是否相符合 不相符合直接返回false
			if($number <> $counts){
				error_insert('400010');
				$model->rollback();//事务回滚
				return false;
			}
			foreach ($seatInfo as $ks => $vs){
				//写入数据
				$maps = array('area'=>$vs['area'],'seat'=>$vs['seat'],'status' => array('eq',2));
				$sale[$ks]=unserialize($vs['sale']);
				//$seats = explode('-', $vs['seat']);
				$remark = print_remark($ticketType[$sale[$ks]['priceid']]['remark'],$plan['product_id']);
				$datas = array(
					'sale' => serialize(array(
						'plantime'=>date('Y-m-d ',$plan['plantime']).date(' H:i',$plan['starttime']),
						'area'=>areaName($vs['area'],1),
						'seat'=>Order::print_seat($vs['seat'],$plan['product_id'],$ticketType[$sale[$ks]['priceid']]['param']['ticket_print'],$ticketType[$sale[$ks]['priceid']]['param']['ticket_print_custom']),
						'games'=>$plan['games'],
						'priceid'=>$sale[$ks]['priceid'],
						'priceName'=>$ticketType[$sale[$ks]['priceid']]['name'],
						'price'=>$ticketType[$sale[$ks]['priceid']]['price'],/*票面价格*/
						'discount'=>$ticketType[$sale[$ks]['priceid']]['discount'],/*结算价格*/
						'remark_type'=>$remark['remark_type'],
						'remark'=>$remark['remark'],
					)),//售出信息 票型  单价
				);
				/*重组座位数据*/
				$seatData[$ks] = array(
					'areaId' =>	$vs['area'],
					'priceid'=>	$sale[$ks]['priceid'],
					'price'=>$ticketType[$sale[$ks]['priceid']]['price'],
					'discount'=>$ticketType[$sale[$ks]['priceid']]['discount'],/*结算价格*/
					'seatid'=>	$vs['seat']
				);
				$up[$ks] = $model->table(C('DB_PREFIX').$plan['seat_table'])->where($maps)->lock(true)->save($datas);
				if(empty($up[$ks])){
					error_insert('400011');
					$model->rollback();//事务回滚
					return false;
					break;
				}
				if($counts == $ks+1){
					$flags = true;
				}
			}
			//格式化订单详情
			$oInfo = unserialize($info['info']);
			//重新组合订单详情  增加座位信息  与选做订单详情一至
			$newData = array('subtotal'	=> $oInfo['subtotal'],'checkin'	=> $oInfo['checkin'],'data' => $seatData,'crm' => $oInfo['crm'],'pay' => $is_pay ? $is_pay : $oInfo['pay'],'param'	=> $oInfo['param']);
			$state = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$info['order_sn']))->setField(array('number'=>$counts,'status'=>1,'uptime'=>$createtime,'pay'=>$is_pay ? $is_pay : $oInfo['pay']));
			$o_status = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn'=>$info['order_sn']))->setField('info',serialize($newData));
			/*活动订单也消耗配额
			if(!empty($oInfo['param'][0]['activity'])){
				$up_quota = \Libs\Service\Quota::up_activity_quota($counts,$oInfo['crm'][0]['qditem'],$info['plan_id'],$oInfo['param'][0]['area']);
				if($up_quota == '400'){
					error_insert('400012');
					$model->rollback();
					return false;
				}
			}*/
			//是否为团队订单 
			if($info['type'] == '2' || $info['type'] == '4' || $info['type'] == '8' || $info['type'] == '9'){
				/*查询是否开启配额 读取是否存在不消耗配额的票型*/
				if($proconf['quota'] == '1'){
					if(in_array($info['type'],array('2','4'))){
						$up_quota = \Libs\Service\Quota::update_quota($quota_num,$oInfo['crm'][0]['qditem'],$info['plan_id']);
					}else{
						//TODO  全员营销的配额
						//$up_quota = \Libs\Service\Quota::up_full_quota($quota_num,$oInfo['crm'][0]['qditem'],$info['plan_id'],$oInfo['param'][0]['area']);
					}
					if($up_quota == '400'){
						error_insert('400012');
						$model->rollback();
						return false;
					}
				}
				//个人允许底价结算,且有返佣
				$crmInfo = google_crm($plan['product_id'],$oInfo['crm'][0]['qditem'],$oInfo['crm'][0]['guide']);
				//严格验证渠道订单写入返利状态
				if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
					error_insert('400018');
					$model->rollback();
					return false;
				}
				//判断是否是底价结算
				if($crmInfo['group']['settlement'] == '1' || $crmInfo['group']['settlement'] == '3'){
					//存储待处理数据
					load_redis('lpush','PreOrder',$info['order_sn']);
				}
			}
			$pre = true;$no_sms = '2';
		}else{
			//支付但不排座
			//更新订单 status=5支付但未排座 TODO  政企订单 TDOD
			$status_one = $info['status'] == 6 ? 6 : 5;
			if($status_one <> '6'){
				$state = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$info['order_sn']))->setField(array('status'=>$status_one));
			}else{
				$state = true;
			}
			//写入待处理订单提醒
			$pre = $model->table(C('DB_PREFIX').'pre_order')->add(array('order_sn'=>$info['order_sn'],'user_id'=>get_user_id(),'status'=>'1','createtime'=>$createtime));
			$flag=true;$flags = true;$o_status = true;$no_sms = 1;
		}
		if($flag && $flags && $state && $o_status && $pre){
			$model->commit();//提交事务
			//发送成功短信
			if(!in_array($info['addsid'],array('1','6')) && $no_sms <> '1'){
			    if($proconf['crm_sms']){
			    	$crminfo = Order::crminfo($plan['product_id'],$oInfo['crm'][0]['qditem']);
			    }
				$msgs = array('phone'=>$oInfo["crm"][0]['phone'],'title'=>planShows($plan['id']),'num'=>$counts,'remark'=>$msg,'sn'=>$info['order_sn'],'crminfo'=>$crminfo,'product'=>$plan['product_name']);
				//根据支付方式选择短信模板 
				$pay = $is_pay ? $is_pay : $oInfo['pay'];
				if($pay == '1' || $pay == '3'){
					Sms::order_msg($msgs,6);
				}else{
					Sms::order_msg($msgs,1);
				}
			}
			return $info['order_sn'];
		}else{
			//dump($flag);dump($flags);dump($state);dump($in_team);dump($up_quota);dump($pre);
			error_insert('400013');
			$model->rollback();//事务回滚
			return false;
		}	
	}
	/*景区、漂流构造打印和检票数据*/
	function create_print($sn,$data,$plan){
		$ticketType = F('TicketType'.$plan['product_id']);
		//构造门票打印数据
		foreach ($data as $key => $value) {
			$remark = print_remark($ticketType[$value['priceid']]['remark'],$plan['product_id']);
			//获取票型数据
			$param = array(
				'plantime'	=>  date('Y-m-d ',$plan['plantime']).date(' H:i',$plan['starttime']),
				'games'	   	=>  $plan['games'],
				'product_name' => $plan['product_name'],
				'priceid'   =>	$value['priceid'],
				'priceName' =>	$ticketType[$value['priceid']]['name'],
				'price'     =>	$ticketType[$value['priceid']]['price'],/*票面价格*/
				'discount'  =>	$ticketType[$value['priceid']]['discount'],/*结算价格*/
				'remark_type'=>	$remark['remark_type'],
				'remark'	=>	$remark['remark'],
			);
			$area[] = array(
				'price_name' => $ticketType[$value['priceid']]['name'],
				'number'	=> $value['num'],
			);
			$rebate += $ticketType[$value['priceid']]['rebate']*$value['num'];
			for ($i=0; $i < $value['num']; $i++) {
				$ciphertext = genRandomString();
				$printList[] = array(
					'order_sn' => $sn,
					'plan_id'=>	$plan['id'],
					'ciphertext' => $ciphertext,
					'price_id'   =>	$value['priceid'],
					'password' => Order::create_ticket_pwd($ciphertext,$plan['encry']),
					'sale' => serialize($param),
					'status' => '2',
					'createtime' => time(),
				);
			}
		}
		$dataList = array(
			'printList' => $printList,
			'rebate'	=>	$rebate,
			'area'		=>	$area,		
			);
		return $dataList;
	}
	/**
	 * 校验景区门票可售数量
	 * @param  int $plan_id  计划id
	 * @param  int $plan_num 峰值
	 * @param  int $num      
	 * @return [type]        
	 */
	function check_salse_num($plan_id,$plan_num,$table,$num){
		//计算已售数量
		$map = array(
			'plan_id' => $plan_id,
			'status'  => array('in','2,99,66'),
			);
		$sale_num = D(ucwords($table))->where($map)->count()+$num;
		if((int)$plan_num >= (int)$sale_num){
			return 200;
		}else{
			return 400;
		}
	}
	/**
	 * 生成检票密码 景区漂流使用
	 * @param  string $ciphertext 明文密码
	 * @param  string $encry      场次密钥
	 * @return [type]             [description]
	 */
	function create_ticket_pwd($ciphertext,$encry){
		return md5($ciphertext . md5($encry));
	}
	
	/*渠道预定单排座
	* @param $oinfo  订单信息
	*/
	function add_seat($oinfo){
		$model = new Model();
		$model->startTrans();
		$flag=false;
		$flags=false;
		$createtime = time();
		$param = unserialize($oinfo['info']);
		$seat = $param['data'];
		//读取订单对应计划
		$plan = F('Plan_'.$oinfo['plan_id']);
		$proconf = cache('ProConfig');
		$proconf = $proconf[$plan['product_id']]['1'];
		$plan_param = unserialize($plan['param']);
		$ticketType = F("TicketType".$plan['product_id']);
		foreach ($seat['area'] as $k=>$v){
			//循环区域
			$count[$k] = count($v['seat']);//统计座椅个数
			foreach($v['seat'] as $ke=>$va){
				//检测是否有足够的座位 TODO   智能排座
				if(!empty($plan_param['auto_group'])){
					$auto[$k] = Autoseat::auto_group($plan_param['auto_group'],$k,$va['num'],$plan['product_id'],$plan['seat_table']);
				}else{
					$auto[$k] = "0";
				}
				//写入数据
				$map = array(
					'area' => $k,
					'group' => $auto[$k] ?  array('in',$auto[$k]) : '0',
					'status' => array('eq',0),
				);
				$data = array(
						'order_sn'=> $oinfo['order_sn'],
						'soldtime'=> $createtime,
						'status'  => '2',
						'price_id' => $va['priceid'],
						'sale'    => serialize(array('priceid'=>$va['priceid'],'price'=>$va['price'])),//售出信息 票型  单价
				);
				$status[$ke] = $model->table(C('DB_PREFIX').$plan['seat_table'])->where($map)->limit($va['num'])->lock(true)->save($data);
				//计算订单返佣金额
				$rebate = $rebate+$ticketType[$va['priceid']]['rebate']*$va['num'];
				/*以下代码用于校验*/
				$money = $money+$ticketType[$va['priceid']]['discount']*$va['num'];
				if(empty($status[$ke])){echo "12";
					$model->rollback();//事务回滚
					return false;
					break;
				}
				if($count[$k] == $ke+1){
					$flag=true;
				}
				//统计订单座椅个数
				$number = (int)$number+$va['num'];
				//按票型发送短信
				if($proconf[$plan['product_id']]['1']['ticket_sms']){$msg = $msg.$ticketType[$va['priceid']]['name'].$va['num']."张";}
			}
			//按区域发送短信
			if($proconf[$plan['product_id']]['1']['area_sms']){$msg = $msg.areaName($k,1).$v['num']."张";}
		}
		/*金额校验
		if($money == $info['subtotal']){
			$subtotal = $money;
		}else{
			$model->rollback();//事务回滚
			return false;
		}*/
		/*座椅信息*/
		$seatInfo = $model->table(C('DB_PREFIX').$plan['seat_table'])->where(array('order_sn'=>$oinfo['order_sn']))->field('order_sn,area,seat,sale')->select();
		/*更新售出信息*/
		$counts = count($seatInfo);//统计座椅个数
		//校验已排座位数与实际座位数是否相符合 不相符合直接返回false
		if($number <> $counts){
			$model->rollback();//事务回滚
			return false;
		}
		foreach ($seatInfo as $ks => $vs){
			//写入数据
			$maps = array('area'=>$vs['area'],'seat'=>$vs['seat'],'status' => array('eq',2));
			$sale[$ks]=unserialize($vs['sale']);
			$remark = print_remark($ticketType[$sale[$ks]['priceid']]['remark'],$plan['product_id']);
			$datas = array(
				'sale' => serialize(array('plantime'=>date('Y-m-d ',$plan['plantime']).date(' H:i',$plan['starttime']),
										'area'=>areaName($vs['area'],1),
										'seat'=>Order::print_seat($vs['seat'],$plan['product_id'],$ticketType[$sale[$ks]['priceid']]['param']['ticket_print'],$ticketType[$sale[$ks]['priceid']]['param']['ticket_print_custom']),
										'games'=>$plan['games'],
										'priceid'=>$sale[$ks]['priceid'],
										'priceName'=>$ticketType[$sale[$ks]['priceid']]['name'],
										'price'=>$ticketType[$sale[$ks]['priceid']]['price'],/*票面价格*/
										'discount'=>$ticketType[$sale[$ks]['priceid']]['discount'],/*结算价格*/
										'remark_type'=>$remark['remark_type'],
										'remark'=>$remark['remark'],
									)),//售出信息 票型  单价
			);
			/*重组座位数据*/
			$seatData[$ks] = array(
				'areaId' =>	$vs['area'],
				'priceid'=>	$sale[$ks]['priceid'],
				'price'=>$ticketType[$sale[$ks]['priceid']]['price'],
				'discount'=>$ticketType[$sale[$ks]['priceid']]['discount'],/*结算价格*/
				'seatid'=>	$vs['seat']
			);
			$up[$ks] = $model->table(C('DB_PREFIX').$plan['seat_table'])->where($maps)->lock(true)->save($datas);
			if(empty($up[$ks])){
				$model->rollback();//事务回滚
				return false;
				break;
			}
			if($counts == $ks+1){
				$flags = true;
			}
		}
		//格式化订单详情
		//$oInfo = unserialize($info['info']);dump($oInfo);
		//重新组合订单详情  增加座位信息  与选做订单详情一至
		$newData = array('subtotal'	=> $param['subtotal'],'checkin'	=> $param['checkin'],'data' => $seatData,'crm' => $param['crm'],'pay' => $is_pay ? $is_pay : $param['pay'],'param'	=> $param['param']);
		$state = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$oinfo['order_sn']))->setField(array('number'=>$counts,'status'=>1,'uptime'=>$createtime));
		$ostate = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn'=>$oinfo['order_sn']))->setField('info',serialize($newData));
		//个人不允许底价结算
		$crmInfo = google_crm($plan['product_id'],$param['crm'][0]['qditem']);
		//严格验证渠道订单写入返利状态
		if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
			error_insert('400018');
			$model->rollback();
			return false;
		}
		//判断是否是底价结算['group']['settlement']
		if($crmInfo['group']['settlement'] == '1'){
			load_redis('lpush','PreOrder',$info['order_sn']);
			/*
			$teamData = array(
				'order_sn' 		=> $oinfo['order_sn'],
				'plan_id' 		=> $oinfo['plan_id'],
				'subtype'		=> '0',
				'product_type'	=> $oinfo['product_type'],//产品类型
				'product_id' 	=> $plan['product_id'],
				'user_id' 		=> $oinfo['user_id'],
				'money'			=> $rebate,
				'number'		=> $counts,
				'guide_id'		=> $param['crm'][0]['guide'],
				'qd_id'			=> $param['crm'][0]['qditem'],
				'status'		=> '1',
				'type'			=> '2',//TODO  先写死
				'userid'		=> '0',
				'createtime'	=> $createtime,
				'uptime'		=> $createtime,
			);
			$in_team = $model->table(C('DB_PREFIX'). 'team_order')->add($teamData);
			if(!$in_team){error_insert('400017');$model->rollback();return false;}
			*/
		}
		//dump($flag);dump($flags);dump($state);dump($in_team);dump($oinfo);
		if($state && $flag && $flags && $ostate){
			$model->commit();//提交事务
			//发送成功短信
			if($proconf['crm_sms']){$crminfo = Order::crminfo($plan['product_id'],$param['crm'][0]['qditem']);}	
			$msgs = array('phone'=>$param["crm"][0]['phone'],'title'=>planShows($plan['id']),'remark'=>$msg,'num'=>$counts,'sn'=>$oinfo['order_sn'],'crminfo'=>$crminfo);
			if($oinfo['pay'] == '1' || $oinfo['pay'] == '3'){
				Sms::order_msg($msgs,6);
			}else{
				Sms::order_msg($msgs,1);
			}
			return $oinfo;
		}else{
			$model->rollback();//事务回滚
			return false;
		}	
	}
	/**
	 * 小商品售出
	 */
	function sales_goods($pinfo){
		$info['info'] = unserialize($info['info']);
		$createtime = time();
		$model = new Model();
		$model->startTrans();
		if(empty($plan)){
			$plan = F('Plan_'.$info['plan_id']);
		}
		$proconf = cache('ProConfig');
		$ticketType = F('TicketType'.$plan['product_id']);
		//构造打印数据
		//$dataList = Order::create_print($info['order_sn'],$info['info']['data']['area'],$plan);
		$table = 'sales_goods';
		$map = array('plan_id' => $info['plan_id'],'status'=>'0');//dump($info);
		foreach ($info['info']['data']['area'] as $key => $value) {
			/*判断是否联合售票*/
			if(!empty($info['info']['child_ticket'])){
				foreach ($info['info']['child_ticket'] as $ck => $cv) {
					$child_t[$cv['priceid']] = array(
						'priceid'	=>	$cv['priceid'],
						'priceName' =>	ticket_single($ticketType[$cv['priceid']]['single_id'],1),
						'price'     =>	$ticketType[$cv['priceid']]['price'],/*票面价格*/
						'discount'  =>	$ticketType[$cv['priceid']]['discount'],/*结算价格*/
					);
				}
				$remark = array(
					'remark_type' => '99',
					'remark'	=>	$child_t,
				);
			}else{
				$remark = print_remark($ticketType[$value['priceid']]['remark'],$plan['product_id']);
			}
			//获取票型数据
			$param = array(
				'plantime'	=>  date('Y-m-d ',$plan['plantime']),
				'games'	   	=>  $plan['games'],
				'product_name' => $plan['product_name'],
				'priceid'   =>	$value['priceid'],
				'priceName' =>	$ticketType[$value['priceid']]['name'],
				'price'     =>	$ticketType[$value['priceid']]['price'],/*票面价格*/
				'discount'  =>	$ticketType[$value['priceid']]['discount'],/*结算价格*/
				'remark_type'=>	$remark['remark_type'],
				'remark'	=>	$remark['remark'],
			);
			//计算消耗配额的票型 只有团队订单时才执行此项操作 21060118
			if($info['type'] == '2' || $info['type'] == '4' && $ticketType[$value['priceid']]['param']['quota'] <> '1'){
				$quota_num += $value['num'];
			}
			//计算补贴
			$rebate += $ticketType[$value['priceid']]['rebate']*$value['num'];
			$printList = array(
				'order_sn' => $info['order_sn'],
				'price_id'   =>	$value['priceid'],
				'sale' => serialize($param),
				'status' => '2',
				'createtime' => $createtime,
			);
			$state = $model->table(C('DB_PREFIX').$table)->where($map)->limit($value['num'])->lock(true)->save($printList);
			if($proconf[$plan['product_id']]['1']['ticket_sms'] == '1'){
				$msg = $msg.$ticketType[$value['priceid']]['name'].$value['num']."张";
			}else{
				$msg = $info['number']."张";
			}
		}
		//获取售票信息
		$saleList = $model->table(C('DB_PREFIX').$table)->where(array('order_sn'=>$info['order_sn']))->field('id,ciphertext,sale,price_id')->select();
		foreach ($saleList as $ks => $vs) {
			$sale[$ks]=unserialize($vs['sale']);
			$dataList[] = array(
					'ciphertext' =>	$vs['ciphertext'],
					'priceid'=>	$sale[$ks]['priceid'],
					'price'=>$ticketType[$sale[$ks]['priceid']]['price'],
					'discount'=>$ticketType[$sale[$ks]['priceid']]['discount'],/*结算价格*/
					'id'	=>	$vs['id'],
					'plan_id' => $info['plan_id'],
					'child_ticket' => arr2string($child_t,'priceid'),
				);
		}
		//是否为团队订单 
		if($info['type'] == '2' || $info['type'] == '4' || $info['type'] == '8'){
			/*查询是否开启配额 读取是否存在不消耗配额的票型*/
			if($proconf[$plan['product_id']]['1']['quota'] == '1'){
				if(in_array($info['type'],array('2','4'))){
					$up_quota = \Libs\Service\Quota::update_quota($quota_num,$info['info']['crm'][0]['qditem'],$info['plan_id']);
				}else{
					//TODO  全员营销的配额
					//$up_quota = \Libs\Service\Quota::up_full_quota($quota_num,$oInfo['crm'][0]['qditem'],$info['plan_id'],$oInfo['param'][0]['area']);
				}
				if($up_quota == '400'){
					error_insert('400012');
					$model->rollback();
					return false;
				}
			}
			//个人允许底价结算,且有返佣
			$crmInfo = google_crm($plan['product_id'],$info['info']['crm'][0]['qditem'],$info['info']['crm'][0]['guide']);
			//严格验证渠道订单写入返利状态
			if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
				error_insert('400018');
				$model->rollback();
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
				$teamData = array(
					'order_sn' 		=> $info['order_sn'],
					'plan_id' 		=> $info['plan_id'],
					'subtype'		=> '0',
					'product_type'	=> $info['product_type'],//产品类型
					'product_id' 	=> $info['product_id'],
					'user_id' 		=> $info['user_id'],
					'money'			=> $rebate,
					'number'		=> $info['number'],
					'guide_id'		=> $info['info']['crm'][0]['guide'],
					'qd_id'			=> $info['info']['crm'][0]['qditem'],
					'status'		=> '1',
					'type'			=> $type,//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
					'userid'		=> '0',
					'createtime'	=> $createtime,
					'uptime'		=> $createtime,
				);
				//窗口团队时判断是否是底价结算
				if($info['type'] == '2' && $proconf[$plan['product_id']]['1']['settlement'] == '2'){
					$in_team = true;
				}else{
					$in_team = $model->table(C('DB_PREFIX'). 'team_order')->addAll($teamData);
					if(!$in_team){error_insert('400017');$model->rollback();return false;}
				}
			}
		}
		
		//更新订单详情
		//重新组合订单详情  增加座位信息  与选做订单详情一至
		$newData = array('subtotal'	=> $info['info']['subtotal'],'checkin'	=> $info['info']['checkin'],'data' => $dataList,'crm' => $info['info']['crm'],'pay' => $is_pay ? $is_pay : $info['info']['pay'],'param'	=> $info['info']['param'],'child_ticket'=>$child_t);
		$o_status = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn'=>$info['order_sn']))->setField('info',serialize($newData));
		//改变订单状态
		$status = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$info['order_sn']))->setField('status','1');
		if($state && $status){
			$model->commit();//提交事务
			if(!in_array($info['addsid'],array('1','6')) && $no_sms <> '1'){
			    //发送成功短信
				if($proconf[$plan['product_id']]['1']['crm_sms']){$crminfo = Order::crminfo($plan['product_id'],$param['crm'][0]['qditem']);}	
				$msgs = array('phone'=>$info['info']['crm'][0]['phone'],'title'=>planShow($plan['id'],1,2),'remark'=>$msg,'num'=>$info['number'],'sn'=>$info['order_sn'],'crminfo'=>$crminfo,'product'=>$plan['product_name']);
				if($info['pay'] == '1' || $info['pay'] == '3'){
					Sms::order_msg($msgs,6);
				}else{
					Sms::order_msg($msgs,1);
				}
			}
			return $info['order_sn'];
		}else{
			error_insert('400006');
			$model->rollback();//事务回滚
			return false;
		}
	}
	/*订单场景初始值 
	* 根据订单场景设置订单的初始值 场景+订单类型 新增场景值 
	* @param $scena 场景标识 11 窗口散客订单 12 窗口团队订单 22 渠道团队 23 微信散客订单
	* 创建场景1窗口选座 6窗口快捷 2渠道版3网站4微信5api 7自助设备
	* 订单类型1散客订单2团队订单4渠道版定单6政府订单8全员销售9三级分销 3小商品 5 物品租聘
	* 支付方式0未知1现金2余额3签单4支付宝5微信支付6划卡
	* 状态0为作废订单1正常2为渠道版订单未支付情况3已取消5已支付但未排座6政府订单7申请退票中9门票已打印11窗口订单创建成功但未排座
	*/
	function is_scena($param = null,$is_pay = null){
		switch ($param) {
			case '11':
				//窗口选座散客
				$return = array('type'=>1,'addsid'=>1,'pay'=>$is_pay ? $is_pay : '1','status'=>11,'createtime'=>time());
				break;
			case '12':
				//窗口选座团队
				$return = array('type'=>2,'addsid'=>1,'pay'=>$is_pay ? $is_pay : '1','status'=>11,'createtime'=>time());
				break;
			case '13':
				//小商品订单
				$return = array('type'=>3,'addsid'=>1,'pay'=>0,'status'=>2,'createtime'=>time());
				break;
			case '15':
				//物品租聘
				$return = array('type'=>5,'addsid'=>1,'pay'=>0,'status'=>2,'createtime'=>time());
				break;
			case '61':
				//窗口快捷散客
				$return = array('type'=>1,'addsid'=>6,'pay'=>$is_pay ? $is_pay : '1','status'=>11,'createtime'=>time());
				break;
			case '62':
				//窗口快捷团队
				$return = array('type'=>2,'addsid'=>6,'pay'=>$is_pay ? $is_pay : '1','status'=>11,'createtime'=>time());
				break;
			case '22':
				//渠道版普通团队
				$return = array('type'=>4,'addsid'=>2,'pay'=>2,'status'=>2,'createtime'=>time());
				break;
			case '26':
				//渠道版政企
				$return = array('type'=>6,'addsid'=>2,'pay'=>3,'status'=>6,'createtime'=>time());
				break;
			case '31':
				//网站散客
				$return = array('type'=>1,'addsid'=>3,'pay'=>0,'status'=>2,'createtime'=>time());
			case '41':
				//微信散客
				$return = array('type'=>1,'addsid'=>4,'pay'=>5,'status'=>11,'createtime'=>time());
				break;
			case '42':
				//微信团队
				$return = array('type'=>2,'addsid'=>4,'pay'=>2,'status'=>2,'createtime'=>time());
				break;
			case '46':
				//微信政企 默认支付方式为未知因为存在微信支付和窗口支付两种方式
				$return = array('type'=>6,'addsid'=>4,'pay'=>0,'status'=>2,'createtime'=>time());
				break;
			case '48':
				//全员销售
				$return = array('type'=>8,'addsid'=>4,'pay'=>5,'status'=>2,'createtime'=>time());
				break;
			case '49':
				//三级分销
				$return = array('type'=>9,'addsid'=>4,'pay'=>5,'status'=>2,'createtime'=>time());
				break;
			case '51':
				//API散客
				$return = array('type'=>1,'addsid'=>5,'pay'=>1,'status'=>2,'createtime'=>time());
				break;
			case '52':
				//API团队
				$return = array('type'=>2,'addsid'=>5,'pay'=>2,'status'=>2,'createtime'=>time());
				break;
		}
		return $return;
	}
	/*返回客户信息
	*@param $product_id int 产品ID
	* $crm_id 渠道商id
	*/
	function crminfo($product_id,$crm_id){
		$crm = F('Crm'.$product_id);
		$info = $crm[$crm_id];
		$return = "电话".$info['phone'];
		return $return;
	}
	/**
	 * 格式化座位号
	 * @param  string $seat        座位号
	 * @param  int $product_id  产品id
	 * @param  int $ticket_type 票型特殊打印标记
	 * @param  int $custom      自定义票面
	 * @return [type]              [description]
	 */
	function print_seat($seat,$product_id,$ticket_type = null,$custom = null){
		$seats = explode('-', $seat);
		if($ticket_type == '1'){
			//定义座位号方式
			$proconf = cache('ProConfig');
			$proconf = $proconf[$product_id]['1'];
			switch ($proconf['print_seat']) {
				case '1':
					$seat = $seats[0].'排'.$seats[1].'号';
					break;
				case '2':
					$seat = $seats[0].'排';
					break;
				case '3':
					$seat = $custom;
					break;
				default :
					$seat = $seats[0].'排'.$seats[1].'号';
					break;
			}
		}else{
			$seat = $seats[0].'排'.$seats[1].'号';
		}
		if($proconf['print_mouth'] == '1'){
			return Order::print_mouth($seats[0]).$seat;
		}else{
			return $seat;
		}
	}
	/**
	 * 自定义入场口
	 * @param  int $row 座位排号
	 * @return [type]      [description]
	 */
	function print_mouth($row){
		//$map = "<=10|10<$row<=17|18<=$row<21";
		if($row <= '10'){
			return "一楼";
		}elseif('10'<$row && $row<='17'){
			return "二楼";
		}elseif('17'<$row && $row<='21'){
			return "三楼";
		}
	}
	
	/**区域组合
	*@param $area 订单区域数据
	*@param $settlement int 结算方式
	*@param $product_id int 产品id
	*@param $produc_type int 产品类型
	*@param $child_ticket array 联票子票型
	*return $seat 包含座椅区域信息 及座椅数量 
	*/
	function area_group($area,$product_id,$settlement,$product_type,$child_ticket){
		if(empty($area)){error_insert('400004');return false;}
		if(!empty($child_ticket)){
			foreach ($child_ticket as $key => $value) {
				$price += $value['price'];
			}	
		}
		/*重新组合区域*/
		foreach($area as $k=>$v){
			if($product_type == '1'){
				$seat['area'][$v['areaId']]['seat'][$k]=array(
					'priceid'=>$v['priceid'],
					'price'=>$v['price'],
					'num'=>$v['num'],
				);
				$seat['area'][$v['areaId']]['num'] += $v['num'];
				$seat['area'][$v['areaId']]['areaId'] = $v['areaId'];
				$seat['area'][$v['areaId']]['price'] = $v['price'];
				$seat['num'] += $seat['area'][$v['areaId']]['num'];
			}else{
				//景区、漂流
				$seat['area'][$v['priceid']] = array(
					'priceid'=>$v['priceid'],
					'price'=>$v['price'],
					'num'=>$v['num'],
				);
				$seat['num'] += $seat['area'][$v['priceid']]['num'];
			}
			//计算订单金额
			$money = Order::amount($v['priceid'],$v['num'],$v['areaId'],$product_id,$settlement);
			if($price != '0'){
				$child_moeny = $price*$v['num'];
			}else{
				$child_moeny = 0;
			}
			if($money != '404'){
				$seat['moneys'] += $money['moneys']+$child_moeny;
				$seat['poor'] += $money['poor']+$child_moeny;
				$seat['money'] += $money['money']+$child_moeny;
			}else{
				return false;
			}
		}
		return $seat;
	}
	/**
	 * 计算订单金额  不相信客户端的计算程序 2016-2-16
	 * @param  int $priceid    价格id
	 * @param  int $number     数量
	 * @param  int $area       区域id 校验区域与价格id是否匹配
	 * @param  int $product_id 产品id
	 * @param  int $type 	   1票面价金额2结算价金额
	 * @return  订单金额
	 */
	function amount($priceid,$number,$area,$product_id,$type = '1'){
		//获取价格缓存
		$ticket = F('TicketType'.$product_id);
		if(empty($ticket)){
			return false;
		}
		$price = $ticket[$priceid]['price'];/*票面价格*/
		$discount = $ticket[$priceid]['discount'];/*结算价格*/
		//计算金额
		if($type == '1'){
			//票面价计算
			$money = $price*$number;
			$poor = 0;//优惠金额
			$moneys = $money;//票面金额
		}else{
			//结算价计算
			$money = $discount*$number;
			$poor = $price - $discount;//优惠金额
			$moneys = $price*$number;//票面金额
		}
		$data = array(
			'money'	=>	$money,
			'moneys'=>	$moneys,
			'poor'	=>	$poor*$number,
			);
		return $data;
	}
	/*判断是不是新的游客
	*@param $phone 电话
	*@param $cid 渠道商id
	*@param $name 渠道商名称
	*@param $product_id 产品id
	*@param $plan_id 计划id
	*return true
	*/
	function is_tourists($name,$phone,$cid,$product_id,$plan_id){
		//判断联系人是否是新增联系人 根据手机号码判断 @印象大红袍
		$db = M('CommonContact');
		if($cid){
			$map = array('phone'=>$phone,'cid'=>$cid);
		}else{
			$map = array('phone'=>$phone);
		}
		$judge = $db->where($map)->find();
		if($judge == false){
			//TODO 设置联系人  不直接成为常用联系人  需要在添加常用联系人时判断是否存在
			$db->add(array('name'=>$name,'phone'=>$phone,'cid'=>$cid,'product_id'=>$product_id,'plan_id'=>$plan_id,'createtime'=>time(),'status'=>0));
		}
		return true;
	}
	/*根据订单号重发短信
    *@param $sn  订单号
    *return 出票员以及出票时间
    */
    function repeat_sms($sn = null){
        if(empty($sn)){return false;}else{
            $info = M('SmsLog')->where(array('order_sn'=>$sn))->find();
            if(empty($info)){
                echo "未找到订单";
            }else{
            	$msgs = array('phone'=>$info['phone'],'content'=>$info['content']);
                Sms::order_msg($msgs,8);
                return true;
            }
        }
    }

    /**
	 * 政府手动排座
	 */
	function govSeat($pinfo){
		$info = json_decode($pinfo,true);//dump($info);	
		if(empty($info)){error_insert('400002');return false;}
		$model = new Model();
		$model->startTrans();
		$flag=false;
		$flags=false;
		$money = 0;
		$rebate	= 0;
		$createtime = time();
		//读取订单
		$oinfo = D('Order')->where(array('order_sn'=>$info['sn']))->relation(true)->find();
		if($oinfo['status'] == '9' || $oinfo['status'] == '1'){
			error_insert('400014');
			$model->rollback();//事务回滚
			return false;
		}
		//读取订单对应计划
		$plan = F('Plan_'.$oinfo['plan_id']);
		$proconf = cache('ProConfig');
		$proconf = $proconf[$plan['product_id']]['1'];
		if(empty($plan)){error_insert('400005');$model->rollback();return false;}
		$ticketType = F("TicketType".$plan['product_id']);
		$count = count($info['data']);//统计座椅个数
		foreach ($info['data'] as $k=>$v){
			$map = array(
				'area' => $v['areaId'],
				'seat' => $v['seatid'],
				'status' => array('not in','2,99'), //政企订单可排预留座位,
			);
			$data = array(
				'order_sn' => $oinfo['order_sn'],
				'soldtime' => $createtime,
				'status'   => '2',
				'sale'	   => serialize(array('plantime'=>date('Y-m-d ',$plan['plantime']).date(' H:i',$plan['starttime']),
											'area'=>areaName($v['areaId'],1),
											'seat'=>Order::print_seat($v['seatid'],$plan['product_id'],$ticketType[$v['priceid']]['param']['ticket_print'],$ticketType[$v['priceid']]['param']['ticket_print_custom']),
											'games'=>$plan['games'],
											'priceid'=>$v['priceid'],
											'priceName'=>$ticketType[$v['priceid']]['name'],
											'price'=>$ticketType[$v['priceid']]['price'],/*票面价格*/
											'discount'=>$ticketType[$v['priceid']]['discount'],/*结算价格*/
									)),//售出信息 票型  单价
			);
			/*重组座位数据*/
			$seatData[$k] = array(
				'areaId' =>	$v['areaId'],
				'priceid'=> $v['priceid'],
				'price'=>$ticketType[$v['priceid']]['price'],
				'discount'=>$ticketType[$v['priceid']]['discount'],/*结算价格*/
				'seatid'=>	$v['seatid'],
			);
			//计算订单返佣金额
			$rebate += $ticketType[$v['priceid']]['rebate'];
			$status[$k]=$model->table(C('DB_PREFIX').$plan['seat_table'])->where($map)->save($data);
			/*以下代码用于校验*/
			if($status[$k] == false){
				error_insert('400009');
				break;
			}
			if($count == $k+1){
				$flag=true;
			}
			$area = $v['areaId'];
		}	
		//短信内容 只有单区域
		$msg = areaName($area,1).$count."张";
		//重组当前订单
		$hdata = unserialize($oinfo['info']);
		foreach($hdata['data']['area'] as $ke=>$va){
			//删除原有的区域数据
			if($va['areaId'] ==  $info['aid']){
				//unset($hdata['data']['area'][$ke]);
				unset($hdata['data']);
				/*政企排座特殊，以排区域ID
				if($hdata['param'][0]['area']){
					$hdata['param'][0]['area'] = $hdata['param'][0]['area'].','.$info['aid'];
				}else{
					$hdata['param'][0]['area'] = $info['aid'];
				}*/
			}
		}
		//检测该订单是否有补贴
		if($oinfo['type'] == '4'){
			$crmInfo = google_crm($plan['product_id'],$hdata['crm'][0]['qditem']);
			//严格验证渠道订单写入返利状态
			if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
				error_insert('400018');
				$model->rollback();
				return false;
			}
			//判断是否是底价结算['group']['settlement']
			if($crmInfo['group']['settlement'] == '1'){
				/*
				$teamData = array(
					'order_sn' 		=> $oinfo['order_sn'],
					'plan_id' 		=> $plan['id'],
					'product_type'	=> $oinfo['product_type'],//产品类型
					'product_type'	=> $plan['product_type'],//产品类型
					'product_id' 	=> $plan['product_id'],
					'user_id' 		=> $oinfo['user_id'],
					'money'			=> $rebate,
					'guide_id'		=> $hdata['crm'][0]['guide'],
					'qd_id'			=> $hdata['crm'][0]['qditem'],
					'status'		=> '1',
					'number' 		=> $count,
					'type'			=> $hdata['type'] == '2' ? $hdata['sub_type'] : '2',//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
					'createtime'	=> $createtime,
					'uptime'		=> $createtime,
				);
				$in_team = $model->table(C('DB_PREFIX'). 'team_order')->add($teamData);
				*/
			}
		}
		//得到新数组
		$hdata['data'] = $seatData;
		$up_order = $model->table(C('DB_PREFIX').'order')->where(array('order_sn' =>$info['sn']))->setField('status','1');
		$states = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn' =>$info['sn']))->setField('info',serialize($hdata));
		if($up_order && $states && $flag){
			$model->commit();//提交事务
			//发送成功短信
			if($proconf['crm_sms']){$crminfo = Order::crminfo($plan['product_id'],$param['crm'][0]['qditem']);}
			$msgs = array('phone'=>$hdata["crm"][0]['phone'],'title'=>planShows($plan['id']),'num'=>$count,'remark'=>$msg,'sn'=>$info['sn'],'crminfo'=>$crminfo);
			//手动排座分为政府和渠道
			if($oinfo['pay'] == '1' || $oinfo['pay'] == '3'){
				Sms::order_msg($msgs,6);
			}else{
				Sms::order_msg($msgs,1);
			}
			return true;
		}else{
			error_insert('400006');
			$model->rollback();//事务回滚
			return false;
		}
	}
	
	/**
	 * 小商品订单
	 */
	function goods_order(){
		//商品名称、价格、数量、总价、单号
		//
	}
}