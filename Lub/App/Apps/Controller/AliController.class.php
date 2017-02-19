<?php
// +----------------------------------------------------------------------
// | LubTMP 云平台--支付宝 蚂蚁金服
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace App\Controller;
use Common\Controller\ManageBase;
class AliController extends ManageBase{
	//短信发送记录
	function index(){
		$this->basePage('SmsLog');
		$this->display();
	}
	/**
	 * 收银台设置
	 * type 6
	 * 设置收银台的支付宝账号和微信支付账号
	 */
	function set_config(){
		$db = M("ConfigProduct");   //产品设置表 
		$type = '6';
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
}