<?php
// +----------------------------------------------------------------------
// | LubTMP  系统校验类
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Common\Model\Model;
class Check{
	/**
	 * 补贴报表
	 * @param  自定义条件 $map  array  日期范围必须
	 * @param  访问类型 $type 1常规轮询2自定义轮询
	 * @return [type]       [description]
	 */
	function check_rebate($map = '',$type = '1'){
		if($type == '1'){
			$list = M('Order')->where(array('type'=>array('in','2,4,8,9'),'status'=>array('in','1,9,7,8')))->limit('1,50')->field('order_sn')->order('id DESC')->select();
		}else{
			$list = M('Order')->where($map)->field('order_sn')->order('id DESC')->select();
		}
		if(!empty($list)){
			$count = 0;
			//判断是否在队列中
			$queue = load_redis('lrange','PreOrder',0,-1);
			//匹配返佣订单
			foreach ($list as $k => $v) {
				if(!in_array($v['order_sn'], $queue)){
					$status = M('TeamOrder')->where(array('order_sn' => $v['order_sn']))->find();
					if(!$status){
					  $count++;
					  load_redis('lpush','PreOrder',$v['order_sn']);
					}
					/*未返利的程序
					if($status['status'] == '1'){
						load_redis('lpush','PreOrder_bak',$v['order_sn']);
					}*/
				}
			}
		}
		load_redis('set','check_rebate',date('Y/m/d H:i:s')."||".$count);
	}
	//退票订单 返款情况、补贴情况
	function check_refund(){

	}
	//过期补贴未补的补贴报表
	//网银支付  排座情况
	function check_pay_order_seat(){
		//读取订单列表，查询本地排座情况，若排座则不存入队列，若没有排座，查询微信支付支付情况，支付完成的调用排座程序
		$order_list = load_redis('lrange','WechatPayOrder',0,-1);
		$ln = load_redis('lsize','WechatPayOrder');
		if($ln > 0){
			$model = D('Item/Order');
			for ($i=0; $i < $ln; $i++) { 
				$sn = load_redis('rPop','WechatPayOrder');
				$info = $model->where(array('order_sn'=>$sn))->relation(true)->find();
				if(!in_array($info['status'],array('1,9'))){
					//查询微信支付情况
					$pay = & load_wechat('Pay',$info['product_id']);
					$query = $pay->queryOrder($info['order_sn']);
					if ($query['result_code'] == 'SUCCESS' && $query['return_code'] == 'SUCCESS' && $query['trade_state'] == 'SUCCESS') {
		                $param = array(
		                  'seat_type' => '1',
		                  'pay_type' =>   '5'
		                );
		                $status = \Libs\Service\Order::mobile_seat($param,$info);
		                if(!$status){
		                	//排座失败
		                	load_redis('lpush','WechatPayOrder',$sn);
		                }else{
		                	//排座成功
		                }
					}else{
						error_insert($pay->errMsg.[$pay->errCode]);
					}
				}
			}
		}
	}
	/**
	 * 主要是红包返利
     * 轮询红包记录,从微信服务器获取状态
     */
    function polling_red(){
        //获取红包队列数据
        $ln = load_redis('lsize','rebate_pay_red');
        if($ln > 0){
            //获取订单号
            $sn = load_redis('rPop','rebate_pay_red');
            //查询红包状态
            
            //获取微信配置,组织查询数据,分析返回状态
        }
        //循环查询红包状态
        //判断状态 领取成功的标记成功，待领取的存入红包队列，稍后查询，单个订单号查询间隔5S钟
    }
    /**
     * 轮询微信服务器支付状态,对于超时的订单进行关闭
     */
    function check_pay_state(){

    }
    /**
     * 检查已过期的场次，是否有过正常销售，未有正常销售的场次将直接删除(将状态标记为-1)
     */
    function check_plan(){
    	//查询当日所有场次
    	$time = date('H');
    	if($time > '22'){
    		$plan = M('Plan')->where(['plantime'=>strtotime(date('Y-m-d')),'status'=>4])->field('id')->select();
	    	$model = D('Order');
	    	foreach ($plan as $k => $v) {
	    		(int)$count = $model->where(['status'=>['in','1,9'],'plan_id'=>$v['id']])->count();
	    		if($count === (int)0){
	    			//执行删除操作
	    			M('Plan')->where(['id'=>$v['id']])->setField('status','-1');
	    		}
	    	}
    	}
    	return true;
    }
    /**
     * 标记电子票 身份证入园的门票标记为完结
     */
    function check_ticket_order_tag()
    {
    	//读取所有产品
    	$product = D('Product')->where(['status'=>1])->field('id')->select();
    	$model = D('Order');
    	foreach ($product as $k => $v) {
    		//读取Redis中已检票的订单号
    		$size = load_redis('lsize','check_order_'.$v['id']);
    		if($size > 0){
    			$list = load_redis('lrange','check_order_'.$v['id'],0,-1);
    			//删除
 				load_redis('delete','check_order_'.$v['id']);
 				$lists = array_unique($list);
 				foreach ($lists as $ka => $va) {dump($va);
 					//获取查询表名称
 					$plan = json_decode(load_redis('get','check_plan_'.$v['id']),true);
 					if(!empty($plan)){
 						//按订单去查询座位是否都已检票 ,查询是否存在未检票的座位
	 					$count = M(ucwords($plan['seat_table']))->where(['order_sn'=>$va,'status'=>2])->count();
	    				//都已检票则标记订单状态为完结
	    				if($count == 0){
	    					D('Order')->where(['order_sn'=>$va])->save(['status'=>9,'uptime'=>time()]);
	    				}
 					}else{
 						load_redis('set','check','未找到检票场次');
 					}
 				}
    		}
    	}
    }
}
	