<?php
// +----------------------------------------------------------------------
// | LubTMP  报表处理类
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Common\Model\Model;
use Libs\Service\Sms;
class Report{
	/*订单拆解
	*@param $datetime string 报表日期
	
	function report($datetime){
		$datetime = "20151116";
		$start_time = strtotime($datetime);
        $end_time = $start_time  + 86399;
        //报表生成条件 1 按日期 且包含预定成功
        $proconf = cache('ProConfig');
        if($proconf['report'] == '1'){
        	//按照日期
			$map = array(
    			'status' => array('in','1,7,9'),//订单状态为支付完成和已出票和申请退票中的报表
    			'createtime' => array(array('EGT', $start_time), array('ELT', $end_time), 'AND'),
    		);
        }else{
        	//按场次
        	$map = array(
    			'status' => array('in','1,7,9'),//订单状态为支付完成和已出票和申请退票中的报表
    			'plantime' => array(array('EGT', $start_time), array('ELT', $end_time), 'AND'),
    		);
        }
    	
    	$status = M('ReportData')->where(array('datetime'=>$datetime))->count();
		//当前日期是否已生成
		if($status!=0){
			if(M('ReportData')->where(array('datetime'=>$datetime,'status'=>'1'))->setField('status',0)){
				$status = Report::strip_order($map,$datetime,1);
			}else{
				//失败记录日志，并发送错误短信
				$msg = array('phone'=>'18631451216','title'=>"",'rema'=>"订单拆解",'code'=>'120001');
				Sms::err_msg($msg);
				return false;
			}
		}else{
			$status = Report::strip_order($map,$datetime,1);
			
		}
		if($status){
		    //成功记录日志
			return true;
		}else{
		    //失败记录日志，并发送错误短信
			$msg = array('phone'=>'18631451216','title'=>"",'rema'=>"订单拆解",'code'=>'120001');
			Sms::err_msg($msg);
			return false;
		}
	}*/
	/**
	 * 按计划读取订单数据 按渠道商分票型合并 针对旧数据
	 * @param  [type] $datetime   [description]
	 * @param  [type] $product_id [description]
	 * @param  string $type       [description]
	 * @return [type]             [description]
	 */
	function create_report($datetime,$product_id,$type = '1'){
		if($type == '1'){
			//报表生成
		}else{

		}
		foreach ($list as $k => $v) {
			//渠道商订单 只针对渠道商合并
			$ndata[$v['channel_id']][$v['price_id']] = array(

			);
			//散客订单 暂时不合并
			//分销订单 暂时不合并
		}
	}
	/**
	 * 销售日历
	 * @param   $datetime   [description]
	 * @param  [type] $product_id [description]
	 * @return [type]             [description]
	 */
	function seale_calendar(){
		array(
			'datetime' 	=>  '',
			'number'	=>	'',
			'price'		=>	'',
			'discount'	=>	'',
			'rebate'	=>	'',
			'than'		=>	'',//团散比
		);
	}
	function report($datetime,$product_id=null){
		$start_time = strtotime($datetime);
        $end_time = $start_time  + 86399;
        $proconf = cache('ProConfig');
        if(empty($product_id)){
        	$product = M('Product')->field('id')->select();
        }else{
			$product = array('id'=>$product_id);
        }
        
        //报表生成条件 1 按日期 且包含预定成功 拉取产品列表
        foreach ($product as $key => $value) {
        	$tproconf = $proconf[$value['id']][1];
        	if($tproconf['report'] == '1'){
	        	//按照日期
				$map = array(
					'product_id' => $value['id'],
	    			'status' => array('in','1,7,9'),//订单状态为支付完成和已出票和申请退票中的报表
	    			'createtime' => array(array('EGT', $start_time), array('ELT', $end_time), 'AND'),
	    		);
	    		$where = array('datetime'=>$datetime,'status'=>'1');
	    		$count = M('ReportData')->where($where)->count();
				//当前日期是否已生成
				if($count != 0){
					if(M('ReportData')->where($where)->setField('status',0)){
						$status = Report::strip_order($map,$datetime,1);
					}else{
						error_insert('111221');
						//失败记录日志，并发送错误短信
						$msg = array('phone'=>'18631451216','title'=>"",'rema'=>"订单拆解",'code'=>'120001');
						Sms::err_msg($msg);
						return false;
					}
				}else{
					$status = Report::strip_order($map,$datetime,1);
				}
	        }else{
	        	//按场次 查询场次id
	        	$plan = M('Plan')->where(array('plantime'=>$start_time))->field('id')->select();
	        	$where = array('plantime'=>$start_time,'status'=>'1');
	        	$count = M('ReportData')->where($where)->count();
				//当前日期是否已生成
				if($count != 0){
					if(M('ReportData')->where($where)->setField('status',0)){
						//当前日期是否已生成
						foreach ($plan as $k => $v) {
				    		$map = array(
								'status' => array('in','1,7,9'),//订单状态为支付完成和已出票和申请退票中的报表
								'product_id' => $value['id'],
								'plan_id' => $v['id'],
							);
							$status = Report::strip_order($map,$datetime,1);
				    	}
					}else{
						//失败记录日志，并发送错误短信
						$msg = array('phone'=>'18631451216','title'=>"",'rema'=>"订单拆解",'code'=>'120001');
						Sms::err_msg($msg);
						return false;
					}
				}else{
					//当前日期是否已生成
					foreach ($plan as $k => $v) {
			    		$map = array(
							'status' => array('in','1,7,9'),//订单状态为支付完成和已出票和申请退票中的报表
							'product_id' => $value['id'],
							'plan_id' => $v['id'],
						);
						$status = Report::strip_order($map,$datetime,1);
			    	}
				}
	        }
        }
        return '200';
	}
	/*
	* 每日订单拆解   写入报表基础数据表
	* @param $map array 读取条件
	* @param $data string 订单详情
	* @param $datetime string 报表日期
	* @param $type int 拆解类型 1 计划任务 2非计划任务拆解
	* @param 
	* return true|false
	*/
	function strip_order($map, $datetime, $type = '1'){//dump($map);
		$list = D('Item/Order')->where($map)->relation(true)->select();
		if(!empty($list)){
			foreach ($list as $key => $value) {
				// TODO 缺少写入成功性验证
				$plan = M('Plan')->where(array('id'=>$value['plan_id']))->field('plantime,games')->find();
				$general = array(
					'datetime'	=>	$datetime,	//报表日期时间
					'product_id'=>	$value['product_id'],
					'plantime'	=>	date('Ymd',$plan['plantime']),
					'games'		=>	$value['product_type'] == '1' ? $plan['games'] : 0,	//场次
					'order_sn'	=>	$value['order_sn'],	//订单号
					'plan_id'	=>	$value['plan_id'],	//计划ID
					'channel_id'=>	$value['channel_id'] ? $value['channel_id'] : '0',//渠道ID
					'user_id'	=>	$value['user_id'],	//操作员
					'createtime'=>	time(),	//创建时间
					'type'		=>	$value['type'],//订单类型1散客订单2团队订单4渠道版定单
					'pay'		=>	$value['pay'],//支付方式1现金2余额
					'addsid' 	=>	$value['addsid'],
					'status'	=>	'1',
				);
				$data = unserialize($value['info']);
				$main[] = Report::ticket_type($general,$data['data'],$data['param'],$datetime,$type,$value['product_type']);
				if(!empty($data['child_ticket'])){
					$child[] = Report::ticket_type($general,$data['child_ticket'],$data['param'],$datetime,$type,$value['product_type'],'1',$value['number']);
				}
			}
			if(empty($child)){
				$status = $main;
			}else{
				$status = array_merge($main,$child);
			}
		}else{
			$status = '200';
		}//计划任务拆解
 		return 	$status;
	}
 	/*按票型统计
 	@param $general array 通用数据
 	@param $seat array 订单内座椅数据
 	@param $datetime
 	@param $type int 拆解类型
 	@param $param array 参数
 	@param $product_type 产品类型
 	@param $number int 数量
 	return $data*/
 	function ticket_type($general, $seat, $param, $datetime, $type, $product_type, $child = null, $number = null){
 		//根据票型归类
 		foreach($seat as $k=>$v){
 			$datalist[$v['priceid']][] = $v;
 		}
 		//计算票型内门票数量 以及重组要写入数组
 		$t_type = array_keys($datalist);//获取当前订单的票型
 		for ($i=0; $i < count($datalist); $i++) {
 			if(!empty($datalist[$t_type[$i]][0]['priceid'])){
 				if($child){
 					$nums[$i] = $number;
 				}else{
 					$nums[$i] = count(array_filter($datalist[$t_type[$i]]));
 				}
	 			$money[$i] = Report::settlement($nums[$i],$t_type[$i],$general['product_id']);
	 			if($product_type == '1'){
	 				$area = $datalist[$t_type[$i]][0]['areaId'];
	 			}else{
	 				$area = $t_type[$i];
	 			}
	  			$data[$i] = array(
	 				'area'		=>	$area,	//区域ID
					'price_id'	=>	$t_type[$i],//票型ID
					'number'	=>	$nums[$i],//数量 	
					'price'		=>  $datalist[$t_type[$i]][0]['price'],	//单价
					'discount'	=>  $datalist[$t_type[$i]][0]['discount'],	//结算价
					'money'		=>  $money[$i]['money'],//票面金额
					'moneys'	=>	$money[$i]['moneys'],//结算金额
					'subsidy'   =>  $money[$i]['rebate'],  //补贴金额  
					'region'	=>	$param[0]['tour'] ? $param[0]['tour'] : '0',
	 			);
	 			$datas[$i] = array_merge($data[$i],$general);
 			}else{
 				//记录异常日志
 				//TODO
 				error_insert('400012');
 			}
 		}
 		if($type == '1'){
 			//计划任务拆解
 			return Report::insert_report($datas);
 		}else{
 			//非计划任务拆解
 			return $datas;
 		}
 	}
 	/*
	*数据写入
	@param $datas array 写入数组
 	*/
 	public function insert_report($datas){
 		if(count($datas) == '1'){
 			$status = D('Item/ReportData')->add($datas[0]);
 		}else{
 			$status = D('Item/ReportData')->addAll($datas);
 			//D('Cron/Cronlog')->add(array('cron_id'=>$status,'performtime'=>time(),'status'=>13));
 		}
 		return $status;
 	}
 	/*非计划拆解 报表显示  如售票员日报表*/
 	function operator($data){
 		foreach ($data as $k => $v) {
			foreach ($v as $key => $value) {
				$list[] = array(
					'datetime'	=>	$value['datetime'],	//报表日期时间
					'product_id'=>	$value['product_id'],
					'plantime'	=>	$value['plantime'],
					'games'		=>	$value['product_type'],	
					'order_sn'	=>	$value['order_sn'],	//订单号
					'plan_id'	=>	$value['plan_id'],	//计划ID
					'channel_id'=>	$value['channel_id'],//渠道ID
					'user_id'	=>	$value['user_id'],	//操作员
					'type'		=>	$value['type'],//订单类型1散客订单2团队订单4渠道版定单
					'pay'		=>	$value['pay'],//支付方式1现金2余额
					'addsid' 	=>	$value['addsid'],
					'area'		=>	$value['area'],	//区域ID
					'price_id'	=>	$value['price_id'],//票型ID
					'number'	=>	$value['number'],//数量 	
					'price'		=>  $value['price'],	//单价
					'discount'	=>  $value['discount'],	//结算价
					'money'		=>  $value['money'],//票面金额
					'moneys'	=>	$value['moneys'],//结算金额
					'subsidy'   =>  $value['subsidy'],  //补贴金额  
				);
			}
		}
		return $list;
 	}
 /*===============================================^^^^^^^^^^^^每日订单拆解入库^^^^^^^^^^^^^======================================*/	
 	/*
	计划合并
	@param $data array 数据
	return array 返回数据
	*/
	function plan_fold($data,$type = '1'){
		if(empty($data)){
			return false;
		}
		if($type == '1'){
			foreach ($data as $k => $v) {
				$arr[$v['plan_id']][] = $v;
			}
		}else{
			//非计划任务拆解
			foreach ($data as $key => $value) {
				foreach ($value as $k => $v) {
					$arr[$v['plan_id']][] = $v;
				}
			}
		}
		return $arr;
	}
	/*
	* 计划拆解
	*/
	function strip_plan($data){
		foreach ($data as $key => $value) {
			$datas[$key] = Report::plan_ticket_fold($value,$key);
		}
		return $datas;
	}
	/*
	*按计划合并票型
	@param $data array 数据
	@param $key int 计划id
	return array 返回数据
	*
	*/
	function plan_ticket_fold($data,$key){
		foreach ($data as $ke => $value) {
			$datas[$value['price_id']]['plan_id'] = $key;
			$datas[$value['price_id']]['price_id'] = $value['price_id'];
			$datas[$value['price_id']]["price"] 	 = $value["price"];
        	$datas[$value['price_id']]["discount"] = $value["discount"];
			$datas[$value['price_id']]['number'] += $value['number'];
		}

		foreach ($datas as $ky => $value) {
			$arr[$ky] = $value;
			$arr[$ky]['moneys'] = $value['discount']*$value['number'];
		}
		return $arr;
	}
	/*
	*  日报表
	*  @param $data 待处理数据
	*  @param $work 是否含工作票 1包含工作票2 不含工作票 3仅含工作票
	*/
	function day_fold($data,$work = '1'){
		//根据合并计划
		foreach ($data as $key => $value) {
			$plan['plan'][$value['plan_id']][]= $value;
		}
		//dump($plan);
		//计划内合并票型
		foreach ($plan['plan'] as $key => $value) {
			$ticket[$key] = Report::plan_ticket_folds($value,$work);
		}
		return $ticket;
	}
	/*
	票型计算
	@param $data array 数据
	@param $datetime int|array 日期
	@param $user_id int 用户id
	@param $channel_id  int 渠道商id
	@param $type int 合并类型  1 计划任务合并 从reprotdata 取数据 2 非计划任务从order 取数据
	return array 返回数据
	*/
	function ticket_fold($data, $datetime = '', $user_id = null, $channel_id = null, $type = '1'){
		foreach ($data as $key => $value) {
			foreach ($value as $ke => $valu) {
				$num[$key][$valu['price_id']]['num'] += $valu['number'];
				$num[$key][$valu['price_id']]['rebate'] += $valu['subsidy'];
				//获取票型金额
				$money = Report::settlement($num[$key][$valu['price_id']]['num'],$valu['price'],$valu['discount']);
				$datas[$key]['price'][$valu['price_id']] = array( 
						'product_id'=> $valu['product_id'],
						'channel_id'=> $valu['channel_id'],
						'plan_id' 	=> $valu['plan_id'], 
					 	'price_id'	=> $valu['price_id'],
					    'price'  	=> $valu['price'],
      					'discount' 	=> $valu['discount'],
      					'number'	=> $num[$key][$valu['price_id']]['num'],
      					'money'		=> $money['money'],
      					'moneys'	=> $money['moneys'],
      					'rebate'	=> $num[$key][$valu['price_id']]['rebate'],
      			);
			}
			sort($datas[$key]['price']);
			$map = array(
				'plan_id'=>$key,
				'datetime'=>$datetime,
				'status'=>1,
			);
			if(!empty($channel_id)){
				$map['channel_id'] = $channel_id;
			}
			if(!empty($user_id)){
				$map['user_id'] = $user_id;
			}
			$datas[$key]['plan'] = $key;
			$datas[$key]['number'] = M('ReportData')->where($map)->sum('number');
			$datas[$key]['money']  = M('ReportData')->where($map)->sum('money');
			$datas[$key]['moneys'] = M('ReportData')->where($map)->sum('moneys');
			$datas[$key]['rebate'] = M('ReportData')->where($map)->sum('subsidy');
		}
		return $datas;
	}
	
	/*渠道商销售统计 汇总*/
	function channel_fold($data){
		//合并渠道商
		$channel = Report::channel_merge($data);
		//单个渠道商合并票型
		$ticket = Report::channel_merge_ticket($channel['channel']);
		ksort($ticket);
		return $ticket;
	}
	/*渠道商销售统计 按场次 明细*/
	function channel_plan_fold($data){
		//合并渠道商
		$channel = Report::channel_merge($data);//dump($channel);
		ksort($channel['channel']);
		//按计划合并
		foreach ($channel['channel'] as $k => $v) {
			$plan[$k] = Report::plan_merge($v);
		}
		//dump($plan);
		foreach ($plan as $key => $value) {
			$ticket[$key]['plan'] = Report::channel_merge_ticket($value);
		}
		return $ticket;
	}
	/*数据按渠道商  做合并处理*/
	function channel_merge($data){
		foreach ($data as $key => $value) {
			if(!empty($value['channel_id'])){
				$channel['channel'][$value['channel_id']][] = $value;
			}else{
				$channel['channel'][$value['user_id']][] = $value;
			}
		}
		return $channel;
	}
	/*单一渠道商 合并票型*/
	function channel_merge_ticket($data){
		//合并渠道商
		//dump($data);
		foreach ($data as $key => $value) {
			$ticket[$key] = Report::plan_ticket_folds($value);
			//$ticket['tic_num'] += $ticket[$key]['tic_num'];
		}
		return $ticket;
	}
	/*计划合并 同一计划合并内整理票型*/
	function plan_merge($data){
		foreach ($data as $k => $v) {
			$plan[$v['plan_id']][] = $v;
		}
		return $plan;
	}
	/*
	*开启代理商制度,只统计一级渠道商
	*将二三级代理销售数据汇总到一级名下
	*@param $data 代理商数据
	*/
	function level_fold($data){
		foreach ($data as $ke => $valu) {
			//判断当前渠道商是否是一级渠道商
			if(Report::if_level($valu['channel_id'],$valu['product_id'])){
				$list[] = $valu;
			}else{
				//将二三级渠道商全部归属到一级渠道商
				$valu['channel_id'] = money_map($valu['channel_id']);
				$list[] = $valu;
			}
		}
		return $list;
	}
	//判断渠道商是否是一级渠道商
	//@param $channel_id 要查询渠道商的ID
	private function if_level($channel_id,$product_id){
		$channel = F('Crm_level'.$product_id);
		return array_key_exists($channel_id,$channel);
	}

	/*票型按渠道商归类 2.0 窗口版废弃  渠道版还在使用
	function channel_ticket_fold($data, $datetime,$channel = null){
		foreach ($data as $key => $value) {
			foreach ($value as $ke => $valu) {
				$channel_id = $channel ? $channel : $valu['channel_id'];
				$num[$key][$valu['price_id']][$channel_id]['num'] += $valu['number'];
				$num[$key][$valu['price_id']][$channel_id]['rebate'] += $valu['subsidy'];
				//获取票型金额
				$money = Report::settlement($num[$key][$valu['price_id']][$channel_id]['num'],$valu['price_id'],$valu['product_id']);
				$datas[$key][$channel_id]['price'][$valu['price_id']] = array( 
						'product_id'=> $valu['product_id'],
						'channel_id'=>	$valu['channel_id'],
						'plan_id' 	=> $valu['plan_id'], 
					 	'price_id'	=> $valu['price_id'],
					    'price'  	=> $valu['price'],
      					'discount' 	=> $valu['discount'],
      					'number'	=> $num[$key][$valu['price_id']][$channel_id]['num'],
      					'money'		=> $money['money'],
      					'moneys'	=> $money['moneys'],
      					'rebate'	=> $num[$key][$valu['price_id']][$channel_id]['rebate'],
      			);
      			$map = array(
					'plan_id'=>$key,
					'datetime'=>$datetime,
					'status'=>1,
					'addsid' => 2,
				);
				$map['channel_id'] = array('in',agent_channel($channel_id,2));
				$datas[$key][$channel_id]['plan'] = $key;
				$datas[$key][$channel_id]['channel'] = $channel_id;
				$datas[$key][$channel_id]['number'] = M('ReportData')->where($map)->sum('number');
				$datas[$key][$channel_id]['money']  = M('ReportData')->where($map)->sum('money');
				$datas[$key][$channel_id]['moneys'] = M('ReportData')->where($map)->sum('moneys');
			}
			
		}
		return $datas;
	}
	*/
	/*区域按渠道商合并*/
	function channel_area_fold($data, $datetime,$channel = null){
		foreach ($data as $key => $value) {
			$channel_id = $channel ? $channel : $value['channel_id'];
			//按渠道商汇总
			$datas['channel'][$channel_id]['num'] += $value['number'];
			$datas['channel'][$channel_id]['rebate'] += $value['subsidy'];
			$datas['channel'][$channel_id]['money'] += $value['money'];
			$datas['channel'][$channel_id]['moneys'] += $value['moneys'];
			$datas['channel'][$channel_id]['product_id'] = $value['product_id'];
			$datas['channel'][$channel_id]['channel_id'] = $value['channel_id'];
			//按分渠道商按区域汇总
			$num[$channel_id][$value['area']]['num'] += $value['number'];
			$num[$channel_id][$value['area']]['rebate'] += $value['subsidy'];
			$num[$channel_id][$value['area']]['money'] += $value['money'];
			$num[$channel_id][$value['area']]['moneys'] += $value['moneys'];
			//按区域汇总
			$datas['area'][$value['area']]['num'] += $value['number'];
			$datas['area'][$value['area']]['rebate'] += $value['subsidy'];
			$datas['area'][$value['area']]['money'] += $value['money'];
			$datas['area'][$value['area']]['moneys'] += $value['moneys'];
			//整体汇总
			$datas['num'] += $value['number'];
			$datas['rebate'] += $value['subsidy'];
			$datas['money'] += $value['money'];
			$datas['moneys'] += $value['moneys'];

			$datas['channel'][$channel_id]['area'][$value['area']] = array(
      					'number'	=> $num[$channel_id][$value['area']]['num'],
      					'money'		=> $num[$channel_id][$value['area']]['money'],
      					'moneys'	=> $num[$channel_id][$value['area']]['moneys'],
      					'rebate'	=> $num[$channel_id][$value['area']]['rebate'],
      			);
		}
		return $datas;
	}
	/*
	* 分场次按票型归类  用于景区按场次汇总  单计划内合并票型   票型合并
	* @param $work int 是否包含工作票 1 含工作票 2不含工作票
	*/
	function plan_ticket_folds($data,$work = '1'){
		//只能显示当前产品的报表
		$product_id = get_product('id');
		foreach ($data as $k => $valu) {
			if($work == '1'){
				//含工作票统计
				$num[$valu['price_id']]['num'] += $valu['number'];
				$num[$valu['price_id']]['rebate'] += $valu['subsidy'];//dump($num[$valu['price_id']]['rebate']);
				$money = Report::settlement($num[$valu['price_id']]['num'],$valu['price_id'],$product_id);
				$datas['price'][$valu['price_id']] = array( 
						'channel_id'=> $valu['channel_id'] ? $valu['channel_id'] : $valu['user_id'],
						'product_id'=> $product_id,
						'plan_id' 	=> $valu['plan_id'], 
					 	'price_id'	=> $valu['price_id'],
					    'price'  	=> $valu['price'],
	  					'discount' 	=> $valu['discount'],
	  					'number'	=> $num[$valu['price_id']]['num'],
	  					'money'		=> $money['money'],
	  					'moneys'	=> $money['moneys'],
	  					'rebate'	=> $num[$valu['price_id']]['rebate'],
	      		);
			}elseif($work == '2'){
				if(!in_array($valu['price_id'],explode(',',zero_ticket()))){
					//不含工作票统计
					$num[$valu['price_id']]['num'] += $valu['number'];
					$num[$valu['price_id']]['rebate'] += $valu['subsidy'];//dump($num[$valu['price_id']]['rebate']);
					$money = Report::settlement($num[$valu['price_id']]['num'],$valu['price_id'],$product_id);
					$datas['price'][$valu['price_id']] = array( 
							'channel_id'=> $valu['channel_id'] ? $valu['channel_id'] : $valu['user_id'],
							'product_id'=> $product_id,
							'plan_id' 	=> $valu['plan_id'], 
						 	'price_id'	=> $valu['price_id'],
						    'price'  	=> $valu['price'],
		  					'discount' 	=> $valu['discount'],
		  					'number'	=> $num[$valu['price_id']]['num'],
		  					'money'		=> $money['money'],
		  					'moneys'	=> $money['moneys'],
		  					'rebate'	=> $num[$valu['price_id']]['rebate'],
		      		);
				}
			}else{
				if(in_array($valu['price_id'],explode(',',zero_ticket()))){
					//不含工作票统计
					$num[$valu['price_id']]['num'] += $valu['number'];
					$num[$valu['price_id']]['rebate'] += $valu['subsidy'];//dump($num[$valu['price_id']]['rebate']);
					$money = Report::settlement($num[$valu['price_id']]['num'],$valu['price_id'],$product_id);
					$datas['price'][$valu['price_id']] = array( 
							'channel_id'=> $valu['channel_id'] ? $valu['channel_id'] : $valu['user_id'],
							'product_id'=> $product_id,
							'plan_id' 	=> $valu['plan_id'], 
						 	'price_id'	=> $valu['price_id'],
						    'price'  	=> $valu['price'],
		  					'discount' 	=> $valu['discount'],
		  					'number'	=> $num[$valu['price_id']]['num'],
		  					'money'		=> $money['money'],
		  					'moneys'	=> $money['moneys'],
		  					'rebate'	=> $num[$valu['price_id']]['rebate'],
		      		);
				}
			}
		}
		sort($datas['price']);
		foreach ($datas['price'] as $k => $v) {
			$datas['channel_id'] = $v['channel_id'];
			$datas['plan'] = $v['plan_id'];
			$datas['number'] += $v['number'];
			$datas['money']  += $v['money'];
			$datas['moneys'] += $v['moneys'];
			$datas['rebate'] += $v['rebate'];	
		}
		//票型数量
		$datas['tic_num'] = count($datas['price']);
		return $datas;
	}
	/*
	* 分场次按场景、票型汇总
	*/
	function plan_scena_ticket_fold($data){
		foreach ($data as $key => $value) {
			foreach ($value as $ke => $valu) {
				$channel_id = $channel ? $channel : $valu['channel_id'];
				$num[$key][$valu['price_id']][$channel_id]['num'] += $valu['number'];
				$num[$key][$valu['price_id']][$channel_id]['rebate'] += $valu['subsidy'];
				//获取票型金额
				$money = Report::settlement($num[$key][$valu['price_id']][$channel_id]['num'],$valu['price_id'],$valu['product_id']);
				$datas[$key][$channel_id]['price'][$valu['price_id']] = array( 
						'product_id'=> $valu['product_id'],
						'channel_id'=>	$valu['channel_id'],
						'plan_id' 	=> $valu['plan_id'], 
					 	'price_id'	=> $valu['price_id'],
					    'price'  	=> $valu['price'],
      					'discount' 	=> $valu['discount'],
      					'number'	=> $num[$key][$valu['price_id']][$channel_id]['num'],
      					'money'		=> $money['money'],
      					'moneys'	=> $money['moneys'],
      					'rebate'	=> $num[$key][$valu['price_id']][$channel_id]['rebate'],
      			);
      			$map = array(
					'plan_id'=>$key,
					'datetime'=>$datetime,
					'status'=>1,
					'addsid' => 2,
				);
				$map['channel_id'] = array('in',agent_channel($channel_id,2));
				$datas[$key][$channel_id]['plan'] = $key;
				$datas[$key][$channel_id]['channel'] = $channel_id;
				$datas[$key][$channel_id]['number'] = M('ReportData')->where($map)->sum('number');
				$datas[$key][$channel_id]['money']  = M('ReportData')->where($map)->sum('money');
				$datas[$key][$channel_id]['moneys'] = M('ReportData')->where($map)->sum('moneys');
			}
			
		}
		return $datas;
	}
	/*
	生成景区日报表
	*/
	function today_scenic($datetime,$product_id){
		$list = M('ReportData')->where(array('datetime'=>$datetime,'status'=>1))->select();
		//根据计划汇总
		$plan_fold = Report::plan_fold($list);
		//根据票型汇总
		$ticket_fold = Report::ticket_fold($plan_fold);
		//写入日报表
		$status = M('Report')->add(array(
			'product_id' 	=> $product_id,
			'type'			=> '21',
			'user_id'		=> '0',
			'starttime'		=>	$datetime,
			'endtime'		=>	$datetime,
			'title'			=>	$datetime."景区日报表",
			'info'			=>	serialize($ticket_fold),
			'createtime'	=>	time(),
			));
		return $status;
	}

	/*
	景区财务日报表
	*/
	function today_financial(){

	}
	/*
	售票员日报表  场景日报表
	$tiny int 生成类型 1 售票员日报表 2 场景报表
	*/
	function today_user($datetime,$product_id,$user_id = null,$type = null, $tiny = '1'){
		if($tiny == 1){
			//售票员结算
			$list = M('ReportData')->where(array('datetime'=>$datetime,'user_id'=>$user_id,'status'=>1))->select();
			$title = $datetime.userName($user_id,1)."售票员日报表";
		}else{
			//按场景结算
			$list = M('ReportData')->where(array('datetime'=>$datetime,'type'=>$type,'status'=>1))->select();
			$title = $datetime.channel_type($type,1)."日报表";
		}
		//根据计划汇总
		$plan_fold = Report::plan_fold($list);
		//根据票型汇总
		$ticket_fold = Report::ticket_fold($plan_fold);
        $info = serialize($ticket_fold);

		//写入日报表
		$status = M('Report')->add(array(
			'product_id' 	=> $product_id,
			'type'			=> $type,
			'user_id'		=>  $user_id ? $user_id : '0',
			'starttime'		=>	$datetime,
			'endtime'		=>	$datetime,
			'title'			=>	$title,
			'info'			=>	$info,
			'createtime'	=>	time(),
			));
	}
	/*渠道返佣汇总  分业务员
	* @param $data array 返佣数据
	[0] => array(15) {
    ["id"] => string(3) "194"
    ["subtype"] => string(1) "0"
    ["order_sn"] => string(14) "50503141233073"
    ["product_type"] => string(1) "1"
    ["product_id"] => string(2) "41"
    ["plan_id"] => string(2) "47"
    ["user_id"] => string(3) "268"
    ["money"] => string(6) "180.00"
    ["guide_id"] => string(3) "281"
    ["qd_id"] => string(1) "3"
    ["userid"] => string(1) "0"
    ["status"] => string(1) "1"
    ["type"] => string(1) "0"
    ["createtime"] => string(10) "1430586028"
    ["uptime"] => string(10) "1430586028"
  }
	*/
	function rakeback($data){
		//按渠道商集合数据
		foreach ($data as $k => $v) {
			$list['channel'][$v['qd_id']][] = $v;

		}
		//按业务员
		foreach ($list['channel'] as $key => $value) {
			$rebate[$key] = Report::rakeback_user($value);
		}
		ksort($rebate);
		return $rebate;
	}
	//返佣按业务员计算
	function rakeback_user($data){
		foreach ($data as $k=>$v) {

			$datas['guide'][$v['guide_id']]['qd_id'] = $v['qd_id'];
			$datas['guide'][$v['guide_id']]['guide'] = $v['guide_id'];
			$datas['guide'][$v['guide_id']]['money'] += $v['money'];
			$datas['guide'][$v['guide_id']]['number'] += $v['number'];
		}
		sort($datas['guide']);
		foreach ($datas['guide'] as $k => $v) {
			$datas['channel_id'] = money_map($v['qd_id']);
			$datas['qd_id'] = $v['qd_id'];
			$datas['number'] += $v['number'];
			$datas['money']  += $v['money'];
		}
		return $datas;
	}
	/*授信汇总
	*/
	function topup($data){
		//按渠道商汇总
    	foreach ($data as $key => $value) {
    		//$channel[$value['crm_id']][] = $value;
    		if($value['type'] == '1'){
    			
    			$list[$value['crm_id']]['topup'] += $value['cash'];
    		}
    		/*
    		switch ($value['type']) {
    			case '1':
    				$list[$value['crm_id']]['topup'] += $value['cash'];
    				break;
    			case '2':
    				$list[$value['crm_id']]['cost'] += $value['cash'];
    				break;
    			case '3':
    				$list[$value['crm_id']]['subsidies'] += $value['cash'];
    				break;
    			case '4':
    				$list[$value['crm_id']]['refund'] += $value['cash'];
    				break;
    			case '5':
    				$list[$value['crm_id']]['now'] += $value['cash'];
    				break;
    		}*/
    	}/*dump($channel);
    	foreach ($channel as $k => $v) {
    		$list[$k] = Report::topup_type($k,$v);
    	}*/
    	return $list;
	}
	//分渠道商 按类型汇总
	function topup_type($crm_id,$data){
		//dump($data);
		//按类型汇总
    	foreach ($data as $k => $v) {
    		if($crm_id == $v['crm_id']){
    			switch ($v['type']) {
	    			case '1':
	    				$list['topup'] += $v['cash'];
	    				break;
	    			case '2':
	    				$list['cost'] += $v['cash'];
	    				break;
	    			case '3':
	    				$list['subsidies'] += $v['cash'];
	    				break;
	    			case '4':
	    				$list['refund'] += $v['cash'];
	    				break;
	    			case '5':
	    				$list['now'] += $v['cash'];
	    				break;
	    		}
    		}
    		
    	}
    	return $list;
	}
	/*售票员日报表即使生成合并
	@param $datetime 结算日期
	@param $product_id 产品id
	*/

	function today_users($datetime,$product_id){
		//根据创建场景查询
		//


	}
	/*计算金额
 	*@param $nums int 数量
 	*@param $product_id 产品ID
 	*@param $price_id 票型ID
 	*return $money float 金额
 	*/
 	function settlement($nums,$price_id,$product_id){
 		$ticketType = F("TicketType".$product_id);
 		$data = array(
 				'money' => $nums * $ticketType[$price_id]['price'],
 				'moneys'=> $nums * $ticketType[$price_id]['discount'],
 				'rebate'=> $nums * $ticketType[$price_id]['rebate'],
 			) ;
 		return $data;
 	}
 	/*渠道商授信日报
	*@param $datetime int 生成时间
	@param $product_id int 产品id
 	*/
 	function daily($datetime,$product_id){
 		$Config = cache("Config");
 		$list = M('Crm')->where(array('level'=>$Config['level_1']))->select();
 		foreach ($list as $key => $value) {
 			$data[$value['id']] = array(
 				'id'   => 	$value['id'],
 				'name' =>	$value['name'],
 				'money'=>	$value['cash'],
 				); 
 		}
 		//写入数据库
 		$status = M('Report')->add(array(
 			'product_id'	=>	$product_id, 
			'type'			=>	'7',
			'user_id'		=>	'1',//系统管理员
			'starttime'		=>	$datetime,
			'endtime'		=>	$datetime,
			'title'			=>	$datetime."渠道商授信余额日报",
			'info'			=>	serialize($data),
			'createtime'	=>	time(),	
 			));
 		return $status;
 	}
 	/**
 	 * 资金来源汇总
 	 * @param  array $param 待处理的数据包
 	 * @return [type]       [description]
 	 * 支付方式0未知1现金2余额3签单4支付宝5微信支付6划卡 
 	 */
 	function source_cash($list){
 		foreach ($list as $k => $v) {
 			$return[$v['plan_id']]['plan'] = $v['plan_id'];
 			$return[$v['plan_id']]['money'] += $v['moneys'];
			switch ($v['pay']) {
 				case '1':
 					//现金
 					$return[$v['plan_id']]['cash'] +=  $v['moneys'];
 					break;
 				case '2':
 					//余额
 					$return[$v['plan_id']]['difference'] +=  $v['moneys'];
 					break;
 				case '3':
 					//签单
 					$return[$v['plan_id']]['sign'] +=  $v['moneys'];
 					break;
 				case '4':
 					//支付宝
 					$return[$v['plan_id']]['alipay'] +=  $v['moneys'];
 					break;
 				case '5':
 					//微信支付
 					$return[$v['plan_id']]['wxpay'] +=  $v['moneys'];
 					break;
 				case '6':
 					//划卡
 					$return[$v['plan_id']]['stamp'] +=  $v['moneys'];
 					break;
 				default:
 					//未知
 					$return[$v['plan_id']]['unknown'] +=  $v['moneys'];
 					break;
 			}
 		}
 		return $return;
 	}
 	//售票员资金一览表
 	function conductor($datatime = '',$plan_id = '',$user_id = '')
 	{	
 		if(empty($plan_id)){
 			$list = D('Item/Order')->where($datatime)->field('plan_id')->select();
 			$list = array_column($list,'plan_id');
 			$plan = array_flip($list);
 			foreach ($plan as $k => $v) {
 				//窗口售票
 				$conductor = Report::sum_pay($k,$user_id,$datatime);
 				//代收款
 				$collection = Report::collection($k,$user_id,$datatime);
 				$money[$k] = array(
 					'plan' => $k,
 					'data' => array_merge($conductor,$collection)
 				);
 			}
 		}else{
 			$conductor = Report::sum_pay($plan_id,$user_id,'');
 			$collection = Report::collection($plan_id,$user_id,'');
 			$money[$plan_id] = array(
 				'plan'   => $plan_id,
 				'data' => array_merge($conductor,$collection)
 			);
 		}
 		foreach ($money as $key => $value) {
 			$sum_money = 0;	
 			foreach ($value['data'] as $ke => $val) {
 				if(in_array($ke,array('cash','dcash'))){$sum['cash'] += $val;}
 				if(in_array($ke,array('sign','dsign'))){$sum['sign'] += $val;}
 				if(in_array($ke,array('alipay','dalipay'))){$sum['alipay'] += $val;}
 				if(in_array($ke,array('wxpay','dwxpay'))){$sum['wxpay'] += $val;}
 				if(in_array($ke,array('stamp','dstamp'))){$sum['stamp'] += $val;}
 				//判断是否显示该计划
	 			$sum_money += $val;
 			}
 			if($sum_money == 0){
 				unset($money[$value['plan']]);
 			}
 		}
 		$return['money'] = $money;
 		$return['sum'] = $sum;
 		return $return;
 	}
 	//根据计划按支付类型汇总金额
 	function sum_pay($plan_id = '',$user_id,$datatime = ''){
 		$map = array(
 			'product_id' => get_product('id'), 
 			'status'	 => array('in','1,7,9'),
 			'user_id'	 => $user_id,
 			'plan_id'	 => $plan_id
 		);
 		if(!empty($datatime)){
 			$map['createtime'] = $datatime['createtime'];
 		}
 		$model = D('Item/Order');
 		$pay = array(
 			'1' => array('pay'=>1,'name'=>'cash'),
 			//'2' => array('pay'=>2,'name'=>'difference'),
 			'3' => array('pay'=>3,'name'=>'sign'),
 			'4' => array('pay'=>4,'name'=>'alipay'),
 			'5' => array('pay'=>5,'name'=>'wxpay'),
 			'6' => array('pay'=>6,'name'=>'stamp'),
 		);
 		foreach ($pay as $k => $v) {
 			$map['pay'] = $v['pay'];
 			$return[$v['name']] = $model->where($map)->sum('money');
 		}
 		return $return;
 	}
 	/**
 	 * 窗口代收款 
 	 */
 	function collection($plan_id = '',$user_id,$datatime = '')
 	{
 		$map = array(
 			'product_id' => get_product('id'),
 			'user_id'	 => $user_id,
 			'status'	 =>	'1',
 			'plan_id'	 => $plan_id
 		);
 		if(!empty($datatime)){
 			$map['createtime'] = $datatime['createtime'];
 		}
 		$pay = array(
 			'1' => array('pay'=>1,'name'=>'cash'),
 			'3' => array('pay'=>3,'name'=>'sign'),
 			'4' => array('pay'=>4,'name'=>'alipay'),
 			'5' => array('pay'=>5,'name'=>'wxpay'),
 			'6' => array('pay'=>6,'name'=>'stamp'),
 		);
 		$db = D('Collection');
 		foreach ($pay as $k => $v) {
 			$map['pay'] = $v['pay'];
 			$return['d'.$v['name']] = $db->where($map)->sum('money');
 		}
 		return $return;
 	}
 	/**
 	 * 汇总月度报表
 	 * 数据源来自Report_data
 	 * 只考虑时间  不考虑场次等信息  且只考虑
 	 */
 	public function months($datetime)
 	{
 		//dump($datetime);
 		if(empty($datetime)){
 			return false;
 		}
 		$months = substr($datetime, 0,6);
 		$day = 'day_'.(int)substr($datetime, 6);
 		$model = D('ReportSum');
 		/**
 		 * 检测是否已经生成
 		 * TODO   多产品下分产品删除
 		 */
 		
 		if($model->where(['plantime'=>$datetime])->count() != 0){
 			//已生成的全部删除
 			$model->where(['plantime'=>$datetime])->delete();
 		}
 		//dump();
 		$map =[
 			'plantime'	=>	$datetime,
 			'type'		=>	['in','2,4'],
 		];
 		//要处理数据 根据日期获取数据
 		$list = D('ReportData')->where($map)->select();
 		echo D('ReportData')->_sql();
 		echo '合并前'.count($list);
 		//dump($map);
 		//根据销售计划归类
 		foreach ($list as $ky => $vae) {
 			$plan[$vae['plan_id']][$vae['channel_id']][$vae['price_id']] = [
	 			'plantime'	 => $vae['plantime'],
	 			'product_id' => $vae['product_id'],
	 			'plan_id' 	 => $vae['plan_id'],
	 			'type'		 => $vae['type'],
	 			'channel_id' => $vae['channel_id'],
	 			'number'     => $vae['number'] + $plan[$vae['plan_id']][$vae['channel_id']][$vae['price_id']]['number'],
	 			'price_id'   => $vae['price_id'],
	 			'price'		 => $vae['price'],
			    'discount'	 => $vae['discount'],
			    'money'		 => $vae['money'] + $plan[$vae['plan_id']][$vae['channel_id']][$vae['price_id']]['money'],
			    'moneys'	 => $vae['moneys'] + $plan[$vae['plan_id']][$vae['channel_id']][$vae['price_id']]['moneys'],
			    'subsidy'	 => $vae['subsidy'] + $plan[$vae['plan_id']][$vae['channel_id']][$vae['price_id']]['subsidy'],
	 		];
 		}
 		//降维处理
 		foreach($plan as $value){    
	        foreach($value as $v){
	        	foreach ($v as $val) {
	        		$arr2[]=$val;
	        	}    
	        }    
	    }
	    echo '合并后'.count($arr2);
 		return $model->addAll($arr2);
 	}
 	//分按月 分渠道商 分票型汇总
 	public function months2($datetime='')
 	{
 		//dump($datetime);
 		if(empty($datetime)){
 			return false;
 		}
 		$months = substr($datetime, 0,6);
 		$day = (int)substr($datetime, 6);
 		$model = D('ReportMonths');
 		//判断产品
 		$product = M('Product')->where(['status'=>1])->field('id')->select();
 		foreach ($product as $i => $ve) {
 			//检测是否重新生成
 			$total = $model->where(['months'=>$months,'product_id'=>$vae['product_id']])->getField('day_'.$day);
 		}
 		

 		$map =[
 			'plantime'	=>	$datetime,
 			'type'		=>	['in','2,4'],
 		];
 		//要处理数据 根据日期获取数据
 		$list = D('ReportData')->where($map)->select();
 		//if($this->procof['agent'] == '1'){
			//开启代理商制度，时执行
			
		//}
		$lists = Report::level_fold($list);
 		foreach ($lists as $ky => $vae) {
 			$plan[$vae['channel_id']][$vae['price_id']] = [
 				'months'	 => $months,
 				'day_'.$day	 => $vae['number'] + $plan[$vae['channel_id']][$vae['price_id']]['number'],
	 			'product_id' => $vae['product_id'],
	 			'channel_id' => $vae['channel_id'],
	 			'number'     => $vae['number'] + $plan[$vae['channel_id']][$vae['price_id']]['number'],
	 			'price_id'   => $vae['price_id'],
	 			'price'		 => $vae['price'],
			    'discount'	 => $vae['discount'],
			    'money'		 => $vae['money'] + $plan[$vae['channel_id']][$vae['price_id']]['money'],
			    'moneys'	 => $vae['moneys'] + $plan[$vae['channel_id']][$vae['price_id']]['moneys'],
			    'subsidy'	 => $vae['subsidy'] + $plan[$vae['channel_id']][$vae['price_id']]['subsidy'],
	 		];
 		}
 		foreach($plan as $value){    
	        foreach($value as $vs){
	        	$arr2[]=$vs;		 
	        }    
	    }
	    foreach ($arr2 as $k => $v) {
	    	//判断该渠道商在当月是否已注册
    		if($model->where(['channel_id'=>$v['channel_id'],'months'=>$v['months'],'price_id'=>$v['price_id']])->find()){
    			$up = [
    				'day_'.$day  => $v['number'],
    				'number'	 => ['exp','number+'.$v['number']],
    				'money'		 => ['exp','money+'.$v['money']],
    				'moneys'	 => ['exp','moneys+'.$v['moneys']],
    				'subsidy'    => ['exp','subsidy+'.$v['subsidy']],
    			];//dump($up);
    			$status = $model->where(['months' => $v['months'],'product_id' => $v['product_id'],'channel_id' => $v['channel_id'],'price_id'=>$v['price_id']])->save($up);
    			//dump($status);
    		}else{
    			$model->add($v);
    		}
	    }
 	}
}