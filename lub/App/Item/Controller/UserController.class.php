<?php
// +----------------------------------------------------------------------
// | LubTMP  商户员工管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

namespace Item\Controller;

use Common\Controller\ItemBase;
use Item\Service\Partner;
use Libs\Service\Operate;

class UserController extends ItemBase{

	protected function _initialize() {
	 	parent::_initialize();
	 }

	/**
	 * 员工列表
	 */

	function index(){
		$where = array('item_id'=>$this->itemid,'is_scene'=>2,'status'=>'1');
		$count = M("User")->where($where)->count();// 查询满足要求的总记录数
		$p = new \Item\Service\Page($count,25);
		$currentPage = !empty($_REQUEST["pageNum"])?$_REQUEST["pageNum"]:1;
		$firstRow = ($currentPage - 1) * 25;
		$info = M("User")->where($where)->order("id DESC,status DESC")->limit($firstRow . ',' . $p->listRows)->select();	
		//$info = Operate::do_read('User',1,array('item_id'=>$this->itemid,'is_scene'=>2),array('id'=>DESC));
		$this->assign('data',$info);
		/*分页设置赋值*/
		$this->assign ( 'totalCount', $count );
		$this->assign ( 'numPerPage', $p->listRows);
		$this->assign ( 'currentPage', $currentPage);
		/*END*/	
		$this->display();
	}

	/**
	 * 添加员工
	 */

	function add(){
		if(IS_POST){
			$pro = I('post.product');
			$arr=array(
				'product' => implode(',', $pro),
				'defaultpro' => $pro[0],
			);
			if(Operate::do_add('User',$arr)){
				$this->srun('新增成功!',$this->navTabId);
			}else{
				$this->erun('新增失败!');
			}
		}else{
			$role = Operate::do_read('Role',1,array('parentid'=>self::$Cache['Config']['client_role_id'],'is_scene'=>2,'status'=>1),array('id'=>DESC));
			
			$item = D('Item')->where(array('id'=>$this->itemid))->relation(true)->find();
			$this->assign('role',$role)
				 ->assign('item_id',$this->itemid)
				 ->assign('ipro',$item['Product'])
			 	 ->display();
		}
	}

	/**
	 * 编辑员工
	 */
	function edit(){
		$id = I('request.id', 0, 'intval');
        if (empty($id)) {
            $this->erun("请选择需要编辑的信息！");
        }
        //判断是否修改本人，在此方法，不能修改本人相关信息
        if (Partner::getInstance()->id == $id) {
           $this->erun("修改当前登录用户信息请进入[我的面板]中进行修改！");
        }
        if (1 == $id) {
            $this->error("该帐号不允许修改！");
        }
        if (IS_POST) {
            if (false !== D('Item/User')->amendManager($_POST)) {
                $this->srun("更新成功！", U("Item/index"));
            } else {
                $error = D('Item/User')->getError();
                $this->erun($error ? $error : '修改失败！');
            }
        } else {
            $id   = I('get.id',0,intval);
			$list = Operate::do_read("User",0,array("id"=>$id));
			$this->assign("data",$list); 
			$role = Operate::do_read('Role',1,array('parentid'=>self::$Cache['Config']['client_role_id'],'is_scene'=>2,'status'=>1),array('id'=>DESC));//dump();
			$item = D('Item')->where(array('id'=>$this->itemid))->relation(true)->find();
			
			$this->assign('role',$role)
				 ->assign('item_id',$this->itemid)
				 ->assign('ipro',$item['Product']);
			$this->display();
        }
	}

	/**
	 * 删除员工
	 * 不会真正删除 在后台将他标记为作废
	 */
	function delete(){
		$id = I('get.id');
		if(!empty($id)){
			$status = M('User')->where(array('id'=>$id))->setField('status',3);
			if($status){
				$this->srun('删除成功!',$this->navTabId);
			} else{ 
				$this->erun('删除失败!');
			}
		} else {
			$this->erun('参数错误!');
		}
	}

	/**
	 * 更改密码
	 */

	function chanpass(){
		if(IS_POST){
			$oldPass = I('post.password', '', 'trim');
            if (empty($oldPass)) {
                $this->error("请输入旧密码！");
            }
            $newPass = I('post.new_password', '', 'trim');
            $new_pwdconfirm = I('post.new_pwdconfirm', '', 'trim');
            if ($newPass != $new_pwdconfirm) {
                $this->error("两次密码不相同！");
            }
            if (D("Item/User")->changePassword(Partner::getInstance()->id, $newPass, $oldPass)) {
                //退出登陆
                Partner::getInstance()->logout();
                //$this->srun("密码已经更新，请从新登陆！");
                $this->ajaxlogin('密码已经更新，请从新登陆！'); 
            } else {
                $error = D("Item/User")->getError();
                $this->erun($error ? $error : "密码更新失败！");
            }
		}else{
			//检查是否登录
	        $uid = (int) Partner::getInstance()->isLogin();
			$this->assign('uid',$uid)
				->display();
		}
	}
	/*
	 * 修改个人信息
	 */

	function myinfo(){
		if(IS_POST){
			if(Operate::do_up('User')){
				$this->srun('更新成功!',$this->navTabId);	
			}else{
				$this->erun('更新失败!');	
			}
		}else{
			//检查是否登录
	        $uid = (int) Partner::getInstance()->isLogin();
	        //获取当前登录用户信息
	        $userInfo = Partner::getInstance()->getInfo();
	        $this->assign('data',$userInfo)
	        	->assign('product',explode(',',$userInfo['product']))
				->display();
		}
	}	

}