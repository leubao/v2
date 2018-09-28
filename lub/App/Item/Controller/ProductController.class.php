<?php
// +----------------------------------------------------------------------
// | LubTMP 产品设置
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
use Item\Service\Partner;
use Libs\Service\Operate;

class ProductController extends ManageBase{

	 protected function _initialize() {
	 	parent::_initialize();
	 }

	/**
	 * 单票设置
	 */
	function single(){
		$product_id = (int) $this->pid;
		if(!empty($product_id)){
			$this->basePage('TicketSingle',array('product_id'=>$product_id),'status DESC,id DESC');
			$this->display();
		}else{
			$this->erun('参数错误!');
		}
	}

	/*
	 * 添加单票
	 * @param $pid int 产品ID
	*/
	function singleadd(){
		if(IS_POST){
			if(Operate::do_add('TicketSingle',array('createtime'=>time(),'user_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_userid'], 'DECODE')))){
				$this->srun('新增成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("添加失败！");
			}
		}else{
			$product_id = (int) $this->pid;
			if(!empty($product_id)){
				$this->assign('pid',$product_id);
				$this->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}

	/**
	 * 编辑单票
	 * @param $pid int 产品ID
	 * @param $sid int 单票ID
	 */

	function singleedit(){
		if(IS_POST){
			$up = Operate::do_up("TicketSingle");
			if($up){
				$this->srun('更新成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('修改失败!');
			}
		}else{
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$list = Operate::do_read("TicketSingle",0,array("id"=>$id));
				$this->assign("data",$list);
				$this->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}

	/**
	 * 删除
	 */

	function singledel(){
		$id = I('get.id',0,intval);
		if(!empty($id)){
			$del = Operate::do_del("TicketSingle",array('id'=>$id));
			if($del){
				$this->srun('删除成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				$this->erun('删除失败!');
			}
		}else{
			$this->erun('参数错误!');
		}
	}

	/**
	 * 票型分组
	 *  @param $pid int 所属产品ID
	 */

	function group(){
		$product_id = (int) $this->pid;
		if(!empty($product_id)){
			$this->basePage('TicketGroup',array('product_id'=>$product_id),"status DESC");
			$this->display();
		}else{
			$this->erun('参数错误!');
		}
	}

	/**
	 * 添加分组
	 *  @param $pid int 所属产品ID
	 */
	function groupAdd(){
		if(IS_POST){
			if(Operate::do_add('TicketGroup')){
				$this->srun('新增成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("添加失败！");
			}
		}else{
			$product_id = (int) $this->pid;
			if(!empty($product_id)){
				$this->assign('pid',$product_id);
				$this->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}

	/**
	 * 编辑分组
	 *  @param $pid int 所属产品ID
	 */

	function groupedit(){
		if(IS_POST){
			$up = Operate::do_up('TicketGroup');
			if($up){
				$this->srun("更新成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('更新失败!');
			}			
		}else{
			$id = I('id');
			if(!empty($id)){
				$data = Operate::do_read('TicketGroup',0,array('id'=>$id));
				$this->assign('data',$data);
				$this->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}

	/**
	 * 删除分组
	 *  @param $pid int 所属产品ID
	 */

	function groupdel(){
		$id = I('get.id',0,intval);
		if(!empty($id)){
			//判断是否存在成员
			$sub = Operate::do_read('TicketType',1,array('group_id'=>$id));
			if($sub != false){
				if(Operate::do_del('TicketGroup',array('id'=>$id))){
					$this->srun("删除成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun('删除失败!');
				}
			}else{
				$this->erun('该分组下存在票型,不能删除!'); 
			}
		}else{
			$this->erun('参数错误!');
		}
	}

	/**
	 * 票型列表
	 * @param $gid int 所属分组ID
	 */

	function type(){
		$product_id = (int) $this->pid;
		if(!empty($product_id)){
			$group_id = I('gid');
			if(!empty($group_id)){//获取单个分组下的票型
				$db = M("TicketType");
				$map = array('product_id'=>$product_id,'group_id'=>$group_id); 
				$this->basePage('TicketType',$map,'status DESC,id DESC');
				$this->assign('gid',$group_id)->display('ticket_type');
			}else{
				//获取分组
				$group = Operate::do_read('TicketGroup',1,array('product_id'=>$product_id,'status'=>1));
				$this->assign('data',$group);
				$this->display();
			}	
		}else{
			$this->erun('参数错误!');
		}
	}

	/**
	 * 添加票型
	 *  @param $pid int 所属产品ID
	 */

	function typeadd(){
		$product_id = (int) $this->pid;
		if(empty($product_id)){
			$this->erun('参数错误!');
		}
		if(IS_POST){
			$param = I('post.param');
			$data = array(
				'price'=>I('post.single_price'),
				'single_id'=>I('post.single_id'),
				'scene'=>implode(',', I('post.scene')),
				'bonus'=>I('post.bonus'),
				'income'=>I('post.income'),
				'param'=>serialize(array('quota'=>$param['quota'],'ticket_print'=>$param['ticket_print'],'ticket_print_custom'=>$param['ticket_print_custom'],'present'=>isset($param['present']) ? $param['present'] : 0,'validity'=> isset($param['validity']) ? $param['validity'] : 0)),
			);
			if(Operate::do_add('TicketType',$data)){
				$this->srun("新增成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("添加失败！");
			}
		}else{
			$group_id = I('get.gid',0,intval);
			if(!empty($group_id)){
				//当前产品类型
				$product = Operate::do_read('Product',0,array('id'=>$product_id));
				if($product['type'] == 1){
					//剧院产品增加座位区域选择
					$area = Operate::do_read('Area',1,array('template_id'=>$product['template_id'],'status'=>'1'),'',array('id','name','template_id','num','status'));
					$this->assign('area',$area);
						 
				}
				$this->assign('product_id',$product_id)->assign('gid',$group_id)->assign('ptype',$product['type']);
				$this->display();
			}else{
				$this->erun('参数错误!');
			}
		}

	}

	/**
	 * 编辑
	 *  @param $pid int 所属产品ID
	 */

	function typeedit(){
		$product_id = (int) $this->pid;
		if(IS_POST){
			$pinfo = I('post.');
			$pinfo['scene'] = implode(',', $pinfo['scene']);
			$param = array(
				'quota'=>$pinfo['param']['quota'],
				'ticket_print'=>$pinfo['param']['ticket_print'],
				'ticket_print_custom'=>$pinfo['param']['ticket_print_custom'],
				'present'=>isset($pinfo['param']['present']) ? $pinfo['param']['present'] : 0,
				'validity'=> isset($pinfo['param']['validity']) ? $pinfo['param']['validity'] : 0
			);
			$pinfo['param'] = serialize($param);
			$model = D('Item/TicketType');
			$status = $model->where(array('id'=>$pinfo['id']))->save($pinfo);
			if($status){
				$model->type_cache($product_id);
				$this->srun("编辑成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('更新失败!');
			}
		}else{
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$data = Operate::do_read('TicketType',0,array('id'=>$id));
				$data['param'] = unserialize($data['param']);
				//当前产品类型
				$product = Operate::do_read('Product',0,array('id'=>$product_id));
				if($product['type'] == 1){
					//剧院产品增加座位区域选择
					$area = Operate::do_read('Area',1,array('template_id'=>$product['template_id'],'status'=>'1'),'',array('id','name','template_id','num','status'));
					$this->assign('area',$area);
				}
				$group = Operate::do_read('TicketGroup',1,array('status'=>1));
				$this->assign('data',$data)
					->assign('group',$group)
					->assign('ptype',$product['type'])
					->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}

	/**
	 * 删除
	 *  @param $id int 票型id
	 */
	function typedel(){
		$id = I('get.id',0,intval);
		if(!empty($id)){
			$map = array("id"=>$id);
			$del = M('TicketType')->where(array('id'=>$id))->setField('status','0');
			//$del = M('TicketType')->where(array('id'=>$id))->delete();
			if($del){
				$this->srun("删除成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else {
				$this->erun('删除失败!');
			}

		}else{
			$this->erun('参数错误!');
		}
	}

	/**
	 * 销售计划
	 */	

	function plan(){
		$product_id = (int) $this->pid;
		if(!empty($product_id)){
			$map = ["product_id"=>$product_id];
			$this->basePage('Plan',$map,'plantime DESC');
			$this->display();
		}else{
			$this->erun('参数错误!');
		}
	}	
	/**
	 * 添加销售计划
	 */

	function planadd(){
		if(IS_POST){
			$data = I('post.');
			if(D("Item/Plan")->add_plan($data)){
				$this->srun("添加成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('新增失败,在同一时间已存在销售计划!');
			}
		}else{
			//产品信息
			$pinfo = get_product('info');dump($pinfo);
			if(empty($pinfo)){$this->erun('未捕获产品信息,请重新登录系统...');}
			switch ($pinfo['type']) {
				case '1':
					//剧场座椅区域信息
					$seat = D('Area')->where(array('template_id'=>$pinfo['template_id'],'status'=>1))->field('id,name,template_id,num')->order('listorder ASC')->select();
					$this->assign('seat',$seat);
					break;
				case '2':
					//景区
					break;
				case '3':
					//漂流
					$tooltype = D('ToolType')->where(array('product_id'=>$pinfo['id'],'status'=>1))->field('id,title')->order('id DESC')->select();
					$this->assign('tooltype',$tooltype);
					break;
			}
			$plantime = D('Item/Plan')->where(['product_id'=>$pinfo['id']])->max('plantime');
			$today = strtotime(date('Ymd'));
			if($plantime < $today){
				$plantime = $today;
			}else{
				$plantime = $plantime + 86400;
			}
			//票型价格信息
			$ticket = D('TicketGroup')->relation(true)->where(array('product_id'=>$pinfo['id'],'status'=>'1'))->select();
			//商品
			$goods = D('Goods')->where(array('status'=>'1'))->field('id,title')->select();
			$this->assign('group',$ticket)
			     ->assign('pinfo',$pinfo)
			     ->assign('plantime',date('Y-m-d',$plantime))
			     ->assign('goods',$goods)
				 ->display();
		}
	}
	/**
	 * 修改销售计划
	 */
	function planedit(){
		if(IS_POST){
			$data = I("post.");
			if(D('Item/Plan')->editPlan($data)){
				$this->srun("修改成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('修改失败');
			}			
		}else{
			$id = I("get.id");   //计划ID
			$list = Operate::do_read('Plan',0,array('id'=>$id));
			$product_id = (int) $this->pid;
			if($list['status'] <> '1'){
				$this->erun('计划已过期或售票中，不可编辑!');
			}else{
				if(!empty($product_id)){
					//产品信息
					$pinfo = M('Product')->where(array('id'=>$product_id))->find();
					//判断产品类型
					switch ($pinfo['type']) {
						case '1':
							//剧场座椅区域信息
							$seat = D('Area')->where(array('template_id'=>$pinfo['template_id'],'status'=>1))->field('id,name,template_id,num')->order('listorder ASC')->select();
							$this->assign('seat',$seat);
							break;
						case '2':
							//景区
							break;
						case '3':
							$tooltype = D('ToolType')->where(array('product_id'=>$product_id,'status'=>1))->field('id,name')->select();
							$this->assign('tooltype',$tooltype);
							//漂流
							break;
					}
					//票型价格信息
					$ticket = D('TicketGroup')->relation(true)->where(array('product_id'=>$product_id,'status'=>'1'))->select();//dump($ticket[0]);
					$this->assign('group',$ticket)
						 ->assign('pid',$product_id)
					     ->assign('pinfo',$pinfo);
				}
				$list["param"] = unserialize($list["param"]);
				$this->assign("data",$list);
				$this->display();
			}			
		}

	}
	/**
	 * 删除计划
	 */
	function plandel(){
		$id  = I("get.id");
		$map = array("id"=>$id,"status"=>1);
		$del = Operate::do_del("Plan",$map);
		if ($del){
			$this->srun("删除成功!", array('tabid'=>$this->menuid.MODULE_NAME));
		}else {
			$this->erun('删除失败!');
		}		
	}
	/**
	 * 修改计划配额
	 */
	function planquota(){
		if(IS_POST){
			$info = I('post.');
			$updata = [
				'number'		=>	$info['number'],//可售总量
	    		'often'			=>	$info['often'],//常规渠道
	    		'political'		=>	$info['political'],//政企渠道
	    		'full'			=>	$info['full'],//全员销售
	    		'directly'		=>	$info['directly'],//电商直营
	    		'electricity'	=>	$info['electricity'],//电商渠道
			];
			$status = D('Item/PinSales')->where(['plan_id'=>$info['plan_id']])->save($updata);
			if($status){
				$this->srun("更新成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('更新失败!');
			}
		}else {
			$id  = I("get.id");
			$plan = D('Plan')->where(['id'=>$id])->field('status')->find();
			if($plan['status'] == '4'){
				$this->erun('销售计划已过期，不允许此项操作...');
			}
			//读取类型销控
			$info = D('Item/PinSales')->where(['plan_id'=>$id])->find();
			if(!$info){
				D('Item/Plan')->pin_sales_type($id,get_product('id'));
				$info = D('Item/PinSales')->where(['plan_id'=>$id])->find();
			}
			$this->assign('data',$info)
				->display();
		}	
	}
	/**
	 * 修改计划可用票型
	 */
	function plan_ticket(){
		if(IS_POST){
			$info = I('post.');
			$infos = Operate::do_read("Plan",0,array('id'=>$info['plan_id']));
			$param = unserialize($infos['param']);
			$param['ticket'] = $info['ticket'];
			if(Operate::do_up("Plan",array('id'=>$info['plan_id']),'',array('param'=>serialize($param)))){
				$this->srun("更新成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('更新失败!');
			}
		}else {
			$id  = I("get.id");
			$map = array("id"=>$id);
			$info = Operate::do_read("Plan",0,$map);
			//票型价格信息
			$ticket = D('TicketGroup')->relation(true)->where(array('product_id'=>$info['product_id'],'status'=>'1'))->select();
			$infos = unserialize($info['param']);
			$this->assign('group',$ticket)
				->assign('data',$infos)
				->assign('pid',$id)
				->display();
		}	
	}
	/**
	 * 更新可用小商品
	 * @return [type] [description]
	 */
	function plan_goods(){
		if(IS_POST){
			$info = I('post.');
			$infos = Operate::do_read("Plan",0,array('id'=>$info['plan_id']));
			$param = unserialize($infos['param']);
			$param['goods'] = $info['goods'];
			if(Operate::do_up("Plan",array('id'=>$info['plan_id']),'',array('param'=>serialize($param)))){
				$this->srun("更新成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('更新失败!');
			}
		}else {
			$id  = I("get.id");
			$map = array("id"=>$id);
			$info = Operate::do_read("Plan",0,$map);
			//票型价格信息
			$goods = D('Goods')->where(array('product_id'=>$info['product_id'],'status'=>'1'))->field('id,title')->select();
			$infos = unserialize($info['param']);
			$this->assign('goods',$goods)
				->assign('data',$infos)
				->assign('pid',$id)
				->display();
		}	
	}
	/**
	 * 计划授权
	 */
	function planauth(){
		if(IS_POST){	
			if (D('Item/Plan')->auth(I('post.plan_id',0,intval))){
				$this->srun("授权成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else {
				$this->erun('授权失败!');
			}
		}else{
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$info = Operate::do_read('Plan',0,array('id'=>$id));
				if(in_array($info['status'],['2','3','4'])){
					$this->erun('销售计划已完成授权,请勿重复操作...');
				}
				$param = unserialize($info['param']);
				//剧院类
				if($info['product_type'] == 1){
					$area = M('Area')->where(array('template_id'=>$info['template_id'],'status'=>1))->field('id,name,template_id,num')->select();
					foreach ($area as $k=>$v){
						foreach ($param['seat'] as $va){
							if($v['id'] == $va){
								$seat[$k] = $v; 
							}
						}
					}
					$this->assign('area',$seat);
				}
				//票型价格信息
				$ticket = D('TicketGroup')->relation(true)->where(array('product_id'=>$info['product_id'],'status'=>'1'))->select();
				foreach ($ticket as $kt=>$vg){
					//票型分组
					foreach ($vg['TicketType'] as $vt){
						//票型分类
						foreach ($param['ticket'] as $val){
							//选中票型
							if($vt['id'] == $val){
							    $vg['tw'][] = $vt;
							}	
						}
						$go[$ke] = $vt;
					}
					$group[] = $vg;	
				}
				$this->assign('data',$info)
					 ->assign('group',$group)
					 ->assign('param',$param)
					 ->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}
	//销售权限
	function auth(){
		if(IS_POST){
			$pinfo = I('post.');
			if(!empty($pinfo['id'])){
				$info = M('Plan')->where(array('id'=>$pinfo['id']))->getField('param');
				$info = unserialize($info);
				//场景数量
				$info['wechat'] = $pinfo['wechat_num'];
				$info['api']	= $pinfo['api_num'];
				$info['ifhelp']	= $pinfo['ifhelp_num'];
				//销售场景
				$info['sales'] = array('wechat'=>$pinfo['wechat'],'web'=>$pinfo['web'],'channel'=>$pinfo['channel'],'window'=>$pinfo['window'],'api'=>$pinfo['api'],'ifhelp'=>$pinfo['ifhelp']);
				//打印场景
				$info['print'] = array('help_print'=>$pinfo['help_print'],'channel_print'=>$pinfo['channel_print']);
				//微信散客购票
				$info['price_group'] = $pinfo['price_group'];
				//支持的活动
				$info['activity'] = $pinfo['activity'];
				//更新plan
				$status = D('Plan')->where(array('id'=>$pinfo['id']))->setField('param',serialize($info));
				if($status){
					\Item\Service\Plan::up_activity_plan($pinfo['activity'],$pinfo['id']);
					$this->srun("更新成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun("更新失败!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}
			}else{
				$this->erun("参数错误，更新失败!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}
		}else{
			$id = I('id');
			$info = M('Plan')->where(array('id'=>$id))->field('id,param,plantime')->find();
			$price = Operate::do_read('TicketGroup',1,array('status'=>1));
			$info['param'] = unserialize($info['param']);
			$activity = \Item\Service\Plan::get_activity($info['plantime']);
			$this->assign('price',$price)->assign('data',$info)->assign('activity',$activity)->display();
		}
	}
	//开始或暂停销售
	function start_sales(){
		$id = I('get.id',0,intval);
		if(!empty($id)){
			$db = D('Plan');
			$info = $db->where(array('id'=>$id))->field('id,product_id,product_type,status')->find();
			if($info['status'] == '3'){
				//判断是否开启配额
				if($this->procof['quota'] == '1' && $info['product_type'] <> '1'){
					$count = M('QuotaUse')->where(array('plan_id'=>$info['id']))->count();
					if($count == '0'){
						\Libs\Service\Quota::reg_quota($info['id'],$info['product_id']);
					}
				}
				//暂停中开始销售
				$status = '2';
			}elseif($info['status'] == '2'){
				//售票中暂停销售
				$status = '3';
				F('Plan_'.$id,null);
			}else{
				$this->erun('计划状态不允许此项操作!');
			}
			if($db->where(array('id'=>$id))->setField('status',$status)){
				$db->plan_cache();
				$this->srun("操作成功!", array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				$this->erun('操作失败!');
			}
		}
	}
	//更新销售区域
	function up_area(){
		$db = D('Plan');
		if(IS_POST){
			$pinfo = I('post.');
			$info = $db->where(array('id'=>$pinfo['id']))->field('id,param,product_id')->find();
			$param = unserialize($info['param']);
			$param['seat'] = $pinfo['area'];
			if($db->where(array('id'=>$pinfo['id']))->setField('param',serialize($param))){
				$db->plan_cache();
				$this->srun("操作成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('操作失败!');
			}
		}else{
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$info = $db->where(array('id'=>$id))->field('id,param,product_id')->find();
				//读取当前产品所有区域
				$template_id = M('Product')->where(array('id'=>$info['product_id']))->getField('template_id');
				$area = M('Area')->where(array('template_id'=>$template_id))->field('id,name')->select();
				$param = unserialize($info['param']);
				$seat = $param['seat'];
				$this->assign('id',$id)->assign('area',$area)->assign('seat',$seat)->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}
	//场次详情
	function planinfo(){
		$id = I('get.id',0,intval);
		$info = M('Plan')->where(array('id'=>$id))->find();
		$info['param'] = unserialize($info['param']);
		$this->assign('data',$info)->display();
	}
	/***漂流***/
	/**
	 * 类型 2人艇3人艇
	 */
	function tooltype(){
		$product_id = (int) $this->pid;
		if(!empty($product_id)){
			$this->basePage('ToolType',array('product_id'=>$product_id),'status DESC,id DESC');
			$this->display();
		}else{
			$this->erun('参数错误!');
		}
	}
	/**
	 * 新增工具类型
	 */
	function add_tooltype(){
		if(IS_POST){
			if(Operate::do_add('ToolType',array('createtime'=>time()))){
				$this->srun('新增成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("添加失败！");
			}
		}else{
			$product_id = (int) $this->pid;
			if(!empty($product_id)){
				$this->assign('pid',$product_id);
				$this->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}
	/**
	 * 新增工具类型
	 */
	function edit_tooltype(){
		if(IS_POST) {
			if(Operate::do_up('ToolType')){
				$this->srun("更新成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("更新失败！");
			}
		} else {
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$data = Operate::do_read('ToolType',0,array('id'=>$id));//dump($data);
				$this->assign('data',$data)
					->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}
	/**
	 * 删除工具类型
	 */
	function del_tooltype(){
		$id = I('get.id',0,intval);
		if(!empty($id)){
			$del = Operate::do_del("ToolType",array('id'=>$id));
			if($del){
				$this->srun('删除成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				$this->erun('删除失败!');
			}
		}else{
			$this->erun('参数错误!');
		}
	}
	/**
	 * 销售控制,设置默认配置
	 * 销控优先级 1销售类型2销售场景
	 */
	function pin_control()
	{
		//优先判断销售类型是否还有门票
		//然后判断销售场景
	}
	/**
	 * 座位销售记录 根据座位表加载销售记录 最多支持查询历史一个月的的数据
	 */
	function seat_sales(){
		//$plan_id = I('');

		$this->basePage();
	}
}