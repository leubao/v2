<?php
// +----------------------------------------------------------------------
// | LubTMP 微信活动支持
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2015-8-25 
// +----------------------------------------------------------------------
namespace Wechat\Controller;
use Common\Controller\LubTMP;
use Wechat\Service\Wechat;
use WeChat\Service\Wxpay;
use Wechat\Service\Api;
use Wechat\Service\Wticket;
use Libs\Service\Order;
use Payment\Client\Charge;
class ScenicController extends LubTMP {

    protected function _initialize() {
        parent::_initialize();
        $this->ginfo = I('get.');
        
        $this->user = session('user');
        $this->param = $this->ginfo['param'];
        
        //加载产品配置信息
        $proconf = get_proconf(100,2);
        $script = &  load_wechat('Script',100,1);
        //获取JsApi使用签名，通常这里只需要传 $url参数  
        //设置统一分享链接
        $options = $script->getJsSign(U('Wechat/Scenic/index',['pid'=>$this->ginfo['pid'],'u'=>$this->user]));

        $this->assign('ginfo',$this->ginfo)->assign('proconf',$proconf)->assign('options',$options);

    }
    public function login()
    {
        if(IS_POST){
            $username = I("post.username", "", "trim");
            $password = I("post.password", "", "trim");
            $type  = I("post.type",0,intval);
            if (empty($username) || empty($password)) {
                $return = [
                    'status'    =>  false,
                    'code'      =>  '1002',
                    'msg'       =>  '用户名或者密码不能为空，请重新输入！'
                ];
                die(json_encode($return));
            }
            $user = D('Wechat/User')->login($username, $password);
            if($user){
                $return = [
                    'status'    =>  true,
                    'code'      =>  '0',
                    'data'      =>  [
                        'nickname' => $user['nickname'],
                        'crm'   => $user['crm']['name'],
                        'url'   => U('wechat/scenic/index'),
                    ],
                    'msg'       =>  'ok'
                ];
            }else{
                $return = [
                    'status'    =>  false,
                    'code'      =>  '1002',
                    'msg'       =>  '账号密码错误'
                ];
            }
            die(json_encode($return));
        }else{
            $user = session('user');
            if(empty($user['user']['openid']) || !empty($this->user)){
                $zinfo['pid'] = 100;
                Wticket::tologin($zinfo);
                $user = session('user');
            }
            $url = U('wechat/scenic/login');
            $this->check_login($url);
            $this->display();
        }
    }
    public function index()
    {
        $this->cklogin();
        $list = D('Product')->where(['status'=>1])->relation(true)->field('id,name')->order('sorting asc')->select();
        $this->assign('list',$list)->display();
    }

    public function view()
    {
        $this->cklogin();
        $ginfo = I('get.');
        if(empty($ginfo['pid'])){
            $this->error('产品不存在!');
        }
        //判断用户是否登录   检查session 是否为空
        

        //根据活动拉取销售计划
        $info = D('Product')->where(array('id'=>$ginfo['pid'],'status'=>1))->relation(true)->field('id,name')->find();

        if(empty($info)){
           $this->error('产品已下架~');
        }

        $plan = Wticket::getplan($ginfo['pid']);
        $user = session('user');
        $global = array_merge($plan,$user);
        $this->assign('global',json_encode($global));
        $this->assign('ginfo',$ginfo);
        $this->assign('product', $info);
        $this->display(); 
    }
    public function create_order()
    {
        if(IS_POST){
            //创建订单
            $info = $_POST['info'];$this->cklogin();//dump($info);
            //判断数据的完整性
            $uinfo = session('user');load_redis('setex','debug', json_encode($uinfo), 3600);
            $order = new Order();
            $sn = $order->mobile($info,$uinfo['user']['scene'],$uinfo['user']);
          
            if($sn != false){
                $return = array(
                    'statusCode' => 200,
                    'url' => U('Wechat/scenic/create_order',array('sn'=>$sn)),
                   // 'url' => U('Wechat/index/scenic_order',array('pid'=>$this->pid,'sn'=>$sn)),
                ); 
            }else{
                $return = array(
                    'statusCode' => 300,
                    'msg' => $order->error,
                );  
            }
            die(json_encode($return));
        }else{
            $info = D('Item/Order')->where(array('order_sn'=>$this->ginfo['sn']))->relation(true)->find();
            $info['info'] = unserialize($info['info']);
           // $this->wx_init($this->pid);
            if(in_array($info['status'],array('1','9'))){
                $this->error("订单状态不允许此项操纵");
            }else{
                //根据订单属性判断是否需要加载微信支付 散客和政企通过微信购票
                if(in_array($info['type'],['1','2','6','8'])){
                    $user = session('user');
                    // 获取预支付ID
                    if($info['money'] == '0'){
                       $money = 0.1*100;
                    }else{
                       $money = $info['money']*100; 
                    }
                    //$money = 1;
                    $proconf = cache('ProConfig');
                    $proconf = $proconf['100'][2];
                    $notify_url = 'http://fsw.leubao.com/index.php/Wechat/Notify/notify.html';
                    //产品名称
                    $product_name = product_name($info['product_id'], 1);
                    $pay = & load_wechat('Pay', '100');//dump($pay);dump($user);
                    $prepayid = $pay->getPrepayId($user['user']['openid'], $product_name, $info['order_sn'], $money, $notify_url, $trade_type = "JSAPI",'',1);
                   
                    if($prepayid){
                        $options = $pay->createMchPay($prepayid);
                    }else{
                        // 创建JSAPI签名参数包，这里返回的是数组
                        $this->assign('error',$pay->errMsg.$pay->errCode);
                    }
                    $this->assign('jsapi',$prepayid)->assign('wxpay',$options);
                }
            }
            $this->assign('data',$info)->display();
        }
    }
    public function pay_success()
    {
        $sn = I('sn');
        $this->assign('sn',$sn)->display();
    }
    public function orderlist()
    {
        $this->cklogin();
        $uinfo = session('user');
        $list = M('Order')->where(array('status'=>array('in','1,9'),'user_id'=>$uinfo['user']['id']))->field('take,phone,order_sn,status,product_id,createtime,money,plan_id,number')->limit('50')->order('createtime DESC')->select();
        $this->assign('data',$list)->display();
    }
    function order_info(){
        $this->cklogin();
        $sn = I('sn');
        $info = D('Item/Order')->where(array('order_sn'=>$sn))->relation(true)->find();
        $info['info'] = unserialize($info['info']);
        $this->assign('data',$info);
        $this->display('orderinfo');
    }
    //判断是否登录
    function check_login($url){
        $ginfo = I('get.');
        $user = session('user');
        if(empty($user['user']['openid']) && !isset($ginfo['code'])){
            $oauth = & load_wechat('Oauth',100,1);
            $urls = $oauth->getOauthRedirect($url, $state, 'snsapi_base');
            header('location:'. $urls);
        }elseif(empty($user['user']['openid'])){
            header('location:'. $url);
        }
    }
    function cklogin()
    {
        $user = session('user');
        if(!$user || $user['user']['id'] == '2'){
            $urls = U('wechat/scenic/login');
            header('location:'. $urls);
        }
    }
}