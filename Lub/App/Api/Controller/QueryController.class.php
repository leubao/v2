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
use Payment\ChargeContext;
use Payment\Config;
use Payment\Common\PayException;

use Workerman\Worker;
class QueryController extends LubTMP {
	function index()
	{
		/*
		if(!IS_CLI){
            die("无法直接访问，请通过命令行启动");
        }*/
		require_once APP_PATH . 'Workerman/Autoloader.php';
		// 每个进程最多执行1000个请求
		define('MAX_REQUEST', 1000);
		//以守护进程运行
		Worker::$daemonize = true;
		Worker::$pidFile = '/home/wwwlogs/LUBWorker/workerman.pid';
		//方便监控WorkerMan进程状态
		Worker::$stdoutFile = '/home/wwwlogs/LUBWorker/stdout.log';
		//输出日志, 如echo，var_dump等
		Worker::$logFile = '/home/wwwlogs/LUBWorker/workerman.log';
		//workerman自身相关的日志，包括启动、停止等,不包含任何业务日志
		$ws_worker = new Worker("websocket://0.0.0.0:7272");

		// 4 processes
		$ws_worker->name = 'LUBWorker';
		$ws_worker->count = 2;
		//dump($ws_worker);
		// Emitted when new connection come
		$ws_worker->onConnect = function($connection)
		{
		  echo "New connection\n";
		};
		//接收客户端发来的信息
		$ws_worker->onMessage = function($connection, $data)
		{
			/*平滑重启*/
			static $request_count = 0;// 已经处理请求数
			if(++$request_count >= MAX_REQUEST){
				//如果请求数达到1000,退出当前进程，主进程会立刻重新启动一个全新进程补充上来，从而完成进程重启
				Worker::stopAll();
			}
		  	//Send hello $data
		  	$info = json_decode($data,true);
		  	$qr = load_redis('get','qr_sn_'.$info['sn']);
		  	$qr = unserialize($qr);
		  	//收到结果
			$connection->send("等待扫码支付...");
		  	//发起查询
		  	//返回结果
		  	set_time_limit(0);
	        $i = 0;
	        $out_time = '50';
	        while(true) {
	        	if($i < 3){
	        		usleep(12000000);
	        	}elseif($i >3 && $i < 6){
	        		usleep(10000000);
	        	}else{
					usleep(5000000);
	        	}
	            //查询微信服务器
	            if($qr['paytype'] == 'wxpay'){
	            	$return  = \Api\Service\Apipay::orderquery('wx_charge',$qr['product_id'],['out_trade_no'=>$info['sn'],'sub_appid'=>'wxd40b47548614c936','sub_mch_id'=>'1441589102']);
	            }
	            if($qr['paytype'] == 'alipay'){
	            	$return  = \Api\Service\Apipay::orderquery('ali_charge',$qr['product_id'],['out_trade_no'=>$info['sn'],]);
	            }
	            if($return['state'] == 'SUCCESS'){
	            	//\Api\Service\Apipay::up_order($info['sn']);
            		$connection->send(json_encode('支付成功,正在为您加载...',JSON_UNESCAPED_UNICODE));
            		//关闭连接
            		$connection->close(json_encode('支付成功,正在为您加载...',JSON_UNESCAPED_UNICODE));
            		usleep(500000);
            		break;
	            }
	            if($return['state'] == 'ERROR'){
	            	$connection->send(json_encode($return['msg'],JSON_UNESCAPED_UNICODE));
	            	//关闭连接
            		$connection->close('支付遇到错误,无法继续...');
	            	break;
	            }
	            if($return['state'] == 'NOTPAY'){
	            	$connection->send(json_encode($return['msg'],JSON_UNESCAPED_UNICODE));
	            }
	            $i++;
	            $connection->send(json_encode($return['msg'],JSON_UNESCAPED_UNICODE));
	            load_redis('lpush','orderquery',$i.'[='.date('Y-m-d H:i:s').'=]'.json_encode($return));
	            //超过次数  关闭订单 TODO     
	            if($i >= $out_time){
	            	//关闭连接
            		$connection->close();
	                break;     
	            }
	        }
		};
		/* Emitted when connection closed*/
		$ws_worker->onClose = function($connection)
		{
		  echo "Connection closed\n";
		};
		Worker::runAll();
	}
}