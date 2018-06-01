<?php
// +----------------------------------------------------------------------
// | LubTMP  渠道商产品售卖
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use Libs\Service\Operate;
use Home\Service\Partner;

class ProductController extends Base{
	function _initialize(){
		parent::_initialize();
		//取得所有产品信息
		$this->products = cache('Product');
		//取得当前产品信息
		$this->product = $this->products[$this->pid];

	}
	function index2(){
		$product_id = I("get.productid",0,intval);    //产品id
		$itemid     = I("get.itemid",0,intval);       //商户id
		$list = D("TicketType")->Distinct(true)->field("lub_area.name,lub_area.id")->join('RIGHT JOIN lub_area ON lub_ticket_type.area = lub_area.id')->where(array("lub_ticket_type.product_id"=>$product_id))->select();
		/*判断是否有不受区域限制的票型*/
		$type = D("TicketType")->where(array("area"=>0,"product_id"=>$product_id))->count();
		if($type != 0){
			$listcount = count($list);
			$list[$listcount]["name"] = "无";
			$list[$listcount]["id"] = 0;
		}

		$this->assign("list",$list)
			->assign("productid",$product_id)
			->assign("itemid",$itemid)
			->assign('uInfo',session("uInfo"))
			->display();
	}
	function index(){
		$ginfo = I('get.');
		if(empty($ginfo)){$this->error('参数错误!');}
		if(if_plan($ginfo['plan_id']) == false){$this->error('抱歉，该场次已停止销售!');}
		$map = array(
			'product_id'=>$ginfo['productid'],
			'status'=>2,//状态必为售票中
			'id' => (int)$ginfo['plan_id'],
			'games' => (int)$ginfo['games'] ? (int)$ginfo['games'] : 1 ,
		);
		//取得今天计划的ID
		$plan = Operate::do_read('Plan',0,$map);
		session('plan',$plan);//缓存	
		$this->public_info_conf($ginfo['plan_id'],$ginfo['productid']);
		$this->assign('plan',$plan) 
			 ->assign('area',unserialize($plan['param']))
			 ->assign('info',$ginfo)
		     ->assign('group',$uInfo)
		     ->assign('tour',F('Province'))
		     ->display();
	}
	/*景区售票*/
	function scenic(){
		$ginfo = I('get.');
		if(empty($ginfo['productid'])){$this->error('参数错误!');}
		//默认日期
		$plantime = date('Y-m-d');
		//选择计划
		//加载票型
		$this->public_info_conf();
		$this->assign('plantime',$plantime)->assign('info',$ginfo)->assign('data',$data)->display();
	}
	/**
	 * 
	 * 根据日期拉取销售计划
	 */
	function get_date_plan(){
		$pinfo = json_decode($_POST['info'],true);
		$plantime = strtotime($pinfo['plantime']);
		$pro_conf = $this->pro_conf($pinfo['product']);
		/*开启预约*/
		if($pro_conf['channel_pre_team'] > 0){
			//读取可预约日期
			$pretime = strtotime(date('Ymd',strtotime('+'.$pro_conf['channel_pre_team'].' day')));
			if($plantime < $pretime){
				$return = array(
					'statusCode'=>'300',
					'msg'	=>	'当前日期不可销售'
				);
				die(json_encode($return));
			}
		}
		$plan = M('Plan')->where(array('plantime'=>$plantime,'status'=>2,'product_id'=>$pinfo['product']))->field('id,starttime,endtime,games,param,product_type')->select();
		foreach ($plan as $k => $v) {
			$param = unserialize($v['param']);
			if($v['product_type'] == '1'){
				$data[] = array(
					'id'	=> $v['id'],
					'pid'   => '1',
					'pId'	=>	'1',
					'plan' 	=>	$v['id'],
					'type'	=>	$pinfo['type'],
					'name'  => '[第'.$v['games'].'场] '. date('H:i',$v['starttime']) .'-'. date('H:i',$v['endtime']),
				);
			}
			if($v['product_type'] == '2'){
				$data[] = array(
					'id'	=>  $v['id'],
					'pid'   =>  '1',
					'pId'	=>	'1',
					'plan' 	=>	$v['id'],
					'type'	=>	$pinfo['type'],
					'name'  =>  date('H:i',$v['starttime']),
				);
			}
			if($v['product_type'] == '3'){
				$data[] = array(
					'id'	=> $v['id'],
					'pid'   => '1',
					'pId'	=>	'1',
					'plan' 	=>	$v['id'],
					'type'	=>	$pinfo['type'],
					'tooltype' => tooltype($param['tooltype'],1),
					'name'  => '[第'.$v['games'].'趟] '. date('H:i',$v['starttime']),
				);
			}
		}
		if(!empty($data)){
			$str = array(
				'id' => '1',
				'pid'=> '0',
				'pId'=>'0',
				'open'=>true,
				'name'=>$pinfo['plantime'].'趟次',
				'children'=>$data,
			);
		}
		$return = array(
			'statusCode'=>'200',
			'plan' => $str,
		);
		die(json_encode($return));
	}
	/**
	 * 快捷售票  获取区域票型
	 */
	public function quickPrice(){
		$pinfo = json_decode($_POST['info'],true);
		if(empty($pinfo)){
			$this->error('参数错误!');
		}

		$uInfo = Partner::getInstance()->getInfo(); //当前登录用户
		//判断是否是政企渠道用户
		if($uInfo['group']['type'] == '3'){
			$type = 4;/*政企渠道*/
		}else{ 
			$type = 2;
		}
		$plan = F('Plan_'.$pinfo['plan']);
		$price_group  = $this->crm_price_group($uInfo['groupid'],$plan['product_id']);
		/*
		//根据分组加载价格
		$tictype = pullprice($plan['id'],$type,$pinfo['area'],2,$price_group,$pinfo['sale']);
		*/
		//根据区域加载门票
		if($pinfo['method'] == 'ordinary'){
			$price = pullprice($pinfo['plan'],$type,$pinfo['area'],2,$price_group,$pinfo['seale']);
		}
		//常规根据计划、区域、产品类型获取销售价格
		if($pinfo['method'] == 'general'){
			$price = pullprice($pinfo['plan'],$type,$pinfo['area'],2,$price_group,$pinfo['seale']);
		}
		//根据销售计划和产品类型以及可售的票型获取整体销售票型
		if($pinfo['method'] == 'activity'){
			//读取当前活动绑定的票型
			$where = [
				'status'	=>	'1',
				'_string'   =>  "FIND_IN_SET(2,is_scene)",
				'id'		=>	$pinfo['actid']
			];
			$ainfo = D('Activity')->where($where)->field('id,type,param')->find();
			$param = json_decode($ainfo['param'],true);
			//在套票时直接加载活动中的价格
			if((int)$ainfo['type'] === 5){
				//判断活动的产品类型TODO
				$number = D('Drifting')->where(['plan_id'=>$pinfo['plan']])->count();
                //获取当前可售数量 TODO 目前不支持票面价和结算价
                $area_num = $plan['quotas'] - $number;
				$price = [
					'id'		=>	$ainfo['id'],
					'name'		=>	$param['info']['price']['name'],
					'area_num' 	=>	$area_num, 
					'area_nums' =>	$number,
					'price'		=>	$param['info']['price']['price'],
					'discount'	=>	$param['info']['price']['discount'],
				];
			}else{
				$ticket = explode(',',$param['info']['ticket']);
				$price = pullprice($pinfo['plan'],$type,$pinfo['area'],2,$price_group,$pinfo['seale'],$ticket);
			}
			
		}

		
		$return = array(
			'statusCode' => '200',
			'price'		 =>	$price,
		);
		die(json_encode($return));	
	}
	/*
	 * 获取当前区域价格与座椅信息 景区产品根据销售计划获取价格信息
	 * @param $areaId int 当前区域ID
	 * @param $type int 读取票型
	 * @param $protype int 产品类型
	 * @param $product_id int 产品ID
	 * @param $group int 当前用户所属分组的票型分组iD
	
	private function getPrice($type, $protype = '1', $areaId = null, $product_id = null, $group = null){
		$plan = session('plan');
		//加载所属分组，为空则为默认
		$group = $group ? $group : '1';
		//可售区域 及授权票型
		$param = unserialize($plan['param']);
		if($protype == '1'){
			$map = array('area'=>$areaId,'status'=>1,'type'=>$type,'product_id'=>$product_id,'group_id'=>$group);
		}else{
			$map = array('status'=>1,'type'=>$type,'product_id'=>$product_id,'group_id'=>$group);
		}
		//获取价格信息
		$tickets = Operate::do_read('TicketType',1,$map);
		foreach ($param['ticket'] as $v){
			foreach ($tickets as $va){
				if($v == $va['id']){
					$price[] = $va;
				}
			}
		}
		return $price;		
	}*/
	/*根据客户分组获取票型分组
	*@param $group 当前用户所属分组
	*@param $product 当前产品
	*return group_id 返回票型分组id
	*/
	function crm_price_group($group,$product){
		$ticket_group = F('CrmGroup');
		$group = $ticket_group[$group]['price_group'];
		return $group;
	}
	/**
	 * 比较配额 @印象大红袍
	 */
	function quota(){
		$ginfo = I('get.');
		$plan = F('Plan_'.$ginfo['plan']);
		if(empty($plan)){
			$data["statusCode"] = "0";echo json_encode($data);return false;
		}else{
			$crm = Partner::getInstance()->crm;
			$status = \Libs\Service\Quota::quota($plan['id'],$plan['product_id'],$crm['id'],(int)$ginfo['num']);
			if($status){
                $data["statusCode"] = "200";echo json_encode($data);return true;
            }else{
                $data["statusCode"] = "0";echo json_encode($data);return false;
            }
		}
	}
	/**
	 * 预约订单
	 * @company  承德乐游宝软件开发有限公司
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2017-12-18
	 * @return   [type]        [description]
	 */
	function pre_order()
	{
		$ginfo = I('get.');
		$ginfo = [
			'type'	=>	'1',
			'productid' => '43'
		];
		//if(empty($ginfo['productid'])){$this->error('参数错误!');}
		//默认日期
		$plantime = date("Y-m-d",strtotime("+1 day"));
		//选择计划
		//加载票型
		$this->public_info_conf();
		$data = [
			'order_sn'	=> 	'get_order_sn()',
			'user_id'	=>	'',
			'datetime'	=>	$info['datetime'],
			'number'	=>	$info['number'],
			'phone'		=>	$info['phone'],
			'channel_id'=>	'',
			'type'		=>	'1',
		];
		$this->assign('plantime',$plantime)->assign('info',$ginfo)->assign('data',$data)->display();
	}
	/**
	 * 渠道售票公共信息
	 * @param  int $plan_id   计划id
	 * @param  int $productid 产品id
	 * @return [type]            [description]
	 */
	function public_info_conf($plan_id = '',$productid = ''){
		$uinfo = Partner::getInstance()->getInfo();
		//获得常用联系人
		$map = array(
			"cid" => $uinfo['cid'],
			'status' => '1',
		);
		//检测是否开启配额个人不检测配额 TODO  现在判断不好
		if($this->proconf['quota'] && $type == '1'){
			\Libs\Service\Quota::check_quota($plan_id,$productid,$uinfo['cid']);
		}
		$list = Operate::do_read('CommonContact',1,$map);
		//判断是否限制区域销售 根据当前用户判定
		//1、当前商户信息
		//2、是否加载限制区域
		$this->assign("list",$list)->assign('uinfo',$uinfo);
	}
	/*状态验证*/
	function checkstatus(){
		$data = $_POST;
		/*处理提交的数据*/
		$data["statusCode"] = "200";
		$data["forwardUrl"] = U("Home/Product/payinfor");
		echo json_encode($data);		
	}
	/*支付成功页面*/
	function paysuccess(){	
		$this->assign('uInfo',session("uInfo"))->display();
	}
	/*根据信息查找*/
	function guidecheck(){
		$name = I('name');
		$phone = I('phone');         
		if(!empty($name)){$map['nickname'] = array('like','%'.$name.'%');}
		if(!empty($phone)){$mao['phone'] = $phone;}
 		$list = D("User")->field("id,nickname,phone")->where($map)->select();
 		$list = $list ? json_encode($list) : "0";
 		echo $list;
 		return true;
	}
	//验证黑名单
	function public_black(){
		$ginfo = I('get.');
		if(empty($ginfo['p'])){
			$data["statusCode"] = "0";echo json_encode($data);return false;
		}else{
			$status = M('Blacklist')->where(array('phone'=>$ginfo['p'],'status'=>'1'))->find();
			if(!$status){
                $data["statusCode"] = "200";echo json_encode($data);return true;
            }else{
                $data["statusCode"] = "0";echo json_encode($data);return false;
            }
		}
	}
}