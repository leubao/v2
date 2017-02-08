<?php
// +----------------------------------------------------------------------
// | LubTMP  网银支付
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use Home\Service\Partner;
use Libs\Service\Operate;
use Libs\Org\Pay;
class PayController extends Base{
    //订单支付
    public function index() {
        if (IS_POST) {
            //页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
            $paytype  = 'alipay';
            //$paybank  = I('post.bank');
            $order_id = I("post.order_id");
            $money    = I("post.money");
            $check    = M("Order")->where(array("order_sn"=>$order_id,"money"=>$money))->count(); //验证订单号以及金额正确
            if($check == 1){
                $pay = new \Think\Pay($paytype, C('payment.' . $paytype));
                $order_no = $pay->createOrderNo();
                $vo = new \Think\Pay\PayVo();
                $vo->setBody("敦煌盛典演出票务")
                    ->setFee($money) //支付金额
                    ->setOrderNo($order_no)
                    ->setTitle("敦煌盛典演出票务")
                    ->setCallback("Home/Order/web_pay")
                    ->setUrl(U("Home/Order/orderinfo"))
                    ->setParam(array('sn' => $order_id,'pay_type'=>'4','seat_type'=>'1'));
                echo $pay->buildRequestForm($vo);
            }else{
                $this->error("支付失败！");
            }
        } else {
            //在此之前goods1的业务订单已经生成，状态为等待支付
            /*$payinfo['callback'] = "Home/Order/web_pay";
            $payinfo['money'] = '12';
            $param = array('sn'=>'50420141102457');
            $ab= R($payinfo['callback'], array('param' => $param));
            dump($ab);*/
            $this->display();
        }

    }
     /**
     * 支付结果返回
     */
    public function notify() {
        $apitype = I('get.apitype');
        $pay = new \Think\Pay($apitype, C('payment.' . $apitype));
        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            exit('Access Denied');
        }
        //验证
        if ($pay->verifyNotify($notify)) {
            //获取订单信息
            $info = $pay->getInfo();
            if ($info['status']) {
                $payinfo = M("Pay")->field(true)->where(array('out_trade_no' => $info['out_trade_no']))->find();
                if ($payinfo['status'] == 0 && $payinfo['callback']) {
                    session("pay_verify", true);
                    $param = unserialize($payinfo['param']);
                    $check = R($payinfo['callback'], array('param' => $param));
                    if ($check != false) {
                        M("Pay")->where(array('out_trade_no' => $info['out_trade_no']))->setField(array('update_time' => time(), 'status' => 1));
                    }else{
                        $this->error("排座错误!",U('Home/Order/index'));
                    }
                }
                if (I('get.method') == "return") {
                    $url = U('Home/Order/orderinfo',array('type'=>2,'sn'=>$param['sn']));
                    $this->success("支付成功!",$url,5);
                } else {
                    $pay->notifySuccess();
                }
                $this->assign("out_trade_no",$info['out_trade_no']);  //流水单号
            } else {
                $this->error("支付失败！");
            }
        } else {
            E("Access Denied");
        }
        //$this->display();
    }
    /**
     * 充值记录
     * @return [type] [description]
     */
    function to_up_pay(){
        $this->display();
    }
    /**
     * 资金充值 
     * @return [type] [description]
     */
    function to_up_cash(){
        if(IS_POST){
            $pinfo = I('post.');
            $sn = get_order_sn();
            //构造数据
            $data = array(
               

            );
            require ('/alidata/www/new/lub/App/Home/Service/ABCpay/PaymentRequest.php');
            $tRequest = new \PaymentRequest();
            $tRequest->order['PayTypeID']      =  'ImmediatePay';//直接支付
            $tRequest->order['OrderNo']        =  $sn;
            $tRequest->order['OrderDate']      =  date('Y-m-d');//订单日期
            $tRequest->order['OrderTime']      =  date('H:i:s');//订单时间
            $tRequest->order['OrderAmount']    =  $pinfo['money'];
            $tRequest->order['CurrencyCode']   =  '156';//设定交易币种
            $tRequest->order['BuyIP']          =  get_client_ip();
            $tRequest->order['Fee']            =  '0';
            $tRequest->order['OrderDesc']      =  $pinfo['remark'];
            $tRequest->order['CommodityType']  =  '0101';//充值类
            $tRequest->order['InstallmentMark']= '0';
            $tRequest->order['PaymentLinkType']= '1';//1：internet网络接入 2：手机网络接入 3:数字电视网络接入
            $tRequest->order['PaymentType']    = 'A';
            $tRequest->order['OrderURL']       = U('Home/pay/up_order',array('sn'=>$sn));


            //商户号103881390000019
            //户名：印象大红袍股份有限公司
            //帐号：13-9701 0104 0011 738
            //开户行：中国农业银行武夷山支行
            $tRequest->order['ReceiveAccount'] = '13970101040011738'; //设定收款方账号
            $tRequest->order['ReceiveAccName'] = '印象大红袍股份有限公司'; //设定收款方户名
            $tRequest->order['NotifyType']     = '0';//通知类型0：URL页面通知 1：服务器通知
            $tRequest->order['ResultNotifyURL']= 'http://www.yx513.net/abcpay.php';//通知URL
            $tRequest->order['IsBreakAccount'] = '0';//分账0否1是
            $tRequest->order['MerchantRemarks']= $pinfo['remark']; //设定附言
            $tResponse = $tRequest->postRequest();
            if($tResponse){
                $this->error("充值成功！");
            }else{
               $this->error("支付失败！"); 
            }
        }else{
            print(dirname(__FILE__));
            $this->display();
        }
    }
    public function up_order()
    {
        return '200';
    }
}