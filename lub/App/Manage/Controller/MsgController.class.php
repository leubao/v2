<?php
// +----------------------------------------------------------------------
// | LubTMP 消息提示
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Manage\Controller;
use Common\Controller\ManageBase;
class MsgController extends ManageBase {

	function index(){

	}
	/*消息提示窗口
	* 提醒级别 success warning info error
	*/
	public function prompt(){
		//需要提示的内容   待排座订单  退单申请  超量申请的订单
		//超量申请的订单
		//未排座的订单 只查询当天+前一天的订单的
		$endtime = strtotime(date("Ymd"));
        $starttime = $starttime - 86399;
		$map = array('createtime' => array(array('EGT', $start_time), array('ELT', $end_time), 'AND'),'status'=>'1');
		$pre = M('PreOrder')->where($map)->count();
		if($pre <> '0'){
			$info[] = array(
				'title'  => '待审核订单',
				'msg' => '系统中存在'.$pre.'个待审核订单等待处理!',
				'url' => U('Item/Work/bookticket'),
				'tabid'=>'348Item',
				'title'=>'待审核订单',
				'titp'=> 'warning',
			);
		}
		//渠道退单申请
		$refund = M('TicketRefund')->where(array('status'=>'1'))->count();
		if($refund <> '0'){
			$info[] = array(
				'title'  => '渠道退单申请',
				'msg' => '系统中存在'.$refund.'个退单申请等待处理!',
				'url' => U('Item/Work/channel_refund'),
				'tabid'=>'356Item',
				'title'=>'渠道退单',
				'titp'=> 'info',
			);
		}
		//查询新订单
		$newOrder = M('Order')->where(['status'=>1])->count();
		if($newOrder <> '0'){
			$info[] = array(
				'title'  => '新订单接入',
				'msg' => '系统中有'.$newOrder.'个新的订单等待处理!',
				'url' => U('Order/Index/index'),
				'tabid'=>'221Order',
				'title'=>'订单列表',
				'titp'=> 'info',
			);
		}
		//开演前30分钟未取票的订单检测
		//下午三点后每隔半个小时检测一次未政企订单排座但未付款的
		//系统异常记录
		/*$singular = M('')->where('')->count();
		if($singular <> '0'){
			$info[] = array(
				'title'  => '系统异常',
				'msg' => '系统运行中存在异常,请及时处理!',
				'url' => U('Item/Work/channel_refund'),
				'tabid'=>'356Item',
				'title'=>'系统异常',
				'titp'=> 'error',
			);
		}*/
		if($pre <> '0' || $refund <> '0' || $newOrder <> '0'){
			$return = array('status' => 'ok','info'=>$info);
		}else{
			$return = array('status' => 'no');
		}
		echo json_encode($return);
		return true;
	}
	//未取票的订单 开演前半小时开始检测
	function did_take(){
		//获取当天的场次
		//计算开演时间与当前时间的时间差
	}
	//下午三点开始检测渠道待定的订单
}