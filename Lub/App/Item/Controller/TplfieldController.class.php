<?php
// +----------------------------------------------------------------------
// | LubTMP 场次模板管理
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Item\Controller;
use Common\Controller\ManageBase;
use Libs\Service\Operate;
/**
 * 在线更新
 * @author huajie <banhuajie@163.com>
 */
class TplfieldController extends ManageBase{

	public function index()
	{
		$map = [];
		$this->basePage('Tplfield',$map,'id DESC,status DESC');
		$this->display();
	}
	/**
	 * 添加接口
	 */
	function add(){
		if(IS_POST){
            $pinfo = I('post.');
            $db = D('Item/Tplfield');
            if($db->create()){
                if($db->add()) {
                    $this->srun("添加成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
                }else{
                    $this->erun("添加失败!");
                }
            }else{
                $this->erun("添加失败1!");
            }
		}else{
			$product_id = (int) $this->pid;
			$this->assign('pid',$product_id);
            $this->display();
		}
	}
	/**
	 * 编辑接口
	 */
	function edit(){
		if(IS_POST){
			if (Operate::do_up('Tplfield')){
                $this->srun("更新成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("更新失败!");
			}	
		}else{
			$id = I('get.id', 0, 'intval');
	        if (empty($id)) {
	            $this->erun('请指定需要场次模板！');
	        }
			$list = D('Item/Tplfield')->where(['id'=>$id])->find();
            $this->assign('data',$list)
				->display();
		}
	}
	//删除行为
    public function delete() {
        $id = I('get.id', 0, 'intval');
        if (empty($id)) {
            $this->erun('请指定需要删除的模板！');
        }
        //删除
        if (Operate::do_del('Tplfield',array('id'=>$id))) {
            $this->srun("模板删除成功!", array('tabid'=>$this->menuid.MODULE_NAME));
        } else {
            $this->erun('删除失败！');
        }
    }
}