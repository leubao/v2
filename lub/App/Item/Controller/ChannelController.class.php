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

	//考核对象
	public function index()
	{	

		$this->basePage('KpiChanel');
		$this->display();
	}
	//考核配置
	function set_config(){
		if(IS_POST){

		}else{
			//读取当前绩效配置  绩效配置的type 5
			$this->display();
		}
	}
	//新增考核对象
	function add(){
		$db = M("ConfigProduct");   //产品设置表
		//定义读取类型
		$list = $db->where(array('product_id'=>$this->pid))->select();
		foreach ($list as $k => $v) {
			$config[$v["varname"]] = $v["value"];
		}
		if(IS_POST){
			$product_id = $_POST["product_id"];
			$type = $_POST['type'];
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
	            //$saveData["product_id"] = $product_id;
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
	//扣分记录
	
	//直接扣分记录
	function points($crm_id){
		$this->basePage();
		$this->display();
	}
	//新增扣分记录
	function add_points(){
		if(IS_POST){

		}else{
			//拉取标准
			$this->display();
		}
	}
	//作废扣分记录
	function del_points(){
		//事务操作
		//先更新分值，再作废记录
		$model = new Model();
		$model->startTrans();
		$model->table(C('DB_PREFIX').$plan['seat_table'])->where($map)->save($data);
		$model->rollback();//事务回滚
		$model->commit();//提交事务
	}
	//TODO  景区门票规则
	/*
	1、新增计划不再新建座位表。
	2、检票表为固定表，售出门票以后新增数据，数据保留60天。这样可实现年卡、次卡、也可以实现通票。一个二维码多个景点，单一景点只允许一次游玩
	   思路：1、如门票选择了五个景点A、B、C、D、E 游玩顺序不限，这个五个景点的标记分别为 11 22 33 44 55 66  检票的为119,229
	   		2、五个景点分别在系统增加5条记录，单一景点，单一检票，门票打印时读取五条记录，组合后打印
	3、*/
}