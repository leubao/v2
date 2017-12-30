<?php
// +----------------------------------------------------------------------
// | LubTMP 活动促销
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use Home\Service\Partner;
use Libs\Service\Operate;
class PromotionsController extends Base{

	protected function _initialize() {
	 	parent::_initialize();
	}
	//当前产品可用活动列表
	public function index()
	{
		$map = [
			'status'	=>	1
		];
		$list = D('Activity')->where($map)->field('id,product_id,title,starttime,endtime,remark')->select();
		$this->assign('data',$list)->display();
	}
	//活动操作页面
	public function work()
	{
		$id = I('get.id');
		//读取活动
		$info = D('Activity')->where(['id'=>$id])->find();
		$info['param'] = json_decode($info['param'],true);
		//判断活动是否正在进行
		if(!$this->check_active($info)){
			$this->error("活动已结束,或已经停用",array('tabid'=>$this->menuid.MODULE_NAME));
		}
		//根据活动类型加载
		switch ($info['type']) {
			case '3':
				$idcard = $info['param']['info']['card'];
				$this->assign('idcard',json_encode($idcard));
				$this->assign('type','1');
				$tempate = 'area_sale';
				break;
			default:
				break;
		}
		$this->public_info_conf();
		//售票类型
		$pinfo = I('get.');//dump($idcard);
		$today = date('Y-m-d');
		$this->assign('today',$today)
			->assign('data',$info)
			->assign('PRO_CONF',json_encode($this->pro_conf($info['product_id'])))
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
	/**
	 * 渠道售票公共信息
	 * @param  int $plan_id   计划id
	 * @param  int $productid 产品id
	 * @return [type]            [description]
	 */
	function public_info_conf(){
		$uinfo = Partner::getInstance()->getInfo();
		//获得常用联系人
		$map = array(
			"cid" => $uinfo['cid'],
			'status' => '1',
		);
		$list = Operate::do_read('CommonContact',1,$map);
		$this->assign('tour',F('Province'))->assign("list",$list)->assign('uinfo',$uinfo);
	}
}