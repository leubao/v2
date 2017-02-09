<?php
// +----------------------------------------------------------------------
// | LubTMP 窗口收银台
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
class CashierController extends ManageBase{
	function _initialize(){
	 	parent::_initialize();
	}
	
	function index()
	{
		$this->display();
	}
	//收银台商品
	function goods()
	{
		$this->basePage('Goods','','id DESC,status DESC');
		$this->display();
	}
	/**
	 * 新增收银台商品
	 * 升舱补卡差价类拟化成商品,以商品形式售卖
	 */
	function add_goods(){
		if(IS_POST){
			$_POST['scene'] = implode(',',$_POST['scene']);
			$product = explode(',',$_POST['product_id']);
			$product = array_unique(explode(',',$_POST['product_id']));
			$_POST['product'] = implode(',',$product);
			$model = D('Item/Goods');
			if($model->create()){
				if($model->add()){
					$this->srun('新增成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun('新增失败',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}
			}else{
				$this->erun('新增失败',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}
		}else{
			$this->display();
		}
	}
	/**
	 * 编辑商品，只允许编辑名称
	 */
	function edit_goods(){
		if(IS_POST){
			$_POST['scene'] = implode(',',$_POST['scene']);
			$product = explode(',',$_POST['product_id']);
			$product = array_unique(explode(',',$_POST['product_id']));
			$_POST['product'] = implode(',',$product);
			$model = D('Item/Goods');
			if($model->create()){
				if($model->save()){
					$this->srun('更新成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun('更新失败',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}
			}else{
				$this->erun('更新失败');
			}
		}else{
			$id = I('id');
			if(!empty($id)){
				$data = D('Goods')->where(array('id'=>$id))->find();
				$data['products'] = explode(',',$data['product']);
				$this->assign('data',$data);
				$this->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}
	/**
	 * 删除商品,直接停用
	 */
	function del_goods(){
		$id = I('get.id',0,intval);
		if(!empty($id)){
			$map = array("id"=>$id);
			$model = D('Item/Goods');
			//停用状态的数据，删除会直接删除
			if($model->where($map)->getField('status') == '0'){
				$del = $model->where($map)->delete();
			}else{
				$del = $model->where($map)->setField('status','0');
			}
			if($del){
				$this->srun("删除成功!", array('tabid'=>$this->menuid.MODULE_NAME));
			}else {
				$this->erun('删除失败!');
			}
		}else{
			$this->erun('参数错误!');
		}
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