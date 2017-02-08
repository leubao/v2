<?php
// +----------------------------------------------------------------------
// | LubTMP  系统接口管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>   2014-10-13
// +----------------------------------------------------------------------
namespace Manage\Controller;
use Common\Controller\ManageBase;
use Libs\Service\Operate;
class ApiController extends  ManageBase{
	
	/**
	 * 接口列表
	 */
	function index(){
		$this->basePage('Port');
		$this->display();
	}
	/**
	 * 添加接口
	 */
	function add(){
		if(IS_POST){
			if (Operate::do_add('Port')){
			    $this->srun("添加成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("添加失败!");
			}
		}else{
			$this->display();
		}
	}
	/**
	 * 编辑接口
	 */
	function edit(){
		if(IS_POST){
			if (Operate::do_up('Port')){
			    $this->srun("更新成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("更新失败!");
			}	
		}else{
			$id = I('get.id', 0, 'intval');
	        if (empty($id)) {
	            $this->erun('请指定需要删除的接口！');
	        }
			$list = Operate::do_read('Port',0,array('id'=>$id));
			$this->assign('data',$list)
				->display();
		}
	}
	//删除行为
    public function delete() {
        $id = I('get.id', 0, 'intval');
        if (empty($id)) {
            $this->erun('请指定需要删除的接口！');
        }
        //删除
        if (Operate::do_del('Port',array('id'=>$id))) {
            $this->srun("接口删除成功，需要更新缓存后生效!", array('tabid'=>$this->menuid.MODULE_NAME));
        } else {
            $this->erun('删除失败！');
        }
    }
	//状态转换
    public function status() {
        $id = I('get.id', 0, 'intval');
        if (empty($id)) {
            $this->erun('请指定需要状态转换的行为！');
        }
        $status = I('get.status',0,'intval') ? 0 : 1;
        //状态转换
        $map = array('id'=>$id);
        if (Operate::do_status('Port',$map,$status)) {
            $this->srun("状态转换成功!", array('tabid'=>$this->menuid.MODULE_NAME));
        } else {
            $this->erun('状态转换失败！');
        }
    }
}