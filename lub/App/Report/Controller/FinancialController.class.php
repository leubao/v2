<?php
// +----------------------------------------------------------------------
// | LubTMP 财务管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19 
// +----------------------------------------------------------------------
namespace Report\Controller;
use Common\Controller\ManageBase;
use Libs\Service\Operate;
use Libs\Service\Report;
class FinancialController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
	}
	//景区日报表 明细
	function index(){
		$starttime = I('starttime') ? I('starttime') : date('Y-m-d',time());
		$type = I('type') ? I('type') : '2';
		$work = I('work') ? I('work') : '1';
		if(empty($starttime)){$this->erun('参数错误');}
		//传递查询条件
		$this->assign('starttime',$starttime);
		//导出条件
	    $export_map = array();
		$where['datetime'] = date('Ymd',strtotime($starttime));
		if($work == '2'){
			$where['price_id'] = array('not in',zero_ticket());
		}elseif($work == '3'){
			$where['price_id'] = array('in',zero_ticket());
		}
		$where['product_id'] = $this->pid;
		$where['status'] = '1';
		$export_map = $where;
		$export_map['report']	= 'today';
		$export_map['type']	= $type;
		if($type == '1'){
			//明细
			$this->basePage('ReportData',$where);
		}else{
			$list = Operate::do_read('ReportData',1,$where,array('plantime ASC,games'));
			//根据计划汇总
			//$plan_fold = Report::plan_fold($list);
			//根据票型汇总
			$ticket_fold = Report::day_fold($list);//dump($ticket_fold);
			//用于报表模板导出
			S('Today'.get_user_id(),$ticket_fold);
			//退票记录 TODO
			$this->assign('data',$ticket_fold);
		}
		$this->assign('type',$type)->assign('work',$work)->assign('export_map',$export_map)
			->display();
	}

	/*渠道销售统计
	*/
	function channel(){
		$start_time = I('starttime');
	    $end_time = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $type = I('type') ? I('type') : '1';
	    $priceid = I('ticket_type');
	    $channel = I('channel_id');
	    $channelname = I('channel_name');
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
        $export_map['datetime'] = $start_time.'至'.$end_time;
        $this->assign('channel_id',$channel)->assign('channel_name',$channelname);
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = date("Ymd",strtotime($start_time));
            $end_time = date("Ymd",strtotime($end_time));
            $map['datetime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }else{
        	//默认显示当天的订单
        	$start_time = date("Ymd");
            $end_time = $start_time;
        	$map['datetime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }
        if(!empty($channel)){
        	$map['channel_id'] = array('in',agent_channel($channel,2));
        }
        //设置订单类型为团队或渠道
        //按照客户分组来统计报表 TODO
        $map['type'] = array('in','2,4,6,7');
        $map['status'] = '1';
        $db = M('ReportData');
		$map['product_id'] = $this->pid;
		$list = $db->where($map)->order('plantime ASC,games')->select();
		if($this->procof['agent'] == '1'){
			//开启代理商制度，时执行
			$list = Report::level_fold($list);
		}
		if($type == '1'){
			//根据计划汇总
		//	$plan_fold = Report::plan_fold($list);
			//根据票型汇总
			//$list = Report::channel_ticket_fold($plan_fold,$map['datetime'],$channel);
			$list = Report::channel_plan_fold($list);
		}else{
			$list = Report::channel_fold($list);
			
		}
		$export_map['report'] = 'channel';
		$export_map['type']	= $type;
		S('ChannelReport'.get_user_id(),$list);
		//加载当前产品配置 TODO
		
		$this->assign('data',$list)->assign('export_map',$export_map)->assign('type',$type)->assign('product_id',$map['product_id'])->display();
	}
	/**
	 * 渠道返佣计算
	 */
	function rakeback(){
		$starttime = I('starttime');
	    $endtime = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $status = I('status');
	    $sn = I('sn');
	    $channel = I('channel_id');
	    $channel_name = I('channel_name');
	    $guide = I('user_id');
	    $guide_name = I('user_name');
	    $plan_id = I('plan_id');
		$plan_name = I('plan_name');

	    $type = I('type') ? I('type') : '1';//1明细2合计
	    $map['product_id'] = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
	    //传递条件
	    $this->assign('starttime',$starttime ? $starttime : $endtime)
        	->assign('endtime',$endtime)->assign('plan_id',$plan_id)
        	->assign('plan_name',$plan_name);
        if(!empty($plan_id)){
        	$map['plan_id'] = $plan_id;
        }else{
        	if (!empty($starttime) && !empty($endtime)) {
	            $starttime = strtotime($starttime);
	            $endtime = strtotime($endtime) + 86399;
	            $map['createtime'] = array(array('GT', $starttime), array('LT', $endtime), 'AND');
	        }else{
	        	//默认显示当天的订单
	        	$starttime = strtotime(date("Ymd"));
	            $endtime = $starttime + 86399;
	        	$map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
	        }
        }
        //dump(agent_channel($channel,2));
		if(!empty($status)){$map['status'] = $status;}
		if(!empty($sn)){$map['order_sn'] = $sn;}
		if(!empty($channel)){$map['qd_id'] = array('in',agent_channel($channel,2));}
        if(!empty($guide)){$map['guide_id'] = array('in',$guide);}
		if($type == '1'){
			$this->basePage('TeamOrder',$map,'id DESC');
		}else{
			$db = M('TeamOrder');
			$list = $db->where($map)->select();
			$list = Report::rakeback($list);
			$this->assign('data',$list);
		}
		$this->assign('type',$type)
			->assign('map',$map)
			->assign('channel_name',$channel_name)
			->assign('guide_name',$guide_name)
			->assign('channel',$channel)
			->assign('guide',$guide)
			->display();
	}
	/*营业销售统计 根据场次查询
	*	
	*/
	function sub_business(){
		$plan_id = I('plan_id');
		$plan_name = I('plan_name');
		//$ticket_id = I('org_id');
		if(!empty($plan_id)){
			$where = array(
				'plan_id' => $plan_id,
				'status'  => '1',
				);//dump($where);
			$list = Operate::do_read('ReportData',1,$where);
			//dump($list);
			//根据票型汇总
			$ticket_fold = Report::plan_ticket_folds($list);//dump($ticket_fold);
			S('Business'.get_user_id(),$ticket_fold);
		}
		$this->assign('plan_id',$plan_id)->assign('plan_name',$plan_name)->assign('data',$ticket_fold)->display();
	}
	/*景区招待票汇总*/
	function reception(){
		if(IS_POST){
			//获取系统中所有0元票票型  已结算金额为0  计算
			$db = D('ReportData');
			$map = array('status'=>'1','price_id'=>array('in',zero_ticket()));
			$start_time = I('starttime');
		    $end_time = I('endtime') ? I('endtime') : date('Y-m-d',time());
		    $plan_id = I('plan_id');
			$plan_name = I('plan_name');
		    //传递条件
		    $this->assign('starttime',$start_time)->assign('endtime',$end_time)->assign('plan_id',$plan_id)->assign('plan_name',$plan_name);
			if (!empty($start_time) && !empty($end_time)) {
	            $start_time = date('Ymd',strtotime($start_time));
	            $end_time = date('Ymd',strtotime($end_time));
	            $map['plantime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
	        }else{
	        	//默认显示当天的订单
	        	$start_time = date("Ymd");
	            $end_time = $start_time;
	        	$map['plantime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
	        }
	        $map['product_id'] = $this->pid;
			$list = $db->where($map)->select();
			$list = Report::day_fold($list);
			//缓存用于导出
			S('Reception'.get_user_id(),$list);
		}
		$this->assign('plan_id',$plan_id)->assign('data',$list)->assign('product_id',$map['product_id'])->display();
	}
	/*售票员日报表*/
	function operator(){
		if(IS_POST){
			$pinfo = I('post.');
			$starttime = strtotime($pinfo['starttime']);
	        $endtime = $starttime  + 86399;
	        $work = I('work') ? I('work') : '1';
	    	$map = array(
	    		'status' => array('in','1,7,9'),//订单状态为支付完成和已出票和申请退票中的报表
	    		'createtime' => array(array('EGT', $starttime), array('ELT', $endtime), 'AND'),
	    	);
	    	if(!empty($pinfo['user'])){
				$map['user_id'] = $pinfo['user'];
	        }
	        if(!empty($pinfo['plan_id'])){
	        	$map['plan_id'] = $pinfo['plan_id'];
	        	$collection = Report::conductor('',$pinfo['plan_id'],$pinfo['user']);
	        }else{
	        	//获取售票员单天所有销售过的场次
	        	
	        	$collection = Report::conductor($map,'',$pinfo['user']);
	        }
	        $map['product_id'] = get_product('id');
	    	//获取订单
			$list = Report::strip_order($map,date('Ymd',strtotime($pinfo['starttime'])),2);
			//构造报表生成数据
			$list = Report::operator($list);
			$list = Report::day_fold($list,$work);
			
			//缓存用于导出
			S('Operator'.get_user_id(),$list);
			$export_map['user'] = $pinfo['user'];
			$export_map['datetime'] = $pinfo['starttime'];
			$export_map['report'] = 'operator';
			$this->assign('data',$list)->assign('conductor',$collection)->assign('work',$work)->assign('export_map',$export_map)->assign('map',$pinfo);
		}
		//查询有售票权限的角色 TODO   不够精确   通过配置售票员角色来解决。并希望是支持多角色
		$access = M('Access')->where(array('controller' => 'Work', 'action'=>array('in','quick,per_window,seatpost')))->field('role_id')->select();
		//根据角色找人
		$map = array('status'=>1,'is_scene'=>1,'role_id'=>array('in',implode(',',array_unique(explode(',',arr2string($access,'role_id'))))));
		$user = M('User')->where($map)->field('id,nickname')->select();
		$this->assign('user',$user)->assign('starttime',$pinfo['starttime'])
			->assign('product_id',get_product('id'))
			->assign('empty','<span class="empty">没有数据</span>')
			->display();
	}

	/*授信记录*/
	function top_up(){
		$starttime = I('starttime');
	    $endtime = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $sn = I('sn');
	    $type = I('type') ? I('type') : '1';
	    $crm_id = I('channel_id');
	    $types = I('types');
	    //传递条件
	    $this->assign('starttime',$starttime);
        $this->assign('endtime',$endtime);
        if (!empty($starttime) && !empty($endtime)) {
            $starttime = strtotime($starttime);
            $endtime = strtotime($endtime) + 86399;
            $map['createtime'] = array(array('GT', $starttime), array('LT', $endtime), 'AND');
        }else{
        	//默认显示当天的订单
        	$starttime = strtotime(date("Ymd"));
            $endtime = $starttime + 86399;
        	$map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
        }
        if(!empty($sn)){
        	$map['order_sn'] = $sn;
        }
        if(!empty($crm_id)){
        	$map['crm_id'] = array('in',$crm_id);
        }
        if($type == '1'){
        	if(!empty($types)){
	        	$map['type'] = $types;
	        }
        	//明细 
        	$this->basePage('CrmRecharge',$map,"id DESC");
        }else{
        	//汇总
        	$list = M('CrmRecharge')->where($map)->select();
        	$list = Report::topup($list);
        	S('Topup'.get_user_id(),$list);
        	$this->assign('data',$list);
        }
        $export_map = $map;
        $export_map['report'] = 'top_up';
		$this->assign('planname',I('plan_name'))
			->assign('channel_name',I('channel_name'))
			->assign('type',$type)->assign('map',$map)->assign('channel_id',$crm_id)->assign('export_map',$export_map)->display();
	}
	/**
	 * 财务管理 票型统计报表
	 */
	function ticket_type(){
		$starttime = I('starttime');
		$endtime = I('endtime') ? I('endtime') : date('Y-m-d');
		$ticket_id = I('ticket_id');
		$channel = I('channel_id');
		$export_map['datetime'] = $starttime.'-'.$endtime;
	    //传递条件
	    $this->assign('starttime',$starttime);
        $this->assign('endtime',$endtime);
		if (!empty($starttime) && !empty($endtime)) {
            $starttime = date("Ymd",strtotime($starttime));
            $endtime = date("Ymd",strtotime($endtime));
            $map['datetime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
        }else{
        	//默认显示当天的订单
        	$starttime = date("Ymd");
            $endtime = $starttime;
        	$map['datetime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
        }
        if(!empty($ticket_id)){
        	$map['price_id'] = array('in', explode(',',$ticket_id));
        }
        if(!empty($channel)){
        	$map['channel_id'] = array('in',agent_channel($channel,2));
        }
        $db = M('ReportData');
		$map['product_id'] = get_product('id');
		$map['status'] = '1';
   		$price = F('TicketType'.$map['product_id']);
        $list = $db->where($map)->select();
        foreach ($list as $k => $v) {
            $data['price'][$v['price_id']]['name'] = $price[$v['price_id']]['name'];
            $data['price'][$v['price_id']]['price'] = $price[$v['price_id']]['price'];
            $data['price'][$v['price_id']]['discount'] = $price[$v['price_id']]['discount'];
        	$data['price'][$v['price_id']]['number'] += $v['number'];
        	$data['price'][$v['price_id']]['money'] += $v['price']*$v['number'];
        	$data['price'][$v['price_id']]['moneys'] += $v['discount']*$v['number'];
        	$data['price'][$v['price_id']]['rebate'] += $v['subsidy'];
        	$data['info']['number'] += $v['number'];
		    $data['info']['money']  += $v['price']*$v['number'];
		    $data['info']['moneys']	+= $v['discount']*$v['number'];
		    $data['info']['rebate'] += $v['subsidy'];
        }
        sort($data['price']);
        //场次汇总 TODO
        $data['info']['games'] = M('Plan')->where(array('plantime'=>array(array('EGT', strtotime($starttime)), array('ELT', strtotime($endtime)), 'AND')))->count();
        
        $export_map['report'] = 'tickets';
        //缓存用于导出
		S('Tickets'.get_user_id(),$data);
		$this->assign('data',$data)
			->assign('map',$map)
			->assign('ticket_id',$ticket_id)
			->assign('ticket_name',I('ticket_name'))
			->assign('channel_name',I('channel_name'))
			->assign('export_map',$export_map)
			->assign('product_id',$map['product_id'])
			->display();
	}
	/**
	 * 手续费报表
	 */
	function poundage(){
		$start_time = I('starttime');
	    $end_time = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $type = I('type') ? I('type') : '1';
	    $sn = I('sn');
	    $channel = I('channel_id');
	    $channelname = I('channel_name');
	    //传递条件
	    $this->assign('starttime',$start_time)
        	->assign('endtime',$end_time)
        	->assign('sn',$sn);
        $export_map['datetime'] = $start_time.'至'.$end_time;
        $this->assign('channel_id',$channel)->assign('channel_name',$channelname);

		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $map['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }else{
        	//默认显示当天的订单
        	$start_time = strtotime(date('Ymd'));
            $end_time = $start_time + 86399;
        	$map['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }
        if(!empty($sn)){
        	$map['order_sn'] = array('like','%'.$sn.'%');
        }
        if(!empty($channel)){
        	$map['channel_id'] = array('in',agent_channel($channel,2));
        }
        $map['product_id'] = $this->pid;
        //设置订单类型为团队或渠道
        //$map['type'] = array('in','2,4,7');
    
		$list = M('OtherIncome')->where($map)->select();
		if($type == '2'){
			//按渠道商合并
			foreach ($list as $key => $value) {
				switch ($value['type']) {
					case '1':
						$lists[$value['channel_id']]['money'] += $value['money'];
						break;
				}
				$lists[$value['channel_id']]['channel_id'] = $value['channel_id'];
			}
			$this->assign('data',$lists);
		}else{
			$this->assign('data',$list);
		}
		$this->assign('type',$type)->assign('map',$map)->display();
	}
	/**
	 * 资金来源报表
	 */
	function source_cash(){
		$starttime = I('starttime');
	    $endtime = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $plan_id = I('plan_id');
		$plan_name = I('plan_name');
		$this->assign('starttime',$starttime ? $starttime : $endtime)
        	->assign('endtime',$endtime)->assign('plan_id',$plan_id)
        	->assign('plan_name',$plan_name);
        /*
        if(!empty($plan_id)){
        	$map['plan_id'] = $plan_id;
        }else{
        	if (!empty($starttime) && !empty($endtime)) {
	            $starttime = strtotime($starttime);
	            $endtime = strtotime($endtime) + 86399;;
	            $map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
	        }else{
	        	//默认显示当天的订单
	        	$starttime = strtotime(date("Ymd"));
	            $endtime = $starttime + 86399;;
	        	$map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
	        }
        }
        $status = array('in','1,7,9');
        $db = D('Order');
		*/

        if (!empty($starttime) && !empty($endtime)) {
            $starttime = date("Ymd",strtotime($starttime));
            $endtime = date("Ymd",strtotime($endtime));
            $map['datetime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
        }else{
        	//默认显示当天的订单
        	$starttime = date("Ymd");
            $endtime = $starttime;
        	$map['datetime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
        }
        $map['status'] = '1';
		$map['product_id'] = get_product('id');
		$db = D('ReportData');
		$list = $db->where($map)->field('id,plan_id,pay,moneys')->select();
		//dump($list);
		$data = Report::source_cash($list);
		$this->assign('data',$data)->assign('map',$map)->assign('product_id',$map['product_id'])->display();
	}
	/**
	 * 提现管理
	 * TODO  支付宝转账和微信转账
	 */
	function manage_cash(){
		$starttime = I('starttime') ? I('starttime') : date('Y-m-d',time());
		$type = I('type') ? I('type') : '2';
		$work = I('work') ? I('work') : '1';
		if(empty($starttime)){$this->erun('参数错误');}
		//传递查询条件
		$this->assign('starttime',$starttime);
		//导出条件
	    $export_map = array();
		$where['datetime'] = date('Ymd',strtotime($starttime));
		if($work == '2'){
			$where['price_id'] = array('not in',zero_ticket());
		}elseif($work == '3'){
			$where['price_id'] = array('in',zero_ticket());
		}
		$where['status'] = '1';
		$export_map = $where;
		$export_map['report']	= 'today';
		$export_map['type']	= $type;
		if($type == '1'){
			//明细
			$this->basePage('ReportData',$where);
		}else{
			$list = Operate::do_read('ReportData',1,$where,array('plantime ASC,games'));
			//根据计划汇总
			//$plan_fold = Report::plan_fold($list);
			//根据票型汇总
			$ticket_fold = Report::day_fold($list);
			//用于报表模板导出
			S('Today'.get_user_id(),$ticket_fold);
			//退票记录 TODO
			$this->assign('data',$ticket_fold);
		}
		$this->assign('type',$type)->assign('work',$work)->assign('export_map',$export_map)
			->display();
	}
}