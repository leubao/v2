<?php
// +----------------------------------------------------------------------
// | LubTMP 渠道考核
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
class ChannelController extends ManageBase{
	function _initialize(){
	 	parent::_initialize();
	}
	//考核对象
	public function index()
	{	
		$this->basePage('KpiChannel',array('product_id'=>$this->pid));
		$this->display();
	}
	//考核配置
	function set_config(){
		$db = M("ConfigProduct");   //产品设置表 
		$type = '5';
		$list = $db->where(array('product_id'=>$this->pid,'type'=>$type))->select();
		foreach ($list as $k => $v) {
			$config[$v["varname"]] = $v["value"];
		}
		if(IS_POST){
			$product_id = $_POST["product_id"];
			if($product_id <> $this->pid){
				$this->erun('配置失败,请刷新页面重试...');
	            return false;
			}
			$data = $_POST;
			if (empty($data) || !is_array($data)) {
	            $this->erun('配置数据不能为空！');
	            return false;
	        }
	        $diff_key = array_diff_key($config,$data);
	        foreach ($data as $key => $value) {
	            if (empty($key)) {
	                continue;
	            }
	            $saveData = array($config,);
	            $saveData["value"] = trim($value);
	            $count = $db->where(array("varname"=>$key,'type'=>$type,'product_id'=>$product_id))->count();
	            $ginfo = array();	
	            if ($count == 0) {//此前无此配置项
	            	if($key!="__hash__"&&$key!="product_id"&&$key!='type'){
		            	$ginfo["varname"] = $key;
		            	$ginfo["value"]   = trim($value);
		            	$ginfo["product_id"] = $product_id;
		            	$ginfo["type"]	=	$type;
		            	$add = $db->add($ginfo);
	            	}
	            }else{
		            if ($db->where(array("varname" => $key,'product_id'=>$product_id,'type'=>$type))->save($saveData) === false) {
		                $this->erun("更新到{$key}项时，更新失败！");
		                return false;
		            }	            	
	            }
	        }
	        //更新未选择的复选框
	        foreach ($diff_key as $key => $value) {
	        	$saveData = array();
	            $saveData["value"] = '0';
	            $saveData["product_id"] = $product_id;
	            if ($db->where(array("varname" => $key,'type'=>$type))->save($saveData) === false) {
		            $this->erun("更新到{$key}项时，更新失败！");
		            return false;
		        }
	        }
	        D('Common/Config')->config_cache();
	        $this->srun("配置成功!", array('tabid'=>$this->menuid.MODULE_NAME));	
		}else{
			$this->assign("vo",$config);
			$this->display();
		}
	}
	//新增考核对象
	function add(){
		if(IS_POST){
			$pdata = I('post.');
			$channel_id = I('channel_id');
			if(D('Item/KpiChannel')->insert($pdata,$this->pid,$channel_id)){
				$this->srun("新增成功",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("新增失败");
			}
		}else{
			//读取当前绩效配置  绩效配置的type 5
			$list = M("ConfigProduct")->where(array('product_id'=>$this->pid,'type'=>5))->select();
			foreach ($list as $k => $v) {
				$config[$v["varname"]] = $v["value"];
			}
			$this->assign('info',$config)->display();
		}
	}
	//编辑考核对象
	function edit(){
		if(IS_POST){
			$pdata = I('post.');
			if(D('Item/KpiChannel')->field('quota,task,id')->save($pdata)){
				$this->srun("更新成功",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("更新失败");
			}
		}else{
			//读取当前绩效配置  绩效配置的type 5
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$info = D('Item/KpiChannel')->where(array('id'=>$id))->find();
				$this->assign('data',$info)->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}
	//作废考核对象
	function delete(){
		//作废考核对象时，作废全部相关考核记录
		$id = I('get.id',0,intval);
		if(!empty($id)){
			//作废记录
			$model = new \Common\Model\Model();
			$model->startTrans();
			//读取记录
			$info = M('KpiChannel')->where(array('id'=>$id))->find();
			if(empty($info) || $info['status'] == '0'){
				$this->erun("考核对象当前状态不允许此项操作");
			}
			$up_channel = array(
				'update_time'	=>	time(),
				'status'		=>	'0'
			);
			$kpi = $model->table(C('DB_PREFIX').'kpi_channel')->where(array('id'=>$id))->save($up_channel);
			$updata = array('status'=>'0','update_time' => time());
			$water = $model->table(C('DB_PREFIX')."kpi_Water")->where(array('crm_id'=>$info['crm_id']))->setField($updata);
			if($water && $kpi){
				$model->commit();//提交事务
				$this->srun('作废成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				error_insert('410008');
				$model->rollback();//事务回滚
				$this->erun('作废失败!');
			}
		}else{
			$this->erun('参数错误!');
		}
	}
	
	//直接扣分记录
	function points(){
		$this->basePage('KpiWater','','status DESC');
		$this->display();
	}
	//新增扣分记录
	function add_points(){
		if(IS_POST){
			$pdata = I('post.');
			$channel_id = I('channel_id');
			if(!$this->is_kpi_obj($channel_id)){
				$this->erun("考核对象不存在....");
			}
			if(D('Item/KpiWater')->insert($pdata,$this->pid,$channel_id,2)){
				$this->srun("新增成功",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("新增失败");
			}
		}else{
			//拉取标准
			$this->display();
		}
	}
	//作废扣分记录
	function del_points(){
		$id = I('get.id',0,intval);
		if(!empty($id)){
			//作废记录
			$model = new \Common\Model\Model();
			$model->startTrans();
			//读取记录
			$info = M('KpiWater')->where(array('id'=>$id))->find();
			if(empty($info) || $info['status'] == '0'){
				$this->erun("记录当前状态不允许此项操作");
			}
			$up_water = array(
				'user_id'		=>	get_user_id(),
				'update_time'	=>	time(),
				'status'		=>	'0'
			);
			$water = $model->table(C('DB_PREFIX').'kpi_water')->where(array('id'=>$id))->save($up_water);
			$updata = array('score' => array('exp','score+'.$info['score']),'update_time' => time());
			$kpi = $model->table(C('DB_PREFIX')."kpi_channel")->where(array('crm_id'=>$info['crm_id']))->setField($updata);
			if($water && $kpi){
				$model->commit();//提交事务
				$this->srun('作废成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				error_insert('410008');
				$model->rollback();//事务回滚
				$this->erun('作废失败!');
			}
		}else{
			$this->erun('参数错误!');
		}
	}
	//常用扣分准侧
	function standard(){
		$this->basePage();
		$this->display();
	}
	/**
	 * 验证考核对象是否存在
	 */
	function is_kpi_obj($channel_id){
		$status = M('KpiChannel')->where(array('crm_id'=>$channel_id))->find();
		return $status;
	}
	//TODO  景区门票规则
	/*
	1、新增计划不再新建座位表。
	2、检票表为固定表，售出门票以后新增数据，数据保留60天。这样可实现年卡、次卡、也可以实现通票。一个二维码多个景点，单一景点只允许一次游玩
	   思路：1、如门票选择了五个景点A、B、C、D、E 游玩顺序不限，这个五个景点的标记分别为 11 22 33 44 55 66  检票的为119,229
	   		2、五个景点分别在系统增加5条记录，单一景点，单一检票，门票打印时读取五条记录，组合后打印
	3、*/
}