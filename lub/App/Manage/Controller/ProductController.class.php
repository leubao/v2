<?php
// +----------------------------------------------------------------------
// | LubTMP 产品管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Manage\Controller;
use Common\Controller\ManageBase;
use Libs\Service\Operate;
class ProductController extends ManageBase{

	function index(){
		$this->basePage('Product');
		$this->display();
	}
	/**
	 * 添加产品
	 */
	function add(){
		if(IS_POST) {
			$item_id = I('post.item_id',0,intval);
			$info = Operate::do_read('Item',0,array('id'=>$item_id));
			//判断之前是否已有产品
			if(!empty($info['product'])){
				$product = explode(',', $info['product']);
				array_push($product, "replace");
				
			}else{
				$product = "replace";
			}
			//构造$transData
			$transData = array(
				array(
					'type'	=>	'save',
					'table'	=>	'Item',
					'map'	=>	array('id'=>$item_id),
					'data'	=>	array('product'=>$product),
				),
			);
			if(Operate::do_add('Product','',true,$transData)){
				//更新商户产品列表    合并原有的产品
				//$sta = M('Item')->where(array('id'=>I('post.item_id',0,intval)))->save();
				$this->srun("添加成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->error("添加失败！");
			}
		} else {
			$item = Operate::do_read('Item',1,array('status'=>1));
			$info = Operate::do_read('Place',1,array('status'=>1));
			$template = Operate::do_read('TemplateList',1,array('status'=>1));
			$this->assign('place',$info)->assign('item',$item)->assign('template',$template);
			$this->display();
		}	
	}
	/**
	 * 编辑产品
	 */
	function edit(){
		if(IS_POST) {

			
			if(Operate::do_up('Product')){
				$this->srun("更新成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->error("更新失败！");
			}
		} else {
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$item = Operate::do_read('Item',1,array('status'=>1));
				$data = Operate::do_read('Product',0,array('id'=>$id));
				$info = Operate::do_read('Place',1,array('status'=>1));
				$this->assign('data',$data)
					->assign('item',$item)
					->assign('place',$info)
					->display();
			}else{
				$this->error('参数错误!');
			}
		}	
	}
	/**
	 * 删除产品
	 */
	function del(){
		$id = I('get.id',0,intval);
		$status = Operate::do_read('Plan','0',array('product_id'=>$id));
		if($status){
			$this->error("该产品已经销售，不能直接删除!");
		}
		if(Operate::do_del('Product',array('id'=>$id))){
			$this->srun("删除成功！", array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$this->error("删除失败！");
		}	
	}
	/**
	 * 更新识别码
	 */
	function upidcode(){
		$id = I('get.id',0,intval);
		if(empty($id)){
			$this->error('参数错误!');
		}
		if(M('Product')->where(array('id'=>$id))->setField('idCode',genRandomString())){
			$this->srun("更新成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		}else{
			$this->error("更新失败！");
		}
	}
}