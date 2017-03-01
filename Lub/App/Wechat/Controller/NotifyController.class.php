<?php
// +----------------------------------------------------------------------
// | LubTMP 微信前台
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2015-8-25 
// +----------------------------------------------------------------------
namespace Wechat\Controller;
use Common\Controller\LubTMP;
class NotifyController extends LubTMP {
    public function notify(){
        //处理业务逻辑
        $pay = & load_wechat('Pay');
		// 获取支付通知
		$notifyInfo = $pay->getNotify();
        load_redis('set','PayssOrder',$pay->errMsg);
        load_redis('set','PayOrder',$notifyInfo['result_code'].'ip'.$notifyInfo['return_code']);
		// 支付通知数据获取失败
		if($notifyInfo===FALSE){
		    // 接口失败的处理
		    echo $pay->errMsg;
		}else{
		    //支付通知数据获取成功
		     if ($notifyInfo['result_code'] == 'SUCCESS' && $notifyInfo['return_code'] == 'SUCCESS') {
		        // 支付状态完全成功，可以更新订单的支付状态了
                load_redis('lpush','WechatPayOrder',$notifyInfo['out_trade_no']);
                // $sn = \Libs\Service\Order::sweep_pay_seat();
                // 2、更新网银支付日志
                $uppaylog = array('status'=>1,'out_trade_no'=>$notifyInfo['transaction_id']);
                $paylog = D('Manage/Pay')->where(array('order_sn'=>$notifyInfo['out_trade_no'],'type'=>2))->save($uppaylog);
                $oinfo = D('Item/Order')->where(array('order_sn'=>$notifyInfo["out_trade_no"]))->relation(true)->find();
                $info = array(
                 'seat_type' => '1',
                 'pay_type' =>   '5'
                );
                $status = \Libs\Service\Order::mobile_seat($info,$oinfo);
                if($status){
                    return xml(['return_code' => 'SUCCESS', 'return_msg' => 'DEAL WITH SUCCESS']);
                }else{

                }
		        // @todo 
		        // 返回XML状态，至于XML数据可以自己生成，成功状态是必需要返回的。
		        // <xml>
		        //    return_code><![CDATA[SUCCESS]]></return_code>
		        //    return_msg><![CDATA[OK]]></return_msg>
		        // </xml>
		     }
		}
    }
}