<?php
// +----------------------------------------------------------------------
// | LubTMP 演出座位属性调整
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
use Item\Service\Partner;

use Libs\Service\Operate;
class SeatController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
		//取得所有产品信息
		$this->products = cache('Product');
		//取得当前产品信息
		$this->product = $this->products[$this->pid];
	 }
	 /*加载初始售票框架*/
	function index(){
		//获取所有销售计划
		if(IS_POST){
			$todaya = I('post.plan');
			if(empty($todaya)){
				$this->erun('请选择售票日期!');
			}
			$info = explode('-', $todaya);
		}else{
			//今天时间戳
			$today = strtotime(date('Y-m-d',time()));
			$todaya = $today."-1";
		}
		/*TODO  将过期的计划改变状态  未来写到计划任务*/
		$today =  strtotime(date('Ymd'));
		$plan = M('Plan')->where(array('plantime'=>array('lt',$today)))->select();
		if(!empty($plan)){
			foreach ($plan as $ke=>$va){
				M('Plan')->where(array('id'=>$va['id'],'status'=>array('NEQ',0)))->setField('status','0');
			}
		}
		$map = array(
			'product_id'=>$this->pid,
			'status'=>2,//状态必为售票中
			'plantime' => (int)$info[0] ? (int)$info[0] : $today,
			'games' => (int)$info[1] ? (int)$info[1] : 1 ,
		);
		//取得今天计划的ID
		$todayplan = Operate::do_read('Plan',0,$map);
		session('plan_kz',$todayplan);//缓存
		// 判断产品类型
		if($this->product['type'] <> '1'){
			$tictype = $this->getPrice('',$this->product['type'],'');
		}
		$plan = Operate::do_read('Plan',1,array('product_id'=>$this->pid,'status'=>2));
		$this->assign('plan',$plan)
		     ->assign('today',$todaya)
			 ->assign('todayplan',$todayplan) 
			 ->assign('area',unserialize($todayplan['param']))
			 ->assign('product',$this->product)
			 ->assign('tictype',$tictype)
		     ->display();
	}
	//高级控座   控座座椅位置
	function control(){
		$plan = session('plan_kz');
		if(empty($plan)){
			//强制刷新售票navtab
		}
		//可售区域 及授权票型
		$param = unserialize($plan['param']);
		$this->assign('param',$param)
			 ->assign('plan',$plan);
		$this->display();
	}
	/*
	获取区域
	*/
	function seat(){
		$aid = I('get.aid',0,intval);
		if(empty($aid)){
			$this->erun('参数错误!');
		}
		$plan = session('plan_kz');
		//加载座椅
		$info = Operate::do_read('Area',0,array('id'=>$aid,'status'=>1));
		$seat = unserialize($info['seat']);
		$row = array_keys($seat);
		$param = unserialize($plan['param']);
		//删除当前区域
		$area = array_diff($param['seat'],array($aid));
		$this->assign('data',$info)
			->assign('seat',$seat)
			->assign('row',$row)
			->assign('aid',$aid)
			->assign('area',$area)
			->assign('plan_id',$plan['id'])
			->display();
	}
	/**
	 * 加载座椅状态
	 */
	function seats(){
		$area = I('get.aid',0,intval);
		$plan_id = I('get.plan_id',0,intval);
		if(empty($area)){
			$this->erun('加载错误!');
		}
		if(!empty($plan_id)){
			$plan = Operate::do_read('Plan',0,array('id'=>$plan_id));
		}else{
			$plan = session('plan_kz');
		}
		$table=ucwords($plan['seat_table']);
		//只查询已售出的座位
		$info = M($table)->where(array('area'=>$area,'status'=>array('NEQ',0)))->field(array('seat'=>'sid','status'))->select();
		$return = array(
			'status' => '1',
			'message' => '区域加载成功!',
			'seat'	=> $info ? $info : 0,
			);
		echo json_encode($return);
	}
	
}