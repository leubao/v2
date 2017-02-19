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
use Wechat\Service\Wechat;
use WeChat\Service\Wxpay;
use Wechat\Service\Api;
use Wechat\Service\Wticket;

//微信支付
use Wechat\Service\Wxpay\WxPayApi;
use Wechat\Service\Wxpay\JsApiPay;
use Wechat\Service\Wxpay\WxPayConfig;
use Wechat\Service\Wxpay\WxPayUnifiedOrder;
use Wechat\Service\Wxpay\WxPayOrderQuery;
use Wechat\Service\Wxpay\WxPayException;
use Wechat\Service\Wxpay\WxPayNotify;
use Wechat\Controller\PayNotifyCallBackController;

class IndexController extends LubTMP {
    public $options;    //使用微信支付的Controller最好有一个统一的微信支付配置参数
    public $wxpaycfg;
    protected function _initialize() {
        parent::_initialize();
        $cache_conf = cache('ProConfig');
        $this->pid = I('get.pid') ? I('get.pid') : '41';
        $this->proconf = $cache_conf[$this->pid]['2'];
       // dump($this->proconf);
        // 开发者中心-配置项-AppID(应用ID)
        $this->appId = $this->proconf['appid'];
        // 开发者中心-配置项-AppSecret(应用密钥)
        $this->appSecret = $this->proconf['appsecret'];
        //受理商ID，身份标识
        $this->mchid = $this->proconf['mchid'];
        //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
        $this->mchkey = $this->proconf['mchkey'];
        // 开发者中心-配置项-服务器配置-Token(令牌)
        $this->token = $this->proconf['token'];
        // 开发者中心-配置项-服务器配置-EncodingAESKey(消息加解密密钥)
        $this->encodingAESKey = $this->proconf['encoding'];
        //项目URL
        $this->url = $this->proconf['wxurl'];
        //订单模板消息
        $this->tplmsgid = $this->proconf['tplmsg_order_id'];
        $this->tplremark = $this->proconf['tplmsg_order_remark'];
        //默认票型分组
        $this->prcie_group = $this->proconf['wx_price_group'];
        //支付回掉地址
        $this->notify_url = $this->url.'index.php/Wechat/Index/notify.html';
        $this->api = new Api(
            array(
                'appId' => $this->appId,
                'appSecret' => $this->appSecret,
                'get_access_token' => function(){
                    // 用户需要自己实现access_token的返回
                    return S('wechat_token');
                },
                'save_access_token' => function($token) {
                    // 用户需要自己实现access_token的保存
                    S('wechat_token', $token);
                }
            )
        );
        // wechat模块 - 处理用户发送的消息和回复消息
        $this->wechat = new Wechat(array(
            'appId' => $this->appId,
            'token' =>  $this->token,
            'encodingAESKey' => $this->encodingAESKey //可选
        )); 
    }
    //页面初始化
    function wx_init(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $jsapi = $this->api->get_jsapi_config($url,'');
        
        $this->assign('wechat',$this->proconf);
        $this->assign('jsapi',$jsapi)->assign('pid',$this->pid)->assign('user',session('user'));
    }
    public function index(){
       // $redirect_url = $this->url."index.php?g=Wechat&m=Index&a=show";
        $redirect_url = $this->url."index.php?g=Wechat&m=Index&a=acty&act=12";
        $redirect_urls = $this->url."index.php?g=Wechat&m=Index&a=auth_channel".$this->pid;
        // api模块 - 包含各种系统主动发起的功能
        $url = $this->api->get_authorize_url('snsapi_base',$redirect_url);
        $urls = $this->api->get_authorize_url('snsapi_userinfo', $redirect_urls);
        $reg = $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/reg'));
        $vie = $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/show',array('to'=>$thi->pid)));
        $u_url = $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/uinfo'));
        //领水展示页面
        $waters = $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/waters',array('to'=>$this->pid)));
        // 获取微信消息
        $msg = $this->wechat->serve();
        if($msg->MsgType == 'text'){
            switch ($msg->Content) {
                case '渠道绑定':
                    $return_msg =  $url;
                    break;
                case '注册':
                    $return_msg =  $reg;
                    break;
                case '用户':
                    $return_msg =  $u_url;
                    break;
                case '领水':
                    $return_msg =  $waters;
                    break;
                default:
                    $return_msg =  $url;
                    break;
            }
            $this->wechat->reply($return_msg);
        }
        //关注事件
        if($msg->MsgType == 'event'){
            $openid = (string)$msg->FromUserName; 
            $EventKey = (string)$msg->EventKey;
            if(!empty($EventKey)){
                $promote = substr($EventKey,1);
            }
            if($msg->Event == 'subscribe'){
                if($this->event_one($openid,$promote)){
                    $this->wechat->reply("亲,您回来了...");
                }else{
                    //首次关注
                    $promote = substr($EventKey,9);
                    $this->add_focus($openid,$promote);
                    //已经关注
                    $html = "<a href='".$waters."'>免费领水</a>";
                    $this->wechat->reply($html);
                }
            }
            //取消关注
            if($msg->Event == 'unsubscribe'){
                error_insert('un');
            }
            //重复关注
            if($msg->Event == 'SCAN'){
                switch ($this->event_one($openid,$promote)) {
                    case '0':
                        $this->add_focus($openid,$promote);
                        $re_msg = "<a href='".$waters."'>免费领水</a>";
                        break;
                    case '1':
                        $re_msg = "<a href='".$waters."'>免费领水</a>";
                        break;
                    case '2':
                        $re_msg = "亲,您回来了...";
                        break;

                    default:
                        $re_msg = "亲,您回来了...";
                        break;
                }
                $this->wechat->reply($re_msg);
            }
        }    
        // 主动发送
        //$this->api->send($msg->FromUserName, "你好....");    
    }
    //查询是否首次关注
    function event_one($openid,$promote){
        $db = M('WxMember');
        $info = $db->where(array('openid'=>$openid))->field('id,promote')->find();
        if(!empty($info['id']) && empty($info['promote'])){
            $db->where(array('id'=>$info['id']))->setField('promote',$promote);
            $return = '1';
        }else{
            if(empty($info['id'])){
                $return = '0';
            }else{
                $return = '2';
            }
            
        }
        
        return $return;
    }
    /**
     * 关注写入用户表
     * @param int $openid  关注者的身份ID
     * @param int $promote 推荐人的id
     */ 
    function add_focus($openid,$promote){
        M('WxMember')->add(array('openid'=>$openid,'promote'=>$promote));
    }
    //微信框架页面，用于扫描后
    function view(){
        $ginfo = I('get.');//参数加密
        //$ginfo = \Libs\Util\Encrypt::authcode($ginfo['param'],'DECODE');
        //跳转购买页面
        $url = U('Wechat/Index/show',array('u'=>$ginfo['u']));
        $urls = $this->api->get_authorize_url('snsapi_base',$url);
        $this->wx_init();
        $this->assign('url',$urls)->display();
    }
    /*微信更新
    * 购票展示页面  TODO  微信购票预留座位的初始值为 88 
    */
    function show(){
        //获取客户信息
        //用户授权
        $plan = array();
        $ginfo = I('get.');
        session('user',null);//TODO  生产环境删除
        //判断用户是否登录   检查session 是否为空
        $user = session('user');
        if(empty($user)){
           $user = $this->tologin($ginfo);
        }
        //与数据比对、是否绑定渠道商\
        //根据当前用户属性  加载价格及座位
        $plan = $this->getplan($this->pid);
        $param = array('pid'=>$this->pid);
        $goods_info = array_merge($plan,$user);
        $this->wx_init();
        $urls = $this->reg_link($user['user']['id']);
        //限制单笔订单最大数
        $this->assign('goods_info',json_encode($goods_info))->assign('ginfo',$ginfo)->assign('uinfo',$user)
            ->assign('urls',$urls)->assign('param',$param)->display();
    }
    /**
     * 活动支持
     */
    function acty(){
        $ginfo = I('get.');
        if(empty($ginfo['act'])){
            $this->error('页面不存在!');
        }
        //根据活动拉取销售计划
        $info = M('Activity')->where(array('id'=>$ginfo['act'],'status'=>1))->field('param,product_id')->find();
        $param = unserialize($info['param']);
        //缓存活动参数
        $param['product'] = $info['product_id'];
        session('param',$param);
        $ticketType = F('TicketType'.$info['product_id']);
        $show_data = array();
        //拉取该活动所有场次
        $plan_list = M('ActivityPlan')->where(array('activity_id'=>$ginfo['act']))->field('plan_id')->select();
      // dump($plan_list);
        foreach ($plan_list as $key => $value) {
            //获取销售计划
            $plan = F('Plan_'.$value['plan_id']);
            if(!empty($plan)){
                $table = ucwords($plan['seat_table']);
                //根据活动设定的区域进行加载
                foreach ($param['info'] as $k => $v) {
                    $area_num = area_count_seat($table,array('area'=>$v['area'],'status'=>'0'),1);
                   // dump($area_num);
                    /*判断当前剩余数量与渠道给定数量的大小
                    if($area_num > $v['quota']){
                        //展示渠道剩余数量
                        //查询已经消耗的数量
                        $quota_nums = M('QuotaUse')->where(array('channel_id'=>$ginfo['act'],'type'=>2,'plan_id'=>$value['plan_id'],'area_id'=>$v['area']))->getField('number');
                        $num = $v['quota'] - $quota_nums;
                    }else{
                        //展示实际数量
                        $num = $area_num;
                    }*/
                    $num = $area_num;
                    $seat[$value['plan_id']][] = array(
                            'area'=>$v['area'],
                            'name'=>areaName($v['area'],1),
                            'priceid'=>$v['price'],
                            'money'=>$ticketType[$v['price']]['price'],
                            'moneys'=>$ticketType[$v['price']]['discount'],
                            'num'   =>  $num
                        );
                }
                //获取销售该销售的所有票型
                $plan_data[] = array(
                    'title' =>  planShow($value['plan_id'],3,1),
                    'id'    =>  $value['plan_id'],
                    'num'   =>  '请选择区域'
                );
            }
            //$info = $value;
        }
       /*  $wxauth = $this->get_openid();
       $user['user'] = array(
            'id' => 2,
            'openid' => $wxauth->openid,
            'maxnum' => '30',
            'guide'  => $ginfo['act'],
            'qditem' => $ginfo['act'],//活动id
            'scene'  => '41',
            'epay'   => '1',
            'channel'=> '0',
        );*/
        session('user',null);
        $user = $this->tologin($ginfo);
        $goods_info = array(
            'plan' => $plan_data,
            'area' => $seat,
            'user' => $user['user'],
        );
        session('user',$user);
        $this->wx_init();
        //TODO 加载页面模板
        $this->assign('goods_info',json_encode($goods_info))
             ->display();
    }
    //活动订单
    function acty_order(){
        if(IS_POST){
            //创建订单
            $info = json_decode($_POST['info'],true);
            //判断是否可以参加活动
            if($this->check_order()){
               // error_insert("4001");
                //读取当前活动票型
                $param = session('param');
                $ticketType = F('TicketType'.$param['product']);
                //获取当前区域活动配置
                $area_set = $param['info'][$info['data'][0]['areaId']];
                //将原来的订单数量减少1
                $num = $info['data'][0]['num'];
                //判断数量是否大于1
                if($num > '1'){
                    $nums = $num - 1;
                    //重新构造请求订单
                    $info['data'] = array(
                        array('areaId'=>$info['data'][0]['areaId'],'priceid'=>$info['data'][0]['priceid'],'price'=>$ticketType[$area_set['price']]['discount'],'num'=>$nums),
                        array('areaId'=>$info['data'][0]['areaId'],'priceid'=>(int)$ticketType[$area_set['prices']]['id'],'price'=>$ticketType[$area_set['prices']]['discount'],'num'=>'1'),
                    );
                    $info['subtotal'] =  $ticketType[$area_set['price']]['discount']*$nums;
                }else{
                    //等于1
                    //重新构造请求订单
                    $info['data'] = array(
                        array('areaId'=>$info['data'][0]['areaId'],'priceid'=>(int)$ticketType[$area_set['prices']]['id'],'price'=>$ticketType[$area_set['prices']]['discount'],'num'=>'1'),
                    );
                    $info['subtotal'] =  '0';
                }
            }
            //增加活动标记
            //$info['param'][0]['activity'] = $info['crm'][0]['qditem'];
            $info['param'][0]['area'] = $info['data'][0]['areaId'];
            $info = json_encode($info);
            //提交订单请求
            $uinfo = session('user');
            $sn = \Libs\Service\Order::mobile($info,$uinfo['user']['scene'],$uinfo['user']);
            
            if($sn != false){
                $return = array(
                    'statusCode' => 200,
                    'url' => U('Wechat/Index/order',array('sn'=>$sn)),
                ); 
            }else{
                $return = array(
                    'statusCode' => 300,
                    'url' => '',
                );  
            }
            echo json_encode($return);
        }
    }
    /*获取销售计划*/
    function getplan($pid){
        $product = M('Product')->where(array('status'=>1,'id'=>$pid))->field('id')->select();
        $info['product'] = arr2string($product,'id');
        //根据当前用户判断   若为散客  读取配置项  微信的价格政策
        $user = session('user');
        $info['group']['price_group'] = $user['user']['pricegroup'];
        $info['scene'] = '4';
        $plan = \Libs\Service\Api::plans($info);
        foreach ($plan['plan'] as $key => $value) {
            $plans['plan'][] = array(
                'title' =>  $value['title'],
                'id'    =>  $value['id'],
                'num'   =>  $value['num'],
            );
            $plans['area'][$value['id']] = $value['param'];
        }
        return $plans;
    }
    //订单确认
    function order(){
        if(IS_POST){
            //创建订单
            $info = $_POST['info'];
            $pid = I('get.pid');
            //判断数据的完整性
            $uinfo = session('user');
            $sn = \Libs\Service\Order::mobile($info,$uinfo['user']['scene'],$uinfo['user']);
            if($sn != false){
               // dump(U('Wechat/Index/order',array('sn'=>$sn,'pid'=>$this->pid)));
                $return = array(
                    'statusCode' => 200,
                    'url' => U('Wechat/Index/order',array('pid'=>$pid,'sn'=>$sn)),
                ); 
            }else{
                $return = array(
                    'statusCode' => 300,
                    'url' => '',
                );  
            }
            echo json_encode($return);
        }else{
            $sn = I('get.sn');
            $info = D('Item/Order')->where(array('order_sn'=>$sn))->relation(true)->find();
            $info['info'] = unserialize($info['info']);
            $this->wx_init();
            if(in_array($info['status'],array('1','9'))){
                $this->error("订单状态不允许此项操纵");
            }else{
                //根据订单属性判断是否需要加载微信支付 散客和政企通过微信购票
                if($info['type'] == '1' || $info['type'] == '6' || $info['type'] == '8'){
                    $user = session('user');
                    if(empty($user)){
                       $user = $this->tologin($ginfo);
                    }
                    
                    //获取公众号信息，jsApiPay初始化参数
                    $this->options['appid'] = $this->appId;
                    $this->options['mchid'] = $this->mchid;
                    $this->options['sub_mchid'] = '1390172002';//子商户模式
                    $this->options['mchkey'] = $this->mchkey;
                    $this->options['secret'] = $this->appSecret;
                    $this->options['notify_url'] = $this->notify_url;//dump($this->options);

                    //$this->options['sub_appid'] = $this->appId;
                    //$this->options['sub_mchid'] = $this->mchid;
                    $this->wxpaycfg = new WxPayConfig($this->options);
                    //①、初始化JsApiPay
                    $tools = new JsApiPay($this->wxpaycfg);
                    $wxpayapi = new WxPayApi($this->wxpaycfg);

                    //②、统一下单
                    
                    if($info['money'] == '0'){
                        $money = 0.1*100;
                    }else{
                       $money = $info['money']*100; 
                    }
                    //产品名称
                    $plan = planShow($info['plan_id'],4,1);
                    $plans = F('Plan_'.$info['plan_id']);
                    $product_name = product_name($plans['product_id'],1);
                    $attach = array('number'=>$info['number'],'pid'=>$plans['product_id'],'product_name'=>$product_name);
                    $input = new WxPayUnifiedOrder($this->wxpaycfg);           
                    //这里带参数初始化了WxPayDataBase
                
                    $input->SetBody($product_name);
                    $input->SetDetail($plan);
                    $input->SetAttach(serialize($attach));//附加数据  
                    $input->SetOut_trade_no($info['order_sn']);
                    $input->SetTotal_fee($money);
                    $input->SetTime_start(date("YmdHis"));
                    $input->SetTime_expire(date("YmdHis", time() + 600));
                    $input->SetTrade_type("JSAPI");
                    $input->SetOpenid($user['user']['openid']);

                    $order = $wxpayapi->unifiedOrder($input);
                    $this->assign('or',$order);
                    if($order['return_code'] == 'FAIL'){
                        error_insert((string)$order['return_msg']);

                    }else{
                        $jsApiParameters = $tools->GetJsApiParameters($order);
                        //获取共享收货地址js函数参数
                        $this->assign('wxpay',$jsApiParameters);
                    }
                    
                    
                }
            }
            $this->assign('data',$info)->display();
        }
    }
    //授信额支付
    public function pay(){
        if(IS_POST){
            $info = $_POST['info'];
            $info = json_decode($info,true);
            //渠道商  支付且开始排座
            $oinfo = D('Item/Order')->where(array('order_sn'=>$info['sn']))->relation(true)->find();
            if(empty($info) || empty($oinfo)){echo json_encode(array('statusCode' => '300','msg' => $oinfo));return false;}
            $status = \Libs\Service\Order::mobile_seat($info,$oinfo);

           // 支付成功，发送模板消息
            if($status != false){
                //构造模板消息
                $user = session('user');
                $attach =  array(
                    'number'=>$oinfo['number'],
                    'product_name'=>'印象普陀',
                    'plan'=> planShow($oinfo['plan_id'],4,1),
                );
                $openid = $user['user']['openid'];
                $result = array(
                    'openid' => $openid,
                    'out_trade_no' => $info['sn'],
                    'attach' => serialize($attach),
                );
                $this->to_tplmsg($result);
                $return = array(
                    'statusCode' => 200,
                    'url' => U('Wechat/Index/pay_success',array('sn'=>$sn)),
                ); 
            }else{
                $return = array(
                    'statusCode' => 300,
                    'msg' => $status.'9',
                );  
            }
            echo json_encode($return);
            return true;
        }
    }
    /*请人代付*/
    function dfpay(){
        $ginfo = I('get.');
        if(empty($ginfo['sn'])){
            $this->error("参数错误");
        }
        //构造支付连接
        $url = $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/order',array('sn'=>$ginfo['sn'])));
        //生成支付二维码
        $qr = qr_base64($url,$ginfo['sn']);
        $this->wx_init();
        $this->assign('qr',$qr)->assign('url',$url)->display();
    }
    /*政企客户更新支付方式*/
    function window_pay(){
        if(IS_POST){
            $info = $_POST['info'];
            $info = json_decode($info,true);
           // //渠道商  支付且开始排座
            $oinfo = D('Item/Order')->where(array('order_sn'=>$info['sn']))->relation(true)->find();
            if(empty($info) || empty($oinfo)){echo json_encode(array('statusCode' => '300','msg' => "$oinfo"));return false;}
            $status = \Libs\Service\Order::mobile_seat($info,$oinfo);
            // 支付成功，发送模板消息
            if($status != false){
                //构造模板消息
                $user = session('user');
                $attach =  array(
                    'number'=>$oinfo['number'],
                    'product_name'=>'印象普陀',
                    'plan'=> planShow($oinfo['plan_id'],4,1),
                );
                $openid = $user['user']['openid'];
                $result = array(
                    'openid' => $openid,
                    'out_trade_no' => $info['sn'],
                    'attach' => serialize($attach),
                );
                $this->to_tplmsg($result);
                $return = array(
                    'statusCode' => 200,
                    'url' => U('Wechat/Index/pay_success',array('sn'=>$sn)),
                ); 
            }else{
                $return = array(
                    'statusCode' => 300,
                    'msg' => "订单状态不允许此项操作",
                );  
            }
            echo json_encode($return);
            return true;
        }
    }
    //支付宝支付
    function alipay(){
        if(IS_POST){
            //页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
            $info = I('post.');
            $this->assign('data',$info)->display();
            
        }else{
            $proconf = S('ProConfig');
            $sn = I('sn');
            if(!empty($sn)){
              $info = D('Item/Order')->where(array('order_sn'=>$sn))->relation(true)->find();
              $info['info'] = unserialize($info['info']);   
            }
            $pay_conf = array(
                // 收款账号邮箱
                'email' => $proconf['alipay'],
                // 加密key，开通支付宝账户后给予
                'key' => $proconf['alikey'],
                // 合作者ID，支付宝有该配置，开通易宝账户后给予
                'partner' => $proconf['aliid'],
            );
            $pay = new \Think\Pay('aliwappay', $pay_conf);
            $vo = new \Think\Pay\PayVo();
            $vo->setBody(planShow($info['plan_id'],2,2))
                ->setFee('0.01') //支付金额
                ->setOrderNo($info['order_sn'])
                ->setTitle("观演门票")
                ->setCallback("Home/Index/pays")
                ->setUrl(U("Home/Index/pay_suess",array('sn' => $info['order_sn'])))
                ->setParam(array('sn' => $info['order_sn']));
            echo $pay->buildRequestForm($vo);
        }
    }
    //支付宝api
    function aliwappay(){
        $proconf = S('ProConfig');
        $sn = I('sn');
        if(!empty($sn)){
          $info = D('Item/Order')->where(array('order_sn'=>$sn))->relation(true)->find();
          $info['info'] = unserialize($info['info']);   
        }
        $pay_conf = array(
            // 收款账号邮箱
            'email' => $proconf['alipay'],
            // 加密key，开通支付宝账户后给予
            'key' => $proconf['alikey'],
            // 合作者ID，支付宝有该配置，开通易宝账户后给予
            'partner' => $proconf['aliid'],
        );
        $pay = new \Think\Pay('aliwappay', $pay_conf);
        $vo = new \Think\Pay\PayVo();
        $vo->setBody(planShow($info['plan_id'],2,2))
            ->setFee('0.01') //支付金额
            ->setOrderNo($info['order_sn'])
            ->setTitle("观演门票")
            ->setCallback("Home/Index/pays")
            ->setUrl(U("Home/Index/pay_suess",array('sn' => $info['order_sn'])))
            ->setParam(array('sn' => $info['order_sn']));
        echo $pay->buildRequestForm($vo);
    }
    /**
     *
     * jsApi微信支付示例
     * 注意：
     * 1、微信支付授权目录配置如下  http://test.uctoo.com/addon/Wxpay/Index/jsApiPay/mp_id/
     * 2、支付页面地址需带mp_id参数
     * 3、管理后台-基础设置-公众号管理，微信支付必须配置的参数都需填写正确
     * 支付完成接收支付服务器返回通知，PayNotifyCallBackController继承WxPayNotify处理定制业务逻辑
     * @param array $mp_id 公众号在系统中的ID
     * @return 将微信支付需要的参数写入支付页面，显示支付页面
     */
    public function notify(){
        $rsv_data = $GLOBALS ['HTTP_RAW_POST_DATA'];
        $result = xmlToArray($rsv_data);
       //获取公众号信息，jsApiPay初始化参数
        //$proconf = cache('ProConfig');
        $attach = unserialize($result["attach"]);
        $this->options['appid'] = $result["appid"];
        $this->options['mchid'] = $result["mch_id"];
        $this->options['mchkey'] = $this->mchkey;
        $this->options['secret'] = $this->appSecret;
        $this->options['notify_url'] = $this->notify_url;
        $this->wxpaycfg = new WxPayConfig($this->options);
        $this->to_tplmsg($result);//error_insert("4001");
        //回复公众平台支付结果
        $notify = new PayNotifyCallBackController($this->wxpaycfg);
        $notify->Handle(false);
        //处理业务逻辑
    }
    /**发送模板消息
     * {{first.DATA}}
     * 订单号：{{OrderID.DATA}}
     * 产品名称：{{PkgName.DATA}}
     * 使用日期：{{TakeOffDate.DATA}}
     * {{remark.DATA}}
    */
    function to_tplmsg($info){
        $attach = unserialize($info['attach']);
        $template = array(
            'touser'=>$info['openid'],//指定用户openid
            //'template_id'=>'pImWc-HOJsHKzfqfedIKKye1wRR-ihAgTl4A2751718',//大红袍
            'template_id'=>$this->tplmsgid,
            'url'   =>  U('Wechat/Index/order_info',array('sn' => $info['out_trade_no'])),
            'topcolor'=>"#7B68EE",
            'data'=>array(
                'first'=>array('value'=>'您好，您的门票订单已预订成功。'."\n"),
                'OrderID' =>array('value'=>$info['out_trade_no'],'color'=>'#5cb85c'),
                'PkgName'=>array('value'=>$attach['product_name'],'color'=>'#5cb85c'),
                'TakeOffDate'=>array('value'=>$attach['plan']."\n",'color'=>'#5cb85c'),
                'remark'=>array('value'=>$this->tplremark), 
            )
        );
        //发送模板消息
        $res = $this->api->sendTemplateMessage($template);
    }
    //支付成功提示页面
    function pay_success(){
        $jssdk_conf = $this->api->get_jsapi_config('', 'json');
        $sn = I('sn');
        $this->wx_init();
        $this->assign('sn',$sn)->assign('jssdk_conf',$jssdk_conf)->display();
    }
    //渠道商账号绑定页面
    function auth_channel(){
        if(IS_POST){
            $pinfo = $_POST;
            $pinfo = json_decode($pinfo['info'],true);
            //验证用户名密码
            $uinfo = D('Wechat/User')->check_pwd($pinfo['username'],$pinfo['password']);
            //确保一个账号只能绑定一个微信号码
            $state = M('WxMember')->where(array('user_id'=>$uinfo['id']))->find();
            if($uinfo != false && $state == false){
                $updata = array('user_id'=>$uinfo['id'],'channel'=>'1');
                if(M('WxMember')->where(array('openid'=>$pinfo['openid']))->save($updata)){
                    //更新用户微信状态
                    M('UserData')->where(array('user_id'=>$uinfo['id']))->setField('wechat',1);
                    //注销登录
                    session('user',null);
                    $return = array(
                        'statusCode' => 200,
                        'url' => U('Wechat/Index/show',array('openid'=>$pinfo['openid'])),
                    ); 
                }else{
                    $return = array(
                        'statusCode' => 300,
                        'msg' => '状态更新失败,未完成绑定',
                    ); 
                }
            }else{
                $return = array(
                    'statusCode' => 300,
                    'msg' =>'账号异常,绑定失败',
                ); 
            }
            echo json_encode($return);
        }else{
            $ginfo = I('get.');
            $info = $this->get_openid($ginfo);
            $status = Wticket::add_wx_user($info);
            $this->wx_init();
            $this->assign('data',objectToArray($info))->assign('type',$status)->display();
        }
    }
    //用户登录  $ginfo  微信授权信息
    function tologin($ginfo){
        //记录推广人员
        $promote = $ginfo['u'];
        $wxauth = $this->get_openid($ginfo);
        //新用户写入
        Wticket::add_wx_user($wxauth,$promote);
        $openid = $ginfo['openid'] ? $ginfo['openid'] : $wxauth->openid;

        //保存openid
        session('openid',$openid);
        $this->assign('openid',$openid);
        //写入必要的当前用户信息
        $uinfo = $this->api->get_auto_auth($openid,$promote);
        if(!empty($uinfo['wechat'])){
            //根据当前登录用户获取支付方式、价格政策、可售数量、单笔订单最大量 30
            //判断是否是政企渠道用户
            switch ($uinfo['group']['type']) {
                case '1':
                    $type = 2;
                    $pay = '2';/*支持授信额支付*/
                    $scene = '42';
                    break;
                case '3':
                    $type = 4;/*政企渠道*/
                    $pay = '5';/*支持微信支付*/
                    $scene = '46';
                    break;
                case '4':
                    $type = 8;/*全员销售*/
                    $pay = '5';/*支持微信支付*/
                    $scene = '48';
                    break;
            }
            $user['user'] = array(
                'id'      => $uinfo['id'],
                'openid'  => $openid,
                'nickname'=> $uinfo['nickname'],
                'maxnum'  => '30',
                'guide'   => $uinfo['id'],
                'qditem'  => $uinfo['cid'] ? $uinfo['cid']:'0',
                'scene'   => $scene,
                'channel' => '1',

                'epay'    => $uinfo['group']['settlement'],
                'pricegroup'=> $uinfo['group']['price_group'],

                'wxid'      => $uinfo['wechat']['user_id'],//微信id
                'promote'   => $uinfo['promote'],
                'activity'  => $ginfo['act'] ? $ginfo['act']:'0',
                'fid'       => $promote,
            );
        }else{
            //微信散客先写死 TODO
            $user['user'] = array(
                'id' => 2,
                'openid' => $openid,
                'maxnum' => '5',
                'guide'  => '0',
                'qditem' => '0',
                'scene'  => '41',
                'epay'   => '2',//结算方式1 票面价结算2 底价结算
                'channel'=> '0',
                'pricegroup'=>$this->prcie_group,
                'wxid'   => $uinfo['wechat']['user_id'],
            );
        }
        //缓存用户信息
        session('user',$user);
        return $user;
    }
    //网页用户授权 用于网页授权
    function get_openid($ginfo){
        list($err, $user_info) = $this->api->get_userinfo_by_authorize('snsapi_base');
        if ($user_info !== null) {
            return $user_info;
        } else {
            return false;
        }
    }
    /**
     * 比较配额 @又见五台山
     */
    function quota(){
        $ginfo = I('get.');
        $plan = F('Plan_'.$ginfo['plan']);
        if(empty($plan)){
            $data["statusCode"] = "300";
            $data['msg'] = "参数错误!";
            echo json_encode($data);return false;
        }else{
            if(empty($ginfo['cid'])){
                //限制目前不限制配额
                $data["statusCode"] = "200";
            }else{
                $status = \Libs\Service\Quota::quota($plan['id'],$plan['product_id'],$ginfo['cid'],$ginfo['num']);
                if($status){
                    $data["statusCode"] = "200";
                }else{
                    $data["statusCode"] = "300";
                    $data['msg'] = "配额不足,请联系渠道管理员";
                }
                
            }
            echo json_encode($data);return false;
            /*
            活动配额限制
            $status = \Libs\Service\Quota::activity_quota($ginfo['num'],$ginfo['cid'],$plan['id'],$ginfo['area']);
            if($status){
                if($this->acty_card($plan['id'],$ginfo['card'])){
                     $data["statusCode"] = "200";
                }else{
                     $data["statusCode"] = "300";
                     $data['msg'] = "您已经购买过该场次门票了";
                }
                echo json_encode($data);return true;
            }else{
                $data["statusCode"] = "0";
                $data['msg'] = "该场次活动票已售罄,请选择其它场次!";
                echo json_encode($data);return false;
            }*/
        }
    }
    /**
     * 单个身份证单场只能购买一次
     * @return [type] [description]
     */
    function acty_card($plan_id,$id_card){
        $map = array(
            'plan_id' => $plan_id,
            'id_card' => $id_card,
            'status'  => array('in','1,9')
            );
       $find = M('Order')->where($map)->find();
       if($find){
            return false;
       }else{
            return true;
       }
    }
    /**
     * 订单列表
     */
    function order_wechat(){
        $user = session('user');
        $where['user_id'] = $user['id'];
        $where['status'] = array('in','1,9');
        $list = M('Order')->where()->limit('10')->select();
        $this->wx_init();
        $this->assign('data',$list)->display();
    }
    /**
     * 订单详情
     */
    function order_info(){
        $ginfo = I('get.');
        if(empty($ginfo)){
            $this->error("参数错误!");
        }
        $info = D('Item/Order')->where(array('order_sn'=>$ginfo['sn']))->relation(true)->find();
        $info['info'] = unserialize($info['info']);
        //区域分类
        foreach ($info['info']['data'] as $key => $value) {
            $area[$value['areaId']]['area'] = $value['areaId'];
            $area[$value['areaId']]['num'] = $area[$value['areaId']]['num']+1;
        }
        $this->wx_init();
        $this->assign('data',$info);
        $this->assign('area',$area);
        $this->display();
    }
    //微信支付成功通知
    function pay_notice(){
        $this->wx_init();
        $this->assign('data',$info)->display();
    }
    //用户中心
    function uinfo(){
        $user = session('user');
        if(empty($user) || $user['user']['id'] == '2'){
            $this->redirect('Wechat/Index/login');
        }
        $uid = $user['user']['id'];
        $info = M('User')->where(array('id'=>$uid))->field('id,nickname,cash,is_scene')->find();
        $this->wx_init();
        $this->assign('data',$info)->display();
    }
    //我的订单
    function orderlist(){
        //加密参数
        $user = session('user');
        $uid = $user['user']['id'];
        $list = M('Order')->where(array('status'=>array('in','1,9'),'user_id'=>$uid))->field('order_sn,status,createtime,money,plan_id')->limit('10')->select();
        $this->wx_init();
        $this->assign('data',$list)->display();
    }
    //推广
    function promote(){
        //加密参数
        $user = session('user');
        $uid = $user['user']['id'];
        $image_file = SITE_PATH."d/upload/".'u-'.$uid;
        $urls = $this->reg_link($uid);
        $base64_image_content = qr_base64($urls,'u-'.$uid);
        $this->wx_init();
        $this->assign('qr',$base64_image_content)->assign('urls',$urls)->display();
    }
    //展示专属关注二维码
    function focus_code(){
        //获取官方永久二维码
        $user = session('user');
        $uid = $user['user']['id'];
        //获取ticket
        $ticket = M('WxMember')->where(array('user_id'=>$uid))->getField('ticket');
        $qr = Api::get_qrcode_url($ticket);
        $this->wx_init();
        $this->assign('qr',$qr)->display();
    }
    //开放注册
    function reg(){
        if(IS_POST){
            $info = json_decode($_POST['info'],true);
            $ginfo = I('get.');
            $verify = genRandomString();
            $data = array(
                'username' => $info['phone'],
                'nickname' => $info['username'],
                "item_id"  => '0',
                'product'  => '0',
                'defaultpro'=>'0',
                "create_time" => time(),
                "update_time" => time(),
                "is_scene" => 4,  //应用场景为4，全员销售
                "type"  =>  $ginfo['type'] ? $ginfo['type'] : '1', //推广
                "cid"    => '0',
                "verify" => $verify,
                'phone' => $info['phone'],
                'email'  => '0',
                'role_id' => '0',
                'legally' => $info['legally'],
                'groupid' => $info['group'],
                "password" => md5($info['password'].md5($verify)),
                'status' => '3',
            );
            if($info['openid']){
                $user_id = D('User')->add($data);
            }
            if($user_id){
                D('UserData')->add(array('user_id'=>$user_id,'wechat'=>'1'));
                $updata = array('user_id'=>$user_id,'channel'=>'1');
                D('WxMember')->where(array('openid'=>$info['openid']))->save($updata);
                session('user',null);
                $return = array(
                    'statusCode' => 200,
                    'url' => $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/view',array('u'=>$user_id))),
                ); 
            }else{
                $return = array(
                    'statusCode' => 300,
                    'msg' => '注册失败...',
                );  
            }
            echo json_encode($return);
        }else{
            $ginfo = I('get.');
            $user = $this->tologin($ginfo);dump($user);
            if($user['user']['openid']){
                $this->wx_init();
                $url = $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/view',array('u'=>$user['user']['id'],'type'=>$ginfo['type'])));
                $this->assign('data',$user)->assign('url',$url)->assign('type',$ginfo['type'])->display();
            }else{
                $this->error("授权失败...");
            }
        }
    }
    //手机号验证
    function phone(){
        $ginfo = I('get.');
        if(empty($ginfo)){
            $return = array(
                'statusCode' => 300
            ); 
            echo json_encode($return);
            exit; 
        }
        $db = M('User');
        $phone = $db->where(array('phone'=>$ginfo['phone']))->find();
        if($phone){
            $return = array('statusCode' => 300,'msg'=>'手机号已被注册...'); 
        }else{
            $legally = $db->where(array('phone'=>$ginfo['legally']))->find();
            if($legally){
               $return = array('statusCode' => 300,'msg'=>'导游证号已被注册...'); 
            }else{
               $return = array('statusCode' => 200);  
           }
        }
        echo json_encode($return);
    }
    //提现
    function mention(){
        $user = session('user');
        if(IS_POST){
            $pinfo = json_decode($_POST['info'],true);
            $user = session('user');
            //构造写入数据
            $postData = array(
                'sn' => get_order_sn(),
                'user_id' => $user['user']['id'],
                'openid'  => $user['user']['openid'],
                'createtime'=>  time(),
                'uptime'    => time(),
                'money' =>  $pinfo['money'],
                'remark'=>  $pinfo['remark'],
                'pay_type'=> '5',
                'status'=>'3',//待审核
            );
            if(M('Cash')->add($postData)){
                $url = $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/uinfo'));
                $return = array('statusCode' => 200,'url'=>$url); 
            }else{
                $return = array('statusCode' => 300); 
            }
            echo json_encode($return);
        }else{
            //获取当前用户可提金额
            $uid = $user['user']['id'];
            $money = M('User')->where(array('id'=>$uid))->getField('cash');
            $this->wx_init();
            $this->assign('money',$money)->display();
        }
    }
    //提现记录
    function mention_list(){
        $user = session('user');
        //获取当前用户可提金额
        $uid = $user['user']['id'];
        $data = M('Cash')->where(array('user_id'=>$uid))->field('sn,money,remark,status,createtime')->select();
        $this->wx_init();
        $this->assign('data',$data)->display();
    }
    //客户开店
    function kd(){
        $ginfo = I('get.');
        //获取当前用户的id
        $this->redirect(U('Wechat/Index/show',array('to'=>$ginfo['to'],'u'=>1)));
    }
    //生成专属连接
    function reg_link($uid){
        $url = U('Wechat/Index/show',array('u'=>$uid));
        $urls = $this->api->get_authorize_url('snsapi_base',$url);
        return $urls;
    }
    //退出登陆
    public function logout() {
        session('[destroy]');
        echo "成功";
    }
    //查询下单人是否存在有效的活动订单
    //不存在有效订单的
    function check_order(){
        //下单人
        $uinfo = session('user');
        //散客不能参加活动
        if($uinfo['user']['id'] == '2' || $uinfo['user']['qditem'] == '0'){
            return false;
        }else{
            //活动标记不为空
            $map['activity'] = array('exp',' ');
            $map['status'] = array('in','1,9');
            $map['user_id'] = $uinfo['user']['id'];
            $status = M('Order')->where($map)->find(); 
            if($status){
               return false;
            }else{
                return true;
            }
        }
    }
    //解除绑定
    function remove(){
        //输入密码
        //解除绑定
        if(IS_POST){
            $pinfo = json_decode($_POST['info'],true);
            $user = session('user');
            $map = array('user_id'=>$user['user']['id'],'wechat'=>'1');
            //读取用户信息
            $uinfo = M('User')->where($map)->find();
            $pwd = md5($pinfo['password'].md5($uinfo['verify']));
            //验证用户密码
            if($uinfo['password'] == $pwd){
                //删除用户授权信息
                $updata = array('user_id'=>'0','channel'=>'0');
                D('WxMember')->where(array('openid'=>$info['openid']))->save($updata);
                //停用用户表
                $status = D('User')->where(array('id'=>$user['user']['id']))->setField('status','2');
                if($status){
                    session('user',null);
                    $return = array(
                        'statusCode' => 200,
                        'url' => $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/show',array('u'=>$user['user']['id']))),
                    ); 
                }else{
                    $return = array(
                        'statusCode' => 300,
                        'msg' => '注册失败...',
                    );  
                }
            }else{
                $return = array(
                    'statusCode' => 300,
                    'msg' => '密码验证失败...',
                );  
            }
            
            echo json_encode($return);
        }else{
            $this->wx_init();
            $this->display();
        }
    }
    //领水页面
    function waters(){
        $this->wx_init();
        $this->display();
    }
    function scenic(){
        $default = date('Y-m');
        $this->assign('default',$default)->display();
    }
}