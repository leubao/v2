<?php
// +----------------------------------------------------------------------
// | LubTMP  渠道商
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use Home\Service\Partner;
use Libs\Service\Operate;
class IndexController extends Base{
	
	protected function _initialize(){
		parent::_initialize();
	}
	/**
	 * 操作首页
	 * @author zhoujing
	 * TODO
	 */
	public function index(){
		//获取产品区域
		$list = $this->plan();
		//查询当前用户可用授信额
		$uinfo = Partner::getInstance()->getInfo();
		if($uinfo['group']['type'] <> '4'){
			//渠道商
			$money = Operate::do_read('Crm',0,array('id'=>$uinfo['cid']),'',array('cash'));
		}else{
			//导游 政企
			$money = Operate::do_read('User',0,array('id'=>$uinfo['id']),'',array('cash'));
		}
		$this->notice();
		$this->assign('data',$list)
			->assign('moneys',$money)
			->assign('score',D('KpiChannel')->where(array('crm_id'=>$uinfo['cid'],'status'=>'1'))->getField('score'))
			->display();           
	}
	/*通知公告*/
	function notice(){
		$data = M('Notice')->where(array('status'=>1))->order('id DESC')->find();
		if(!empty($data)){
			$this->assign('notice',$data);
		}else{
			$this->assign('notice','1');
		}
	}
	function notice_info()
	{
		$id = I('id');
		if($id){
			$info = M('Notice')->where(array('id'=>$id))->find();
			$this->assign('info',$info);
		}else{
			$this->error('参数有误');
		}
		$this->display();
	}
	/**
	 * 产品列表
	 */
	function product(){
		$this->assign('data',$this->plan())
			->display();
	}
	/**
	 * 渠道销售情况   1级看自己和2级
	 */
	function seale(){
		//判断当前登录用户
		$plan_id = I('get.plan',0,intval);
		if(empty($plan_id)){
			$this->error("参数错误!");
		}
		$uInfo = Partner::getInstance()->getInfo();
		$db = D('QuotaUse');
		switch ($uInfo['crm']['level']){
				case self::$Cache['Config']['level_1'] :
					//一级渠道商
					$channel = M('Crm')->where(array('f_agents'=>$uInfo['crm']['id'],'status'=>'1'))->field('id')->select();
					$list = $db->where(array('channel_id'=>array('in',implode(',', array_column($channel, 'id'))),'plan_id'=>$plan_id))->select();
					break;
				case self::$Cache['Config']['level_2'] :
					//二级级渠道商
					$channel = M('Crm')->where(array('f_agents'=>$uInfo['crm']['id'],'status'=>'1'))->field('id')->select();
					$list = $db->where(array('channel_id'=>array('in',implode(',', array_column($channel, 'id'))),'plan_id'=>$plan_id))->select();
					break;
				case self::$Cache['Config']['level_3'] :
					//三级渠道商  获取二级的上一级ID
					$list = $db->where(array('channel_id'=>$uInfo['crm']['id']))->find();
					break;
		}
		$this->assign('data',$list)
			->display();
	}
	//显示销售计划 
	function plan(){
		//根据当前用户信息差选产品、根据产品查询销售信息channel_pre_team
		$uInfo = Partner::getInstance()->getInfo();
		//根据商户ID查询相应产品
		$pro = Operate::do_read('Item',0,array('id'=>$uInfo['item_id']),'',array('product'));
		$proArr = explode(',', $pro['product']);
		$proConf = cache('ProConfig');
		//TODO 判断产品是否都可用
		foreach ($proArr as $k=>$v){
			$product = M('Product')->where(['id'=>$v,'status'=>1])->find();
			if(!empty($product)){
				$list[$k] = $product;
				$list[$k]['quota'] = M('CrmQuota')->where(array('crm_id'=>$uInfo['cid'],'product_id'=>$v))->getField('quota');
				if($list[$k] != false){
					$list[$k]['area'] = Operate::do_read('Area',1,array('template_id'=>$list[$k]['template_id']),'listorder ASC',array('id','name'));
					$pre_team = $proConf[$v]['1']['channel_pre_team'];
					$list[$k]['plan'] = $this->getPlanList($v, $pre_team);
				}
			}
		}
		$list = array_filter($list);
		$this->assign('uinfo',$uInfo);
		return $list;
	}
	public function getPlanList($product, $pre_team = '')
	{
		if(empty($pre_team)){
			$list = Operate::do_read('Plan',1,array('product_id'=>$product,'status'=>2),"plantime ASC,games ASC", array('id,product_id,plantime,games,seat_table'));
			$append = ['seale' => 1];
			array_walk($list, function(&$v, $k, $a){
                $v = array_merge($v, $a);
            }, $append);
		}else{
			$plantime = strtotime(date('Y-m-d'));
			$plan = Operate::do_read('Plan',1,array('product_id'=>$product,'status'=>2),"plantime ASC,games ASC",array('id,product_id,plantime,games,seat_table'));
			//
			foreach ($plan as $k => $v) {
				if($plantime == $v['plantime']){
					$list[$k] = $v;
					$list[$k]['seale'] = 1;
				}else{
					$list[$k] = $v;
					$list[$k]['seale'] = 0;
				}
			}

		}
		return $list;
	}
	//拉取区域
	public function public_get_area()
	{
		$area = F('Province');
		if(empty($area)){
			D('Item/Province')->province_cache();
			$area = F('Province');
		}
		die(json_encode($area));
	}
}