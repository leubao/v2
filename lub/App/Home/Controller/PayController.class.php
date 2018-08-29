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
use Payment\Client\Charge;
class PayController extends Base{
    //订单支付
    public function index() {
        /*
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
            dump($ab);*
            $this->display();
        }*/
        if (IS_POST) {
            $this->redirect('home.php?g=home&m=pay&a=index', $_POST);
        }
        $crm_id = I('id');//商户id
        //校验发送过来的商户是否是当前商户的子商户
        if(!check_crm_child($crm_id)){
            $this->error("未找到有效商户!");
        }
        $type = I('type') ? I('type') : '0';
        $channel = I('get.crm');
        if(empty($crm_id) || empty($channel)){$this->error("参数错误!");}
        /*查询条件START*/
        $start_time = I("starttime");
        $end_time   = I("endtime");
        $this->assign("starttime",$start_time);
        $this->assign("endtime",$end_time)->assign('channel',$channel);
        /*查询条件END*/
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }elseif(empty($type)){
            //默认只查询3个月内的数据
            $start_time = strtotime("-1 month");
            $end_time = time();
            $where['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }
        if(!empty($type)){
            $where['type'] = $type;
        }
        $where["crm_id"] = array('in',agent_channel($crm_id));
        $db = D('CrmRecharge');
        $count = $db->where($where)->count();
        $Page  = new \Home\Service\Page($count,15);
        $show  = $Page->show();
        $list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('data',$list)
            ->assign('page',$show)
            ->assign("cid",$crm_id)
            ->assign("type",$type)
            ->display();
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
    function to_up_cash_temp(){
        if(IS_POST){
            $pinfo = I('post.');
            $sn = get_order_sn();
            //构造数据
            require SITE_PATH.'Lub/App/Home/Service/ABCpay/PaymentRequest.php';
            $tRequest = new \PaymentRequest();
            $tRequest->order['PayTypeID']      =  "ImmediatePay";//直接支付
            $tRequest->order['OrderNo']        =  $sn;
            $tRequest->order['OrderDate']      =  date('Y-m-d');//订单日期
            $tRequest->order['OrderTime']      =  date('H:i:s');//订单时间
            $tRequest->order['OrderAmount']    =  $pinfo['money'];
            $tRequest->order['CurrencyCode']   =  "156";//设定交易币种
            $tRequest->order['BuyIP']          =  get_client_ip();
            $tRequest->order['Fee']            =  "0";
            $tRequest->order['OrderDesc']      =  $pinfo['remark'];
            $tRequest->order['CommodityType']  =  "0101";//充值类
            $tRequest->order['InstallmentMark']= "0";
            $tRequest->order['PaymentLinkType']= "1";//1：internet网络接入 2：手机网络接入 3:数字电视网络接入
            $tRequest->order['PaymentType']    = "A";
            $tRequest->order['OrderURL']       = U('Home/pay/up_order',array('sn'=>$sn));


            //商户号103881390000019
            //户名：印象大红袍股份有限公司
            //帐号：13-9701 0104 0011 738
            //开户行：中国农业银行武夷山支行
            $tRequest->order['ReceiveAccount'] = "13970101040011738"; //设定收款方账号
            $tRequest->order['ReceiveAccName'] = "印象大红袍股份有限公司"; //设定收款方户名
            $tRequest->order['NotifyType']     = "0";//通知类型0：URL页面通知 1：服务器通知
            $tRequest->order['ResultNotifyURL']= "http://www.yx513.net/abcpay.php";//通知URL
            $tRequest->order['IsBreakAccount'] = "0";//分账0否1是
            $tRequest->order['MerchantRemarks']= $pinfo['remark']; //设定附言
            dump($tRequest);
            /*$tResponse = $tRequest->postRequest();
            if($tResponse){
                $this->error("充值成功！");
            }else{
               $this->error("支付失败！"); 
            }*/
        }else{
            $this->display();
        }
    }
    public function up_order()
    {
        return '200';
    }
    /**
     *  充值
     */
    function to_up_cash(){
        if(IS_POST){

            $cash   = I("post.money");   //当前充值金额
            $id     = I("post.crmid");  //充值的客户id
            $channel= I('post.channel');
            $remark = I('post.remark'); //重置备注
            $model = new \Think\Model();
            $model->startTrans();
            //判断是企业还是个人1企业4个人
            if(!check_crm_child($id)){
                $model->rollback();
                $this->error("未找到有效商户!");
            }
            if($cash <= 0){
                $model->rollback();
                $this->error("充值金额必须大于0!");
            }
            $crmData = array('cash' => array('exp','cash+'.$cash),'uptime' => time());
            
            //渠道商客户
            $c_pay = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$id))->setField($crmData);
            
            //充值成功后，添加一条充值记录
            $data = array(
                'order_sn'  =>  time(),
                'cash'      =>  $cash,
                'addsid'    =>  '2',
                'user_id'   =>  get_user_id(),
                'crm_id'    =>  $id,
                'createtime'=>  time(),
                'type'      =>  '1',
                'balance'   =>  balance($id,'1'),
                'tyint'     =>  '1',//客户类型1企业4个人
                'remark'    =>  $remark,
            );      
            $recharge = $model->table(C('DB_PREFIX')."crm_recharge")->add($data);
            if($c_pay && $recharge){
                $model->commit();//成功则提交crm=2&id=32
                $url = U('Home/Pay/index',array('id'=>$id,'crm'=>$channel));
                $this->success('充值成功!',$url);
            }else{
                $model->rollback();//不成功，则回滚
                $this->error("充值失败!");
            }
        }else{
            $crmid = I("cid");  //客户的id
            $channel = I('channel');
            if(empty($crmid) || empty($channel)){$this->error("参数错误，请重新选择商户!");}
            //查询当前客户分组
            $this->assign("cid",$crmid)->assign('channel',$channel);
            $this->display();
        }
    }
    //网银充值
    function recharge()
    {
        if(IS_POST){
            
            try {
                $pinfo = I('post.');

                $money = trim($pinfo['money']);
                /*
                if (bccomp($amount, '10000', 2) === -1) {
                    $this->error('支付金额不能低于 10000 元',U('home/pay/index'));
                }*/
                $wxConfig = load_payment('ccb_web',10);
                $uinfo = Partner::getInstance()->getInfo();
                $crm =  D('Crm')->where(['id'=>$uinfo['cid']])->field('id,cash,name')->find();
                $sn = get_order_sn($crm['id']);
                $payData = [
                    'amount'    =>  $money,
                    'order_no'  =>  $sn,
                    'txcode'    =>  '520100',
                    'remark'    =>  $pinfo['remark'],//备注
                    'remark2'   =>  '',//$crm['name']//备注
                ];
                try {
                    $url = Charge::run('ccb_web', $wxConfig, $payData);
                    //记录充值日志
                    $log = [
                        'tyint'     => '1',
                        'addsid'    => '2',
                        'cash'      => $money,
                        'order_sn'  => $sn,
                        'crm_id'    => $crm['id'],
                        'type'      => 1,
                        'pay'       => '7',
                        'balance'   => '',
                        'remark'    => $pinfo['remark'],
                        'user_id'   => get_user_id('id'),
                    ];
                    load_redis('setex','pay_'.$sn,json_encode($log),'4200');
                } catch (PayException $e) {
                    echo $e->errorMessage();
                    exit;
                }
                header('Location:' . $url);
            } catch (PayException $e) {
                echo $e->errorMessage();
                exit;
            }
        }else{
            $uinfo = Partner::getInstance()->getInfo();
            $crm =  D('Crm')->where(['id'=>$uinfo['cid']])->field('id,cash,name')->find();
            $this->assign('crm',$crm)->display();
        }
    }
}