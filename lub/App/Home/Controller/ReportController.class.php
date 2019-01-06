<?php
// +----------------------------------------------------------------------
// | LubTMP  渠道商报表查询统计
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use Home\Service\Partner;
use Libs\Service\Operate;
use Libs\Service\Exports;
use Libs\Service\Report;
class ReportController extends Base{
	function _initialize(){
	 	parent::_initialize();
	}
	
	//财务管理
	function index(){
		if (IS_POST) {
            $this->redirect('home.php?g=home&m=report&a=index', $_POST);
        }
		$db = D('CrmRecharge');
		$where = array();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $type = I('type');
        $sn = I('sn');
        //读取渠道商下所有员工
        //按操作员
		if (!empty($start_time) && !empty($end_time)) {
            $starttime = strtotime($start_time);
            $endtime = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('GT', $starttime), array('LT', $endtime), 'AND');
        }else{
        	//查询时间段为空时默认查询一个月内的数据
	        $starttime = time() - (86400 * 30);
	        $where['createtime'] = array(array('GT', $starttime), array('LT', time()), 'AND');
        }
		if(!empty($uid)){
        	$where['crm_id'] = $channel_id;
        }else{
        	//读取所有渠道商
			$where['crm_id'] =	array('in',$this->get_channel());
		}
		if($sn != ''){
			$where['order_sn'] = $sn;
		}
		if($type != ''){
			$where['type'] = $type;
		}
		$count = $db->where($where)->count();
		$Page  = new \Home\Service\Page($count,15);
		$show  = $Page->show();
		$list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('data',$list)
			->assign('page',$show)
			->assign('where',$where)
			->assign('start_time',$start_time)
			->assign('end_time',$end_time)
			->display();
	}

	/*
	 * 今日销售明细
	 * 销售明细可时时显示，并可查询近15天的数据，销售明细只能按天查看
	 * 汇总只能查看隔天，汇总可按时间段查询
	 
	function today_sales(){
		if (IS_POST) {
			$_POST['sum_det'] = $_POST['sum_det'] ? $_POST['sum_det'] : '1';
        	$this->redirect('', $_POST);
        }
        $sum_det = I('sum_det');
        $type = I('type');//票型
        $channel = I('channel');
        $uid = I('ur');
		$where = array();
		$today = date('Y-m-d',time());
		$start_time = I('start_time') ? I('start_time') : $today;

		/*if($start_time != $today){
			//历史报表直接从报表数据中直接获取
			$db = D('Report');
		}else{
			$db = D('ReportData');
		}
		$db = D('ReportData');
        $this->assign('start_time',$start_time);
		$where['status'] = '1';
		if (!empty($start_time)) {
            $where['datetime'] = date('Ymd',strtotime($start_time));
        }
        //按渠道
        if(!empty($channel)){
        	 $where['channel_id'] = $channel;
        }
        //按票型
        if(!empty($type)){
        	$where['priceid'] = $type;
        }
        //按操作员
        if(!empty($uid)){
        	$where['user_id'] = $uid;
        }else{
        	//读取渠道商下所有员工
			$where['user_id'] =	array('in',$this->get_channel_user());
		}
       	$uinfo = Partner::getInstance()->getInfo();//dump($uinfo);
        	
		//$pro = explode(',',$uinfo['product']);//返回产品集
       	$where["product_id"] = array('in',$uinfo['product']);
        //汇总
        if($sum_det == '1'){
        	//$views = "sales_sum";
        	//读取渠道符合时间段的订单
        	$list = $db->where($where)->order("product_id,plan_id,channel_id,priceid")->select();
        	$channel_list = explode(',',$this->get_channel());//渠道商集合
        	$channel_list = array_filter($channel_list);  //去除空元素
        	$num = count($list); //总条数
        	foreach ($list as $k1 => $v1) {
        		$pro = M("Product")->where(array("product_id"=>$v1["product_id"]))->find();   //产品
        		$list[$k1]["product_name"] = $pro["name"];
        		$channel = M("Crm")->where(array("id"=>$v1["channel_id"]))->find();           //渠道
        		$list[$k1]["channel_name"] = $channel["name"];
        		$type = M("TicketType")->where(array("id"=>$v1["priceid"]))->find();          //票型
        		$list[$k1]["price_type"] = $type["name"];

        	}
        	foreach ($list as $key => $value){
        		//搜索出同类数据并将结算金额、数量、补贴求和
        		if($key != ($num-1)){
        			$i=$key+1;
					for($i;$i<=$num-1;$i++){
						if($value["product_id"] == $list[$i]["product_id"] && $value["plan_id"] == $list[$i]["plan_id"] && $value["priceid"] == $list[$i]["priceid"] && $value["channel_id"] == $list[$i]["channel_id"]){
							$list[$key]["moneys"] += $list[$i]["moneys"];
							$list[$key]["number"] += $list[$i]["number"];
							$list[$key]["subsidy"] += $list[$i]["subsidy"];
							$list[$key]["moneys"] = number_format($list[$key]["moneys"],2);
							$list[$key]["subsidy"] = number_format($list[$key]["subsidy"],2);
							unset($list[$i]);	
						}
	        		}
        		} 				
        	}
        }
       	//明细 
		if($sum_det == '2'){
			$count = $db->where($where)->count();
			$Page  = new \Home\Service\Page($count,15);
			$show  = $Page->show();
			$list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        }  
		
		$this->assign('data',$list)
			->assign('page',$show)
			->assign('sum_det',$sum_det)
			->assign('where',$where)
			->assign('type',$this->ticket_type())
			->assign('user',$this->get_channel_user_list())
			->display($views);
	}
*/
	function today_sales(){
		if (IS_POST) {
			$_POST['sum_det'] = $_POST['sum_det'] ? $_POST['sum_det'] : '1';
        	$this->redirect('', $_POST);
        }
        $sum_det = I('sum_det');
        $type = I('type');//票型
        $channel = I('channel');
        $uid = I('ur');
		$where = array();
		$today = date('Y-m-d',time());
		$start_time = I('start_time') ? I('start_time') : $today;
		$this->assign('sum_det',$sum_det)
			->assign('start_time',$start_time)->assign('channel_id',$channel);
		//当前用户
		$uinfo = Partner::getInstance()->getInfo();
        if(!empty($channel)){//按渠道
        	$where['channel_id'] = $channel;
        }else{
        	$where['channel_id'] = array('in',$this->get_channel());
        }
        if(!empty($type)){//按票型
        	$where['priceid'] = $type;
        }
        if(!empty($uid)){//按操作员
        	$where['user_id'] = $uid;
        }else{//读取渠道商下所有员工
			$where['user_id'] =	array('in',$this->get_channel_user());
		}
        $where['datetime'] = date("Ymd",strtotime($start_time));
		$db = M('ReportData');
		//汇总
		//$where['product_id'] = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
		//$where['product_id'] = $uinfo['product'];
		//dump($where);
		$list = $db->where($where)->field('datetime,order_sn,games,area,guide_id,createtime,region,pay,type,plantime,games,user_id,status',true)->select();//dump($list);
		if($sum_det == '1'){
			$ticket_fold = Report::channel_plan_fold($list);
			//根据计划汇总
			//$plan_fold = Report::plan_fold($list);
			//根据票型汇总
			//$ticket_fold = Report::channel_ticket_fold($plan_fold,$where['datetime']);
		}else{
			if($this->proconf['agent'] == '1'){
				//开启代理商制度，时执行
				$list = Report::level_fold($list);
			}
			$ticket_fold = Report::channel_plan_fold($list);
		}
		$this->user_channel();
		//获取下级所有下级渠道商
		$this->assign('data',$ticket_fold)
			->assign('where',$where)
			->display();

	}
	//根据当前渠道商级别获取渠道商员工和下级渠道商
	function user_channel(){
		//读取当前当前渠道商所有员工
		$this->assign('user',$this->get_channel_user_list());
		//获取当前用户所属渠道商
		//$this->assign('channel',$this->get_channel());
		$this->assign('channel',$this->get_channel_list());
		//获取所有票型
		$this->assign('type',$this->ticket_type());
	}

	/*按时间段查询
	function sales(){
		if (IS_POST) {
			$_POST['sum_det'] = $_POST['sum_det'] ? $_POST['sum_det'] : '1';
            $this->redirect('', $_POST);
        }
        $sum_det = I('sum_det');
        $type = I('type');//票型
        $db = D('Order');
		$where = array();
		$start_time = I('start_time');
        $end_time = I('end_time');
        //传递查询时间
        $this->assign('start_time',$start_time)
        	->assign('end_time',$end_time);
        //读取产品票型列表
        $pro = Partner::getInstance()->defaultpro;
        $type = F('TicketType'.$pro);
        foreach ($type  as $key => $value) {
        	if($value['type'] <> 2){
        		unset($type[$key]);
        	}
        }
        //读取渠道商下所有员工
        //设置渠道商
        $tlevel = Partner::getInstance()->crm;
		$where = array(
			'channel_id' =>	array('in',channel($tlevel['id'], $tlevel['level'])),	
		);
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }else{
        	//查询时间段为空时默认查询一个月内的数据
	        $start_time = time() - (86400 * 30);
	        $where['createtime'] = array(array('EGT', $start_time), array('ELT', time()), 'AND');
        }
        //汇总
        if($sum_det == '1'){
        	 $where['status'] = '1';//TODO 门票打印完成后计算 
        	 
        	//$views = "sales_sum";
        	//读取渠道符合时间段的订单
        	$list = $db->where($where)->relation(true)->order("product_id,plan_id,channel_id")->select();
        	//dump($list);
      		$a = 0;
        	foreach ($list as $k1 => $v1) {
        		$v1["info"] = unserialize($v1["info"]);
        		array_multisort($v1["info"]["data"],SORT_ASC,"priceid");
        		foreach ($v1["info"]["data"] as $k2 => $v2) {
        			$list1[$a]  = $v1;
        			$list1[$a]["priceid"]    = $v2["priceid"];        			
        			$list1[$a]["moneys"]     = $v2["discount"];
        			$list1[$a]["subsidy"]    = $v2["price"]-$v2["discount"];
        			$list1[$a]["number"]     = 1;

	        		$pro = M("Product")->where(array("product_id"=>$v1["product_id"]))->find();   //产品
	        		$list1[$a]["product_name"] = $pro["name"];
	        		$channel = M("Crm")->where(array("id"=>$v1["channel_id"]))->find();           //渠道
	        		$list1[$a]["channel_name"] = $channel["name"];
	        		$type = M("TicketType")->where(array("id"=>$v2["priceid"]))->find();          //票型
	        		$list1[$a]["price_type"] = $type["name"];
        			
        			$a++;
        		}
        	}
        	$num = count($list1); //总条数
        	foreach ($list1 as $key => $value){
        		//搜索出同类数据并将结算金额、数量、补贴求和

        		if($key != ($num-1)){
        			$i=$key+1;
					for($i;$i<=$num-1;$i++){

						if($value["product_id"] == $list1[$i]["product_id"] && $value["plan_id"] == $list1[$i]["plan_id"] && $value["priceid"] == $list1[$i]["priceid"] && $value["channel_id"] == $list1[$i]["channel_id"]){
							$list1[$key]["moneys"] = $list1[$i]["moneys"];
							$list1[$key]["number"] += $list1[$i]["number"];
							$list1[$key]["subsidy"] += $list1[$i]["subsidy"];
							$list1[$key]["moneys"] = number_format($list1[$key]["moneys"],2);
							$list1[$key]["subsidy"] = number_format($list1[$key]["subsidy"],2);
							unset($list1[$i]);	
						}
	        		}
        		} 				
        	}
        	$list = $list1;	
        }
       	//明细 
		if($sum_det == '2'){
			if ($status != '') {
	            $where['status'] = $status;
	        }
			if($sn != ''){
				$where['order_sn'] = $sn;
			}
			$count = $db->where($where)->relation(true)->count();
			$Page  = new \Home\Service\Page($count,15);
			$show  = $Page->show();
			$list = $db->where($where)->relation(true)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
			$a = 1;

			//dump($list);
        	foreach ($list as $k1 => $v1) {
        		$v1["info"] = unserialize($v1["info"]);
        		array_multisort($v1["info"]["data"],SORT_ASC,"priceid");
        		foreach ($v1["info"]["data"] as $k2 => $v2) {
        			$list1[$a]  = $v1;
        			$list1[$a]["priceid"]    = $v2["priceid"];
        			$list1[$a]["area"]       = $v2["areaId"];     			
        			$list1[$a]["price"]      = $v2["price"];
        			$list1[$a]["discount"]   = $v2["discount"];
        			$list1[$a]["subsidy"]    = $v2["price"]-$v2["discount"];
        			$list1[$a]["number"]     = 1;

	        		$pro = M("Product")->where(array("product_id"=>$v1["product_id"]))->find();   //产品
	        		$list1[$a]["product_name"] = $pro["name"];
	        		$channel = M("Crm")->where(array("id"=>$v1["channel_id"]))->find();           //渠道
	        		$list1[$a]["channel_name"] = $channel["name"];
	        		$type = M("TicketType")->where(array("id"=>$v2["priceid"]))->find();          //票型
	        		$list1[$a]["price_type"] = $type["name"];
        			
        			$a++;
        		}
        	}
        	$list = $list1;	
        	//dump($list);		
			//$views = "sales_det";
        }  
		if(I('execl') == '1'){
			if($sum_det == '2'){//dump($list);
				Exports::templateExecl($list,sales_det,22);
			}
			
		}else{
			//dump($list);
			$this->assign('data',$list)
				->assign('page',$show)
				->assign('sum_det',$sum_det)
				->assign('where',$where)
				->assign('type',$this->ticket_type())
				->assign('user',$this->get_channel_user_list())
				->display($views);
		}		
	}*/
	function sales(){
		if(IS_POST) {
			$_POST['sum_det'] = $_POST['sum_det'] ? $_POST['sum_det'] : '1';
            $this->redirect('', $_POST);
        }
	    $start_time = I('start_time');
        $end_time = I('end_time') ? I('end_time') : date('Y-m-d',time());
	    $sum_det = I('sum_det') ? I('sum_det') : '2';
	    $channel = I('channel');
	    $product_id = I('product');
	    //传递条件
	    $this->assign('sum_det',$sum_det)
	        ->assign('starttime',$start_time)
             ->assign('endtime',$end_time)->assign('channel_id',$channel);
        $export_map['datetime'] = $start_time.'至'.$end_time;
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
        $uInfo = Partner::getInstance()->getInfo();
        $map['channel_id'] = array('in',agent_channel($uInfo['crm']['id'],2));
        $product = M('Product')->field('id,name')->select();
        //设置订单类型为团队或渠道
        $map['type'] = array('in','2,4,7');
        $map['status'] = '1';
        $db = M('ReportData');
		if(!empty($product_id)){ 
			$map['product_id'] = $product_id;
		}
		$list = $db->where($map)->order('plantime ASC,games')->field('datetime,order_sn,games,area,guide_id,createtime,region,pay,type,plantime,games,user_id,status',true)->select();
		if($sum_det == '1'){
			$list = Report::channel_plan_fold($list);
		}else{
			$list = Report::channel_fold($list);
		}
		$export_map['report'] = 'channel';
		$export_map['type']	= $sum_det;
		S('ChannelReport'.get_user_id(),$list);
		$this->user_channel();
		//加载当前产品配置 TODO
		$this->assign('data',$list)->assign('export_map',$export_map)->assign('product_id',$product_id)->assign('product',$product)->display();
	}
	/**
	 * 今日销售明细
	 */
	function sales_detail(){
		$pinfo = I('post.');
		$start_time = strtotime($pinfo['start_time']);
	    $end_time = $start_time  + 86399;
	    $map = array(
	    	'status' => array('in','1,7,9'),//订单状态为支付完成和已出票和申请退票中的报表
	    	'createtime' => array(array('EGT', $start_time), array('ELT', $end_time), 'AND'),
	    	'user_id'	=>  \Item\Service\Partner::getInstance()->id,
	    );
	    //获取订单
		$list = Report::strip_order($map,date('Ymd',strtotime($pinfo['start_time'])),2);
		//按计划合并
		$list = Report::plan_fold($list,2);
		//按票型合并
		$list = Report::strip_plan($list);
	}
	/**
	 * 今日汇总
	 */
	function sales_sum(){
		$db = D('Order');
		$where = array();
        $start_time = I('start_time');
        $end_time = I('end_time');
        //传递查询时间
        $this->assign('start_time',$start_time)
        	->assign('end_time',$end_time);
		//读取渠道商下所有员工
        $tlevel = Partner::getInstance()->crm;
		$where = array(
			'crm_id'	=>	array('in',channel($tlevel['id'], $tlevel['level'])),	
		);
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
        }else{
        	//查询时间段为空时默认查询一个月内的数据
	        $start_time = time() - (86400 * 30);
	        $where['createtime'] = array(array('GT', $start_time), array('LT', time()), 'AND');
        }
		$this->assign('data',$list)
			->assign('page',$show)
			->display();
	}
	/**
	 * 返利报表
	 */
	function rakeback(){
		if (IS_POST) {
            $this->redirect('home.php?g=home&m=report&a=rakeback', $_POST);
        }
		$db = D('TeamOrder');
		$where = array();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $status = I('status');
        $sn = I('sn');
        $uid = I('ur');
        $cid = I('cid');
        //传递查询时间
        $this->assign('start_time',$start_time)
        	->assign('end_time',$end_time);
      	//按操作员
        if(!empty($uid)){
        	$where['user_id'] = $uid;
        }
        //TODO 判断当前用户是导游还是渠道商
        if(!empty($cid)){
        	$where['qd_id'] = $channel_id;
        }else{
        	//读取所有渠道商
			$where['qd_id'] =	array('in',$this->get_channel());
		}
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }else{
        	//查询时间段为空时默认查询一个月内的数据
	        $start_time = time() - (86400 * 30);
	        $where['createtime'] = array(array('EGT', $start_time), array('ELT', time()), 'AND');
        }
        if ($status != '') {
            $where['status'] = $status;
        }
		if($sn != ''){
			$where['order_sn'] = $sn;
		}
		$count = $db->where($where)->count();
		$Page  = new \Home\Service\Page($count,15);
		$show  = $Page->show();
		$list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->user_channel();
		$this->assign('data',$list)
			->assign('page',$show)
			->assign('where',$where)
			->display();
	}
	
	/*
	* 到处Execl
	*/
	function export_execl(){
		$ginfo = I('get.');
		
		
	}
	/*读取渠道商下所有员工*/
	public function f_crm(){
		$tlevel = Partner::getInstance()->crm;
		return channel($tlevel['id'], $tlevel['level']);
	} 
	/*返回票型*/
   public function ticket_type(){
        //读取产品票型列表
        $pro = Partner::getInstance()->defaultpro;
        $type = F('TicketType'.$pro);
        foreach ($type  as $key => $value) {
            if($value['type'] <> 2){
                unset($type[$key]);
            }
        }
        return $type;
    }
    
	/**
	 * 产品列表
	 */
	function product(){
		$this->display();
	}
}