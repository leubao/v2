<?php
// +----------------------------------------------------------------------
// | LubTMP 演出控座
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
use Item\Service\Partner;
class BlockController extends ManageBase{
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
		/**
		 * TODO  将过期的计划改变状态  未来写到计划任务
		 */
		$today =  strtotime(date('Ymd',time()));
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
		session('plan',$todayplan);//缓存
		// 判断产品类型
		if($this->product['type'] <> '1'){
			$tictype = $this->getPrice('',$this->product['type'],'');//dump($tictype);
		}//dump($this->product);
		$plan = Operate::do_read('Plan',1,array('product_id'=>$this->pid,'status'=>2));
		$this->assign('plan',$plan)
		     ->assign('today',$todaya)
			 ->assign('todayplan',$todayplan) 
			 ->assign('area',unserialize($todayplan['param']))
			 ->assign('product',$this->product)
			 ->assign('tictype',$tictype)
		     ->display();
	}
	//基本控座 只控制数量  不控制座椅位置
	function basics(){

	}
	//高级控座   控座座椅位置
	function senior(){

	}
}