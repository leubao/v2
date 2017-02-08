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
		if(!empty($info['priceid'])){
			$map['priceid'] = $info['priceid'];
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
   				'area'		=>	areaName($v['area'],1),
   				'ticket'	=>	ticketName($v['priceid'],1), 
   				'price'		=>	$v['price'], 
   				'discount'  =>	$v['discount'], 
	   			'number'	=>	$v['number'],
	   			'money'		=>	$v['money'],
	   			'moneys'	=>	$v['moneys'],
	   			'subsidy'	=>	$v['subsidy'],
	   			'user'		=>	userName($v['user_id'],1),
	   			);
   		}
   		//景区日报表明细 表头
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
			
			//查询数据
			$list = $db->where($map)->select();
			//构造导出数据集
			foreach ($list as $k => $v) {
	   			$data[] = array(
	   				'datetime'	=>	date('Y-m-d H:i:s',$v['createtime']),
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
?>