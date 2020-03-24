<?php
// +----------------------------------------------------------------------
// | LubTMP API 收款接口，支持扫码 刷卡支付
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Service; 
use Common\Model\Model;
use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Client\Query;
use Libs\Service\Order;
class Apipay{
    /**
     * 请求扫码支付的接口
     * @param  int $product_id 产品id
     * @param  string  $sn   订单号
     */
    public static function get_pay_qr($channel,$product_id,$payData){
      	$config = load_payment($channel,$product_id);
        try {
            $payData = array_merge($payData, ['sub_appid' => $config['sub_appid'],'sub_mch_id' => $config['sub_mch_id']]);
		    $ret = Charge::run($channel, $config, $payData);
		} catch (PayException $e) {
		    return $e->errorMessage();
		    exit;
		}
        return $ret;
    }
    
    /**
     * 后台查询
     *  SUCCESS—支付成功
        REFUND—转入退款
        NOTPAY—未支付
        CLOSED—已关闭
        REVOKED—已撤销（刷卡支付）
        USERPAYING--用户支付中
        PAYERROR--支付失败(其他原因，如银行返回失败)
     * @return [type] [description]
     */
    public static function orderquery($channel, $product_id, $payData, $type = '1'){
        $config = load_payment($channel,$product_id);
        try {
            $payData = array_merge($payData, ['sub_appid' => $config['sub_appid'],'sub_mch_id' => $config['sub_mch_id']]);
            $ret = Query::run($channel, $config, $payData);
            load_redis('setex','qr_paoy_'.$payData['out_trade_no'],json_encode($ret),'3600');

            if($ret['is_success'] == 'T'){
                if(in_array(strtoupper($ret['response']['trade_state']), ['REFUND','CLOSED','REVOKED','PAYERROR'])){
                    return ['state'=>'NOTPAY','msg'=>'支付超时,请前往售票窗口完成支付'];
                }
                if(in_array(strtoupper($ret['response']['trade_state']), ['NOTPAY','USERPAYING'])){
                    return ['state'=>'NOTPAY','msg'=>'等待扫码支付...'];
                }
                //当返回全部成功时,更新订单状态
                if(strtoupper($ret['response']['trade_state']) == 'SUCCESS'){
                    if($type == '2'){
                        Apipay::up_order($payData['out_trade_no']);
                    }
                    return ['state'=>'SUCCESS','msg'=>'支付成功'];
                }
            }
            if($ret['is_success'] == 'F'){
                return ['state'=>'ERROR','msg'=>$ret['error']];
            }
        } catch (PayException $e) {
          return $e->errorMessage();
          exit;
        }
    }
    //更新订单状态
    public static function up_order($sn){
        load_redis('setex','qr_pays222_'.$sn,'22','3600');
        //政企订单更新支付方式
        $oinfo = D('Item/Order')->where(['order_sn'=>$sn])->relation(true)->find();
        load_redis('setex','qr_pays2_'.$sn,serialize($oinfo).'oooo','3600');
        if($oinfo['type'] == '6' || $oinfo['status'] == '1'){
            //政企订单只更新支付方式
            D('Item/Order')->where(['order_sn'=>$sn])->setField('pay',5);
            $record = ['state'=>'SUCCESS','phone'=>$oinfo['phone']];
        }else{
           $param = array(
                'seat_type' => '1',//排座
                'pay_type'  => '5',//先固定为微信支付 TODO
                'order_type'=> '2',//模拟窗口快捷售票
            );
            $order = new \Libs\Service\Order();
            $run = $order->sweep_pay_seat($param,$oinfo); 
            if($run != false){
                //排座成功
                $record = ['state'=>'SUCCESS','msg'=>$oinfo['phone']];
                load_redis('setex','qr_pay_'.$payData['out_trade_no'],serialize($record),'3600');
            }else{
                //排座失败执行退款程序 TODO
            }
        }
        return $record;
    }
}
