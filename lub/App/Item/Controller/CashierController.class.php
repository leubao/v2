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
use Libs\Service\Operate;
class CashierController extends ManageBase{
	function _initialize(){
	 	parent::_initialize();
	}
	
	function index()
	{	
		//加载当前商品计划
		$today = strtotime(date('Y-m-d'))."-1";
		$plan = Operate::do_read('Plan',1,array('product_id'=>get_product('id'),'status'=>2));
		$type = '1';
		//根据计划加载商品
		$this->assign('plan',$plan)
		     ->assign('today',$today)
		     ->assign('type',$type)
		     ->display();
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
}