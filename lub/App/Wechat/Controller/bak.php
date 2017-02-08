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
//支付宝

class IndexController extends LubTMP {
	protected function _initialize() {
    	parent::_initialize();
        /*判断是否是微信打开
        if(is_weixin() == false){
            header('Content-type:text/html;charset=utf-8');
            exit('请在微信中打开...');
        }*/
        /*
    	$ginfo = I('get.');
       // dump($ginfo);
        //获取URL参数
        $param = url_param($ginfo['param'],'DECODE');
        $info = session('wechat');
        if(!$info){
            $info = M('Wechat')->where(array('id'=>$ginfo['to']))->find();
            $info['config'] = unserialize($info['config']);
            $info['tpl']    = unserialize($info['tpl']);
            session('wechat',$info);
        }*/
        $proconf = cache('ProConfig');
        // 开发者中心-配置项-AppID(应用ID)
        $this->appId = $proconf['appid'];
        // 开发者中心-配置项-AppSecret(应用密钥)
        $this->appSecret = $proconf['appsecret'];
        //受理商ID，身份标识
        $this->mchid = $proconf['mchid'];
        //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
        $this->mchkey = $proconf['mchkey'];
        // 开发者中心-配置项-服务器配置-Token(令牌)
        $this->token = $proconf['token'];
        // 开发者中心-配置项-服务器配置-EncodingAESKey(消息加解密密钥)
        $this->encodingAESKey = $proconf['encoding'];
        //订单模板消息
        $this->tplmsgid = $proconf['tplmsg_order_id'];
        //项目URL
        $this->url = $proconf['wxurl'];
        
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
        $jsapi = $this->api->get_jsapi_config(self::$Cache['Config']['siteurl'],'');
        $this->assign('wechat',cache('ProConfig'));
        $this->assign('jsapi',$jsapi);
    }
	public function index(){
		// 获取微信消息
        $msg = $this->wechat->serve();
        $url = $this->api->get_authorize_url('snsapi_base',"http://new.leubao.com/index.php?g=Wechat&a=show&to=5");
        // 回复文本消息
        if ($msg->MsgType == 'text' && $msg->Content == '你好') {
            $this->wechat->reply($url);
        } else {
            $this->wechat->reply("0000");
        }
        // 主动发送
        $this->api->send($msg->FromUserName, '这是我主动发送的一条消息');
	}
	//微信框架页面，用于扫描后
    function view(){
        $ginfo = I('get.param');//参数加密
        $ginfo = \Libs\Util\Encrypt::authcode($ginfo['param'],'DECODE');
        //解密
        $redirect_url = "http://new.leubao.com/index.php?g=Wechat&a=show&to=".$ginfo['to']."&uid=".$ginfo['uid'];
        // api模块 - 包含各种系统主动发起的功能
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa3f3c6a0595d91f4&redirect_uri=".urlencode($redirect_url)."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        $this->wx_init();
        $this->assign('url',$url)->display();
    }

    /*微信更新
    * 购票展示页面  TODO  微信购票预留座位的初始值为 88 
    */
    function show(){
        //获取客户信息用户授权
        $plan = array();
        $ginfo = I('get.');
        if(!session('ginfo')){
           session('ginfo',$ginfo);
        }
        session('user',null);//TODO  生产环境删除
        //判断用户是否登录   检查session 是否为空
        $user = session('user');
        if(empty($user)){
           $user = $this->tologin($ginfo);
        }
        //与数据比对、是否绑定渠道商\
        //根据当前用户属性  加载价格及座位
        $plan = Wticket::getplan();
        $goods_info = array_merge($plan,$user);
        $this->wx_init();
        //限制单笔订单最大数
        $this->assign('goods_info',json_encode($goods_info))
            ->assign('ginfo',session('ginfo'))
             ->display();
    }
    //用户登录  $ginfo  微信授权信息
    function tologin($ginfo){
        $wxauth = $this->get_openid($ginfo);
        if($wxauth){
            //新用户写入
            Wticket::add_wx_user($wxauth);
        }
        $openid = $ginfo['openid'] ? $ginfo['openid'] : $wxauth->openid;
        $this->assign('openid',$openid);
        //写入必要的当前用户信息
        $uinfo = $this->api->get_auto_auth($openid);
        if($uinfo['wechat']['channel'] == '1'){
            //根据当前登录用户获取支付方式、价格政策、可售数量、单笔订单最大量 30
            //判断是否是政企渠道用户
            if($uinfo['group']['type'] == '3'){
                $type = 4;/*政企渠道*/
                $pay = '5';/*支持微信支付*/
                $scene = '46';
            }else{
                $type = 2;
                $pay = '2';/*支持授信额支付*/
                $scene = '42';
            }
            $user['user'] = array(
                'id' => $uinfo['id'],
                'openid' => $openid,
                'nickname'=> $uinfo['nickname'],
                'maxnum'  => '30',
                'guide'   => $uinfo['id'],
                'qditem'  => $uinfo['cid'],
                'scene'   => $scene,
                'epay'    => $uinfo['group']['settlement'],
                'channel' => '1',
                'pricegroup'=> $uinfo['group']['price_group'],
                'wxid'      => '',//微信id
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
                'wxid'   => '',
            );
        }
        //缓存用户信息
        session('user',$user);
        return $user;
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
    //网页用户授权 用于网页授权
    function get_openid($ginfo){
        list($err, $user_info) = $this->api->get_userinfo_by_authorize('snsapi_base');
        if ($user_info !== null) {
            return $user_info;
        } else {
            return false;
        }
    }
    //订单确认
    function order(){
        if(IS_POST){
            //创建订单
            $info = $_POST['info'];
            //判断数据的完整性
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
        }else{
            $sn = I('get.sn');
            $info = D('Item/Order')->where(array('order_sn'=>$sn))->relation(true)->find();
            $info['info'] = unserialize($info['info']);
            
            //根据订单属性判断是否需要加载微信支付 散客和政企通过微信购票
            if($info['type'] == '1' || $info['type'] == '6'){

                $user = session('user');
                if(empty($user)){
                   $user = $this->tologin($ginfo);
                }
                //获取公众号信息，jsApiPay初始化参数
                $options['appid'] = $this->appId;
                $options['mchid'] = $this->mchid;
                $options['mchkey'] = $this->mchkey;
                $options['secret'] = $this->appSecret;
                $options['notify_url'] = self::$Cache['Config']['siteurl'].'index.php/Wechat/Index/notify.html';
                $this->wxpaycfg = new WxPayConfig($options);
                //①、初始化JsApiPay
                $tools = new JsApiPay($this->wxpaycfg);
                $wxpayapi = new WxPayApi($this->wxpaycfg);

                //②、统一下单
                $money = $info['money']*100;
                //产品名称
                $plan = planShow($info['plan_id'],4,1);
                $product_name = '印象大红袍';
                $attach = array('number'=>$info['number'],'product_name'=>$product_name,'plan'=>$plan);
                $input = new WxPayUnifiedOrder($this->wxpaycfg);           //这里带参数初始化了WxPayDataBase
            
                $input->SetBody($product_name);
                $input->SetDetail($plan);
                $input->SetAttach(serialize($attach));//附加数据  
                $input->SetOut_trade_no($info['order_sn']);
                $input->SetTotal_fee($money);
                $input->SetTime_start(date("YmdHis"));
                $input->SetTime_expire(date("YmdHis", time() + 600));
                $input->SetTrade_type("JSAPI");
                $input->SetOpenid($user['user']['openid']);

                $order = $wxpayapi->unifiedOrder($input);//dump($order);
                $jsApiParameters = $tools->GetJsApiParameters($order);
                //获取共享收货地址js函数参数
                //$editAddress = $tools->GetEditAddressParameters();
                //③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
                /**
                 * 注意：
                 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
                 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
                 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
                 */
                $this->assign('wxpay',$jsApiParameters);
            }
            $this->wx_init();
            if(in_array($info['status'],array('1','9'))){
                $this->assign('sn',$sn)->display('pay_success');
            }else{
                $this->assign('data',$info)->display();
            }
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
                    'product_name'=>'印象大红袍',
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
    /*请人代付*/
    function dfpay(){
        $ginfo = I('get.');

        //构造支付连接
        $url = $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/order',array('sn'=>$ginfo['sn'])));
        //dump($url);
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
                    'product_name'=>'又见五台山',
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
        $options['appid'] = $result['appid'];
        $options['mchid'] = $result['mchid'];
        $options['mchkey'] = $this->mchkey;
        $options['secret'] = $this->appSecret;
        $options['notify_url'] = self::$Cache['Config']['siteurl'].'index.php/Wechat/Index/notify.html';
        $this->wxpaycfg = new WxPayConfig($options);
        //发送模板消息
        $this->to_tplmsg($result); 
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
            'template_id'=>$this->tplmsgid,
         //   'template_id'=>'DZmSSzzMGMZlEqPtk5_Fh4wVqQ23napgq6ZUTv24R2o',//大红袍
            'url'   =>  U('Wechat/Index/order_info',array('sn' => $info['out_trade_no'])),
            'topcolor'=>"#7B68EE",
            'data'=>array(
                'first'=>array('value'=>'您好，您的门票订单已预订成功。'."\n"),
                'OrderID' =>array('value'=>$info['out_trade_no'],'color'=>'#5cb85c'),
                'PkgName'=>array('value'=>$attach['product_name'],'color'=>'#5cb85c'),
                'TakeOffDate'=>array('value'=>$attach['plan']."\n",'color'=>'#5cb85c'),
                'remark'=>array('value'=>"请凭订单号到茶博园游客中心一楼兑换门票，或通过印象大红袍自助取票机自助取票。\n点击消息详情查看座位。"), 
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
    /**
     * 比较配额 @印象大红袍
     */
    function quota(){
        $ginfo = I('get.');//dump($ginfo);
        $plan = F('Plan_'.$ginfo['plan']);
        if(empty($plan)){
            $data["statusCode"] = "300";
            $data['msg'] = "参数错误!";
            echo json_encode($data);return false;
        }else{
            //$crm = Partner::getInstance()->crm;
            //$status = \Libs\Service\Quota::quota($plan['id'],$plan['product_id'],$ginfo['cid'],$ginfo['num']);
            if(empty($ginfo['cid'])){
                //限制目前不限制配额
                $data["statusCode"] = "200";
            }else{
                $status = \Libs\Service\Quota::quota($plan['id'],$plan['product_id'],$ginfo['cid'],$ginfo['num']);
                if($status){
                    $data["statusCode"] = "200";
                }else{
                    $data["statusCode"] = "0";
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
    //用户中心
    function uinfo(){
        $uinfo = session('user');
        if(empty($uinfo) || $uinfo['user']['id'] == '2'){
            $this->redirect('Wechat/Index/login');
        }
        dump($uinfo);
        $this->assign('data',$uinfo)->display();
    }
    //账户登录
    function login(){
        if(IS_POST){

        }else{
            $this->wx_init();
            $this->display();
        }
    }
    //我的订单
    function myorder(){

        $this->display();
    }
    //推广
    function promote(){
        $wechat = session('wechat');
        //加密参数
        $uid = \Libs\Util\Encrypt::authcode(session(),'DECODE');
        $image_file = SITE_PATH."d/upload/".'U'.$uid;
        //二维码是否已经生成
        if(!file_exists($image_file)){
            $param = "uid=".$uid."&to=".$wechat['id'];
            $param = \Libs\Util\Encrypt::authcode($param,'ENCODE');
            //构造链接
            $url = U('Wechat/Index/View',array('param'=>$param));  
        }
        $base64_image_content = qr_base64($url,'U'.$uid);
        $this->wx_init();
        $this->assign('qr',$base64_image_content)->display();
    }
    //请人代付
    
    //客户开店
    function kd(){
        $ginfo = I('get.');
        //获取当前用户的id
        $this->redirect(U('Wechat/Index/show',array('to'=>$ginfo['to'],'u'=>1)));
    }
}	