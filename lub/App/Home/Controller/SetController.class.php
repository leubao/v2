<?php
// +----------------------------------------------------------------------
// | LubTMP  渠道商自助设置
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use Home\Service\Partner;
use Libs\Service\Operate;

class SetController extends Base{
	function _initialize(){
		parent::_initialize();
	}
	
	function index(){
		$this->display();
	}
	/**
	 * 渠道商列表
	 */
	function channel(){
		if (IS_POST) {
            $this->redirect('home.php?g=home&m=Set&a=channel', $_POST);
        }
		$db = D('Crm');
		$where = array();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $status = I('status');
		$where = array(
			'f_agents'=>\Home\Service\Partner::getInstance()->cid,
		);

		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['create_time'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }
        if ($status != '') {
            $where['status'] = $status;
        }
		$count = $db->where($where)->count();
		$Page  = new \Home\Service\Page($count,25);
		$show  = $Page->show();
		$list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('data',$list)
			->assign('page',$show)
			->display();
	}
	/**
	 * 新增渠道商
	 * 一级渠道商新增默认为二级二级新增默认为三级
	 */
	function add_channel(){
		if(IS_POST){
			$uInfo = Partner::getInstance()->getInfo();
			//判断新增级别
			$tlevel = M('Crm')->where(array('id'=>$uInfo['cid']))->field('agent,level')->find();

			if($tlevel['level'] == self::$Cache['Config']['level_1']){
				$level = self::$Cache['Config']['level_2'];
				//设置配额
				//读取上级配额
				//$M('Crm')->where(array(''))->getField();
			}else{
				$level = self::$Cache['Config']['level_3'];
				//设置配额
				
			}
			$data = array(
				'f_agents'=>$uInfo['cid'],
				'product_id'=>$uInfo['product'],
				'groupid'	=>	$uInfo['groupid'],
				'create_time'=> time(),
				'itemid'	=> $uInfo['item_id'],
				'level'		=> $level,
				'agent'		=> $tlevel['agent'],
			);
			$status = Operate::do_add('Crm',$data);
			if($status){
				//更新先前计划配额
				//读取当前所有可售计划及产品
				$plan = M('Plan')->where(array('status'=>2))->field('id,product_id')->select();
				foreach ($plan as $ke=>$va){
					$dataList[] = array(
						'number'	=>	'0',
						'channel_id'=>$status,
						'plan_id'	=>	$va['id'],
						'product_id'	=> $va['product_id'],
					);
				}
				D('QuotaUse')->addAll($dataList);
				$this->success("新增成功!",U('Home/Set/channel'));
			}else{
				$this->error("新增失败!");
			}
		}else{
			$level = Operate::do_read('Role',1,array('parentid'=>self::$Cache['Config']['channel_agents_id'],'is_scene'=>3,'status'=>1),array('id'=>DESC));//dump();
			$this->assign("level",$level)
				->display();
		}
	}
	/**
	 * 编辑渠道商
	 * 不可修改渠道商级别
	 */
	function edit_channel(){
		if(IS_POST){
			if(Operate::do_up('Crm')){
				$this->success("更新成功！", U("Set/channel"));
			}else{
				$this->error('修改失败！');
			}
		}else{
			$id=I('get.id',0,intval);
			if(empty($id)){
				$this->error('参数错误');
			}
			$info = M('Crm')->where(array('id'=>$id))->find();
			$level = Operate::do_read('Role',1,array('parentid'=>self::$Cache['Config']['channel_agents_id'],'is_scene'=>3,'status'=>1),array('id'=>DESC));//dump();
			$this->assign('data', $info)
				->assign("level",$level)
				->display();
		}
	}
	/**
	 * 删除
	 * 检测是否存在员工
	 */
	function del_channel(){
		$id = I('get.id',0,intval);
		if(empty($id)){
			$this->error("参数错误!");
		}
		if(Operate::do_read('User',0,array('cid'=>$id))){
			$this->error("渠道商下存在员工，无法删除!");
		}
		if(Operate::do_del('Crm',array('id'=>$id))){
			$this->success("删除成功!",U('Home/Set/channel'));
		}else{
			$this->error("删除失败!");
		}		
	}
	/**
	 * 渠道商详情
	 */
	function channel_info(){
		$id = I('get.id',0,intval);
		if(empty($id)){
			$this->error("参数错误!");
		}
		$info = Operate::do_read('Crm',0,array('id'=>$id));
		$this->assign('data',$info)
			->display();
	}
	/**
	 * 产品列表
	 */
	function product(){
		$this->display();
	}
	/**
	 * 新增常用联系人
	 */
	function contact_add(){
		if(IS_POST){
			$data = array(
				"createtime" => time(),
				"cid" => Partner::getInstance()->cid,   //渠道商id
			);
			if(Operate::do_add("CommonContact",$data)){
				$this->success("新增成功!",U('Home/Set/contact'));
			}else{
				$this->error("新增失败!");
			}		
		}else{
			$this->display();
		}
	}
	/**
	 * 修改常用联系人
	 */
	function contact_edit(){
		if(IS_POST){
			if(Operate::do_up("CommonContact")){
				$this->success("更新成功!",U('Home/Set/contact'));
			}else{
				$this->error("更新失败!");
			}
		}else{
			$id = I("get.id",0,intval);
			$this->assign("id",$id);
			$map = array(
				"id" => $id
			);
			$list = Operate::do_read('CommonContact',0,$map);
			$this->assign("data",$list);

			$this->display();
		}
	}
	/*
	 * 删除
	 */
	function contact_del(){
		$id=I('get.id',0,intval);
		if(empty($id)){
			$this->error('参数错误');
		}
		//不实际删除 而是停用
		if(Operate::do_up('CommonContact',array('id'=>$id),'',array('status'=>'2'))){
			 $this->success("删除成功！", U("Home/Set/contact"));
		}else{
			 $this->error('删除失败！');
		}
	}
	/**
	 * 常用联系人列表
	 */
	function contact(){
		if (IS_POST) {
            $this->redirect('home.php?g=home&m=Set&a=contact', $_POST);
        }
		$where = array();
		$where = array(
			"cid" => Partner::getInstance()->cid,   //渠道商id
		);
        $start_time = I('start_time');
        $end_time = I('end_time');
        $status = I('status');
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['create_time'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }else{
        	//查询时间段为空时默认查询一个月内的数据
	        $start_time = time() - (86400 * 30);
	        $where['create_time'] = array(array('GT', $start_time), array('LT', time()), 'AND');
        }
	 	if ($status != '') {
            $where['status'] = array(array('NEQ',2),array('EQ',$status), 'AND');
        }else{
        	$where['status'] = array('NEQ',2);//删除之后的不予显示
        }
		$db = D('CommonContact');     
		$count = $db->where($where)->count();
		$Page  = new \Home\Service\Page($count,18);
		$show  = $Page->show();
		$list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('data',$list)
			->assign('page',$show)
			->display();
	}
}