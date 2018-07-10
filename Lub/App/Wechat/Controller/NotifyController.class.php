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
        //load_redis('set','PayssOrder',$pay->errMsg);
        load_redis('lpush','PayOrder',json_encode($notifyInfo));
		// 支付通知数据获取失败
		if($notifyInfo===FALSE){
		    // 接口失败的处理
		    echo $pay->errMsg;
		}else{
		    //支付通知数据获取成功
		     if ($notifyInfo['result_code'] == 'SUCCESS' && $notifyInfo['return_code'] == 'SUCCESS') {
		        // 支付状态完全成功，可以更新订单的支付状态了
                load_redis('lpush','WechatPayOrder',$notifyInfo['out_trade_no']);
                // 2、更新网银支付日志
                $uppaylog = array('status'=>1,'out_trade_no'=>$notifyInfo['transaction_id']);
                $paylog = D('Manage/Pay')->where(array('order_sn'=>$notifyInfo['out_trade_no'],'type'=>2))->save($uppaylog);
                $orderMap = [
                    'order_sn'=> $notifyInfo["out_trade_no"],
                    'status'  => ['in',['11','2']],
                ];
                $oinfo = D('Item/Order')->where($orderMap)->relation(true)->find();
                if(!empty($oinfo)){
                    $info = array(
                        'seat_type' => '1',
                        'pay_type'  => '5'
                    );
                    $order = new \Libs\Service\Order;
                    $status = $order->mobile_seat($info,$oinfo);
                    load_redis('lpush','GuiPay',$status.'='.$order->error);
                }else{
                    $status = true;
                }
                if($status){
                    $this->to_tplmsg($oinfo,$notifyInfo['sub_openid']);
                    //echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
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
    public function test()
    {
        $ginfo = I('get.');
        $oinfo = D('Item/Order')->where(array('order_sn'=>$ginfo['sn']))->relation(true)->find();
        $info = array(
         'seat_type' => '1',
         'pay_type' =>   '5'
        );
        dump($oinfo);
        $order = new \Libs\Service\Order;
        $status = $order->mobile_seat($info,$oinfo);
        dump($order->error);
        $openid = "oBQ9fwZuxl7f71d_MKBgcFQd4bWY";
        $msg = $this->to_tplmsg($oinfo,$openid);
        dump($status);
    }
    function to_tplmsg($info,$openid){
        $template = array(
            'touser'=>$openid,//指定用户openid
            'template_id'=>'reFcUgdOun9-HiMid4lMfBLIU2lNcQWg3bBYrLc22uc',
            'url'   =>  U('Wechat/Index/scenic_ticket',array('sn' => $info['order_sn'])),
            'topcolor'=>"#7B68EE",
            'data'=>array(
                'first'=>array('value'=>'您好，您的门票订单已预订成功。'."\n"),
                'keyword1' =>array('value'=>$info['order_sn'],'color'=>'#5cb85c'),
                'keyword2' =>array('value'=>product_name($info['product_id'],1),'color'=>'#5cb85c'),
                'keyword3' =>array('value'=>$info['order_sn'],'color'=>'#5cb85c'),
                'keyword4' =>array('value'=>$info['number'].'人','color'=>'#5cb85c'),
                'keyword5'=>array('value'=>planShow($info['plan_id'],5,1)."\n",'color'=>'#5cb85c'),
                'remark'=>array('value'=>'欢迎来到《梦里老家》太子惊魂惊悚体验馆,点击立即使用'), 
            )
        );
        $sndMsg = & load_wechat('Receive',$info['product_id'],1);
        $res = $sndMsg->sendTemplateMessage($template);
        load_redis('set','msg',json_encode($res).$openid.json_encode($info));
        /**
         * array(3) {
  ["errcode"] => int(0)
  ["errmsg"] => string(2) "ok"
  ["msgid"] => int(348281068903874560)
}
         */
        if(!$res){
            $msgtpl = [
                'info'  => $info,
                'openid'=> $openid,
                'tplmsg'=> $template,
                'number'=> 1
            ];
            load_redis('lpush','preMsgTpl',json_encode($msgtpl));
            //半个小时内发送五次 TODO
        }
        return $res;
        
        //TODO  回传模板消息发送状态
    }
    public function fillToMsg()
    {
        $ln = load_redis('lsize','preMsgTpl');
        load_redis('set','msgtpltime',date('Y-m-d H:i:s'));
        $msgtplJson = (int)$ln > 0 ? load_redis('rPop','preMsgTpl') : 0;
        if((int)$ln > 0){
            $msgtpl = json_decode($msgtplJson,true);
            if($msgtpl['number'] > 10){
                return true;
            }
            $product_id = $msgtpl['info']['product_id'];
            $sndMsg = & load_wechat('Receive',$product_id,1);
            $res = $sndMsg->sendTemplateMessage($msgtpl['tplmsg']);
            if(!$res){
                $$msgtpl['number'] = $msgtpl['number']+1;
                
                load_redis('lpush','preMsgTpl',json_encode($msgtpl));
                //半个小时内发送五次 TODO
            }
        }else{
            return true;
        }
    }
}