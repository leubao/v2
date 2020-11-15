<?php
namespace Api\Controller;
use Common\Controller\LubTMP;
/**
 * 客户端需要继承该接口，并实现这个方法，在其中实现对应的业务逻辑
 * Class TestNotify
 * anthor helei
 */
class NotifyController extends LubTMP {

    //分账回调
    public function settle_notify()
    {
        $pinfo = I('post.');
        if(!$this->checkSign($pinfo)){
            echo 'FAILED';
            return false;
        }
        load_redis('lpush', 'settle_notify', json_encode($pinfo));
    }
    //支付回调
    public function pay_notify()
    {
        $pinfo = I('post.');
        if(!$this->checkSign($pinfo)){
            echo 'FAILED';
            return false;
        }
        // load_redis('set', 'anotify', json_encode($pinfo));
        load_redis('lpush', 'pay_notify', json_encode($pinfo));
        //判断结果
        if($pinfo['status'] === 'SUCCESS'){
            $data = [
                'mch_id'        =>  $pinfo['mch_id'],
                'out_trade_no'  =>  $pinfo['out_trade_no'],
                'timestamp'     =>  time(),
                'nonce_str'     =>  genRandomString(8,1),//随机字符串
            ];
            $data['sign'] = \Libs\Service\ArrayUtil::setSign($data);
            $url = 'https://api.pay.xzusoft.cn/pay/order_query';
            $res = json_decode(getHttpContent($url, 'POST', $data), true);
            load_redis('lpush', 'pay_notify', json_encode($res));
            if($res['trade_state'] === 'SUCCESS'){
                $uppaylog = array('status'=>1,'out_trade_no'=>$notifyInfo['transaction_id']);
                $paylog = D('Manage/Pay')->where(array('order_sn'=>$notifyInfo['out_trade_no'],'type'=>2))->save($uppaylog);
                $orderMap = [
                    'order_sn'=> $res['out_trade_no'],
                    'status'  => ['in',['11','2']],
                ];
                $oinfo = D('Item/Order')->where($orderMap)->relation(true)->find();
                load_redis('lpush','GuiPay1',json_encode($orderMap).json_encode($oinfo));
                if(!empty($oinfo)){
                    $info = array(
                        'seat_type' => '1',
                        'pay_type'  => '5'
                    );
                    $order = new \Libs\Service\Order;
                    $status = $order->mobile_seat($info, $oinfo);
                    load_redis('lpush','GuiPay',$status.'='.json_encode($order->error));
                }else{
                    $status = true;
                }
            }
        }
        //查询交易
        //改变状态
        echo "SUCCESS";
    }
    public function checkSign($pinfo)
    {
        //校验时间差
        //校验时间差
        $sign = \Libs\Service\ArrayUtil::setSign($pinfo);
        if($pinfo['sign'] === $sign){
            return true;
        }else{
            return false;
        }
    }
}