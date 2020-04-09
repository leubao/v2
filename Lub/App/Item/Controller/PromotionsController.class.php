<?php
// +----------------------------------------------------------------------
// | LubTMP 活动促销
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
class PromotionsController extends ManageBase{

	protected function _initialize() {
	 	parent::_initialize();
	}
	//当前产品可用活动列表
	public function index()
	{
		$map = [
			'product_id'	=>	get_product('id'), 
			'status'		=>	1,
			'_string'   	=>  "FIND_IN_SET(1,is_scene)"
		];
		$list = D('Activity')->where($map)->field('id,title,type,starttime,endtime,remark')->select();
		$this->assign('data',$list)->display();
	}
	//活动操作页面
	public function work($id)
	{
		//读取活动
		$info = D('Activity')->where(['id'=>$id])->find();
		$info['param'] = json_decode($info['param'],true);
		//判断活动是否正在进行
		if(!$this->check_active($info)){
			$this->erun("活动已结束,或已经停用",array('tabid'=>$this->menuid.MODULE_NAME));
		}
		//根据活动类型加载
		switch ($info['type']) {
			case '1':
				//买赠
				$idcard = $info['param']['info']['card'];
				$this->assign('idcard',json_encode($idcard));
				$this->assign('type',$info['is_team']);
				$tempate = 'buy';
				break;
			case '3':
				$idcard = $info['param']['info']['card'];
				$this->assign('idcard',json_encode($idcard));
				$this->assign('type',$info['is_team']);
				$tempate = 'area_sale';
				break;
			case '5':
				$idcard = $info['param']['info']['card'];
				$tempate = 'pack';
				break;
			case '6':
				$idcard = $info['param']['info']['card'];
				$tempate = 'pack';
				break;	
			default:
				break;
		}
		//售票类型
		$pinfo = I('get.');//dump($idcard);
		$today = date('Y-m-d');
		$this->assign('today',$today)
			->assign('data',$info)
			 ->assign('product',$this->product);
		//读取相关配置
		$this->display($tempate);
	}
	//校验活动是否正在进行时
	public function check_active($info)
	{
		if($info['status'] == 0){
			return false;
		}
		$today = strtotime(date('Ymd'));
		if($info['endtime'] < $today){
			D('Activity')->where(['id'=>$info['id']])->setField('status',0);
			return false;
		}
		return true;
	}
}