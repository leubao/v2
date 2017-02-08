<?php
// +----------------------------------------------------------------------
// | LubTMP 商户管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Manage\Controller;

use Common\Controller\ManageBase;
use Libs\Service\Operate;

class ItemController extends ManageBase{
	//商户列表
	function index(){
		$list = Operate::do_read('Item',1,'','id DESC');
		$this->assign('list',$list)
			->display();
	}
	/**
	 * 添加商户
	 */
	function add(){
		if(IS_POST) {
			if(Operate::do_add('Item')){
				$this->srun("添加成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("添加失败！");
			}
		} else {
			$info = Operate::do_read('Place',1,array('status'=>1));
			$this->assign('place',$info);
			$this->display();
		}	
	}
	/**
	 * 编辑商户
	 */
	function edit(){
		if(IS_POST) {
			if(Operate::do_up('Item')){
				$this->srun("添加成功！", U("Item/index"));
			}else{
				$this->error("添加失败！");
			}
		}else{ echo "ssa";
			$id = I('get.id',0,intval);dump($id);
			if(!empty($id)){
				$data = Operte::do_read('Item',0,array('id'=>$id));
				$this->assign('data',$data);
				$this->display();
			}else{
				$this->error('参数错误');
			}
		}	
	}
	/**
	 * 删除商户
	 */
	function del(){
		$id = I('get.id',0,intval);
		$status = Operate::do_read('Product','0',array('item_id'=>$id));
		if($status){
			$this->error("存在产品，不能直接删除!");
		}
		if(Operate::do_del('Item',array('id'=>$id))){
			$this->srun("删除成功！", array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$this->error("删除失败！");
		}
	}
	/**
	 * 员工管理头部菜单
	 */
	function tempnav($param){
		//菜单导航
       	$Custom = array(
           	array('name' => '员工列表', 'app' => MODULE_NAME, 'controller' => CONTROLLER_NAME, 'action' => 'user', 'parameter' => "iid={$param}"),
            array('name' => '添加员工', 'app' => MODULE_NAME, 'controller' => CONTROLLER_NAME, 'action' => 'userAdd', 'parameter' => "iid={$param}"),
            array('name' => '操作记录', 'app' => MODULE_NAME, 'controller' => CONTROLLER_NAME, 'action' => 'logs', 'parameter' => "iid={$param}"),
            array('name' => '登录日志', 'app' => MODULE_NAME, 'controller' => CONTROLLER_NAME, 'action' => 'llogs', 'parameter' => "iid={$param}"),
            array('name' => '产品列表', 'app' => MODULE_NAME, 'controller' => CONTROLLER_NAME, 'action' => 'product', 'parameter' => "iid={$param}"),
         );
        $menuReturn = array('name' => '返回商户列表', 'url' => U('Item/index'));
        $this->assign('Custom', $Custom)
             ->assign('menuReturn', $menuReturn);
	}
	/**
	 * 员工管理
	 */
	function user(){
		$item_id = I('get.iid',0,intval);
		if(!empty($item_id)){
			$info = Operate::do_read('User',1,array('item_id'=>$item_id,'is_scene'=>'2'));
			$this->assign('data',$info);
			$this->tempnav($item_id);
			$this->display();
		}else{
			$this->error('参数错误!');
		}
	}
	/**
	 * 添加员工
	 */
	function userAdd(){
		if(IS_POST){
			if(Operate::do_add('User')){
				$this->srun("添加管理员成功！", U('Item/user',array('iid'=>I('post.item_id',0,intval))));
			}else{
				$error = D('Manage/User')->getError();
                $this->error($error ? $error : '添加失败！');
			}
		} else {
			$item_id = I('get.iid',0,intval);
			if(!empty($item_id)){
				$this->assign("role", D('Manage/Role')->selectHtmlOption(0, 'name="role_id"'))
					 ->assign('item_id',$item_id);
				$this->tempnav($item_id);
				$this->display();
			} else {
				$this->error('参数错误!');
			}
		}
	}
	/**
	 * 产品列表
	 */
	function product(){
		if(IS_POST){
			$product = implode(',', I('post.pro'));
			$item_id = I('post.item_id',0,intval);
			if(M('Item')->where(array('id'=>$item_id))->setField('product',$product)){
				$this->srun("更新成功！", U("Item/product",array('iid'=>$item_id)));
			}else{
				
				$this->erun("更新失败！".M('Item')->getError());
			}
		}else{ 
			$item_id = I('get.iid',0,intval);
			if(!empty($item_id)){
				$proAll = Operate::do_read('Product',1,array('status'=>1));//所有产品
				$pro = M('Item')->where(array('id' => $item_id))->getField('product');//商户下所有产品
				$proArr = explode(',',$pro);//转换为数组
				foreach ($proAll as $val){
					//查询直接产品
					if($val['item_id'] == $item_id){
						$zpro[] = $val;
					}else{
						$dpro[] = $val;
					}
				}
				$this->tempnav($item_id);
				$this->assign('zpro',$zpro)
					 ->assign('dpro',$dpro)
					 ->assign('proArr',$proArr)
					 ->assign('item_id',$item_id)
				     ->assign('empty','<center><div class='/onShow/' id='/nameTip/'><font color='/red/'>该商户无直接产品 </font></div></center>');
				$this->display();
			} else {
				$this->error('参数错误!');
			}
		}
	}
	/**
	 * 员工的产品权限
	 */
	function user_product(){
		if(IS_POST){
			$pro = I('post.pro');
			$product = implode(',', $pro);
			$user_id = I('post.user_id',0,intval);
			$item_id = I('post.item_id',0,intval);
			if(M('User')->where(array('id'=>$user_id))->setField(array('product'=>$product,'defaultpro'=>$pro[0]))){
				$this->srun("更新成功！", U("Item/user_product",array('iid'=>$item_id,'uid'=>$user_id)));
			}else{
				
				$this->erun("更新失败！".M('Item')->getError());
			}
		}else{
			$item_id = I('get.iid',0,intval);
			$uid = I('get.uid',0,intval);
			if(!empty($item_id) || !empty($uid)){
				$pro = Operate::do_read('Item',0,array('id'=>$item_id));//该商户所有权限产品
				$upro = Operate::do_read('User',0,array('id'=>$uid));
				$uproArr = explode(',',$upro['product']);//转换为数组
				$iproArr = explode(',',$pro['product']);
				$this->tempnav($item_id);
				$this->assign('pro',$iproArr)
					 ->assign('upro',$uproArr)
					 ->assign('uid',$uid)
					 ->assign('item_id',$item_id)
					 ->assign('empty','<center><div class='/onShow/' id='/nameTip/'><font color='/red/'>所属商户无产品 </font></div></center>');
				$this->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}
	/**
	 * logs 该商户员工操作日志
	 */
	function logs(){
		if (IS_POST) {
            $this->redirect('index', $_POST);
        }
        $uid = I('uid');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $ip = I('ip');
        $status = I('status');
        if (!empty($uid)) {
            $data['uid'] = array('eq', $uid);
        }
        if (!empty($start_time) && !empty($end_time)) {
            $data['_string'] = " `time` >'$start_time' AND  `time`<'$end_time' ";
        }
        if (!empty($ip)) {
            $data['ip '] = array('like', '%' . $ip . '%');
        }
        if ($status != '') {
            $data['status'] = array('eq', (int) $status);
        }
        if (is_array($data)) {
            $data['_logic'] = 'or';
            $map['_complex'] = $data;
        } else {
            $map = array();
        }
        $count = M("Operationlog")->where($map)->count();
        $page = $this->page($count, 20);
        $Logs = M("Operationlog")->where($map)->limit($page->firstRow . ',' . $page->listRows)->order(array("id" => "desc"))->select();
        $this->assign("Page", $page->show());
        $this->assign("logs", $Logs);
        $this->tempnav(I('get.iid',0,intval));
        $this->display();
	}
	/**
	 * logs 该商户员工登录日志
	 */
	function llogs(){
		if (IS_POST) {
            $this->redirect('loginlog', $_POST);
        }
        $where = array();
        $username = I('username');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $loginip = I('loginip');
        $status = I('status');
        if (!empty($username)) {
            $where['username'] = array('like', '%' . $username . '%');
        }
        if (!empty($start_time) && !empty($end_time)) {
            $where['_string'] = " `logintime` >'$start_time' AND  `logintime`<'$end_time' ";
        }
        if (!empty($loginip)) {
            $where['loginip '] = array('like', '%' . $loginip . '%');
        }
        if ($status != '') {
            $where['status'] = array('eq', $status);
        }
        $model = D("Manage/Loginlog");
        
        $count = $model->where($where)->count();
        $page = $this->page($count, 20);
        $data = $model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array('id' => 'DESC'))->select();
        $this->tempnav(I('get.iid',0,intval));
        $this->assign("Page", $page->show())
                ->assign("data", $data)
                ->assign('where', $where)
                ->display();
	}
}