<?php
// +----------------------------------------------------------------------
// | LubTMP 信任接口服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Common\Controller\LubTMP;
use Libs\Service\Api;
use Libs\Service\Order;
use Libs\Service\Operate;
class TrustController extends LubTMP {
	//信任地址验证
	function _initialize(){
		/*获取请求的url 
		$get_url = get_url();
		//不成功返回错误
		
		//获取已经配置的url
		if($get_url){
			$return = array(
				'status'=>	'0',
				'msg'	=>	'不合法请求',
			);
			$this->ajaxReturn($return);
		}*/
	}
	function index(){}
	//信任计划获取
	function get_plan(){
		$ginfo = I('get.');
		
		switch ($ginfo['type']) {
			case '1':
				//获取当天的场次
				$map = array(
					'status' => '2',
					'plantime'=>strtotime(date('Ymd')),
					);
				break;
			case '2':
				//获取指定日期的场次
				$map = array(
					'status' => '2',
					'plantime'=>strtotime($ginfo['datetime']),
				);
				break;
			case '3':
				//获取所有可售场次
				$map = array(
					'status' => '2',
				);
				break;
		}
		if(empty($ginfo['price'])){
			$plan = M('Plan')->where($map)->field('id,plantime,starttime,endtime,product_id,games')->select();
		}else{
			//获取带销售价格的场次
			//构造查询条件
			$proconf = cache('ProConfig');
			$proconf = $proconf[$ginfo['product']]['1'];
			$info = array(
				'scene' => $ginfo['scene'], 
				'product'=> $ginfo['product'],
				'group'=>array('price_group'=>$proconf['web_price']));
			$plans = \Libs\Service\Api::plans($info);

			foreach ($plans['plan'] as $key => $value) {
	            $plan['plan'][] = array(
	                'title' =>  $value['title'],
	                'id'    =>  $value['id'],
	                'num'   =>  $value['num'],
	                'pid'	=>	$value['product_id'],
	            );
	            $plan['area'][$value['id']] = $value['param'];
	        }
		}
		$return = array(
			'status' => '200',
			'info'	 => $plan,
			'msg'	 => 'ok',
			);
		$this->ajaxReturn($return);
	}

	//信任获取产品
	function get_product(){
		$ginfo = I('get.');
		//获取当天的场次
		$map = array(
			'status' => '1',
			'type'=>'1',
		);
		$product = M('Product')->where($map)->select();
		$return = array(
			'status' => '200',
			'info'	 => $product,
			'msg'	 => 'ok',
		);
		$this->ajaxReturn($return);
	}
	/*下单*/
	function insert_order(){
		if(IS_POST){
			//官网
			$scena = '31';
			$scena = Order::is_scena($scena);
			//构造用户
			$uInfo = array(
                'id' => 3,
                'guide'  => '0',
                'qditem' => '0',
                'scene'  => '31'
            );
            $sn = Order::quick_order(I('post.'),$scena,$uInfo,2);
            $return = array(
				'status' => '200',
				'info'	 => $sn,
				'msg'	 => 'ok',
			);
			$this->ajaxReturn($return);
		}
	}
	/*支付完成排座*/
	function pay_suess_seat(){
		$ginfo = I('get.');
		$oinfo = D('Item/Order')->where(array('order_sn'=>$ginfo['sn']))->relation(true)->find();
        if(empty($oinfo)){return false;}
        if($oinfo['status'] == '2'){
            //构造配置数组
            $info = array(
                'seat_type' =>  $ginfo['seat_type'],//立即排座
                'pay_type'  =>  $ginfo['pay_type'],//支付宝支付
            );
            $status = Order::mobile_seat($info,$oinfo); 
        }
        if($status){$info = '1';}else{$info = '0';}
        $return = array(
			'status' => '200',
			'info'	 => $info,
			'msg'	 => 'ok',
		);
		$this->ajaxReturn($return);
	}
	//校验传递过来的数据
    function format_seat($pinfo,$appInfo){
        $plan = F('Plan_'.$pinfo['plan']);
        //重组座位
        $seat = Order::area_group($pinfo['oinfo'],$plan['product_id'],$appInfo['group']['settlement']);
        
        $ticketType = F("TicketType".$plan['product_id']);
        foreach ($seat['area'] as $k => $v) {
          foreach ($v['seat'] as $ke => $va) {
            $price = $ticketType[$va['priceid']];
            $money += $va['num']*$price['price'];
            $moneys += $va['num']*$price['discount'];
          }
        }
        if(bccomp((float)$pinfo['money'],(float)$moneys,2) == 0){
          return $seat;
        }else{
          return false;
        }
    }
	/*自助机*/
	/*处理对接数据*/
	function dealpre(){
		\Libs\Service\Rebate::ajax_rebate_order();
		return true;
	}
	/*校验程序*/
	function check()
	{
		if(IS_POST){
			//远端手动执行
			$pinfo = I('post.');
			if (!empty($pinfo['start_time']) && !empty($pinfo['end_time'])) {
	            $start_time = strtotime($pinfo['start_time']);
	            $end_time = strtotime($pinfo['end_time']) + 86399;
	            $where['logintime'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
	        }
	        $where['type'] = array('in','2,4,8,9');$where['status']=array('in','1,9,7,8');
			\Libs\Service\Check::check_rebate($where,2);
			return '200';
		}else{
			//校验返利
			\Libs\Service\Check::check_rebate();
			//校验微信支付排座情况
			\Libs\Service\Check::check_pay_order_seat();
			return true;	
		}
		
	}
	/**
* 用户信息查询
*/
public function index(){
	if(!IS_CLI){
		die("access illegal");
	}
	require_once APP_PATH.'Workerman/Autoloader.php';

	// 每个进程最多执行1000个请求
	define('MAX_REQUEST', 1000);

	Worker::$daemonize = true;//以守护进程运行
	Worker::$pidFile = '/data/wwwlogs/CMSWorker/workerman.pid';//方便监控WorkerMan进程状态
	Worker::$stdoutFile = '/data/wwwlogs/CMSWorker/stdout.log';//输出日志, 如echo，var_dump等
	Worker::$logFile = '/data/wwwlogs/CMSWorker/workerman.log';//workerman自身相关的日志，包括启动、停止等,不包含任何业务日志

	$worker = new Worker('text://172.16.0.10:10024');
	$worker->name = 'CMSWorker';
	$worker->count = 2;
	//$worker->transport = 'udp';// 使用udp协议，默认TCP

	$worker->onWorkerStart = function($worker){
		echo "Worker starting...\n";
	};
	$worker->onMessage = function($connection, $data){
		static $request_count = 0;// 已经处理请求数
		var_dump($data);
		$connection->send("hello");
		/*
		* 退出当前进程，主进程会立刻重新启动一个全新进程补充上来，从而完成进程重启
		*/
		if(++$request_count >= MAX_REQUEST){// 如果请求数达到1000
			Worker::stopAll();
		}
	};

	$worker->onBufferFull = function($connection){
		echo "bufferFull and do not send again\n";
	};
	$worker->onBufferDrain = function($connection){
		echo "buffer drain and continue send\n";
	};

	$worker->onWorkerStop = function($worker){
		echo "Worker stopping...\n";
	};

	$worker->onerror = function($connection, $code, $msg){
		echo "error $code $msg\n";
	};

	// 运行worker
	Worker::runAll();
}


}