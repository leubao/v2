<?php
namespace Wechat\Controller;

use Wechat\Service\Wxpay\WxPayApi;
use Wechat\Service\Wxpay\WxPayConfig;
use Wechat\Service\Wxpay\WxPayException;
use Wechat\Service\Wxpay\WxPayNotify;
use Wechat\Service\Wxpay\WxPayOrderQuery;


class PayNotifyCallBackController extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery($this->wxpaycfg);
		$input->SetTransaction_id($transaction_id);
		$result = $this->wxpayapi->orderQuery($input);

		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理方法，成功的时候返回true，失败返回false，处理商城订单
	public function NotifyProcess($data, &$msg)
	{

		$notfiyOutput = array();
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			error_insert("40114");
			$msg = "订单查询失败";
			return false;
		}

		//查询订单
		$oinfo = D('Item/Order')->where(array('order_sn'=>$data["out_trade_no"]))->relation(true)->find();
		if($oinfo){
			//记录微信支付
	        $pay_log = array(
	        	'out_trade_no' =>	$data['transaction_id'], //微信支付单号
	        	'money'		   =>	$oinfo['money'],
	        	'order_sn'	   =>	$data["out_trade_no"],
	        	'param'		   =>	serialize($data),
	        	'status'	   =>	'1',
	        	'type'		   =>	'2',
	        	'pattern'	   =>   '1',
	        	'create_time'  =>	time(), 
	        	'update_time'  =>	time(),
	        	);
	        M('Pay')->add($pay_log);
	        $info = array(
	        	'seat_type' => '1',
	        	'pay_type' =>	'5'
	        );
	        $status = \Libs\Service\Order::mobile_seat($info,$oinfo);
	        if($status != false){
	        	return true;
	        }else{
	        	error_insert('400022');
	        	//执行退款程序
	        	$msg = "排座失败";
				return false;
	        }
		}else{
			error_insert('400023');
			//执行退款程序
			$msg = "订单查询失败";
			return false;
		}
	}
}