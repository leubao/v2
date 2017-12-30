<?php
// +----------------------------------------------------------------------
// | LubTMP 报表管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19 
// +----------------------------------------------------------------------
namespace Report\Controller;
use Common\Controller\ManageBase;
use Libs\Service\Operate;
use Libs\Service\Report;
use Item\Service\Partner;
class ReportController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
	}
	/*
	*销售折线 人数 金额 散客
	*/
	function wire(){
		$a = '';
		echo $a;
	}
	//渠道商销售统计 分区域
	function channel(){
		$start_time = I('starttime');
	    $end_time = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $priceid = I('ticket_type');
	    $channel = I('channel_id');
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
        $this->assign('channel_id',$channel)->assign('channel_name',I('channel_name'));
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = date("Ymd",strtotime($start_time));
            $end_time = date("Ymd",strtotime($end_time));
            //$map['plantime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
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
        $map['type'] = array('in','1,2,4,7');
        $map['status'] = '1';
        $db = M('ReportData');
		$map['product_id'] = get_product('id');
		$list = $db->where($map)->order('plantime ASC,games')->select();
		//加载当前产品配置 TODO
		if($this->procof['agent'] == '1'){
			//开启代理商制度，时执行
			$list = Report::level_fold($list);
		}
		//按渠道商汇总
		$channe_fold = Report::channel_area_fold($list,$map['plantime'],$channel);
		$this->area();
		$this->assign('data',$channe_fold)
			->display();
	}
	//渠道销售统计分区域 分票型
	function canal(){
		$start_time = I('starttime');
	    $end_time = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $priceid = I('ticket_type');
	    $channel = I('orgLookup_id');
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
        $this->assign('channel_id',$channel);
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
        $db = M('Report');
        $map['product_id'] = get_product('id');
		$list = $db->where($map)->select();
		$this->area();
		$this->assign('data',$channe_fold)
			->assign('user_id',\Item\Service\Partner::getInstance()->id)
			->display();
	}
	/*区域销售统计*/
	function sub_seat_area(){
		if(IS_POST){

		}else{
			$this->display();
		}
	}
	//上座率  可分场次查询  可按时间段查询  单一票型贡献上座率
	function attendance(){

	}
	//场景销售统计
	function scenario(){
		if(IS_POST){
			
		}
		$this->assign('data',$data)->display();
	}
	//加载当前产品的区域
	function area(){
		$product_id = (int) get_product('id');
		if(!empty($product_id)){
			$info = Operate::do_read('TicketGroup',1,array('product_id'=>$product_id));
			$template_id = M('Product')->where(array('id'=>$product_id))->getField('template_id');
			$area = M('Area')->where(array('status'=>'1','template_id'=>$template_id))->field('id,name')->select();
			$this->assign('area',$area);
			$this->assign('area_num',count($area));
		}else{
			$this->erun('参数错误!');
		}
	}
	/*分场次票型销售统计 时时更新*/
	function plan_ticket(){
		if(IS_POST){
			$plan_id = I('plan_id');
			$ticket_id = I('ticket_id');
			$map = [];
			if(empty($plan_id)){$this->erun("参数错误");}
			$plan = F('Plan_'.$plan_id);
			if(empty($plan)){
				$plan = M('Plan')->where(array('id'=>$plan_id))->field('id,plantime,seat_table')->find();
			}
			$plantime = date('Y-m-d',$plan['plantime']);
			$timediff = timediff(date('Y-m-d'),$plantime,'day');
			$product_id = get_product('id');
			if(!empty($ticket_id)){
				$ticket_id = explode(',',$ticket_id);
				$this->assign('ticket_id',$ticket_id);
			}else{
				$ticket_id = M('TicketType')->where(array('product_id'=>$product_id))->field('id')->select();
				$ticket_id = array_column($ticket_id,'id');
			}
			$price = F('TicketType'.$product_id);
			$map['plan_id'] = $plan_id;
			$map['product_id'] = $product_id;
			//判断演出日期是否超过30天
			if($timediff['day'] < '30'){
				$db = M(ucwords($plan['seat_table']));
				foreach ($price as $v) {
		        	if(in_array($v['id'],$ticket_id)){
		        		$map['price_id'] = $v['id'];
		        		(int)$number = $db->where($map)->count();
			        	if($number <> (int)0){
			        		$list[$v['id']] = $v;
				        	$list[$v['id']]['number'] = $number;
				        	$list[$v['id']]['money']  = $v['price']*$number;
				        	$list[$v['id']]['moneys'] = $v['discount']*$number;
				        	$list[$v['id']]['rebate'] = $v['rebate']*$number;
				        	$info['number'] += $number;
				        	$info['money']  += $list[$v['id']]['money'];
				        	$info['moneys']	+= $list[$v['id']]['moneys'];
				        	$info['rebate']	+= $list[$v['id']]['rebate'];
			        	}
		        	}
		        }
			}else{
				$db = M('ReportData');
				foreach ($price as $v) {
		        	if(in_array($v['id'],$ticket_id)){
		        		$map['price_id'] = $v['id'];
		        		(int)$number = $db->where($map)->sum('number');
			        	if($number <> (int)0){
			        		$list[$v['id']] = $v;
				        	$list[$v['id']]['number'] = $number;
				        	$list[$v['id']]['money']  = $v['price']*$number;
				        	$list[$v['id']]['moneys'] = $v['discount']*$number;
				        	$list[$v['id']]['rebate'] = $v['rebate']*$number;
				        	$info['number'] += $number;
				        	$info['money']  += $list[$v['id']]['money'];
				        	$info['moneys']	+= $list[$v['id']]['moneys'];
				        	$info['rebate']	+= $list[$v['id']]['rebate'];
			        	}
		        	}
		        }
			}
		}

        $this->assign('data',$list)
        	->assign('plan_id',$plan_id)
			->assign('ticket_name',I('ticket_name'))
			->assign('plan_name',I('plan_name'))
        	->assign('info',$info)
			->display();
	}
	//按销售员统计  分渠道类型 酒店 旅行社 .. 按区域汇总
	function market_report(){
		if(IS_POST){
			$starttime = I('starttime') ? I('starttime') : date('Y-m-d');
		    $endtime = I('endtime') ? I('endtime') : date('Y-m-d');
		    $user_id = I('user_id');
		    if(empty($user_id)){
		    	$this->erun('员工为必须条件，不能为空');
		    }
		    $where = get_crm_guide($user_id);//dump($where);
	 	    //传递条件
		    $this->assign('starttime',$starttime);
	        $this->assign('endtime',$endtime);
	        $this->assign('user_id',$user_id)->assign('user_name',I('user_name'));
			if (!empty($starttime) && !empty($endtime)) {
	            $starttime = strtotime($starttime);
	            $endtime = strtotime($endtime);
	            $map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
	            $maps['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
	        }
	        $db = D('ChannelView');
	        if(!empty($where['channel'])){
	        	$map['qd_id'] = array('in',$where['channel']);//公共导游id 318 
	        	$map['guide_id'] = array('eq','295');
	        	$channel = $db->where($map)->select();
	        	foreach ($channel as $key => $value) {
	        		$list['channel'][$value['qd_id']]['number'] += $value['number'];
	        		$list['channel'][$value['qd_id']]['qd_id'] = $value['qd_id'];
	        		$list['info']['number'] +=  $value['number'];
	        	}
	        }
	        if(!empty($where['guide'])){
	        	$maps['guide_id'] = array('in',$where['guide']);//公共导游id
	        	$maps['guide_id'] = array('neq','295');
	        	$guide = $db->where($maps)->select();
	        	foreach ($guide as $k => $v) {
	        		$list['guide'][$v['guide_id']]['number'] += $v['number'];
	        		$list['guide'][$v['guide_id']]['guide_id'] = $v['guide_id'];
	        		$list['info']['number'] += $v['number'];
	        	}
	        }
		}
		$this->assign('data',$list)
			->display();

	}
	//周返利 统计数量
	function channel_rebate(){
		$start_time = I('start_time');
	    $end_time = I('end_time') ? I('end_time') : date('Y-m-d',time());
	    $status = I('status');
	    $sn = I('sn');
	    $channel = I('orgLookup_id');
	    $guide = I('orgLookup_ids');
	    $sum_det = I('sum_det') ? I('sum_det') : '1';//1明细2合计
	    $map['product_id'] = get_product('id');
	    //传递条件
	    $this->assign('starttime',$start_time ? $start_time : $end_time)
        	->assign('endtime',$end_time)
        	->assign('sum_det',$sum_det);
		C('VAR_PAGE','pageNum');
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $map['createtime'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
        }else{
        	//默认显示当天的订单
        	$start_time = strtotime(date("Ymd"));
            $end_time = $start_time + 86399;
        	$map['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }
        $db = M('TeamOrder');
		if(!empty($status)){$map['status'] = $status;}
		if(!empty($sn)){$map['order_sn'] = $sn;}
		if(!empty($channel)){$map['qd_id'] = array('in',agent_channel($channel,2));}
        if(!empty($guide)){$map['guide_id'] = $guide;}
		if($sum_det == '1'){
			/*分页设置*/
			$count = $db->where($map)->count();// 查询满足要求的总记录数
			$num = 20;
			$p = new \Item\Service\Page($count,$num);
			$currentPage = !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
			$firstRow = ($currentPage - 1) * $num;
			$listRows = $currentPage * $num;		
			$list = $db->where($map)->order("id DESC")->limit($firstRow . ',' . $p->listRows)->select();
			$this->assign ( 'totalCount', $count );
			$this->assign ( 'numPerPage', $p->listRows);
			$this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
		}else{
			$list = $db->where($map)->select();
			$list = Report::rakeback_us($list);
		}	
		$this->assign('data',$list)
			->assign('map',$map)
			->assign('channel',$channel)
			->display();
	} 
	//渠道退减汇总
	function less(){
		$start_time = I('starttime');
	    $end_time = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $map['createtime'] = $maps['createtime'] =array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
            //查询所有一级渠道商
			$product_id = get_product('id');
			$crm = F('Crm_level'.$product_id);
			foreach ($crm as $key => $value) {
				//根据一级渠道商查询旗下所有渠道商
				$map['channel_id'] = $maps['crm_id'] = array('in',agent_channel($value['id'],2));
				$map['subtract'] = '1';
				//查询每个渠道商 退单数 和核减数
				//操作过核减的订单数
				$channel_id = $value['id'];
				$subtract = M('Order')->where($map)->count();//dump($map);
				$number = M('Order')->where($map)->sum('subtract_num');
				$maps['status'] = array('in','1,3');
				//退单数
				$refund	= M('TicketRefund')->where($maps)->count();
				if(!empty($subtract) || !empty($number) || !empty($refund)){
					$info[$value['id']] = array(
						'channel_id'	=>	$value['id'],
						'subtract'		=>	$subtract,
						'number'		=>	$number,
						'refund'		=>	$refund,
						'ratio'			=> round(100*($subtract/$refund),2),
					);
				}
			}
        }
		$this->assign('data',$info)->display();
	}
	//出票员报表
	function drawer_report(){
		$start_time = I('starttime');
	    $end_time = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = date("Ymd",strtotime($start_time));
            $end_time = date("Ymd",strtotime($end_time));
            $map['datetime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }
        
	}
	//地区统计
	function region(){
		$start_time = I('starttime');
	    $end_time = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = date("Ymd",strtotime($start_time));
            $end_time = date("Ymd",strtotime($end_time));
            $map['datetime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }
        $map['region'] = array('neq','');
        $list = M('ReportData')->where($map)->select();
        foreach ($list as $key => $value) {
        	$info[$value['region']]['region'] = $value['region'];
        	$info[$value['region']]['number'] += $value['number']; 
        }
        foreach ($info as $key => $value) {
        	$number[$key] = $value['number'];
        	$region[$key] = $value['region'];
        }
        //多维数组排序
        array_multisort($number,SORT_DESC,$region,$info);
        $this->assign('data',$info)->display();
	}
	//渠道配额消耗一览表
	function quota_lass(){
		if(IS_POST){
			//渠道商级别默认只查询一级
			$plan_id = I('plan_id');
	        $level = I('level');
	        if(empty($plan_id)){
	        	$this->erun("请选择查询场次...");
	        }
	        $map['plan_id'] = $plan_id;
	        $map['channel_id'] = array('in',channel_level($level));
	        $map['type'] = '1';
	        $db = D('QuotaUse');
	        $list = $db->where($map)->select();
		}else{
			$level = '16';//一级
			$list = '404';
		}
		
        //读取销售计划 过期的两天 和当前未过期的全部
		$plantime = strtotime(date('Y-m-d'));
		$plan = M('Plan')->where(array('plantime'=>array('egt',$plantime)))->order('plantime ASC')->select();
        
        $this->assign('data',$list)->assign('plan',$plan)
             ->assign('level',$level)->assign('plan_id',$plan_id)->display();
	}
	/**
	 *	全员销售统计
	 * @return [type] [description]
	 */
	function full_sales(){
		$start_time = I('starttime');
	    $end_time = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $type = I('type') ? I('type') : '1';
	    $priceid = I('ticket_type');
	    $channel = I('channel_id');
	    $channelname = I('channel_name');
	    $industry = I('industry');
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
        $export_map['datetime'] = $start_time.'至'.$end_time;
        $this->assign('channel_id',$channel)->assign('channel_name',$channelname)->assign('industry',$industry);
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
        if(!empty($industry)){
        	//获取行业内所有人员
        	$where = [
        		'status' => '1',
        		'industry' => $industry,
        	];
        	$user = D('Crm/UserView')->where($where)->field('id')->select();
        	$user_id = arr2string($user,'id');
        	$map['user_id'] = array('in',$user_id);
        }
        //设置订单类型为团队或渠道
        $map['type'] = array('in','8,9');
        $map['status'] = '1';
        $map['product_id'] = get_product('id');
        $db = M('ReportData');
		$list = $db->where($map)->order('plantime ASC,games')->select();
		if($this->procof['agent'] == '1'){
			//开启代理商制度，时执行
			$list = Report::level_fold($list);
		}
		if($type == '1'){
			//根据计划汇总
			//$plan_fold = Report::plan_fold($list);
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
	//渠道商销售排行
	function channel_sales_ranking(){

	}
	/**
     * 退票日志
     * @Company  承德乐游宝软件开发有限公司
     * @Author   zhoujing      <zhoujing@leubao.com>
     * @DateTime 2017-11-28
     * TODO
     */
    function report_refund(){
    	if(IS_POST){
    		$plan_id = I('plan_id');
    	}
        $where = array();
        $start_time = I('starttime');
        $end_time = I('endtime');
        $this->assign('starttime',$start_time)->assign('endtime',$end_time);
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }
        $where = [
        	'status'	=>	3,
        	'product_id'=> get_product('id')
        ];
        $model = D('Item/TicketRefund');
        $field = ['createtime','order_sn','applicant','param','reason','status','re_money','re_type','updatetime','poundage','poundage_type','against_reason','order_status','user_id'];
        $list = $model->where($where)->field($field,true)->select();
        
        //按照场次分退票场景合并
        $this->assign('where', $where)->display();
    }
	//渠道任务完成情况
	function channel_kpi(){
		$this->display();
	}
}