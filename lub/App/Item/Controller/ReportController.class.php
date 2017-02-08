<?php
// +----------------------------------------------------------------------
// | LubTMP 报表管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
use Libs\Service\Operate;
use Libs\Service\Report;
use Item\Service\Partner;
class ReportController extends ManageBase{
	function _initialize(){
	 	parent::_initialize();
	}
	/**
	 * 景区销售明细
	 */
	function index(){
		if(IS_POST){
			$map = array(
				'product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
			);
		}else{
			$map = array(
				'product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),				
			);
		}
		$list = Operate::do_read('Order',1,$map,array('id'=>'desc'));
		$this->assign('data',$list)
			->display();
	}
	//剧院销售明细  按票型划分
	function theatre_sales(){
		$start_time = I('start_time');
	    $end_time = I('end_time') ? I('end_time') : date('Y-m-d',time());
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
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
		$db = D('ReportData');
		/*分页设置*/
		$count = $db->where($map)->count();// 查询满足要求的总记录数
		$num = 25;
		$p = new \Item\Service\Page($count,$num);
		$currentPage = !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
		$firstRow = ($currentPage - 1) * $num;
		$listRows = $currentPage * $num;		
		$list = $db->where($map)->order("id DESC")->limit($firstRow . ',' . $p->listRows)->select();
		$this->assign ( 'totalCount', $count );
		$this->assign ( 'numPerPage', $p->listRows);
		$this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);		
		$this->assign('data',$list)->display(); 
	}
	/**
	 * 报表管理
	 */
	function manage(){
		$start_time = I('starttime');
		$type = I('type');
		$this->assign('starttime',$start_time);
		if (!empty($start_time)) {
            $map['starttime'] = date("Ymd",strtotime($start_time));
        }else{
        	//默认显示当天的订单
        	$map['starttime'] = date("Ymd",strtotime("-1 day"));     	
        }
		if($type != ''){
			$map['type'] = $type;
		}
		$list = M('Report')->where($map)->select();
		$this->assign('data',$list)->display();
	}
	/**
	 * 订单详情
	 */
	function orderInfo(){
		$sn = I('get.sn');
		$this->assign('data',Operate::do_read('Order',0,array('order_sn'=>$sn),'','',true))
			->display();
	}
	
	/**
	 * 景区日报表
	 */
	function scenic(){
		$start_time = I('start_time') ? I('start_time') : date('Y-m-d',time());
		$sum_det = I('sum_det') ? I('sum_det')  : '1';
		if(empty($start_time)){$this->erun('参数错误');}
		//传递查询条件
		$this->assign('start_time',$start_time);
		$where['datetime'] = date('Ymd',strtotime($start_time));
		$list = Operate::do_read('ReportData',1,$where,array('plantime ASC,games'));
		if($sum_det == '1'){
			//明细
			$this->assign('data',$list);
		}else{
			//根据计划汇总
			$plan_fold = Report::plan_fold($list);
			//根据票型汇总
			$ticket_fold = Report::ticket_fold($plan_fold,$where['datetime']);
			//退票记录 TODO
			$this->assign('data',$ticket_fold);
		}
		$this->assign('sum_det',$sum_det)
			->display();
	}
	/*
	*营业销售统计
	*/
	function sub_business(){
		if(IS_POST){
			$plan_id = I('orgLookup_planid');
			//$ticket_id = I('org_id');
			if(empty($plan_id)){$this->erun("参数错误");}
			$where = array(
				'plan_id' => $plan_id,
				'status'  => '1',
				);//dump($where);
			$list = Operate::do_read('ReportData',1,$where);
			//M('ReportData')->where(array('plan_id'=>$plan_id))->delete();
			//根据票型汇总
			$ticket_fold = Report::plan_ticket_folds($list);//dump($ticket_fold);

		}else{
			//批量生成报表
			/*$datetime = "20150616";
			$start_time = strtotime($datetime);
			for ($i=1; $i < 36; $i++) {
			   	$end_time = $start_time  + 86399*$i;
				$time = date('Ymd',$end_time);

				if(Report::report($time)){
					echo $time."生成完毕。。。<br>";
				}else{
					echo $time."生成失败....<br>";
				}
			}*/
			
		}
		$this->assign('plan_id',$plan_id)->assign('data',$ticket_fold)->display();
	}
	/*景区招待票汇总*/
	function reception(){
		if(IS_POST){
			//获取系统中所有0元票票型
			$db = D('ReportData');

			$price = M('TicketType')->where(array('free'=>'1'))->field('id')->select();
			$map = array('status'=>'1','priceid'=>array('in',arr2string($price,'id',',')));
			$start_time = I('starttime');
		    $end_time = I('endtime') ? I('endtime') : date('Y-m-d',time());
		    //传递条件
		    $this->assign('starttime',$start_time);
	        $this->assign('endtime',$end_time);
			if (!empty($start_time) && !empty($end_time)) {
	            $start_time = date('Ymd',strtotime($start_time));
	            $end_time = date('Ymd',strtotime($end_time));
	            $map['plantime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
	        }else{
	        	//默认显示当天的订单
	        	$start_time = date("Ymd");
	            $end_time = $start_time;
	        	$map['plantime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
	        }//dump($map);
			$list = $db->where($map)->select();
			$list = Report::plan_fold($list,1);
			$list = Report::strip_plan($list);
		}
		//dump($list);
		$this->assign('plan_id',$plan_id)->assign('data',$list)->display();
	}
	/**
	 * 财务管理 票型统计报表
	 */
	function ticket_type(){
		$start_time = I('starttime');
		$end_time = I('endtime') ? I('endtime') : date('Y-m-d');
		$ticket_id = I('org_id');
		$channel = I('orgLookup_id');
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
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
        if(!empty($ticket_id)){
        	$map['priceid'] = array('in', explode(',',$ticket_id));
        }else{
			$ticket_id = M('TicketType')->where(array('status'=>1))->field('id')->select();
			$ticket_id = array_column($ticket_id,'id');
		}
        if(!empty($channel)){
        	$map['channel_id'] = array('in',agent_channel($channel,2));
        }
        $db = M('ReportData');
		$map['product_id'] = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
		$map['status'] = '1';
   		$price = F('TicketType'.$map['product_id']);
        $list = $db->where($map)->select();
        foreach ($list as $k => $v) {
            $data[$v['priceid']]['name'] = $price[$v['priceid']]['name'];
            $data[$v['priceid']]['price'] = $price[$v['priceid']]['price'];
            $data[$v['priceid']]['discount'] = $price[$v['priceid']]['discount'];
        	$data[$v['priceid']]['number'] += $v['number'];
        	$data[$v['priceid']]['money'] += $v['price']*$v['number'];
        	$data[$v['priceid']]['moneys'] += $v['discount']*$v['number'];
        	$info['number'] += $v['number'];
		    $info['money']  += $v['price']*$v['number'];
		    $info['moneys']	+= $v['discount']*$v['number'];

        }
        //场次汇总 TODO
        $info['games'] = M('Plan')->where(array('plantime'=>array(array('EGT', strtotime($start_time)), array('ELT', strtotime($end_time)), 'AND')))->count();
		$this->assign('data',$data)
			->assign('ptype',$price)
			->assign('ticket_type',$priceid)
			->assign('info',$info)
			->display();
	}
	/*分场次票型销售统计 时时更新*/
	function plan_ticket(){
		if(IS_POST){
			$plan_id = I('orgLookup_planid');
			$ticket_id = I('org_id');
			if(empty($plan_id)){$this->erun("参数错误");}
			$plan = F('Plan_'.$plan_id);
			if(empty($plan)){
				$plan = M('Plan')->where(array('id'=>$plan_id))->find();
			}
			//TODO 超过一个月的直接从历史报表中读取
			$product_id = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
			if(!empty($ticket_id)){
				$ticket_id = explode(',',$ticket_id);
			}else{
				$ticket_id = M('TicketType')->where(array('status'=>1))->field('id')->select();
				$ticket_id = array_column($ticket_id,'id');
			}
			if(date('Ymd',$plan['plantime']) <= '20150602'){$this->erun("亲，非常抱歉，该场次不支持此功能!");}
			$price = F('TicketType'.$product_id);
			$db = M(ucwords($plan['seat_table']));
	        foreach ($price as $v) {
	        	if(in_array($v['id'],$ticket_id)){
	        		$map['price_id'] = $v['id'];
	        		$number = $db->where($map)->count();
		        	if($number <> '0'){
		        		$list[$v['id']] = $v;
			        	$list[$v['id']]['number'] = $number;
			        	$list[$v['id']]['money']  = $v['price']*$number;
			        	$list[$v['id']]['moneys'] = $v['discount']*$number;
			        	$info['number'] = $info['number']+$number;
			        	$info['money']  = $info['money']+$list[$v['id']]['money'];
			        	$info['moneys']	= $info['moneys']+$list[$v['id']]['moneys'];
		        	}
	        	}
	        }
		}
        $this->assign('data',$list)
        	->assign('plan_id',$plan_id)
        	->assign('info',$info)
			->display();
	}
	
	/**
	 * 渠道统计报表
	 */
	function channel(){
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
        //设置订单类型为团队或渠道
        $map['type'] = array('in','2,4,7');
        $map['status'] = '1';
        $db = M('ReportData');
		$map['product_id'] = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');//dump($map);
		$list = $db->where($map)->order('plantime ASC,games')->select();
		//加载当前产品配置 TODO
		if($this->procof['agent'] == '1'){
			//开启代理商制度，时执行
			$list = Report::level_fold($list);
		}
		//根据计划汇总
		$plan_fold = Report::plan_fold($list);
		//根据票型汇总
		$ticket_fold = Report::channel_ticket_fold($plan_fold,$map['datetime'],$channel);
		$this->assign('data',$ticket_fold);
		$this->display();
	}
	/* API 接口销售统计
	* 
	*/
	function api_channel(){
		$start_time = I('starttime');
	    $end_time = I('endtime') ? I('endtime') : date('Y-m-d',time());
	    $priceid = I('ticket_type');
	    $channel = I('orgLookup_id');
	    $addsid = I('addsid');
	    //传递条件
	    $this->assign('starttime',$start_time)
        	->assign('endtime',$end_time)
        	->assign('channel_id',$channel)
        	->assign('addsid',$addsid);
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = date("Ymd",strtotime($start_time));
            $end_time = date("Ymd",strtotime($end_time));
            $map['plantime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }else{
        	//默认显示当天的订单
        	$start_time = date("Ymd");
            $end_time = $start_time;
        	$map['plantime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }
        if(!empty($channel)){
        	$map['channel_id'] = array('in',agent_channel($channel,2));
        }
        if(IS_POST){
        	if(!empty($addsid)){
	        	$map['addsid'] = $addsid;
	        }else{
	        	$this->erun("请选择场景");
	        }	
        }
        $db = M('ReportData');
		$map['product_id'] = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
		$list = $db->where($map)->select();
		//加载当前产品配置 TODO

		if($this->procof['agent'] == '1'){
			//开启代理商制度，时执行
			$list = Report::level_fold($list);
		}
		//根据计划汇总
		$plan_fold = Report::plan_fold($list);
		//根据票型汇总
		$ticket_fold = Report::channel_ticket_fold($plan_fold,$map['plantime'],$channel);
		$this->assign('data',$ticket_fold);
		$this->display();
	}
	
	/**
	 * 分销商统计报表
	 */
	function distributors(){
		//以下代码只做临时使用
		if(IS_POST){
			$datetime = date('Ymd',time());
	    	$start_time = strtotime($datetime);
	        $end_time = $start_time  + 86399;
	    	$map = array(
	    		'status' => array('in','1,9'),//订单状态为支付完成和已出票
	    		'createtime' => array(array('EGT', $start_time), array('ELT', $end_time), 'AND'),
	    	);
	    	$list = D('Item/Order')->where($map)->relation(true)->select();
	    	foreach ($list as $key => $value) {
	    		// TODO 缺少写入成功性验证
	    		Report::order($value['order_sn'],$value['info'],$datetime);
	    	}
		}else{
			$datetime = date('Ymd',time());
			$status = Libs\Service\Report::report($datetime);
	    	/*$start_time = strtotime('20150307');
	        $end_time = $start_time  + 86399;
	    	$map = array(
	    		'status' => array('in','1,9'),//订单状态为支付完成和已出票
	    		'createtime' => array(array('EGT', $start_time), array('ELT', $end_time), 'AND'),
	    	);
	    	$status = M('ReportData')->where(array('datetime',$datetime))->find();
			//当前日期是否已生成
			if(!empty($status)){
				if(M('ReportData')->where(array('datetime'=>$datetime,'status'=>'1'))->setField('status',0)){
					$status = Libs/Service/Report::strip_order($map,$datetime);
				}else{
					//失败记录日志，并发送错误短信
					return false;
				}
			}else{
				$status = Libs/Service/Report::strip_order($map,$datetime);
			}
			if($status){
			    //成功记录日志

			}else{
			    //失败记录日志，并发送错误短信
*/	//	}
		}
	}
	/**
	 * 渠道返佣计算
	 */
	function rakeback(){
		$start_time = I('start_time');
	    $end_time = I('end_time') ? I('end_time') : date('Y-m-d',time());
	    $status = I('status');
	    $sn = I('sn');
	    $channel = I('orgLookup_id');
	    $guide = I('orgLookup_ids');
	    $sum_det = I('sum_det') ? I('sum_det') : '1';//1明细2合计
	    $map['product_id'] = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
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
		//dump($list);
		$this->assign('data',$list)
			->assign('map',$map)
			->assign('channel',$channel)
			->display();
	}
	/*
	 * 返利
	 * $type 按订单返利
	
	function rebate(){
		$ginfo = I('get.');
		if(!empty($ginfo)){
			$info = Operate::do_read('TeamOrder',0,array('id'=>$ginfo['id']));
			if($info['status'] == '4'){
				$this->erun("已完成返利!");
			}
			$user_id = \Libs\Util\Encrypt::authcode($_SESSION['lub_imuid'], 'DECODE');
			$model = new \Think\Model();
			$model->startTrans();
			//查询渠道商信息
			$result = M("Crm")->where(array('id'=>$info['qd_id']))->find();
			$cid = money_map($result);
			//先充值  后标记.
			$top_up = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$cid))->setInc('cash',$info['money']);
			//充值成功后，添加一条充值记录
			$data = array(
					'type'=> 3,
					'cash'=> $info['money'],
					'user_id'  => $user_id,
					'crm_id'   => $info['qd_id'],//售出信息 票型  单价
					'createtime' =>time(),
					'order_sn'	=> $info['order_sn']
			);			
			$recharge = $model->table(C('DB_PREFIX')."crm_recharge")->add($data);
			//更新返利状态
			$up = $model->table(C('DB_PREFIX')."team_order")->where(array('id'=>$info['id']))->save(array('status'=>'4','userid'=>$user_id));
			if($top_up && $recharge && $up){
				$model->commit();//成功则提交
				$this->srun('返利成功!',$this->navTabId);
			}else{
				$model->rollback();//不成功，则回滚
				$this->erun("返利失败!");
			}
		}else{
			$this->erun("参数错误!");
		}
	} */
	//开始返利
	function rebate(){
		$ginfo = I('get.');//dump($ginfo);
		if(!empty($ginfo)){
			$info = Operate::do_read('TeamOrder',0,array('id'=>$ginfo['id']));
			if($info['status'] == '4'){
				$this->erun("已完成返利!");
			}
			//返利方式
			if($ginfo['type'] == '1'){
				//返到授信 返利对象
				if($info['type'] == '1'){//返利给渠道商
					$status = $this->credit($info['type'],$info['qd_id'],$info['money'],$info['order_sn'],$ginfo['id']);
				}else{//返利给导游
					$status = $this->credit($info['type'],$info['guide_id'],$info['money'],$info['order_sn'],$ginfo['id']);
				}
			}else{//返到银行卡 返到现金
				$data = array('status'=>'4','subtype'=>2,'userid'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_imuid'], 'DECODE'));
				$status = M('TeamOrder')->where(array('id'=>$info['id']))->save($data);
			}
			if($status){
				$this->srun('补贴成功!',$this->navTabId);
			}else{
				$this->erun("补贴失败!");
			}
		}else{
			$this->erun("参数错误!");
		}
	}
	/*返到授信额
	@param $type 返利方式
	@param $tag ID 返利对象
	@param $money 金额
	@param $sn 订单号
	@param $team_id 团队订单id*/
	private function credit($type,$tag,$money,$sn,$team_id){
		$model = new \Think\Model();
		$model->startTrans();
		//返利对象
		if($info['type'] == '1'){
			//返利给渠道商
			$top_up = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$tag))->setInc('cash',$money);
			$data = array(
				'type'=> 3,
				'cash'=> $money,
				'tyint'=>1,
				'user_id'  => \Libs\Util\Encrypt::authcode($_SESSION['lub_imuid'], 'DECODE'),
				'guide_id'	=> '',
				'crm_id'   =>  $tag,//售出信息 票型  单价
				'createtime' =>time(),
				'order_sn'	=> $sn,
			);	
		}else{
			//返利给导游
			$top_up = $model->table(C('DB_PREFIX')."user")->where(array('id'=>$tag))->setInc('cash',$money);
			$data = array(
				'type'=> 3,
				'tyint'=>'2',
				'cash'=> $money,
				'user_id'  => \Libs\Util\Encrypt::authcode($_SESSION['lub_imuid'], 'DECODE'),
				'guide_id'	=> $tag,
				'crm_id'   => '',//售出信息 票型  单价
				'createtime' =>time(),
				'order_sn'	=> $sn,
			);
		}
		
		//充值成功后，添加一条充值记录
		$recharge = $model->table(C('DB_PREFIX')."crm_recharge")->add($data);
		//dump($recharge);
		//更新返利状态
		$up = $model->table(C('DB_PREFIX')."team_order")->where(array('id'=>$team_id))->save(array('status'=>'4','subtype'=>1));
		//dump($up);
		if($top_up && $recharge && $up){
			$model->commit();//成功则提交
			return true;
		}else{
			$model->rollback();//不成功，则回滚
			return false;
		}
	}
	/**
	 * 自定义条件报表
	 */
	function custom(){
	}
	
	/**
	 * 操作员日报表
	 */
	function operator(){
		if(IS_POST){
			$pinfo = I('post.');
			$start_time = strtotime($pinfo['datetime']);
	        $end_time = $start_time  + 86399;
	    	$map = array(
	    		'status' => array('in','1,7,9'),//订单状态为支付完成和已出票和申请退票中的报表
	    		'createtime' => array(array('EGT', $start_time), array('ELT', $end_time), 'AND'),
	    	);
	    	if(!empty($pinfo['user'])){
				$map['user_id'] = $pinfo['user'];
	        }
	    	//获取订单
			$list = Report::strip_order($map,date('Ymd',strtotime($pinfo['datetime'])),2);
			//按计划合并
			$list = Report::plan_fold($list,2);
			//按票型合并
			$list = Report::strip_plan($list);
			$info['money'] = M('Order')->where($map)->sum('money');
			$info['number'] = M('Order')->where($map)->sum('number');
			$this->assign('data',$list)
				->assign('map',$map)
				->assign('info',$info)
				->assign('datetime',$pinfo['datetime']);

			/*$list = M('ReportData')->where(array('datetime'=>date('Ymd',strtotime($pinfo['datetime'])),'status'=>1))->select();
 			$plan = Report::plan_fold($list);
 			//票型合并 
 			$ticket_fold = Report::ticket_fold($plan,date('Ymd',strtotime($pinfo['datetime'])));
 			//写入日报表
			$status = M('Report')->add(array(
			'product_id' 	=> '43',
			'type'			=> '2',
			'user_id'		=> '0',
			'starttime'		=>	date('Ymd',strtotime($pinfo['datetime'])),
			'endtime'		=>	date('Ymd',strtotime($pinfo['datetime'])),
			'title'			=>	date('Ymd',strtotime($pinfo['datetime']))."景区日报表",
			'info'			=>	serialize($ticket_fold),
			'createtime'	=>	time(),
			));*/
			
		}
		//TODO  按角色缓存 用户数据
		$user = M('User')->where(array('status'=>1,'is_scene'=>2))->field('id,nickname')->select();
		$this->assign('user',$user)
			->display();
	}

	/*
	 * 报表生成
	 */
	function generate(){
		$db = D('Order');
		if(IS_PSOT){
			$pinfo = I('post.');
			$where = array();
			$number = array();
			$start_time = $pinfo['starttime'];
			$end_time = $pinfo['endtime'];
			if (!empty($start_time) && !empty($end_time)) {
	            $start_time = strtotime($start_time);
	            $end_time = strtotime($end_time) + 86399;
	            $where['createtime'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
	        }
	        /*必须为完成订单 */
	        $where['status']='1';
	        $list = $db->where($where)->relation(true)->select();
	        //拆分数据
	        foreach ($list as $k=>$v){
	        	$list[$k]['info'] = unserialize($v['info']);
	        }
	        foreach($list as $ke=>$va){
	        	 //读取价格政策
	        	$ticketType = F("TicketType".$va['product_id']);
	        	//按区域集合
	        	foreach ($va['info']['data'] as $key=>$val){
	        		$number[$va['order_sn']][$val['priceid']] = $number[$va['order_sn']][$val['priceid']]+1;
	        		$dataList[$va['order_sn']][$val['priceid']] = array(
	        			'order_sn'	=>	$va['order_sn'],
	        			'area'			=>	$val['areaId'],
	        			'priceid'		=>	$val['priceid'],
	        			'price'			=>	$val['price'],
	        			'discount'	=>   $ticketType[$val['priceid']]['discount'],
	        			'money'		=>	$number[$va['order_sn']][$val['priceid']]*$ticketType[$val['priceid']]['discount'],
	        			'number'		=>	$number[$va['order_sn']][$val['priceid']],
	        			'plan_id'		=>	$va['plan_id'],
	        			'channel_id'=>	$va['channel_id'],
	        			'user_id'		=>	$va['user_id'],
	        			'createtime'	=>	$va['createtime'],
	        			'type'			=>	$va['type'],
	        			'pay'			=>	$va['pay'],
	        			'addsid'		=>	$va['addsid'],
	        		);
	        	}
	        	//dump($dataList[$va['order_sn']]);
	        	$status = M('ReportData')->add($dataList[$va['order_sn']][$val['priceid']]);
	        }
	       if($status){
	        	$this->srun("报表生成成功!");
	        }else{
	        	$this->erun("报表生成失败!");
	        }
		}else{
			
		}
	}

	/*
	*条件生成、整理
	*@param $type int 请求类型 1当前用户日报表 2财务日报表 3渠道日报表
	*@param $postdata array 整理数据
	*return $map array
	*/
	function maps($type = null, $postdata){
		//当前产品
		$map['product_id'] = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
		//当前用户
		$uinfo = Partner::getInstance()->getInfo();
		switch ($type) {
			case '1':
				//当前用户日报表
				$map = array(
					//'user_id'	=> $uinfo['id'],
					'datetime'  => date('Ymd',strtotime($postdata['datetime'])),
				);
				break;
			case '2':
				//景区日报表
				$map = array(
					'datetime'  => date('Ymd',strtotime($postdata['datetime'])),
				);
				break;
			case '3':
				//渠道日报表
				$map = array(
					//'user_id'	=> $uinfo['id'],
					'datetime'  => date('Ymd',strtotime($postdata['datetime'])),
				);
				break;
			case '4':
				//返佣日报表

				break;
			case '5':
				//票型统计日报表

				break;
			case '6':
				//区域销售统计

				break;
			default:
				//财务日报表

				break;
		}
		return $map;
	}
	
	/*
	动态表名称
	@param $createtime 查询时间
	return $table 返回表名称
	*/
	function dynamic_table($createtime){
		$starttime = date('Ymd',time());
		$endtime = date('Ymd',strtotime($createtime));
		if($starttime == $endtime){
			//查询当天
			return '1';
		}else{
			//查询以往
			return '2';
		}
	}
	//报表下载
	function down_report(){
		$ginfo = I('get.');
		$info = M('Report')->where(array('id'=>$ginfo['id']))->find();
		$execl_temp = $this->execl_temp($ginfo['type']);
		\Libs\Service\Exports::templateExecl($info,$execl_temp,$ginfo['type']);
	}
	//报表模板名称
	function execl_temp($type){
		switch ($type) {
			case '1':
				return "today_user";
				break;
			case '2':
				//景区日报表
				return "today_scenic";
				break;
			case '3':
				//渠道日报表
				$map = array(
					'datetime'  => date('Ymd',strtotime($postdata['datetime'])),
				);
				break;
			case '4':
				//返佣日报表
				return "today_recycle";
				break;
			case '5':
				//票型统计日报表

				break;
			case '6':
				//区域销售统计
				return "today_recycle";
				break;
			case '7':
				//渠道商授信快照
				return "today_credit";
				break;
			default:
				//财务日报表

				break;
		}
		return $map;
	}
	//渠道商充值记录
	function top_up(){
		$start_time = I('start_time');
	    $end_time = I('end_time') ? I('end_time') : date('Y-m-d',time());
	    $sn = I('sn');
	    $type = I('type');
	    $crm_id = I('crm_id');
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
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
        if(!empty($sn)){
        	$map['order_sn'] = $sn;
        }
        if(!empty($type)){
        	$map['type'] = $type;
        }
        if(!empty($crm_id)){
        	$map['crm_id'] = $crm_id;
        }
		$db = D('CrmRecharge');
		$channel = F('Crm_level'.\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'));

		//$datetime = date('Ymd',time());
		//	$status = \Libs\Service\Report::report($datetime);

		/*分页设置crm_recharge*/
		$count = $db->where($map)->count();// 查询满足要求的总记录数
		$num = 25;
		$p = new \Item\Service\Page($count,$num);
		$currentPage = !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
		$firstRow = ($currentPage - 1) * $num;
		$listRows = $currentPage * $num;		
		$list = $db->where($map)->order("id DESC")->limit($firstRow . ',' . $p->listRows)->select();
		$this->assign ( 'totalCount', $count );
		$this->assign ( 'numPerPage', $p->listRows);
		$this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);		
		$this->assign('data',$list)->assign('where',$map)->assign('channel',$channel)->display();
	}
	//渠道商余额日报
	function daily(){
		$datetime = I('datetime');
		$info = M('ReportData')->where(array('datetime'=>$datetime))->find();
		$this->assign('data',$info);
		$this->display();
	}

	/****===================================================财务操作======================================================*/
	//提现列表
	function cash_list(){

	}
	//授信查询汇总
	function channel_recharge(){
		$start_time = I('start_time');
	    $end_time = I('end_time') ? I('end_time') : date('Y-m-d',time());
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
	    if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $createtime = array(array('GT', $start_time), array('LT', $end_time), 'AND');
        }
	    $crm_id = I('crm_id');
	    $db = D('CrmRecharge');
		$channel = F('Crm_level'.\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'));
		//dump(\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'));
		foreach ($channel as $k => $v) {
			$data[$v['id']]['id'] = $v['id'];
			$data[$v['id']]['name'] = $v['name'];
			$data[$v['id']]['topup'] = $db->where(array('createtime'=>$createtime,'crm_id'=>$v['id'],'type'=>1))->sum('cash');
			$data[$v['id']]['cost'] = $db->where(array('createtime'=>$createtime,'crm_id'=>$v['id'],'type'=>2))->sum('cash');
			$data[$v['id']]['subsidies'] = $db->where(array('createtime'=>$createtime,'crm_id'=>$v['id'],'type'=>3))->sum('cash');
			$data[$v['id']]['refund'] = $db->where(array('createtime'=>$createtime,'crm_id'=>$v['id'],'type'=>4))->sum('cash');
		}
		$this->assign('data',$data)
			->display();
	}
}