<?php 
// +----------------------------------------------------------------------
// | LubTMP 报表导出
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19 
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use \Libs\Service\Exports; 
class ExprotController extends Base{
	/*
	* 导出Execl
	*/
	function export_execl(){
		$info = I('get.');
		switch ($info['report']) {
			case 'channel':
				return $this->channel($info);
				break;
			case 'operator':
				//售票员日报
				return $this->operator($info);
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
			//构造查询条件
			$starttime = $info('starttime');
		    $endtime = $info('endtime') ? $info('endtime') : date('Y-m-d',time());
		    $sn = $info('sn');
		    $type = $info('type') ? $info('type') : '1';
		    $crm_id = $info('channel_id');
		    $types = I('types');
		    //传递条件
		    $starttime = strtotime($info['starttime']);
            $endtime = strtotime($info['endtime']) + 86399;
            $map['createtime'] = array(array('GT', $starttime), array('LT', $endtime), 'AND');
	        if(!empty($info['sn'])){
	        	$map['order_sn'] = $info['sn'];
	        }
	        if(!empty($info['crm_id'])){
	        	$map['crm_id'] = $info['crm_id'];
	        }
			
			//查询数据
			$list = $db->where($map)->select();
			//构造导出数据集
			foreach ($list as $k => $v) {
	   			$data[] = array(
	   				'datetime'	=>	date('Y-m-d H:i:s',$v['createtime']),
	   				'type'		=>	operation($v['type'],1),
	   				'sn'		=>	$v['order_sn'],
		   			'money'		=>	$v['money'],
		   			'moneys'	=>	$v['moneys'],
		   			'user'		=>	userName($v['user_id'],1),
		   			'channel'	=>	crmName($v['crm_id'],1),
		   			'remark'	=>	$v['remark'],
		   			);
	   		}
	   		//景区日报表明细 表头
			$headArr = array(
	   			'datetime'	=>	'操作时间',
	   			'type'		=>	'类型',
	   			'sn'		=>	'订单号',
	   			'money'		=>	'操作金额',
	   			'moneys'	=>	'余额',
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