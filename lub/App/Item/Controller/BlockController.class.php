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

use Libs\Service\Operate;
class BlockController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
		//取得所有产品信息
		$this->products = cache('Product');
		//取得当前产品信息
		$this->product = $this->products[$this->pid];
	 }
	/*加载初始售票框架'plantime'=>$todays,*/
	function index(){
		$today = strtotime(date('Y-m-d'))."-1";
		$todays = strtotime(date('Y-m-d'));
		$plan = Operate::do_read('Plan',1,array('product_id'=>$this->pid,'status'=>array('in','2,3')));
		$this->assign('plan',$plan)
		     ->assign('today',$today)
			 ->assign('product',$this->product)
		     ->display();
	}
	/*设置门票销售session*/
	function set_session_plan(){
		if(IS_POST){
			$pinfo = json_decode($_POST['info'],true);
			$info = explode('-', $pinfo['plan']);
			$map = array(
				'product_id'=>$this->pid,
				'status'=>array('in','2,3'),//状态必为售票中
				'plantime' => (int)$info[0] ? (int)$info[0] : $today,
				'games' => (int)$info[1] ? (int)$info[1] : 1 ,
			);
			$plan = M('Plan')->where($map)->field('id,param,seat_table,product_type,plantime,starttime,endtime,games')->find();
			$param = unserialize($plan['param']);
			foreach ($param['seat'] as $k => $v) {
				$area[] = array(
					'id'	=>	$v,
					'name'	=>	areaName($v,1),
					'number'=>  areaSeatCount($v,1),
					'num'	=>  area_count_seat($plan['seat_table'],array('status'=>'0','area'=>$v),1),
					'nums'	=>	area_count_seat($plan['seat_table'],array('status'=>array('in','2,66,99'),'area'=>$v),1),//已售出
					'numb'	=>	area_count_seat($plan['seat_table'],array('status'=>array('in','66'),'area'=>$v),1),//预定数
				); 
			}
			$sale = array(
				'nums'	=>	area_count_seat($plan['seat_table'],array('status'=>array('in','2,66,99')),1),
				'numb'	=>	area_count_seat($plan['seat_table'],array('status'=>array('in','66')),1),
				'money' =>	format_money(M('Order')->where(array('status'=>array('in','1,7,9'),'plan_id'=>$plan['id']))->sum('money')),
			);
			//dump($plan);dump($param);
			$return = array(
				'statusCode' => '200',
				'info'	=>	'',
				'plan'	=> $plan['id'],
				'area'	=> $area,
				'sale'	=> $sale,
			);
			//设置session
			session('plan_kz',$plan);
			echo json_encode($return);
			return true;
		}
	}
	//基本控座 只控制数量  不控制座椅位置
	function basics(){
		$plan = session('plan_kz');
		if(empty($plan)){
			//强制刷新售票navtab
		}
		//可售区域 及授权票型
		$param = unserialize($plan['param']);
		$this->assign('param',$param)
			 ->assign('plan',$plan)
			 ->display();
	}
	//开始控座
	function control_block(){
		$plan = session('plan_kz');
		$table=ucwords($plan['seat_table']);
		if(IS_POST){
			$ginfo = I('post.');
			if($ginfo['type'] == '2'){
				//基本控座
				foreach ($ginfo['area'] as $key => $value) {
					if($ginfo['area_num'][$key] > '0'){
						$area[$key]=array(
							'area'=>$value,
							'num'	=> $ginfo['area_num'][$key],
						);
						$status = $this->insert_block($table,$area[$key],2,2);
					}
					
				}
				//TODO 判断不严谨
				if($status){
					session('plan_kz',null);
					$this->srun("控座成功!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun("控座失败!");
				}
			}else{
				//高级控座
				$ginfo = json_decode($_POST['info'],true);
				$status = $this->insert_block($table,$ginfo,1,2);
				if($status){
					$return = array(
						'statusCode' => '200',
					);
					session('plan_kz',null);
				}else{
					$return = array(
						'statusCode' => '300',
					);
				}
				echo json_encode($return);
			}
		}
	}
	//高级控座   控座座椅位置
	function senior(){
		$area = I('get.area',0,intval);
		$plan = I('get.plan',0,intval);
		$type = I('get.type',1,intval);
		if(!empty($plan)){
			$this->available($plan);
			$this->assign('type',$type)->assign('area',$area)->assign('plan',$plan)->display();
		}else{
			$this->erun('参数有误!');
		}
	}
	/*
	获取区域
	*/
	function seat(){
		$area = I('get.area',0,intval);
		$planid = I('get.plan',0,intval);
		$type = I('get.type',1,intval);
		$plan = session('plan_kz');
		if(empty($plan) || $plan['id'] <> $planid){
			$this->erun("参数错误!");
		}else{
			//加载座椅
			$info = Operate::do_read('Area',0,array('id'=>$area,'status'=>1),'','id,name,face,is_mono,seats,num,template_id');
			$info['seats'] = unserialize($info['seats']);
			$this->assign('data',$info)
				->assign('area',$area)
				->assign('plan',$plan)
				->assign('type',$type)
				->display();
		}
	}
	//释放控座  一般释放
	function basics_release(){
		$plan = session('plan_kz');
		if(empty($plan)){
			//强制刷新售票navtab
		}
		//可售区域 及授权票型
		//TODO 限制最大释放座位
		$param = unserialize($plan['param']);
		$this->assign('param',$param)
			 ->display();
	}
	//释放座位
	function release(){
		$plan = session('plan_kz');
		$table = ucwords($plan['seat_table']);
		if(IS_POST){
			$ginfo = I('post.');
			if($ginfo['type'] == '2'){
				//基本控座
				foreach ($ginfo['area'] as $key => $value) {
					if($ginfo['area_num'][$key] > '0'){
						$area[$key]=array(
							'area'=>$value,
							'num'	=> $ginfo['area_num'][$key],
						);
						$status = $this->insert_block($table,$area[$key],2,1);
					}
				}
				//TODO 判断不严谨
				if($status){
					$this->srun("释放控座成功!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun("释放控座失败!");
				}
			}else{
				//高级控座
				$ginfo = json_decode($_POST['info'],true);
				$status = $this->insert_block($table,$ginfo,1,1);
				if($status){
					$return = array(
						'statusCode' => '200',
					);
				}else{
					$return = array(
						'statusCode' => '300',
					);
				}
				echo json_encode($return);
			}
		}
	}
	/**
	 * 快捷控座位
	 */
	function set_control(){
		$db = M('ControlSeat');
		if(IS_POST){
			$pinfo = I('post.');
			//向对应销售计划写入控座分组id
			if(empty($pinfo['control']) || empty($pinfo['plan'])){
				$this->erun("参数错误!");
			}
			$map = array(
				'status' => '1',
				'id'	 =>	array('in',implode(',',$pinfo['control'])),
			);
			$plan = M('Plan')->where(array('id'=>$pinfo['plan']))->field('id,seat_table')->find();
			$list = $db->where($map)->field('id,state,type,seat')->select();
			foreach ($list as $k => $v) {
				$seat = unserialize($v['seat']);
				foreach ($seat as $ke => $va) {
					if(!empty($va['seat'])){
						$area = array(
							'area'  => $va['id'],
							'seat'	=> $va['seat'],
						);
						$status = $this->insert_block($plan['seat_table'],$area,1,$pinfo['type'],$v['state']);
					}
				}
			}
			//TODO 判断不严谨
			if($status){
				$this->srun("操作成功!",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("操作失败!");
			}
		}else{
			$ginfo = I('get.');
			$list = $db->where(array('status'=>1))->field('id,name')->select();
			$this->assign('ginfo',$ginfo)->assign('data',$list)->display();
		}
	}
	/**
	 * 写入控座	
	 * @param  string  $table   表名称
	 * @param  array   $data    更新条件
	 * @param  int  $type    类型1按座位2按区域
	 * @param  int  $release 操作类型1释放座位2控制座
	 * @param  integer $state   状态
	 * @return [type]           [description]
	 */
    private function insert_block($table,$data,$type,$release = null,$state = 66){
   		if($release == '1'){
   			//释放座位
   			if($type == '1'){
   				/*按座位号更新*/
   				$up = M(ucwords($table))->where(array('area'=>$data['area'],'seat'=>array('in',$data['seat']),'status'=>'66'))->setField('status',0);
   				return $up;
   			}else{
   				//按区域更新
   				$status = M(ucwords($table))->where(array('area'=>$data['area'],'status'=>'66'))->limit($data['num'])->setField('status',0);
   				return $status;
   			}
   		}else{
   			//控制座位
   			if($type == '1'){
   				/*按座位号更新*/
   				$up = M(ucwords($table))->where(array('area'=>$data['area'],'seat'=>array('in',$data['seat']),'status'=>array('neq','2,99')))->setField('status',$state);
   				return $up;
   			}else{
   				//按区域更新
   				$status = M(ucwords($table))->where(array('area'=>$data['area'],'status'=>'0'))->limit($data['num'])->setField('status',66);
   				return $status;
   			}
   		}
    }	
}