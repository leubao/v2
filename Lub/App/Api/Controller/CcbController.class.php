<?php
// +----------------------------------------------------------------------
// | LubTMP 建设银行支付回调
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

namespace Api\Controller;

use Common\Controller\LubTMP;
use Payment\Client\Helper;
use Payment\Client\Query;
use Common\Model\Model;
use Payment\Utils\ArrayUtil;
use Payment\Utils\RsaMd5;
use Payment\Utils\RsaEncrypt;
class CcbController extends LubTMP {

	//同步返回
	public function notify()
	{
		$ginfo = I('get.');
		$wxConfig = load_payment('ccb_charge',10);
		//校验签名 TODO 
		try {
			if($ginfo['SUCCESS'] === 'Y'){
				//发起查询
				$signData = ArrayUtil::createLinkstring($ginfo);
				$sign = $this->get_data_from_server("127.0.0.1",55533,$signData."\n");
				if($sign[0] === 'Y'){
					$log = json_decode(load_redis('get','pay_'.$ginfo['ORDERID']),true);
		            $log['balance'] = balance($id,'1');
		            $log['createtime'] = time();
		            if(D('CrmRecharge')->where(['crm_id'=>$log['crm_id'],'order_sn'=>$ginfo['ORDERID']])->count('id')){
		            	$this->success("更新成功!",U('Home/index/index'));
		            }else{
		            	$model = new Model();
						$model->startTrans();
						$crmData = array('cash' => array('exp','cash+'.$log['cash']),'uptime' => time());
						$c_pay = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$log['crm_id']))->setField($crmData);
						$c_pay2 = $model->table(C('DB_PREFIX').'crm_recharge')->add($log);
						if($c_pay == false || $c_pay2 == false){
							$model->rollback();//事务回滚
							$this->error("充值同步失败，请联系管理员!");
						}else{
							$model->commit();
							$this->success("更新成功!",U('Home/index/index'));
						}
		            }
				}else{
					$this->error("充值同步失败，请联系管理员!");
				}
	            
			}
        } catch (PayException $e) {
            echo $e->errorMessage();
            exit;
        }
	}
	//异步返回
	public function notify_async()
	{
		$pinfo = I('post.');
	}
	//发起查询
	//验证签名
	function get_data_from_server($address, $service_port, $send_data) {
	    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	    if ($socket < 0) {
	        echo "socket创建失败原因: " . socket_strerror($socket) . "\n";
	    } else {
	        //echo "socket_create OK，HE HE.\n";
	    }   
	    $result = socket_connect($socket, $address, $service_port);
	    if ($result < 0) {
	        echo "SOCKET连接失败原因: ($result) " . socket_strerror($result) . "\n";
	    } else {
	        //echo "OK.\n";
	    }   
	    //发送命令
	    $in = $send_data;
	    $out = '';
	    //echo "Send ..........";
	    socket_write($socket, $in, strlen($in));
	    //echo "OK.\n";
	    /*echo "Reading Backinformatin:\n\n";
	    while ($out = socket_read($socket, 2048)) {
	        return $out;
	    }*/
	    $out = socket_read($socket, 2048);
	    
	    //echo "Close socket........";
	    socket_close($socket);
	    //echo "OK,He He.\n\n";
	   	$out = explode('|',$out);
	    return $out;
	}
}