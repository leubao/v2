<?php
// +----------------------------------------------------------------------
// | LubTMP 商户操作首页
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
use Item\Service\Partner;
use Item\Service\Menu;
class IndexController extends ManageBase{
	
	/**
	 * 操作首页
	 * @author zhoujing
	 */
	public function index(){
		//获取顶部菜单
		$menu = D("Item/Menu")->getMenuList();
    	$leftemu = $menu[self::$Cache["Config"]['clientid']];
    	$userinfo = Partner::getInstance()->getInfo();   	
    	$this->assign('menu',$menu)
    	     ->assign('leftmenu',$leftemu)
    	     ->assign('userInfo',$userinfo)
    	     ->display();		
	}
	
	/**
	 * 切换产品
	 * @param $proid int 产品ID
	 */
	function changProduct(){
		$id = I('get.proid',0,intval);
		if(!empty($id)){
			 session('lub_proId',\Libs\Util\Encrypt::authcode((int) $id, ''));
			 $pid = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
			 if($pid == $id){
			 	redirect(U('Manage/Index/index'));
			 }else{
			 	$this->erun('切换失败!');
			 }
		}else{
			$this->erun('参数错误!');
		}
	}
	
	/**
	 * 基于产品的客户端数据更新 
	 */
	function cache(){
		$product_id = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
		
		$this->display();
	}
	function cacheall(){
		$product_id = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
		
	}
	//提现详情
	function public_cashinfo(){
		$ginfo = I('get.');
		if(empty($ginfo['id'])){
			$this->erun('参数错误');
		}
		$info = M('Cash')->where(array('id'=>$ginfo['id']))->find();
		$this->assign('data',$info)->display();
	}
	
}