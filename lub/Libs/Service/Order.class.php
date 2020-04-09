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
    public $error = '';
	/**************************************************选座订单****************************************************/
	/**
	 * 选座订单
	 * @param  string $pinfo     请求数据包
	 * @param $scena int 场景 1窗口2渠道版3网站4微信5api
	 * @param $uinfo array 当前用户信息
	 */
	public function rowSeat($pinfo,$scena,$uinfo = null){
		$info = json_decode($pinfo,true);
		$plan = F('Plan_'.$info['plan_id']);
		if(empty($plan)){
			$this->error = "400005 : 销售计划已暂停销售...";
			return false;
		}
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
												'mouth' => self::print_mouth($plan['product_id'], $v['seatid']),
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
					$this->error = '400009 : 座椅信息更新失败';
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
						$this->error = '400019 : 结算方式不明确';
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
				$model->rollback();//事务回滚
				$this->error = '400018 : 金额校验失败';
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
				$model->rollback();
				$this->error = '渠道商信息获取失败';
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
			'activity'		=> empty($info['param'][0]['activity']) ? 0 : $info['param'][0]['activity'],//活动标记
		);
		$state = $model->table(C('DB_PREFIX').'order')->add($orderData);
		$newInfo = [
			'oid'=>$state,
			'order_sn' => $sn,
			'remark' =>	$info['param'][0]['remark'],
			'info' => serialize([
				'subtotal'	=> $info['subtotal'],
				'type'=>$info['type'],
				'checkin'=> $info['checkin'],
				'sub_type'=>$info['sub_type'],
				'num'=>$info['num'],
				'data' => $seatData,
				'crm' => $info['crm'],
				'pay' => $info['param'][0]['is_pay'] ? $info['param'][0]['is_pay'] : '1',
				'param'=> $info['param']
			])
		];
		$oinfo = $model->table(C('DB_PREFIX').'order_data')->add($newInfo);
		/*记录售票员操作日志*/
		if($flag && $state && $oinfo){
			$model->commit();//提交事务
			//写入第三方系统
			//\Api\Controller\ZhiyoubaoControlle::orderhandler($sn);
			$sn = array('sn' => $sn,'is_pay' => $info['param'][0]['is_pay'],'money'=>$info['subtotal']);
			return $sn;
		}else{
			$model->rollback();//事务回滚
			$this->error = '订单写入失败';
			return false;
		}	
	}
	/**
	 * 选座排座
	 */
	public function choose_seat($seat, $info, $sub_type = '2', $channel = '0', $is_pay = null){
		$plan = F('Plan_'.$info['plan_id']);
		if(empty($plan)){$this->error = "400005 : 销售计划已暂停销售...";return false;}
		if(empty($info['order_sn'])){
			$this->error = '未找到有效订单';
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
											'mouth' => self::print_mouth($plan['product_id'], $v['seatid']),
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
				$this->error = '400009 : 座椅信息更新失败';
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
			$this->error = '400018 : 金额校验失败';
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
				$this->error = '400019 : 结算方式不明确';				
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
			$model->rollback();//事务回滚
			//$this->errCode = '400006';
			$this->error = '400006 : 订单写入失败';
			return false;
		}
	}
	/**************************************************窗口订单****************************************************/
	/*快捷售票 数据处理
	*@apram $pinfo array 数据
	*@param $scena int 场景 1窗口2渠道版3网站4微信5api
	*@param $uinfo array 当前用户信息
	*/
	public function quick($pinfo, $scena, $uinfo = null){
		if(empty($pinfo) || empty($scena)){
			$this->error = '400001 : 数据传递失败,请重试...';
			return false;
		}
		$info = json_decode($pinfo,true);
		if(empty($info)){$this->error = '400002 : 部分数据丢失,解析失败...';return false;}
		//获取订单初始数据 
		$scena = Order::is_scena($scena, $info['param'][0]['is_pay']);
		
		//判断是否选择的是微信或支付宝刷卡支付 1现金2余额3签单4支付宝5微信支付6划卡
		if(in_array($info['param'][0]['is_pay'],array('4','5'))){$is_seat = '2';}else{$is_seat = '1';}
		$sn = Order::quick_order($info, $scena, $uinfo, $is_seat);
		if($sn != false){
			$return = array('sn' => $sn['order_sn'],'act'=>$sn['act'],'is_pay' => $info['param'][0]['is_pay'],'money'=>$info['subtotal']);
			
			return $return;
		}else{
			return $sn;
		}
		
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
	function mobile($pinfo,$scena = null,$uinfo = null,$is_seat = 2){
		if(empty($pinfo) || empty($scena)){$this->error = '400001 : 数据传递失败,请重试...';return false;}
		$info = json_decode($pinfo,true);
		if(empty($info)){$this->error = '400002 : 部分数据丢失,解析失败...';return false;}
        $scena = Order::is_scena($scena);
		return Order::quick_order($info,$scena,$uinfo,$is_seat); 
	}
	/*微信/官网网页订单支付 
	* @param $info array 客户端提交数据
	* @param $oinfo array 订单数据
	* @param $channel_type 2 企业/政府 客户 8个人客户全员分销
	* @param 只有企业客户和全员销售的个人客户允许授信额支付
	*/
	function mobile_seat($info,$oinfo,$channel_type = '2'){
		/*获取座位区域信息
		$param = unserialize($oinfo['info']);
		$seat = $param['data'];
		//判断订单类型 pay_type seat_type
		//是否是预订单  政府的单子可选择手动排座还是自动排座 其它预定的单子默认手动排座
		if($param['param'][0]['pre'] == '1' && $param['param'][0]['gov'] == '1'){
			$info['seat_type'] = $info['seat_type'];
		}elseif($param['param'][0]['pre'] == '1'){
			$info['seat_type'] = '2';
		}
		$status = Order::quickSeat($seat,$oinfo,'',$channel_type,$info['seat_type'],$info['pay_type']);
		return $status;*/

		//获取座位区域信息
		$param = unserialize($oinfo['info']);
		$seat = $param['data'];//dump($param);
		//判断订单类型 pay_type seat_type
		//是否是预订单  政府的单子可选择手动排座还是自动排座 其它预定的单子默认手动排座
		if($param['param'][0]['pre'] == '1' && $param['param'][0]['gov'] == '1'){
			$info['seat_type'] = $info['seat_type'];
		}elseif($param['param'][0]['pre'] == '1'){
			$info['seat_type'] = '2';
		}
		if($oinfo['product_type'] == '1'){
			//剧场
			$status = Order::quickSeat($seat,$oinfo,'',$channel_type,$info['seat_type'],$info['pay_type']);
		}else{
			//景区漂流 套票
			if((int)$param['param'][0]['atype'] === 5){
				$status = Order::packTicket($oinfo, '', '', $channel_type, $info['pay_type']);
			}else{
				$status = Order::quickScenic($oinfo, '', '', $channel_type, $info['pay_type']);
			}
		}
		return $status;
	}
	/**************************************************渠道订单****************************************************/
	function channel($pinfo,$scena,$uinfo = null,$act = '', $is_pay = ''){
		if(empty($pinfo) || empty($scena)){$this->error = '400001 : 数据传递失败,请重试...';return false;}
		$info = json_decode($pinfo,true);
		if(empty($info) || if_plan($info['plan_id']) == false){
			$this->error = '400002 : 部分数据丢失,解析失败或该渠道已停止销售...';
			return false;
		}
		//获取订单初始数据
		$scena = Order::is_scena($scena, $is_pay);
		
		switch ((int)$act) {
		 	case 5:
		 		//多产品组合套票
		 		return Order::pack_order($info,$scena,$uinfo,2,2,5); 
		 		break;
		 	case 8:
		 		//前台预约推送,直接排座
		 		return Order::quick_order($info, $scena, $uinfo, 2, 1, $act); 
		 		break;
		 	default:
		 		return Order::quick_order($info,$scena,$uinfo,2,2,$act); 
		 		break;
		 } 
		
	}
	/**
	 * 限制数量销售验证是否可售
	 */
	private function limited_order($actid,$plan,int $number)
	{
		//读取单场数量
		$tab = 'act_'.$actid.'_'.$plan;
		$sku = load_redis('get',$tab);
		if((int)$sku === 0 && $sku !== false){
			$this->error = '抱歉,余量不足!';
			return false;
		}
		if($sku !== false){
			$count = (int)$sku - $number;
			if($count < 0){
				$this->error = '抱歉,余量不足!';
				return false;
			}
			return true;
		}else{
			$param = M('Activity')->where(['id'=>$actid])->getField('param');
			$param = json_decode($param,true);
			$count = (int)$param['info']['number'] - $number;
			load_redis('set',$tab,$count);
			return true;
		}
	}
	/*渠道订单支付
	* @param $info array 客户端提交数据
	* @param $oinfo array 订单数据
	*/
	function channel_seat($info,$oinfo){
		//获取座位区域信息
		$param = unserialize($oinfo['info']);
		$seat = $param['data'];//dump($param);
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
			//景区漂流 套票
			if((int)$param['param'][0]['atype'] === 5){
				$status = Order::packTicket($oinfo,'','',1,$info['pay_type']);
			}else{
				$status = Order::quickScenic($oinfo,'','',1,$info['pay_type']);
			}
			
		}
		return $status;
	}
	/****************************************API订单**************************************************/
	/*
	*API订单处理
	*@param $info array 接口提交数据
	*@param $uinfo array 接口APP数据
	*@param $is_seat int 是否立即排座 1 立即排座 2不排做 只返回订单号
	*/
	function orderApi($info,$scena,$uinfo,$is_seat = '1'){
		//获取订单初始数据
		$scena = $this->is_scena($scena);
		$channel = $uinfo['is_pay'] == 2 ? 1 : 2;
		//判断是否还允许销售
		if(if_plan($info['plan_id']) == false){
			//场次已停止销售
			$this->error = '400005 : 销售计划已暂停销售...';
			return false;
		}
		$return = $this->quick_order($info,$scena,$uinfo,$is_seat,$channel);
		//dump($return);
		if($return != false){
			//M('ApiOrder')->add(array('app_sn'=>$info['app_sn'],'order_sn'=>$return['order_sn']));
		}
		return $return;
	}
	/************************************通用处理****************************************************/
	private function pack_order($info, $scena, $uinfo, $is_seat = '1',$channel = null)
	{
		//获取销售计划 以基准产品的销售计划为准
		$plan = F('Plan_'.$info['plan_id']);
		if(empty($plan)){$this->error = "400005 : 销售计划已暂停销售...";return false;}
		$seat = Order::pack_group($info['data'],$plan['product_id'],$info['param'][0]['settlement'],$plan['product_type'],$info['param'][0]['activity'],$channel);
		//dump($seat);
		/*景区*/
		if($plan['product_type'] <> '1'){
			if($this->check_salse_num($info['plan_id'],$plan['quotas'],$plan['seat_table'],$seat['num']) == '400'){
				$this->error = '400031 : 门票库存不足...';
				return false;
			}
		}
		if($seat == false){
			return false;
		}
		//订单金额校验
		if(bccomp((float)$info['subtotal'],(float)$seat['money'],2) <> 0){
          	$this->error = '400018 : 金额校验失败';
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
			$this->is_tourists($info['crm'][0]['contact'],$info['crm'][0]['phone'],$info['crm'][0]['qditem'],$plan['product_id'],$plan['id']);
		}
		/*校验身份证号码是否正确*/
		$id_card = strtoupper($info['param'][0]['id_card']);
		if(!empty($id_card)){
			if(!checkIdCard($id_card)){
				$this->error = '400030 : 身份证号码有误...';
				return false;
			}
		}
		/*写入订单信息*/
		$orderData = array(
			'order_sn' 		=> $sn,
			'plan_id' 		=> $plan['id'],
			'product_type'	=> $plan['product_type'],//产品类型
			'product_id' 	=> $plan['product_id'],
			'user_id' 		=> $uinfo['id'],
			'id_card'		=> $id_card,
			'channel_id' 	=> $info['crm'][0]['qditem'],
			'guide_id'		=> $info['crm'][0]['guide'],
			'number'		=> $seat['num'],
			'money' 		=> $info['subtotal'],
			'phone'			=> $info['crm'][0]['phone'],//取票人手机
			'sub_type'		=> $info['sub_type'] ? $info['sub_type'] : 2,//补贴对象
			'take'			=> $info['crm'][0]['contact'],
			'activity'		=> empty($info['param'][0]['activity']) ? 0 : $info['param'][0]['activity'],//活动标记	
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
					$status = $this->quickSeat($seat,$order_info,$info['sub_type'],$channel,$is_seat,$scena['pay']);
				}else{
					$status = $this->quickScenic($order_info,$plan);
				}
				return $status;
			}else{
				return $sn;
			}
		}else{
			$this->error = '400006 : 订单创建失败';
			$model->rollback();//事务回滚
			return false;
		}
	}
	/**
	 * @param    array        $info                 订单数据
	 * @param    array        $plan                 销售计划
	 * @param    int        $sub_type             [description]
	 * @param    array        $channel              渠道商信息
	 * @param    is_pay        $is_pay               支付方式
	 * @return   [type]                              [description]
	 */
	private function packTicket($info,$plan = null,$sub_type = '2',$channel = '0',$is_pay = null){
		$info['info'] = unserialize($info['info']);
		$createtime = time();
		$model = new Model();
		$model->startTrans();
		if(empty($plan)){
			$plan = F('Plan_'.$info['plan_id']);
		}
		$proconf = cache('ProConfig');
		$proconf = $proconf[$plan['product_id']]['1'];
		/*==============================渠道版扣费 start===============================================*/
		if(in_array($channel,['1','4']) && $is_pay == '2'){
			
			//获取产品信息
			$product = cache('Product');
			$product = $product[$plan['product_id']];//dump($product);
			$itemConf = cache('ItemConfig');
            if($itemConf[$product['item_id']]['1']['level_pay']){
				//开启分级扣款
            	//获取扣款连条
            	$payLink = crm_level_link($info['channel_id']);
            	//判断链条中所有人余额充足
            	 
            	//统一扣除订单金额，每天返利
            	
            	//渠道商客户
				$db = M('Crm');
				$payWhere = [
					'id'	=>	['in', implode(',',$payLink)],
					'cash'	=>	['EGT',$info['money']]
				];
				$balanceCount = $db->where($payWhere)->field('id')->count();
				if((int)$balanceCount === (int)count($payLink)){
					$crmData = array('cash' => array('exp','cash-'.$info['money']),'uptime' => time());
					$c_pay = $model->table(C('DB_PREFIX')."crm")->where(['id'=>['in',implode(',',$payLink)]])->setField($crmData);
					//TODO 不同级别扣款金额不同
					foreach ($payLink as $p => $l) {
						$data[] = array(
							'cash'		=>	$info['money'],
							'user_id'	=>	$info['user_id'],
							'guide_id'	=>	$l,//TODO  这个貌似没什么意义
							'addsid'	=>	$info['addsid'],
							'crm_id'	=>	$l,
							'createtime'=>	$createtime,
							'type'		=>	'2',
							'order_sn'	=>	$info['order_sn'],
							'balance'	=>  balance($l,$channel),
							'tyint'		=>	$channel,//客户类型1企业4个人
						);
					}
					$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->addAll($data);
					if($c_pay == false || $c_pay2 == false){
						$model->rollback();//事务回滚
						$this->error = '400008 : 扣费失败';
						return false;
					}
				}else{
					$model->rollback();//事务回滚
					$this->error = '400008 : 客户余额不足,或上级余额不足';
					return false;
				}
			}else{
				if($channel == '1'){
					//渠道商客户
					$db = M('Crm');
					//获取扣费条件
					$cid = money_map($info['channel_id'],$channel);
				}
				if($channel == '4'){
					//个人客户
					$db = M('User');
					$cid = $info['guide_id'];
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
						$model->rollback();//事务回滚
						$this->error = '400008 : 扣费失败';
						return false;
					}
				}else{
					//error_insert('400008');
					$model->rollback();//事务回滚
					$this->error = '400008 : 客户余额不足';
					return false;
				}
			}
		}elseif(in_array($channel,['1','4'])){
			//判断是否有权限使用其它支付方式
			//读取信息
			$crm = F('Crm');
			$crm = $crm[$info['info']['crm'][0]['qditem']];
			if(!in_array('2',explode(',',$crm['param']['ispay']))){
				$model->rollback();//事务回滚
				$this->error = '400008 : 未被支持的支付方式';
				return false;
			}
		}
		/*==============================渠道版扣费 end=================================================*/
		
		$ainfo = D('Activity')->where(['id'=>$info['info']['param'][0]['activity']])->field('id,type,param')->find();
		$aparam = json_decode($ainfo['param'],true);

		foreach ($info['info']['data']['area'] as $key => $value) {
			//获取销售计划
			if((int)$info['product_id'] != (int)$value['product']){

				$plan_id = D('Plan')->where(['plantime'=> $plan['plantime'],'status'=>2,'product_id'=>$value['product']])->getField('id');
				$plan = F('Plan_'.$plan_id);//dump($plan);
			}else{
				$plan = F('Plan_'.$info['plan_id']);
			}
			
			$table = $plan['seat_table'];
			$ticketType = F('TicketType'.$plan['product_id']);
			$map = array('plan_id' => $plan['id'],'status'=>'0');
			$remark = print_remark($ticketType[$value['priceid']]['remark'],$plan['product_id']);
			$printList = [];
			//获取票型数据
			$param = array(
				'plantime'	=>  date('Y-m-d ',$plan['plantime']),
				'games'	   	=>  $plan['games'],
				'product_name' => $aparam['info']['price']['name'],
				'priceid'   =>	$value['priceid'],
				'priceName' =>	$aparam['info']['price']['name'],
				'price'		=>	$aparam['info']['price']['price'],
				'discount'	=>	$aparam['info']['price']['discount'],
				'remark_type'=>	$remark['remark_type'],
				'remark'	=>	$remark['remark'],
			);
			$i = 0;
			for ($i=0; $i < (int)$value['num']; $i++) { 
				$printList[] = array(
					'order_sn' => $info['order_sn'],
					'plan_id'=>	$plan['id'],
					'ciphertext' => genRandomString(),
					'price_id'   =>	$value['priceid'],
					'sale' => serialize($param),
					'idcard' => empty($value['idcard']) ? '0' : $value['idcard'],
					'status' => '2',
					'createtime' => $createtime,
				);
			}
			//判断门票数据是否一致
			if((int)count($printList) <> (int)$info['number']){
				$model->rollback();//事务回滚
				$this->error = '400018 : 出票失败';
				return false;
			}
			//批量新增数据
			$state = $model->table(C('DB_PREFIX').$table)->where($map)->lock(true)->addAll($printList);
			//获取售票信息
			$saleList = $model->table(C('DB_PREFIX').$table)->where(array('order_sn'=>$info['order_sn']))->field('id,ciphertext,plan_id,sale,price_id,idcard')->select();
			foreach ($saleList as $ks => $vs) {
				$sale[$ks] = unserialize($vs['sale']);
				$dataList[] = array(
						'ciphertext' =>	$vs['ciphertext'],
						'priceid'=>	$sale[$ks]['priceid'],
						'price'=>$ticketType[$sale[$ks]['priceid']]['price'],
						'discount'=>$ticketType[$sale[$ks]['priceid']]['discount'],/*结算价格*/
						'id'	=>	$vs['id'],
						'idcard' => $vs['idcard'],
						'plan_id' => $vs['plan_id'],
						'child_ticket' => arr2string($child_t,'priceid'),
					);
				//统计身份证号
				$idcard[] = [
					'plan_id'		=>	$vs['plan_id'],
					'order_sn'		=>	$info['order_sn'],
					'idcard'		=>	$vs['idcard'],
					'ticket'		=>	$vs['ciphertext'],
					'activity_id'	=>	$info['info']['param'][0]['activity']
				];
			}
		}
		$msg = $info['number']."张";
		//是否为团队订单 
		if($info['type'] == '2' || $info['type'] == '4' || $info['type'] == '8'){
			/*查询是否开启配额 读取是否存在不消耗配额的票型*/
			if($proconf['quota'] == '1'){
				if(in_array($info['type'],array('2','4'))){
					$quota = new \Libs\Service\Quota();
					$up_quota = $quota->update_quota($quota_num,$info['info']['crm'][0]['qditem'],$info['plan_id'],$info['type']);
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
				$model->rollback();
				$this->error = '400018 : 客户信息查询失败';
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
		//判断活动属性 todo 读取活动属性
		
		if(!empty($info['info']['param'][0]['activity'])){
			$actInfo = D('Activity')->where(['id'=>$info['info']['param'][0]['activity']])->field('real,param')->find();
			$actParam = json_decode($actInfo['param'],true);
			if($actInfo['real'] && $actParam['info']['voucher'] == 'card'){
				$is_print = 1;
				$msgTpl = 8;//后期短信模板动态配置
				//写入身份证号
				D('IdcardLog')->addAll($idcard);
			}else{
				$is_print = 0;
				$msgTpl = 8;
				//写入身份证号
				D('IdcardLog')->addAll($idcard);
			}	
		}

		//更新订单详情
		//重新组合订单详情  增加座位信息  与选做订单详情一至
		$newData = array('subtotal'	=> $info['info']['subtotal'],'checkin'	=> $info['info']['checkin'],'data' => $dataList,'crm' => $info['info']['crm'],'pay' => $is_pay ? $is_pay : $info['info']['pay'],'param'	=> $info['info']['param'],'child_ticket'=>$child_t);
		$o_status = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn'=>$info['order_sn']))->setField('info',serialize($newData));
		//改变订单状态
		$status = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$info['order_sn']))->setField(['status'=>'1','pay'=>$newData['pay']]);
		if($state && $status){
			$model->commit();//提交事务
			if(!in_array($info['addsid'],array('1','6')) && $no_sms <> '1'){
			    /*发送成功短信*/
				if($proconf['crm_sms']){$crminfo = Order::crminfo($plan['product_id'],$param['crm'][0]['qditem']);}	
				$msgs = array('phone'=>$info['info']['crm'][0]['phone'],'title'=>planShow($plan['id'],1,2),'remark'=>$msg,'num'=>$info['number'],'sn'=>$info['order_sn'],'crminfo'=>$crminfo,'product'=>$plan['product_name']);
				if($info['pay'] == '1' || $info['pay'] == '3'){
					Sms::order_msg($msgs,6);
				}else{
					Sms::order_msg($msgs,1);
				}
			}
			//设置低金额报警 
			if(empty($cid) && $newData['pay'] == '2'){
				$checkCrm =  end($payLink);
			}else{
				$checkCrm = $cid;
			}
			\Libs\Service\Kpi::if_money_low($product['item_id'],$checkCrm,$info['money']);

			return $info['order_sn'];
		}else{
			error_insert('400006');
			$model->rollback();//事务回滚
			return false;
		}
	}
	/*生成订单号
	* @param $info 订单信息
	* @param $uinfo 当前用户信息
	* @param $is_seat 是否立即排座 1 立即排座 2不排做 只返回订单号
	* @param $channel 是否是团队使用授信额支付 后期窗口出票也可以使用授信额度
	* @param $actType 活动类型
	*/
	private function quick_order($info, $scena, $uinfo, $is_seat = '1',$channel = null,$actType = ''){
		//获取销售计划
		$plan = F('Plan_'.$info['plan_id']);
		if(empty($plan)){$this->error = "400005 : 销售计划已暂停销售...";return false;}
		//获取订单号 1代表检票方式1人一票2 一团一票
		$printtype = $info['checkin'] ? $info['checkin'] : 1;
		$sn = $info['order_sn'] ? $info['order_sn'] : get_order_sn($plan['id'],$printtype);

		$seat = $this->area_group($info['data'],$plan['product_id'],$info['param'][0]['settlement'],$plan['product_type'],$info['child_ticket'],$channel);//dump($seat);
		if((int)$seat['num'] > 200){$this->error = "400005 : 单笔订单门票数不能超过200...";return false;}
		/*景区*/
		if($plan['product_type'] <> '1'){
			if($this->check_salse_num($info['plan_id'],$plan['quotas'],$plan['seat_table'],$seat['num']) == '400'){
				$this->error = '400031 : 门票库存不足...';
				return false;
			}
		}
		if($seat == false){
			$this->error = '400031 : 数据校验失败...';
			return false;
		}
		//活动订单处理
		if(!empty($info['param'][0]['activity'])){
			if((int)$actType === 6){
				if(!Order::limited_order($info['param'][0]['activity'],$info['plan_id'],$seat['num'])){
					return false;
				}
				$afterData = [
					'number'	=>	$seat['num'],
					'tab'		=>	'act_'.$info['param'][0]['activity'].'_'.$info['plan_id']
				];
				load_redis('setex','limit_order_'.$sn,json_encode($afterData),1800);
			}
		}
		//订单金额校验
		if(bccomp((float)$info['subtotal'],(float)$seat['money'],2) <> 0){
          	$this->error = '400018 : 金额校验失败';
			return false;
        }
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
			$this->is_tourists($info['crm'][0]['contact'],$info['crm'][0]['phone'],$info['crm'][0]['qditem'],$plan['product_id'],$plan['id']);
		}
		/*校验身份证号码是否正确*/
		$id_card = strtoupper($info['param'][0]['id_card']);
		if(!empty($id_card)){
			if(!checkIdCard($id_card)){
				$this->error = '400030 : 身份证号码有误...';
				return false;
			}
		}
		/*校验当前登录用户和商户
		if(!checkUserToCrm($uinfo,$info['crm'][0]['qditem'])){
			return false;
		}*/
		/*写入订单信息*/
		$orderData = array(
			'order_sn' 		=> $sn,
			'plan_id' 		=> $plan['id'],
			'product_type'	=> $plan['product_type'],//产品类型
			'product_id' 	=> $plan['product_id'],
			'user_id' 		=> $uinfo['id'],
			'id_card'		=> $id_card,
			'channel_id' 	=> $info['crm'][0]['qditem'],
			'guide_id'		=> $info['crm'][0]['guide'],
			'number'		=> $seat['num'],
			'money' 		=> $info['subtotal'],
			'phone'			=> $info['crm'][0]['phone'],//取票人手机
			'sub_type'		=> $info['sub_type'] ? $info['sub_type'] : 2,//补贴对象
			'take'			=> $info['crm'][0]['contact'],
			'activity'		=> empty($info['param'][0]['activity']) ? 0 : $info['param'][0]['activity'],//活动标记	
			'openid'		=> isset($uinfo['openid']) ? $uinfo['openid'] : ''
		);
		$orderData = array_merge($orderData,$scena);
		$state = $model->table(C('DB_PREFIX').'order')->add($orderData);
		$oinfo = $model->table(C('DB_PREFIX').'order_data')->add(array('oid' => $state,'order_sn' => $sn,'info' => serialize($newData),'remark' => $info['param'][0]['remark']));
		
		if($state && $oinfo){
			$model->commit();//提交事务
			//设置订单完结有效期
			load_redis('setex','period_'.$sn,json_encode(array_merge($orderData,$newData)),1800);
			if($is_seat == '1'){
				//窗口订单 开始排座 剧场
				$order_info = D('Item/Order')->where(array('order_sn'=>$sn))->relation(true)->find();
				if($plan['product_type'] == '1'){
					$status = $this->quickSeat($seat,$order_info,$info['sub_type'],$channel,$is_seat,$scena['pay']);
				}else{
					$status = $this->quickScenic($order_info,$plan);
				}
				return $status;
			}else{
				return $sn;
			}
		}else{
			$this->error = '400006 : 订单创建失败';
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
	private function quickScenic($info,$plan = null,$sub_type = '2',$channel = '0',$is_pay = null){
		$info['info'] = unserialize($info['info']);
		$createtime = time();
		$model = new Model();
		$model->startTrans();
		if(empty($plan)){
			$plan = F('Plan_'.$info['plan_id']);
		}
		$proconf = cache('ProConfig');
		$proconf = $proconf[$plan['product_id']]['1'];
		/*==============================渠道版扣费 start===============================================*/
		if(in_array($channel,['1','4']) && $is_pay == '2'){
			
			//获取产品信息
			$product = cache('Product');
			$product = $product[$plan['product_id']];//dump($product);
			$itemConf = cache('ItemConfig');
            if($itemConf[$product['item_id']]['1']['level_pay']){
				//开启分级扣款
				
            	//获取扣款连条
            	$payLink = crm_level_link($info['channel_id']);
            	//判断链条中所有人余额充足
            	
            	//统一扣除订单金额，每天返利
            	
            	//渠道商客户
				$db = M('Crm');
				$payWhere = [
					'id'	=>	['in', implode(',',$payLink)],
					'cash'	=>	['EGT',$info['money']]
				];
				$balanceCount = $db->where($payWhere)->field('id')->count();
				if((int)$balanceCount === (int)count($payLink)){
					$crmData = array('cash' => array('exp','cash-'.$info['money']),'uptime' => time());
					$c_pay = $model->table(C('DB_PREFIX')."crm")->where(['id'=>['in',implode(',',$payLink)]])->setField($crmData);
					//TODO 不同级别扣款金额不同
					foreach ($payLink as $p => $l) {
						$data[] = array(
							'cash'		=>	$info['money'],
							'user_id'	=>	$info['user_id'],
							'guide_id'	=>	$l,//TODO  这个貌似没什么意义
							'addsid'	=>	$info['addsid'],
							'crm_id'	=>	$l,
							'createtime'=>	$createtime,
							'type'		=>	'2',
							'order_sn'	=>	$info['order_sn'],
							'balance'	=>  balance($l,$channel),
							'tyint'		=>	$channel,//客户类型1企业4个人
						);
					}
					$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->addAll($data);
					if($c_pay == false || $c_pay2 == false){
						$model->rollback();//事务回滚
						$this->error = '400008 : 扣费失败';
						return false;
					}
				}else{
					$model->rollback();//事务回滚
					$this->error = '400008 : 客户余额不足,或上级余额不足';
					return false;
				}
			}else{
				if($channel == '1'){
					//渠道商客户
					$db = M('Crm');
					//获取扣费条件
					$cid = money_map($info['channel_id'],$channel);
				}
				if($channel == '4'){
					//个人客户
					$db = M('User');
					$cid = $info['guide_id'];
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
						$model->rollback();//事务回滚
						$this->error = '400008 : 扣费失败';
						return false;
					}
				}else{
					//error_insert('400008');
					$model->rollback();//事务回滚
					$this->error = '400008 : 客户余额不足';
					return false;
				}
			}
		}elseif(in_array($channel,['1','4'])){
			//判断是否有权限使用其它支付方式
			
			//读取信息
			$crm = F('Crm');
			$crm = $crm[$info['info']['crm'][0]['qditem']];
			if(!in_array('2',explode(',',$crm['param']['ispay']))){
				$model->rollback();//事务回滚
				$this->error = '400008 : 未被支持的支付方式';
				return false;
			}
		}
		/*==============================渠道版扣费 end=================================================*/
		$ticketType = F('TicketType'.$plan['product_id']);
		//构造打印数据
		//$dataList = Order::create_print($info['order_sn'],$info['info']['data']['area'],$plan);
		
		$table = $plan['seat_table'];
		$map = array('plan_id' => $info['plan_id'],'status'=>'0');
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
			//改变景区售票入库模式
			//构造数据
			$i = 0;
			for ($i=0; $i < (int)$value['num']; $i++) { 
				$printList[] = array(
					'order_sn' => $info['order_sn'],
					'plan_id'=>	$info['plan_id'],
					'product_id'=>	$plan['product_id'],
					'ciphertext' => genRandomString(),
					'price_id'   =>	$value['priceid'],
					'sale' => serialize($param),
					'idcard' => $value['idcard'],
					'status' => '2',
					'createtime' => $createtime,
				);
			}
			if($proconf['ticket_sms'] == '1'){
				$msg = $msg.$ticketType[$value['priceid']]['name'].$value['num']."张";
			}else{
				$msg = $info['number']."张";
			}
		}
		//判断门票数据是否一致
		if((int)count($printList) <> (int)$info['number']){
			$model->rollback();//事务回滚
			$this->error = '400018 : 出票失败';
			return false;
		}
		//批量新增数据
		$state = $model->table(C('DB_PREFIX').$table)->where($map)->lock(true)->addAll($printList);
		//获取售票信息
		$saleList = $model->table(C('DB_PREFIX').$table)->where(array('order_sn'=>$info['order_sn']))->field('id,ciphertext,sale,price_id,idcard')->select();
		foreach ($saleList as $ks => $vs) {
			$sale[$ks] = unserialize($vs['sale']);
			$dataList[] = array(
					'ciphertext' =>	$vs['ciphertext'],
					'priceid'=>	$sale[$ks]['priceid'],
					'price'=>$ticketType[$sale[$ks]['priceid']]['price'],
					'discount'=>$ticketType[$sale[$ks]['priceid']]['discount'],/*结算价格*/
					'id'	=>	$vs['id'],
					'idcard' => $vs['idcard'],
					'plan_id' => $info['plan_id'],
					'child_ticket' => arr2string($child_t,'priceid'),
				);
			//统计身份证号
			$idcard[] = [
				'plan_id'		=>	$info['plan_id'],
				'order_sn'		=>	$info['order_sn'],
				'idcard'		=>	$vs['idcard'],
				'ticket'		=>	$vs['ciphertext'],
				'activity_id'	=>	$info['info']['param'][0]['activity']
			];
		}
		//是否为团队订单 
		if($info['type'] == '2' || $info['type'] == '4' || $info['type'] == '8'){
			/*查询是否开启配额 读取是否存在不消耗配额的票型*/
			if($proconf['quota'] == '1'){
				if(in_array($info['type'],array('2','4'))){
					$quota = new \Libs\Service\Quota();
					$up_quota = $quota->update_quota($quota_num,$info['info']['crm'][0]['qditem'],$info['plan_id'],$info['type']);
				}else{
					//TODO  全员营销的配额
					//$up_quota = \Libs\Service\Quota::up_full_quota($quota_num,$oInfo['crm'][0]['qditem'],$info['plan_id'],$oInfo['param'][0]['area']);
				}
				if($up_quota == '400'){
					$this->error = '400018 : 配额消耗失败';
					$model->rollback();
					return false;
				}
			}
			//个人允许底价结算,且有返佣
			$crmInfo = google_crm($plan['product_id'],$info['info']['crm'][0]['qditem'],$info['info']['crm'][0]['guide']);
			//严格验证渠道订单写入返利状态
			if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
				$model->rollback();
				$this->error = '400018 : 客户信息查询失败';
				return false;
			}
			//判断是否是底价结算
			if($crmInfo['group']['settlement'] == '1' || $crmInfo['group']['settlement'] == '3'){
				//存储待处理数据 回头处理多产品分销 TODO
				//load_redis('lpush','PreOrder',$info['order_sn']);
			}
		}
		//判断活动属性 todo 读取活动属性
		
		if(!empty($info['info']['param'][0]['activity'])){
			$real = D('Activity')->where(['id'=>$info['info']['param'][0]['activity']])->getField('real');
			if($real){
				$is_print = 1;
				$msgTpl = 8;//后期短信模板动态配置
				//写入身份证号
				D('IdcardLog')->addAll($idcard);
			}
		}

		//更新订单详情
		//重新组合订单详情  增加座位信息  与选做订单详情一至
		$newData = array('subtotal'	=> $info['info']['subtotal'],'checkin'	=> $info['info']['checkin'],'data' => $dataList,'crm' => $info['info']['crm'],'pay' => $is_pay ? $is_pay : $info['info']['pay'],'param'	=> $info['info']['param'],'child_ticket'=>$child_t);
		$o_status = $model->table(C('DB_PREFIX').'order_data')->where(array('order_sn'=>$info['order_sn']))->setField('info',serialize($newData));
		//改变订单状态
		$status = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$info['order_sn']))->setField(['status'=>'1','pay'=>$newData['pay']]);
		if($state && $status){
			$model->commit();//提交事务
			if(!in_array($info['addsid'],array('1','6')) && $no_sms <> '1'){
			    /*发送成功短信*/
				if($proconf['crm_sms']){$crminfo = Order::crminfo($plan['product_id'],$param['crm'][0]['qditem']);}	
				$msgs = array('phone'=>$info['info']['crm'][0]['phone'],'title'=>planShow($plan['id'],2,1),'remark'=>$msg,'num'=>$info['number'],'sn'=>$info['order_sn'],'crminfo'=>$crminfo,'pid'=>$plan['product_id'],'product'=>$plan['product_name']);
				if($info['pay'] == '1' || $info['pay'] == '3'){
					Sms::order_msg($msgs,6);
				}else{
					Sms::order_msg($msgs,1);
				}
			}
			//后置处理
			Order::afterOrder($info['order_sn']);
			//设置低金额报警 
			if(empty($cid) && $newData['pay'] == '2'){
				$checkCrm =  end($payLink);
			}else{
				$checkCrm = $cid;
			}
			return ['order_sn' => $info['order_sn'],'act'=> $oInfo['param'][0]['activity']];
		}else{
			error_insert('400006');
			$this->error = '400006 : 订单创建失败';
			$model->rollback();//事务回滚
			return false;
		}	
	}
	/**
	 * 订单形成后置处理
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2018-07-23
	 * @param    int        $sn                   订单号
	 */
	public function afterOrder($sn)
	{
		//限制数量销售更新余量
		$limit_order = json_decode(load_redis('get','limit_order_'.$sn),true);
		load_redis('decrby',$limit_order['tab'],$limit_order['number']);
		load_redis('delete','limit_order_'.$sn);
		//删除有效期标记
		load_redis('delete','period_'.$sn);
	}
	/*
	* 快捷 团队(非排座)  售票  排座
	* @param $seat array 票型及座位数量
	* @param $info string 客户端传递数据
	* @param $sub_type int 0散客或不存在返佣 1返给导游 2返给旅行社
	* @param $channel int 0 窗口自动排座 1渠道自动排座 4个人渠道商扣费 （进行相应的扣费操作）
	* @param $is_seat int 是否排座 1排座 2不排座
	* @param $is_pay int 是否改变支付方式 支付方式0未知1现金2余额3签单4支付宝5微信支付6划卡
	*/
	private function quickSeat($seat, $info, $sub_type = '2', $channel = '0', $is_seat = '1', $is_pay = null){
		$plan = F('Plan_'.$info['plan_id']);
		$plan_param = unserialize($plan['param']);
		$createtime = time();
		$ticketType = F("TicketType".$plan['product_id']);
		$proconf = cache('ProConfig');
		$proconf = $proconf[$plan['product_id']]['1'];
		$idcard = [];
		if(empty($ticketType) || empty($seat)){$this->error = '4000076 : 票型获取失败';return false;}
		/*多表事务*/
		$model = new Model();
		$model->startTrans();
		$flags = false;
		$money = 0;
		$rebate	= 0;
		$msgTpl = 1;//默认短信模板
		$quota_num = 0;//默认配额消耗量
		/*==============================渠道版扣费 start===============================================*/
		if(in_array($channel,['1','4']) && $is_pay == '2' && $info['money'] > 0){
			
			//获取产品信息
			$product = cache('Product');
			$product = $product[$plan['product_id']];//dump($product);
			$itemConf = cache('ItemConfig');
            if($itemConf[$product['item_id']]['1']['level_pay']){
				//开启分级扣款
				
            	//获取扣款连条
            	$payLink = crm_level_link($info['channel_id']);
            	//判断链条中所有人余额充足
            	//统一扣除订单金额，每天返利
            	
            	//渠道商客户
				$db = M('Crm');
				$payWhere = [
					'id'	=>	['in', implode(',',$payLink)],
					'cash'	=>	['EGT',$info['money']]
				];
				$balance = $db->where($payWhere)->field('id')->find();
				if($balance){
					$crmData = array('cash' => array('exp','cash-'.$info['money']),'uptime' => time());
					$c_pay = $model->table(C('DB_PREFIX')."crm")->where(['id'=>['in',implode(',',$payLink)]])->setField($crmData);
					//TODO 不同级别扣款金额不同
					foreach ($payLink as $p => $l) {
						$data[] = array(
							'cash'		=>	$info['money'],
							'user_id'	=>	$info['user_id'],
							'guide_id'	=>	$l,//TODO  这个貌似没什么意义
							'addsid'	=>	$info['addsid'],
							'crm_id'	=>	$l,
							'createtime'=>	$createtime,
							'type'		=>	'2',
							'order_sn'	=>	$info['order_sn'],
							'balance'	=>  balance($l,$channel),
							'tyint'		=>	$channel,//客户类型1企业4个人
						);
					}
					$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->addAll($data);
					if($c_pay == false || $c_pay2 == false){
						$model->rollback();//事务回滚
						$this->error = '400008 : 扣费失败';
						return false;
					}
				}else{
					$model->rollback();//事务回滚
					$this->error = '400008 : 客户余额不足,或上级余额不足';
					return false;
				}
			}else{
				if($channel == '1'){
					//渠道商客户
					$db = M('Crm');
					//获取扣费条件
					$cid = money_map($info['channel_id'],$channel);
					\Libs\Service\Kpi::if_money_low($product['item_id'],$cid,$info['money']);
				}
				if($channel == '4'){
					//个人客户
					$db = M('User');
					$cid = $info['guide_id'];
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
						$model->rollback();//事务回滚
						$this->error = '400008 : 扣费失败';
						return false;
					}
				}else{
					//error_insert('400008');
					$model->rollback();//事务回滚
					$this->error = '400008 : 客户余额不足';
					return false;
				}
			}
		}
		/*==============================渠道版扣费 end=================================================*/
		/*==============================自动排座开始 start =============================================*/
		if($is_seat == '1'){
			//辅助排座
			foreach ($seat['aux_seat'] as $s => $a) {
				//检测是否有足够的座位 TODO   智能排座 判断是否有相同区域不同票型的
				if(!empty($plan_param['auto_group'])){
					//不同票型，但相同区域合并
					$auto[$a['areaId']] = Autoseat::auto_group($plan_param['auto_group'],$a['areaId'],$a['num'],$plan['product_id'],$plan['seat_table']);
				}else{
					$auto[$a['areaId']] = "0";
				}
			}
			foreach ($seat['area'] as $k=>$v){
				//检测是否有足够的座位 TODO   智能排座
				if(!empty($plan_param['auto_group'])){
					$auto[$k] = Autoseat::auto_group($plan_param['auto_group'],$v['areaId'],$v['num'],$plan['product_id'],$plan['seat_table']);
				}else{
					$auto[$k] = "0";
				}
				//写入数据
				$map = array(
					'area' => $v['areaId'],
					'group' => $auto[$k] ?  array('in',$auto[$k]) : '0',
					'status' => array('eq',0),
				);
				/*校验身份证号码是否正确*/
				$id_card = $v['idcard'] ? strtoupper($v['idcard']) : 0;
				if(!empty($id_card)  && (int)$info['param'][0]['cert_type'] === 1){
					if(!checkIdCard($id_card)){
						$this->error = '400030 : 身份证号码有误...';
						$model->rollback();
						return false;
						break;
					}
				}
				$data = array(
					'order_sn'=> $info['order_sn'],
					'soldtime'=> $createtime,
					'status'  => '2',
					'price_id'=> $v['priceid'],
					'idcard'  => $id_card,
					'sale'    => serialize(array('priceid'=>$v['priceid'],'price'=>$v['price'])),//售出信息 票型  单价
				);
				//计算消耗配额的票型 只有团队订单时才执行此项操作 21060118
				if($info['type'] == '2' || $info['type'] == '4' && $ticketType[$v['priceid']]['param']['quota'] <> '1'){

					$quota_num += $v['num'];
				}
				$status[$k] = $model->table(C('DB_PREFIX').$plan['seat_table'])->where($map)->limit($v['num'])->lock(true)->save($data);
				//echo $model->table(C('DB_PREFIX').$plan['seat_table'])->_sql();
				/*以下代码用于校验*/
				$money = $money+$ticketType[$v['priceid']]['discount']*$v['num'];
				if(empty($status[$k])){
					$model->rollback();//事务回滚
					$this->error = '400009 : 排座失败';
					return false;
					break;
				}
				/*
				$log[$k] = $count[$k] .'-'.  $ke;
				dump($ke);
				TODO  这里似乎没有任何意义
				if($count[$k] == $ke+1){
					$flag = true;
				}*/
				$flag = true;
				//统计订单座椅个数
				$number = (int)$number+$v['num'];

				//按票型发送短信
				if($proconf['ticket_sms']){$msg = $msg.$ticketType[$v['priceid']]['name'].$v['num']."张";}
	
				//按区域发送短信
				if($proconf['area_sms']){
					$areaSms[$v['areaId']] = [
						'area' => $v['areaId'],
						'num'  => $areaSms[$v['areaId']]['num'] + $v['num']
					];
				}
			}
			//短信
			foreach ($areaSms as $ke => $va) {
				$msg = $msg.areaName($va['area'],1).$va['num']."张";
			}
			/*座椅信息*/
			$seatInfo = $model->table(C('DB_PREFIX').$plan['seat_table'])->where(array('order_sn'=>$info['order_sn']))->field('order_sn,area,seat,sale,idcard')->select();
			/*更新售出信息*/
			$counts = count($seatInfo);//统计座椅个数
			//校验已排座位数与实际座位数是否相符合 不相符合直接返回false
			if($number <> $counts){
				//error_insert('400010');
				$model->rollback();//事务回滚
				$this->error = '400010 : 排座失败';
				return false;
			}
			//格式化订单详情
			$oInfo = unserialize($info['info']);
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
						'mouth' => self::print_mouth($plan['product_id'], $vs['seat']),
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
					'areaId' 	=>	$vs['area'],
					'priceid'	=>	$sale[$ks]['priceid'],
					'idcard'	=>	$vs['idcard'],
					'price'		=>	$ticketType[$sale[$ks]['priceid']]['price'],
					'discount'	=>	$ticketType[$sale[$ks]['priceid']]['discount'],/*结算价格*/
					'seatid'	=>	$vs['seat']
				);
				//统计身份证号
				$idcard[] = [
					'plan_id'		=>	$info['plan_id'],
					'order_sn'		=>	$info['order_sn'],
					'idcard'		=>	$vs['idcard'],
					'ticket'		=>	$vs['seat'],
					'activity_id'	=>	$oInfo['param'][0]['activity']
				];
				$up[$ks] = $model->table(C('DB_PREFIX').$plan['seat_table'])->where($maps)->lock(true)->save($datas);
				if(empty($up[$ks])){
					//error_insert('400011');
					$model->rollback();//事务回滚
					$this->error = '400011 : 更新座椅状态失败';
					return false;
					break;
				}
				if($counts == $ks+1){
					$flags = true;
				}
			}//dump($flag);
			
			//判断活动属性 todo 读取活动属性
			if(!empty($oInfo['param'][0]['activity'])){
				$actInfo = D('Activity')->where(['id'=>$oInfo['param'][0]['activity']])->field('real,param')->find();
				$actParam = json_decode($actInfo['param'],true);
				if($actInfo['real'] && $actParam['info']['voucher'] == 'card'){
					$is_print = 1;
					$msgTpl = 8;//后期短信模板动态配置
					//写入身份证号
					D('IdcardLog')->addAll($idcard);
				}else{
					$is_print = 0;
					$msgTpl = 1;
					//写入身份证号
					D('IdcardLog')->addAll($idcard);
				}	
			}
			//重新组合订单详情  增加座位信息  与选做订单详情一至
			$newData = array('subtotal'	=> $oInfo['subtotal'],'checkin'	=> $oInfo['checkin'],'data' => $seatData,'crm' => $oInfo['crm'],'pay' => $is_pay ? $is_pay : $oInfo['pay'],'param'	=> $oInfo['param']);
			$state = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$info['order_sn']))->setField(array('number'=>$counts,'status'=>1,'uptime'=>$createtime,'pay'=>$is_pay ? $is_pay : $oInfo['pay'],'is_print'=> $is_print ? $is_print : '0'));
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
				if($proconf['quota'] == '1' && $quota_num > 0){
					$up_quota = \Libs\Service\Quota::update_quota($quota_num,$oInfo['crm'][0]['qditem'],$info['plan_id'],$plan['product_id'],$info['type']);
					if($up_quota == '400'){
						//error_insert('400012');
						$model->rollback();
						$this->error = '400012 : 销售配额不足';
						return false;
					}
				}
				//个人允许底价结算,且有返佣
				$crmInfo = google_crm($plan['product_id'],$oInfo['crm'][0]['qditem'],$oInfo['crm'][0]['guide']);
				//严格验证渠道订单写入返利状态
				if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
					//error_insert('400018');
					$model->rollback();
					$this->error = '400018 : 客户信息获取失败';
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
				$updata = [
					'pay'=>$is_pay ? $is_pay : $oInfo['pay'],
					'status'=>$status_one
				];
				$state = $model->table(C('DB_PREFIX').'order')->where(array('order_sn'=>$info['order_sn']))->setField($updata);
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
			    if(!empty($oInfo["crm"][0]['phone'])){
					$msgs = array('phone'=>$oInfo["crm"][0]['phone'],'title'=>planShows($plan['id']),'num'=>$counts,'remark'=>$msg,'sn'=>$info['order_sn'],'crminfo'=>$crminfo,'product'=>$plan['product_name']);
					/*根据支付方式选择短信模板 */
					$pay = $is_pay ? $is_pay : $oInfo['pay'];
					if($pay == '1' || $pay == '3'){
						Sms::order_msg($msgs,6);
					}else{
						Sms::order_msg($msgs,$msgTpl);
					}
			    }
			}

			//设置低金额报警 
			if(empty($cid) && $newData['pay'] == '2'){
				$checkCrm =  end($payLink);
			}else{
				$checkCrm = $cid;
			}
			\Libs\Service\Kpi::if_money_low($product['item_id'],$checkCrm,$info['money']);
			
			return ['order_sn' => $info['order_sn'],'act'=> $oInfo['param'][0]['activity']];
		}else{
			//dump($flag);dump($flags);dump($state);dump($in_team);dump($up_quota);dump($pre);
			//error_insert('400013');
			$model->rollback();//事务回滚
			$this->error = '400013 : 排座失败';
			return false;
		}	
	}
	//授信额扣费

	private function deduction($product_id,$channel_id)
	{
		//获取产品信息
		$product = cache('Product');
		$product = $product[$plan['product_id']];//dump($product);
		$itemConf = cache('ItemConfig');
        if($itemConf[$product['item_id']]['1']['level_pay']){
			//开启分级扣款
			
        	//获取扣款连条
        	$payLink = crm_level_link($info['channel_id']);
        	//判断链条中所有人余额充足
        	//统一扣除订单金额，每天返利
        	
        	//渠道商客户
			$db = M('Crm');
			$payWhere = [
				'id'	=>	['in', implode(',',$payLink)],
				'cash'	=>	['EGT',$info['money']]
			];
			$balance = $db->where($payWhere)->field('id')->find();
			if($balance){
				$crmData = array('cash' => array('exp','cash-'.$info['money']),'uptime' => time());
				$c_pay = $model->table(C('DB_PREFIX')."crm")->where(['id'=>['in',implode(',',$payLink)]])->setField($crmData);
				//TODO 不同级别扣款金额不同
				foreach ($payLink as $p => $l) {
					$data[] = array(
						'cash'		=>	$info['money'],
						'user_id'	=>	$info['user_id'],
						'guide_id'	=>	$l,//TODO  这个貌似没什么意义
						'addsid'	=>	$info['addsid'],
						'crm_id'	=>	$l,
						'createtime'=>	$createtime,
						'type'		=>	'2',
						'order_sn'	=>	$info['order_sn'],
						'balance'	=>  balance($l,$channel),
						'tyint'		=>	$channel,//客户类型1企业4个人
					);
				}
				$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->addAll($data);
				if($c_pay == false || $c_pay2 == false){
					$model->rollback();//事务回滚
					$this->error = '400008 : 扣费失败';
					return false;
				}
			}else{
				$model->rollback();//事务回滚
				$this->error = '400008 : 客户余额不足,或上级余额不足';
				return false;
			}
		}else{
			if($channel == '1'){
				//渠道商客户
				$db = M('Crm');
				//获取扣费条件
				$cid = money_map($info['channel_id'],$channel);
			}
			if($channel == '4'){
				//个人客户
				$db = M('User');
				$cid = $info['guide_id'];
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
					$model->rollback();//事务回滚
					$this->error = '400008 : 扣费失败';
					return false;
				}
			}else{
				//error_insert('400008');
				$model->rollback();//事务回滚
				$this->error = '400008 : 客户余额不足';
				return false;
			}
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
	
	
	/*渠道预定单排座
	* @param $oinfo  订单信息
	*/
	function add_seat($oinfo){
		$model = new Model();
		$model->startTrans();
		$flag=true;//TODO 无用变量了 暂时保留
		$flags=false;
		$createtime = time();
		$param = unserialize($oinfo['info']);
		$seat = $param['data'];
		//读取订单对应计划
		$plan = F('Plan_'.$oinfo['plan_id']);

		$proconf = cache('ProConfig');
		$proconf = $proconf[$plan['product_id']]['1'];
		$plan_param = unserialize($plan['param']);
		$ticketType = F("TicketType".$plan['product_id']);//dump($seat);
		foreach ($seat['area'] as $k=>$v){
			//检测是否有足够的座位 TODO   智能排座
			
			if(!empty($plan_param['auto_group'])){
				$auto[$k] = Autoseat::auto_group($plan_param['auto_group'],$v['areaId'],$v['num'],$plan['product_id'],$plan['seat_table']);
			}else{
				$auto[$k] = "0";
			}//dump($v);
			//写入数据
			$map = array(
				'area' => $v['areaId'],
				'group' => $auto[$k] ?  array('in',$auto[$k]) : '0',
				'status' => array('eq',0),
			);
			$data = array(
					'order_sn'=> $oinfo['order_sn'],
					'soldtime'=> $createtime,
					'status'  => '2',
					'price_id' => $v['priceid'],
					'sale'    => serialize(array('priceid'=>$v['priceid'],'price'=>$v['price'])),//售出信息 票型  单价
			);
			$status[$k] = $model->table(C('DB_PREFIX').$plan['seat_table'])->where($map)->limit($v['num'])->lock(true)->save($data);
			//计算订单返佣金额
			$rebate = $rebate+$ticketType[$v['priceid']]['rebate']*$v['num'];
			/*以下代码用于校验*/
			$money = $money+$ticketType[$v['priceid']]['discount']*$v['num'];
			if(empty($status[$k])){
				$model->rollback();//事务回滚
				$this->error = '400009 : 排座失败';
				return false;
				break;
			}
			/*
			if($count[$k] == $ke+1){
				$flag=true;
			}*/
			//统计订单座椅个数
			$number = (int)$number+$v['num'];
			//按票型发送短信
			if($proconf['ticket_sms']){$msg = $msg.$ticketType[$v['priceid']]['name'].$v['num']."张";}
	
			//按区域发送短信
			if($proconf['area_sms']){$msg = $msg.areaName($v['areaId'],1).$v['num']."张";}
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
			$this->error = '400009 : 座椅数量错误';
			return false;
		}
		foreach ($seatInfo as $ks => $vs){
			//写入数据
			$maps = array('area'=>$vs['area'],'seat'=>$vs['seat'],'status' => array('eq',2));
			$sale[$ks]=unserialize($vs['sale']);
			$remark = print_remark($ticketType[$sale[$ks]['priceid']]['remark'],$plan['product_id']);
			$datas = array(
				'idcard'=>	$vs['idcard'],
				'sale' => serialize(array('plantime'=>date('Y-m-d ',$plan['plantime']).date(' H:i',$plan['starttime']),
										'area'=>areaName($vs['area'],1),
										'seat'=>Order::print_seat($vs['seat'],$plan['product_id'],$ticketType[$sale[$ks]['priceid']]['param']['ticket_print'],$ticketType[$sale[$ks]['priceid']]['param']['ticket_print_custom']),
										'mouth' => self::print_mouth($plan['product_id'], $vs['seat']),
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
				'areaId' 	=>	$vs['area'],
				'priceid'	=>	$sale[$ks]['priceid'],
				'price'		=>	$ticketType[$sale[$ks]['priceid']]['price'],
				'discount'	=>	$ticketType[$sale[$ks]['priceid']]['discount'],/*结算价格*/
				'seatid'	=>	$vs['seat'],
				'idcard'	=>	$vs['idcard']
			);
			$up[$ks] = $model->table(C('DB_PREFIX').$plan['seat_table'])->where($maps)->lock(true)->save($datas);
			if(empty($up[$ks])){
				$model->rollback();//事务回滚
				$this->error = '400011 : 更新座椅状态失败';
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
			$this->error = '400018 : 客户信息获取失败';
			$model->rollback();
			return false;
		}
		//判断是否是底价结算['group']['settlement']
		if($crmInfo['group']['settlement'] == '1'){
			load_redis('lpush','PreOrder',$info['order_sn']);
		}
		//dump($flag);dump($flags);dump($state);dump($ostate);//dump($in_team);
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
			$this->error = '400013 : 排座失败';
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

    /**
	 * 政府手动排座
	 */
	function govSeat($pinfo){
		$info = json_decode($pinfo,true);	
		if(empty($info)){$this->error = '400002 : 数据解析失败';return false;}
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
			//error_insert('400014');
			$model->rollback();//事务回滚
			$this->error = '400014 : 订单状态不允许此项操作';
			return false;
		}
		//读取订单对应计划
		$plan = F('Plan_'.$oinfo['plan_id']);
		$proconf = cache('ProConfig');
		$proconf = $proconf[$plan['product_id']]['1'];
		if(empty($plan)){$this->error = '400005 : 销售计划获取失败';$model->rollback();return false;}
		$ticketType = F("TicketType".$plan['product_id']);
		$count = count($info['data']);//统计座椅个数
		foreach ($info['data'] as $k=>$v){
			$map = array(
				'area' => $v['areaId'],
				'seat' => $v['seatid'],
				'status' => array('not in','2,99'), //政企订单可排预留座位,
			);
			$sale = array(
				'plantime'=>date('Y-m-d ',$plan['plantime']).date(' H:i',$plan['starttime']),
				'area'=>areaName($v['areaId'],1),
				'seat'=>Order::print_seat($v['seatid'], $plan['product_id'],$ticketType[$v['priceid']]['param']['ticket_print'], $ticketType[$v['priceid']]['param']['ticket_print_custom']),
				'mouth' => self::print_mouth($plan['product_id'], $v['seatid']),
				'games'=>$plan['games'],
				'priceid'=>$v['priceid'],
				'priceName'=>$ticketType[$v['priceid']]['name'],
				'price'=>$ticketType[$v['priceid']]['price'],/*票面价格*/
				'discount'=>$ticketType[$v['priceid']]['discount'],/*结算价格*/
			);
			$data = array(
				'order_sn' => $oinfo['order_sn'],
				'soldtime' => $createtime,
				'status'   => '2',
				'price_id' => $v['priceid'],
				'sale'	   => serialize($sale),//售出信息 票型  单价
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
				$this->error = '400009 : 排座失败';
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
				unset($hdata['data']);
			}
		}
		//检测该订单是否有补贴
		if($oinfo['type'] == '4'){
			$crmInfo = google_crm($plan['product_id'],$hdata['crm'][0]['qditem']);
			//严格验证渠道订单写入返利状态
			if(empty($crmInfo['group']['settlement']) || empty($crmInfo['group']['type'])){
				//error_insert('400018');
				$model->rollback();
				$this->error = '400018 : 客户信息获取失败';
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
			$model->rollback();//事务回滚
			$this->error = '400006 : 排座失败';
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
	/**区域组合
	*@param $area 订单区域数据
	*@param $settlement int 结算方式
	*@param $product_id int 产品id
	*@param $produc_type int 产品类型
	*@param $child_ticket array 联票子票型
	*@param $channel int 是否是渠道订单
	*return $seat 包含座椅区域信息 及座椅数量 
	*/
	public function area_group($area,$product_id,$settlement,$product_type,$child_ticket = '',$channel = null){
		if(empty($area)){$this->error = "门票类型为空,操作终端...";return false;}
		//$this->error = "门票类型为空,操作终端...";return false;
		if(!empty($child_ticket)){
			foreach ($child_ticket as $key => $value) {
				$price += $value['price'];
			}	
		}
		/*重新组合区域*/
		foreach($area as $k=>$v){
			if($product_type == '1'){
				//排座
				$seat['area'][] = [
					'areaId' => $v['areaId'],
					'priceid'=> $v['priceid'],
					'price'	 => $v['price'],
					'num'	 =>	$v['num'],
					'idcard' => $v['idcard']
				];
				//辅助排座 按区域归类
				$seat['aux_seat'][$v['areaId']]['areaId'] = $v['areaId'];
				$seat['aux_seat'][$v['areaId']]['num'] += $v['num'];
				$seat['num'] += $v['num'];
				
			}else{
				//景区、漂流
				if(empty($v['idcard'])){
					$seat['area'][$v['priceid']] = array(
						'priceid'=>$v['priceid'],
						'price'=>$v['price'],
						'num'=>$v['num'],
						'idcard'=> ''
					);
					$seat['num'] += $seat['area'][$v['priceid']]['num'];
				}else{
					$seat['area'][] = array(
						'priceid'=>$v['priceid'],
						'price'=>$v['price'],
						'num'=>$v['num'],
						'idcard'=> $v['idcard']
					);
					$seat['num'] += $v['num'];
				}
			}
			//TODO 临时解决散客销售底价结算问题
			//if($v['priceid'] == 468 || $v['priceid'] == 471 || $v['priceid'] == 469){
			if(in_array($v['priceid'],['468','471','471','520'])){
				$settlement = 2;
			
			}
			//计算订单金额
			$money = Order::amount($v['priceid'],$v['num'],$v['areaId'],$product_id,$settlement,$channel);
			if($price != '0'){
				$child_moeny = $price*$v['num'];
			}else{
				$child_moeny = 0;
			}//dump($money);
			if($money){
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
	打包产品重新组合
	*@param $area 订单区域数据
	*@param $settlement int 结算方式
	*@param $product_id int 产品id
	*@param $produc_type int 产品类型
	*@param $child_ticket array 联票子票型
	*@param $channel int 是否是渠道订单
	*return $seat 包含座椅区域信息 及座椅数量 
	*/
	private function pack_group($area,$product_id,$settlement,$product_type,$activity_id = '',$channel = null){
		//读取打包规则
		$ainfo = D('Activity')->where(['id'=>$activity_id])->field('id,type,param')->find();
		$param = json_decode($ainfo['param'],true);
		$packRule = $param['info']['packages'];
		foreach($area as $k=>$v){
			foreach ($packRule as $ka => $ve) {
				//获取当前产品票型
				$ticket = F('TicketType'.$ve['product']);
				$seat['area'][] = array(
					'product'=>$ve['product'],
					'priceid'=>$ve['ticket'],
					'price'=>$ticket[$ve['ticket']]['price'],
					'num'=>$v['num']
				);

				
				$money = Order::amount($ve['ticket'],$v['num'],$v['areaId'],$ve['product'],$settlement,$channel);
				if($money){
					$seat['moneys'] += $money['moneys'];
					$seat['poor'] += $money['poor'];
					$seat['money'] += $money['money'];
				}else{
					return false;
				}
			}
			$seat['num'] += $v['num'];
		}
		
		return $seat;
	}
	/**
	 * 计算订单金额  不相信客户端的计算程序 2016-2-16
	 * @param  int $priceid    价格id
	 * @param  int $number     数量
	 * @param  int $area       区域id 校验区域与价格id是否匹配
	 * @param  int $product_id 产品id
	 * @param  int $type 	   1票面价金额2结算价金额3结算金额结算有返佣
	 * @param  int $channel    是否是渠道订单
	 * @return  订单金额
	 */
	private function amount($priceid,$number,$area,$product_id,$type = '1',$channel = null){
		//获取价格缓存
		//是否开启多级扣款，开启多级扣款之后结算价会不一样 
		//判断创建场景是否是渠道版
		$product = cache('Product');
		$product = $product[$product_id];
		$itemConf = cache('ItemConfig');
		$ticket = F('TicketType'.$product_id);
		if(empty($ticket)){$this->error = "票型获取失败";return false;}
		//(int)$channel === (int)2
        if($itemConf[$product['item_id']]['1']['level_pay'] && in_array($channel,['1','2','4'])){
        	$discount = $this->channel_level_price($priceid,$ticket);
        }else{
			$discount = $ticket[$priceid]['discount'];/*结算价格*/
        }//dump($itemConf[$product['item_id']]['1']['level_pay']);
        //dump($type);
		$price = $ticket[$priceid]['price'];/*票面价格*/
		//计算金额
		if((int)$type === (int)1){
			//票面价计算
			$money = $price*$number;
			$poor = 0;//优惠金额
			$moneys = $money;//票面金额
		}
		if((int)$type === (int)2 || (int)$type === (int)3){
			//结算价计算
			$money = $discount*$number;
			$poor = $price - $discount;//优惠金额
			$moneys = $price*$number;//票面金额
		}
		$data = array(
			'money'	=>	$money,
			'moneys'=>	$moneys,
			'poor'	=>	$poor*$number,
		);//dump($data);
		return $data;
	}
	/**
	 * 获取多级扣款的  渠道版专用 
	 * @Company  承德乐游宝软件开发有限公司
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2018-03-26
	 * @return   [type]        [description]
	 */
	private function channel_level_price($priceid,$ticketList = null){
		$uinfo = \Home\Service\Partner::getInstance()->getInfo();
		//读取当前用户的价格政策
		$ticketLevel = F('TicketLevel');
		if(!$ticketLevel){
	        D('Home/TicketLevel')->ticke_level_cache();
	        $ticketLevel = F('TicketLevel');
	    }
	    $itemConf = cache('ItemConfig');
	    //一级代理商直接显示景区结算价格
	    if($itemConf[$uinfo['crm']['itemid']]['1']['level_pay'] && $uinfo['crm']['level'] > 16){
	        $ticket = $ticketLevel[$uinfo['crm']['f_agents']];
			$discount = $ticket[$priceid]['discount'];/*结算价格*/
			//如果没有设置   直接读取景区分销价
			if(empty($discount)){
				$discount = $ticketList[$priceid]['discount'];
			}
	    }else{
	    	$discount = $ticketList[$priceid]['discount'];
	    }
		
		return $discount;
	} 
	/**
	* 订单场景初始值 
	* 根据订单场景设置订单的初始值 场景+订单类型 新增场景值 
	* @param $scena 场景标识 11 窗口散客订单 12 窗口团队订单 22 渠道团队 23 微信散客订单
	* 创建场景1窗口选座 6窗口快捷 2渠道版3网站4微信5api 7自助设备
	* 订单类型1散客订单2团队订单4渠道版定单6政府订单7预约订单8全员销售9三级分销 3小商品 5 物品租聘
	* 支付方式0未知1现金2余额3签单4支付宝5微信支付6划卡
	* 状态0为作废订单1正常2为渠道版订单未支付情况3已取消5已支付但未排座6政府订单7申请退票中9门票已打印11窗口订单创建成功但未排座
	*/
	private function is_scena($param = null, $is_pay = null){
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
			case '27':
				//渠道版预约
				$return = array('type'=>7,'addsid'=>2,'pay'=>$is_pay ? $is_pay : '1','status'=>11,'createtime'=>time());
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
	/**
	 * 校验景区门票可售数量
	 * @param  int $plan_id  计划id
	 * @param  int $plan_num 峰值
	 * @param  int $num      
	 * @return [type]        
	 */
	private function check_salse_num($plan_id,$plan_num,$table,$num){
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
	 * 返回客户信息
	 * @param $product_id int 产品ID
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
		return $seat;
		// if($proconf['print_mouth'] == '1'){
		// 	return Order::print_mouth($seats[0]).$seat;
		// }else{
			
		// }
	}
	/**
	 * 自定义入场口
	 * @param  int $row 座位排号
	 * @return [type]      [description]
	 */
	static function print_mouth($product_id, $seat){
		$proconf = cache('ProConfig');
		$proconf = $proconf[$product_id]['1'];
		if($proconf['print_mouth'] == '1'){
			$seats = explode('-', $seat);
			$c1Arr = [
				'1-29',
				'1-31',
				'1-33',
				'1-35',
				'2-29','2-31','2-33','2-35',
				'3-29','3-31','3-33','3-35',
				'4-33','4-31','4-35',
				'5-31','5-33','5-35',
				'6-31',
				'6-33',
				'6-35',
				'7-33',
				'7-35',
				'8-35'
			];
			$c2Arr = [
				'2-30','2-32','2-34','2-36','2-38','2-40',
				'3-30','3-32','3-34','3-36','3-38','3-40',
				'4-30','4-32','4-34','4-36','4-38','4-40',
				'5-32','5-34','5-36','5-38','5-40',
				'6-34','6-36','6-38','6-40',
				'7-34','7-36','7-38','7-40',
				'8-34','8-36','8-38','8-40',
				'9-36','9-38','9-40',
				'10-36','10-38','10-40',
				'11-38','11-40',
				'12-40',
				'13-40',
				'14-40',
			];
			$c3Arr = [
				'17-39','17-41',
				'18-39','18-41',
				'19-41',
				'21-41',
				'22-41',
				'23-41',
				'24-41',
				'25-39','25-41',
				'26-39','26-41',
				'27-39','27-41',
				'28-39','28-41',
				'29-39','29-41',
				'30-39','30-41',
				'31-39','31-41',
			];
			$c4Arr = [
				'18-40',
				'22-40',
				'24-40',
				'25-40',
				'26-38','26-40',
				'27-40',
				'28-40',
				'29-40',
				'30-40',
				'31-40',
			];
			if(isOdd((int)$seats[1])){
				//单号
				if($seats[0] > 16){
					//北一
					if((int)$seats[1] > 43 || in_array($seat, $c3Arr)){
						$area = 'C3区';
					}elseif(18 < $seats[0] && $seats[0] < 27){
						$area = 'B区';
					}
					return '北一口' .$area;
				}else{
					if((int)$seats[1] > 35 || in_array($seat, $c1Arr)){
						$area = 'C1区';
					}else{
						$area = 'C区';
					}
					return '北二口 ' .$area;
				}
			}else{
				//双号
				if($seats[0] > 16){
					if((int)$seats[1] > 40 || in_array($seat, $c4Arr)){
						$area = 'C4区';
					}elseif(18 < $seats[0] && $seats[0] < 27){
						$area = 'B区';
					}
					return '南一口' .$area;
				}else{
					if((int)$seats[1] > 40 || in_array($seat, $c2Arr)){
						$area = 'C2区';
					}else{
						$area = 'C区';
					}
					return '南二口' .$area;
				}
			}
		}else{
			return '';
		}
		
	}

	/**
	 * 判断是不是新的游客
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
	/**
	 * 根据订单号重发短信
     * @param $sn  订单号
     * return 出票员以及出票时间
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
	 * 生成检票密码 景区漂流使用
	 * @param  string $ciphertext 明文密码
	 * @param  string $encry      场次密钥
	 * @return [type]             [description]
	 */
	private function create_ticket_pwd($ciphertext,$encry){
		return md5($ciphertext . md5($encry));
	}
	/**
	 * 校验当前操作员是否属于当前商户
	 */
	public function checkUserToCrm($uinfo,$crm_id)
	{
		$count = D('User')->where(['id'=>$uinfo['id'],'cid'=>$crm_id])->count();
		if((int)$count === 0){
			$this->error = "当前商户,未找到有效用户";
			return false;
		}
		return ture;
	}
}