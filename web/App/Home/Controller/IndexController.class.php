<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    protected function _initialize() {
        $this->url = 'http://www.yxpttk.com/';
        $this->initweb();
    }
    function initweb(){
        $proconf = S('ProConfig');
        if(empty($proconf)){
            //产品缓存
            $list = M('Product')->field('id')->select();
            foreach ($list as $key => $value) {
                $product_data[$value['id']] = M("ConfigProduct")->where(array('product_id'=>$value['id']))->getField("varname,value");
            }
            S('ProConfig',$product_data);
        }
        //dump($proconf);
        //S('ProConfig',null);
        $this->assign('proconf',$proconf[1]);
    }
    public function index(){
        $ginfo = I('get.');
        switch ($ginfo['type']) {
            case '1':
                //首页
                //拉取销售计划
                $this->plan();
                $url = U('Home/Index/shop');
                break;
            case '2':
                //订单支付结束成功按钮
                $url = U('Home/Index/pay_suess');
                break;
            case '3':
                //首页
                $url = U('Home/Index/google');
                break;
            case '4':
                //首页
                break;
            default:
                //拉取销售计划
                $this->plan();
                $url = U('Home/Index/shop');
                break;
        }
    	$this->assign('url',$url)->display('shop');
    }
    function shop(){
        //拉取销售计划
        $this->plan();
        $this->display();
    }
    //加载场次
    function plan(){
        $info = I('get.');
        //读取可销售产品
        $p_url = $this->url.'api.php?m=trust&a=get_product';
        $product = getHttpContent($p_url,1);
        $products = json_decode($product,true);
        //读取
        //TODO 先写死  回头动态配置
        $map = "&type=3&price=1&scene=3&product=".arr2string($products['info'],'id');
        $url = $this->url.'api.php?m=trust&a=get_plan'.$map;
        $info = getHttpContent($url,1);
        $plan = json_decode($info,true);//dump($plan);
        $this->assign('area',json_encode($plan['info']['area']));
        $this->assign('plan',$plan['info']['plan']);
        
    }
    //完善订单信息页面
    function cart(){
        if(IS_POST){
            $pinfo = json_decode($_POST['info'],true);
            session('info',$pinfo);
            $return = array(
                'statusCode'=>200,
                'url'   =>  U('Home/Index/cart'),
            );
            $this->ajaxReturn($return);
            //下单
        }else{
            $this->assign('data',session('info'))->display();
        }
    }
    function cart2(){
        if(IS_POST){
            $pinfo = json_decode($_POST['info'],true);
            //合并订单请求数据
            $cart = session('info');
            $data = array_merge($pinfo,$cart);
            //向票务系统下单
            $o_url = $this->url.'api.php?m=trust&a=insert_order';
            $info = getHttpContent($o_url,'POST',$data);
            $info = json_decode($info,true);
            $data['sn'] = $info['info'];
            session('info',$data);
            $return = array(
                'statusCode'=>200,
                'url'   =>  U('Home/Index/cart2'),
            );
            $this->ajaxReturn($return);
        }else{
            $this->assign('data',session('info'))->display();
        }
    }
    //支付
    function pay(){
        if(IS_POST){
            //页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
            $proconf = S('ProConfig');
            $oinfo = session('info');
            $pay_conf = array(
                // 收款账号邮箱
                'email' => $proconf[$oinfo['pid']]['alipay'],
                // 加密key，开通支付宝账户后给予
                'key' => $proconf[$oinfo['pid']]['alikey'],
                // 合作者ID，支付宝有该配置，开通易宝账户后给予
                'partner' => $proconf[$oinfo['pid']]['aliid'],
            );
            $oinfo['subtotal'] = '0.01';
            $pay = new \Think\Pay('alipay', $pay_conf);
            $vo = new \Think\Pay\PayVo();
            $vo->setBody(planShow($oinfo['plan_id'],2,2))
                ->setFee($oinfo['subtotal'])
                ->setOrderNo($oinfo['sn'])
                ->setTitle("观演门票")
                ->setCallback("Home/Index/pays")
                ->setUrl(U("Home/Index/pay_suess",array('sn' => $oinfo['sn'])))
                ->setParam(array('sn' => $oinfo['sn']));
            echo $pay->buildRequestForm($vo);
        }else{
            //在此之前goods1的业务订单已经生成，状态为等待支付
            $this->assign('info',$info)->display();
        }
    }
    /**
     * 返回产品配置信息
     * 去除敏感信息
     */
    public function pro_conf(){
        $unset = array(
            'alipay_email'=>'',
            'alipay_partner'=>'',
            'alipay_key'=>'',
            'aliwappay_email'=>'',
            'aliwappay_partner'=>'',
            'aliwappay_key'=>'',
            'plan_start_time'=>'', 
            'plan_end_time'=>'',
            'ticket_sms'=>'',
            'win_subtract'=>'',
            'channel_quota'=>'',
            'channel_time'=>'',
            'print_seat_custom'=>'',
            'print_seat'=>'',
            'webpay'=>'',
            'area_sms'=>'',
            'crm_sms'=>'',
            'print_remrak'=>'',
            'print_field'=>''
        );
        $return = array_diff_key($this->proconf,$unset);
        return $return;
    }
    /**
     * 订单支付成功
     * @param type $money
     * @param type $param
     */
    public function pays($param) {
        if (session("pay_verify") == true) {
            session("pay_verify", null);
            
            $map = "&seat_type=1&pay_type=4&sn=".$param;
            $url = $this->url.'api.php?m=trust&a=pay_suess_seat'.$map;
            $info = getHttpContent($url,1);
            $greturn = json_decode($info,true);
            //返回结果
            if($greturn['info'] == '1'){
                return true;
            }else{
                //写入待处理事件
                return false;
            }
        } else {
            E("Access Denied");
        }
    }
    /**
     * 支付结果返回
     */
    public function notify() {
        $apitype = I('get.apitype');
        $trade_no = I('get.trade_no');
        $sn  = I('get.out_trade_no');
        $pid = D('Order')->where(array('order_sn'=>$sn))->getField('product_id');
        if(empty($pid)){
            exit('Access Denied !');
        }else{
            $proconf = S('ProConfig');
            $pay_conf = array(
                // 收款账号邮箱
                'email' => $proconf[$pid]['alipay'],
                // 加密key，开通支付宝账户后给予
                'key' => $proconf[$pid]['alikey'],
                // 合作者ID，支付宝有该配置，开通易宝账户后给予
                'partner' => $proconf[$pid]['aliid'],
            );
            $pay = new \Think\Pay($apitype, $pay_conf);
            if (IS_POST && !empty($_POST)) {
                $notify = $_POST;
            } elseif (IS_GET && !empty($_GET)) {
                $notify = $_GET;    
                unset($notify['method']);
                unset($notify['apitype']);
            } else {
                exit('Access Denied 1');
            }//dump($notify);
            //验证trade_no
            if ($pay->verifyNotify($notify)) {
                //获取订单信息
                $info = $pay->getInfo();
                if ($info['status']) {
                    $payinfo = M("Pay")->field(true)->where(array('order_sn' => $info['out_trade_no']))->find();
                    if ($payinfo['status'] == 0 && $payinfo['callback']) {
                        session("pay_verify", true);
                        session("info",null);
                        $check = R($payinfo['callback'], array('sn'=>$info['out_trade_no']));
                        if ($check !== false) {
                            M("Pay")->where(array('order_sn' => $info['out_trade_no']))->setField(array('update_time' => time(), 'status' => 1,'out_trade_no'=>$trade_no));
                        }else{
                            //写入待处理事件
                            //写入待处理订单提醒
                            M('pre_order')->add(array('order_sn'=>$info['out_trade_no'],
                                'user_id'=>'3',
                                'status'=>'1',
                                'remark'=>'客户已支付完成,系统未能完成自动排座',
                                'type'  =>'3',
                                'createtime'=>time()));
                            $this->error("自动排座失败,请联系官方客户处理!客服电话:".$proconf['call']);
                        }
                    }
                    if (I('get.method') == "return") {
                        $url = U("Home/Index/pay_suess",array('sn' => $info['out_trade_no']));
                        redirect($url);
                    } else {
                        $pay->notifySuccess();
                    }
                } else {
                    $this->error("支付失败！");
                }
            } else {
                E("Access Denied 2");
            }
        }
        
    }
    //订单查询
    function google(){
        $pinfo = $_POST;
        $pinfo = json_decode($pinfo['info'],true);
        if($pinfo['type'] == '1'){
            //订单号
            $map['order_sn'] = $pinfo['data'];
        }else{
            //手机号
            $map['phone'] = $pinfo['data'];
        }
        $map['status'] = array('in','1,9');
        //读取销售计划 过期的两天 和当前未过期的全部
        $plantime = strtotime(" -2 day ",strtotime(date('Y-m-d')));
        $plan = M('Plan')->where(array('plantime'=>array('egt',$plantime)))->order('plantime ASC')->field('id')->cache(true)->select();
        $map['plan_id'] = array('in',arr2string($plan));
        $info = D('Order')->where($map)->limit(3)->relation(true)->field('id,plan_id,order_sn,status')->select();
        foreach ($info as $key => $value) {
            $data[] =  array(
                'plan'     => planShow($value['plan_id'],1),
                'order_sn' => $value['order_sn'],
                'status'   => $value['status'],
                'info'     => unserialize($value['info']),
            );
        }
        if(empty($pinfo['type'])){
            $this->display();
        }else{
            $this->assign('data',$data)->display('go_list');
        }
    }
    //支付完成或遇到问题
    function pay_suess(){
        $ginfo = I('get.');
        if(empty($ginfo['sn'])){
            $this->error("参数错误");
        }
        //查询订单状态
        $oinfo = M('Order')->where(array('order_sn'=>$ginfo['sn']))->cache(true)->field('id,plan_id,product_id,order_sn,status')->find();
        if($oinfo['status'] == '1'){
            $oinfo['info'] = unserialize($oinfo['info']);
        }

        $this->assign('data',$oinfo)->display();
    }
    function oinfo(){
        $ginfo = I('get.');
        if(empty($ginfo['sn'])){
            $this->error("参数错误");
        }
        $oinfo = D('Order')->where(array('order_sn'=>$ginfo['sn'],'status'=>'1'))->cache(true)->relation(true)->field('id,plan_id,product_id,order_sn,status')->find();
        if($oinfo['status'] != '1'){
            $oinfo['msg'] = "订单状态未知,无法完成您的请求...";
        }else{
            $oinfo['info'] = unserialize($oinfo['info']);
        }
        $this->assign('data',$oinfo)->display();
    }
    //误删补贴报表
    function butie(){
    	//读取订单
    	$sn = '';
    	$info = M('Order')->where(array('order_sn'=>$sn))->find();
    	//读取计划
    	$plan = M('Plan')->where(array('id'=>$info['plan_id']))->field('id,seat_table,product_id')->find();
    	$ticketType = F("TicketType".$plan['product_id']);
    	//读取座位
    	$ticket_list = M(ucwords($plan['seat_table']))->where(array('order_sn' => $sn))->field('price_id')->select();
    	//计算补贴金额
    	foreach ($ticket_list as $k => $v) {
    		$rebate += $ticketType[$v['priceid']]['rebate'];
    	}
    	//dump($rebate);
    	//写入补贴报表
    	/*
    	$teamData = array(
			'order_sn' 		=> $sn,
			'plan_id' 		=> $plan['id'],
			'product_type'	=> $info['product_type'],//产品类型
			'product_type'	=> $plan['product_type'],//产品类型
			'product_id' 	=> $plan['product_id'],
			'user_id' 		=> '1',
			'money'			=> $rebate,
			'guide_id'		=> $info['crm'][0]['guide'],
			'qd_id'			=> $info['crm'][0]['qditem'],
			'status'		=> '1',
			'number' 		=> $count,
			'type'			=> $info['type'] == '2' ? $info['sub_type'] : '2',//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
			'createtime'	=> time(),
			'uptime'		=> time(),
		);
		$in_team = D('TeamOrder')->add($teamData);
		*/
    }
}