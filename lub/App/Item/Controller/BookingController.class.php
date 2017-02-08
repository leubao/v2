<?php
// +----------------------------------------------------------------------
// | LubTMP 窗口预定
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
use Item\Service\Partner;
use Libs\Service\Operate;
class BookingController extends ManageBase{
	//预定单列表
	function index(){
		//检索可售场次
		$this->assign('plan',$this->can_plan());
		//只查询预定订单   预定订单包括 窗口预定  渠道版预定
		$map['status'] = '1';
		$info = Operate::do_read('Order',0,array('order_sn'=>$sn),'','',true);
		$this->display();
	}
	//窗口预定
	function per_window(){
		if(IS_POST){
			
		}else{
			$plan_id = I('plan');
			$plan =F('Plan_'.$plan_id);
			if(empty($plan_id) || empty($plan)){$this->erun("参数错误!");}
			$param = unserialize($plan['param']);
			$this->assign('param',$param)
			 	->assign('plan',$plan)
				->display();
		}
	}
	//为预定区域排座
	function per_seat(){
		if(IS_POST){

		}else{
			$this->display();
		}
	}
	//编辑预定
	function edit_pre(){
		if(IS_POST){
			
		}else{
			$this->display();
		}
	}
	//删除预定  删除预定同时释放座位
	function del_pre(){

	}
}