<?php 
// +----------------------------------------------------------------------
// | LubTMP 报表导出
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19 
// +----------------------------------------------------------------------
namespace Report\Controller;
use Common\Controller\ManageBase;
use \Libs\Service\Exports;
class ExprotController extends ManageBase{

	protected function _initialize() {
		parent::_initialize();
	}
	/*
	* 导出Execl
	*/
	function export_execl(){
		$info = I('get.');
		switch ($info['report']) {
			case 'today':
				return $this->today($info);
				break;
			case 'channel':
				return $this->channel($info);
				break;
			case 'operator':
				//售票员日报
				return $this->operator($info);
				break;
			case 'tickets':
				//票型销售统计
				return $this->tickets($info);
				break;
			case 'top_up':
				//授信记录导出
				return $this->top_up($info);
				break;
			case 'channel_months':
				return $this->months_report($info);
				break;
			case 'team_order':
				return $this->team_order($info);
				break;
			case 'source_cash':
				//资金来源
				return $this->source_cash($info);
				break;
			case 'print_log':
				//打印日志导出
				return $this->print_log($info);
				break;	
			default:
				# code...
				break;
		}
		

	}
	//景区日报表明细
	function today($info){
		//数据源头
		$db = D('ReportData');
		//构造查询条件
		$map['datetime'] = $info['datetime'];
		$map['product_id'] = $info['product_id'];
		if(!empty($info['price_id'])){
			$map['price_id'] = ['in',rawurldecode($info['price_id'])];
		}
		$map['status'] = '1';
		//查询数据
		$list = $db->where($map)->select();
		//构造导出数据集
		foreach ($list as $k => $v) {
   			$data[] = array(
   				'sn'		=>	$v['order_sn'],
   				'plan'		=>	planShow($v['plan_id'],2,1),
   				'scena'		=>	addsid($v['addsid'],1).'('.channel_type($v['type'],1).')',
   				'channel'	=>	$v['type'] == '1' ? "散客" : crmName($v['channel_id'],1),
   				'user'		=>	userName($v['user_id'],1,1),
   				'area'		=>	areaName($v['area'],1),
   				'ticket'	=>	ticketName($v['price_id'],1), 
   				'price'		=>	$v['price'], 
   				'discount'  =>	$v['discount'], 
	   			'number'	=>	$v['number'],
	   			'money'		=>	$v['money'],
	   			'moneys'	=>	$v['moneys'],
	   			'subsidy'	=>	$v['subsidy'],
	   			
	   		);
   		}
   		//景区日报表明细 表头
		$headArr = array(
   			'sn'		=>	'订单号',
   			'plan'		=>	'销售计划',
   			'scena'		=>	'场景(类型)',
   			'channel'	=>	'渠道商',
   			'user'		=>	'下单人',
   			'area'		=>	'区域',
   			'ticket'	=>	'票型',
   			'price'		=>	'票面价',
   			'discount'	=>	'结算价',
   			'number'	=>	'数量',
   			'money'		=>	'票面金额',
   			'moneys'	=>	'结算价',
   			'subsidy'	=>	'差额',
   			
   		);
   		$filename = $info['datetime']."景区日报表明细";
   		if($info['type'] == '1'){
   			//景区日报表 明细 数据打印
			return Exports::getExcel($filename,$headArr,$data);
   		}else{
   			//景区日报表 汇总 模板打印
			$data = S('Today'.get_user_id());
			if(empty($data)){
				$this->erun("缓存已过期，请重新查询!");
			}
			return Exports::templateExecl($data,'today',$headArr,2);
   		}
		return true;
	}
	//渠道销售统计
	function channel($info){
		$data = S('ChannelReport'.get_user_id());
		if(empty($data)){
			$this->erun("缓存已过期，请重新查询!");
		}
		if($info['type'] == '1'){
			//明细
			return Exports::templateExecl($data,'channel_detail',$info,3);
		}else{
			//汇总
			return Exports::templateExecl($data,'channel_sum',$info,1);
		}
	}
	//售票员日报表
	function operator($info){
		//售票员日报
		$data = S('Operator'.get_user_id());
		if(empty($data)){
			$this->erun("缓存已过期，请重新查询!");
		}
		return Exports::templateExecl($data,'operator',$info,4);
	}
	//票型销售统计
	function tickets($info){
		$data = S('Tickets'.get_user_id());
		if(empty($data)){
			$this->erun("缓存已过期，请重新查询!");
		}
		return Exports::templateExecl($data,'tickets',$info,5);
	}
	//授信记录导出
	function top_up($info){
		if($info['type'] == '1'){
			//导出列表
			//数据源头
			$db = D('CrmRecharge');
		    //传递条件
		    $starttime = strtotime($info['starttime']);
            $endtime = strtotime($info['endtime']) + 86399;
            $map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
	        if(!empty($info['sn'])){
	        	$map['order_sn'] = $info['sn'];
	        }
	        if(!empty($info['crm_id'])){
	        	$map['crm_id'] = array('in',$info['crm_id']['1']);
	        }
			if(!empty($info['types'])){
				$map['type'] = $info['types'];
			}
			//查询数据
			$list = $db->where($map)->select();
			//构造导出数据集
			foreach ($list as $k => $v) {
	   			$data[] = array(
	   				'datetime'	=>	date('Y-m-d H:i:s', $v['createtime']),
	   				'type'		=>	operation($v['type'],1),
	   				'sn'		=>	$v['order_sn'],
		   			'money'		=>	$v['cash'],
	   				'balance'	=>	$v['balance'],
		   			'user'		=>	userName($v['user_id'],1,1),
		   			'channel'	=>	crmName($v['crm_id'],1),
		   			'remark'	=>	$v['remark'],
		   		);
	   		}
	   		//景区日报表明细 表头
			$headArr = array(
	   			'datetime'	=>	'操作时间',
	   			'type'		=>	'类型',
	   			'sn'		=>	'订单号',
	   			'money'		=>	'金额',
   				'balance'	=>	'余额',
	   			'user'		=>	'操作员',
	   			'channel'	=>	'渠道商',
	   			'remark'	=>	'备注',
	   		);
	   		$filename = "授信记录明细表";
	   		return Exports::getExcel($filename,$headArr,$data);
		}else{
			//导出汇总
		}
	}
	//渠道商销售统计月度汇总
	function months_report($info)
	{
		$data = S('ChannelMonths'.get_user_id());
		if(empty($data)){
			$this->erun("缓存已过期，请重新查询!");
		}
		//汇总
		return Exports::templateExecl($data,'channel_sum',$info,8);
	}
	function team_order($info)
	{
		
	}
	/**
	 * 资金来源导出
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-09-03T16:15:18+0800
	 * @param    [type]                   $info                 [description]
	 * @return   [type]                                         [description]
	 */
	public function source_cash($info)
	{
		$data = S('Source'.get_user_id());
		if(empty($data)){
			$this->erun("缓存已过期，请重新查询!");
		}
		//汇总
		return Exports::templateExecl($data,'source_cash',$info,10);
	}
	public function print_log($info)
	{
		if(!isset($info['starttime']) || empty($info['starttime']) || !isset($info['endtime']) || empty($info['endtime'])){
			$this->erun("亲，请选择导出日期范围....");
        	return false;
		}
		$map = [];
		$endtime = strtotime($info['endtime']) + 86399;
        $map['createtime'] = array(array('EGT', strtotime($info['starttime'])), array('ELT', $endtime), 'AND');
		if (!empty($info['type'])) {
            $map['type'] = array('eq', $info['type']);
        }
        if(!empty($info['user_id'])){
            $map['uid'] = array('in',$info['user_id']);
        }
        if(!empty($info['scene'])){
            $map['scene'] = $info['scene'];
        }
        if(!empty($info['sn'])){
            $map['order_sn'] = $info['sn'];
        }

		$db = D('PrintLog');
		//查询数据
		$list = $db->where($map)->select();
		
		if(count($list) > 10000){
			$this->erun("亲，单次导出不超过1万条数据,请修改日期范围....");
        	return false;
		}
		//构造导出数据集
		foreach ($list as $k => $v) {
			switch ($v['type']) {
				case '1':
					$type ='首次打印';
					break;
				case '2':
					$type ='二次打印';
					break;
				default:
					$type ='同步通信';
					break;
			}
   			$data[] = array(
   				'sn'		=>	$v['order_sn'],
   				'type'		=>	$type,
	   			'name'		=>	$v['scene'] == '7' ? crmName($v['uid'],1) : userName($v['uid'],1,1),
	   			'user'		=>	$v['type'] == '2' ? pwd_name($v['user_id'],1) : '首次打印',
	   			'number'	=>	$v['number'],
	   			'scene'		=>	scene($v['scene'],1),
	   			'datetime'	=>	date('Y-m-d H:i:s', $v['createtime']),
	   			'remark'	=>	$v['remark'],
	   		);
   		}
   		//景区日报表明细 表头
		$headArr = array(
   			'sn'		=>	'订单号',
   			'type'		=>	'打印次数',
   			'name'		=>	'操作员',
   			'user'		=>	'授权员',
   			'number'	=>	'数量',
   			'scene'		=>	'打印场景',
   			'datetime'	=>	'操作时间',
   			'remark'	=>	'备注',
   		);
   		$filename = "打印日志";
   		return Exports::getExcel($filename,$headArr,$data);
	}
	//构造表头
	function headArr(){
		$headArr = array();
		switch ($type) {
			case '1':
				//景区日报表明细
				$headArr = array(
		   			'sn'		=>	'订单号',
		   			'plan'		=>	'销售计划',
		   			'scena'		=>	'场景(类型)',
		   			'channel'	=>	'渠道商',
		   			'area'		=>	'区域',
		   			'ticket'	=>	'票型',
		   			'price'		=>	'票面价',
		   			'discount'	=>	'结算价',
		   			'number'	=>	'数量',
		   			'money'		=>	'票面金额',
		   			'moneys'	=>	'结算价',
		   			'subsidy'	=>	'差额',
		   			'user'		=>	'操作员',
		   		);
				break;
			case '2':
				//渠道补贴报表
				$headArr = array(
		   			'sn'		=>	'订单号',
		   			'plan'		=>	'销售计划',
		   			'channel'	=>	'渠道商',
		   			'guide'		=>	'导游',
		   			'area'		=>	'下单人',
		   			'number'	=>	'数量',
		   			'money'		=>	'金额',
		   			'moneys'	=>	'创建时间',
		   			'user'		=>	'操作员',
		   		);
				break;
			default:
				# code...
				break;
		}
		return $headArr;
	}
}
