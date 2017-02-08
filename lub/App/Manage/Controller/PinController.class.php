<?php
// +----------------------------------------------------------------------
// | LubTMP 系统清理程序 只能在单一电脑上执行
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

namespace Manage\Controller;

use Common\Controller\ManageBase;
use Manage\Service\User;
class PinController extends ManageBase {
	
	function _initialize(){
		//判断是否是被授权的电脑
	}
	//操作面板
	function index(){
		if(IS_POST){
			$info = I('post.');
			switch ($setp) {
				case '1':
					if($this->del_order()){

					}
					break;
				case '2':
					if($this->del_order()){

					}
					break;
				case '3':
					if($this->del_order()){

					}
					break;
				case '4':
					if($this->del_order()){

					}
					break;
				default:
					if($this->del_order()){

					}
					break;
			}
		}else{
			$this->display();
		}
		
	}
	//

	/**
	 * 删除订单数据
	 * @param $plan int 计划ID
	 * @param $status string   如：1,2,3
	 * @param $type int 操作类型  1 根据计划删除订单 2 根据计划和状态删除订单   3  根据状态删除订单
	 */
	function del_order($plan, $type, $status){
		//关联删除
		switch ($type) {
			case '1':
				$map = array('plan_id'=>$plan);
				break;
			case '2':
				$map = array('plan_id'=>$plan,'status'=>array('in',$status));
				break;
			case '3':
				$map = array('status'=>array('in',$status));
				break;
		}

	}
	/**
	 * 删除报表数据
	 * @param $param int 计划ID
	 */
	function del_report($plan){

	}
	//补贴记录
	function del_sub($plan){

	}
	//授信记录
	function del_credit($plan){

	}
	//场次记录
	function del_plan($plan){
		//删除座位表
		//查询订单、待处理订单、补贴记录、授信记录、报表数据是否存在  存在则不能删除  不存在删除
	}
	
	/*座位表回收机制*/

}