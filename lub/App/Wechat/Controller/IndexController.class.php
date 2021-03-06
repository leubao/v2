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
use Wechat\Service\Wticket;
use \Wechat\WechatReceive;
use Libs\Service\Order;
use Payment\Client\Charge;
class IndexController extends LubTMP {
    /**
     * 微信消息对象
     * @var WechatReceive 
     */
    protected $wechat;
    /**
     * 微信openid
     * @var type 
     */
    protected $openid;
    protected function _initialize() {
        parent::_initialize();
        $this->ginfo = I('get.');
        $this->pid = $this->ginfo['pid'];
        $this->user = $this->ginfo['u'];
        $this->param = $this->ginfo['param'];
        if(empty($this->pid) && empty(session('pid'))){
            $this->error("参数错误");
        }
        if(empty($this->pid)){
            $this->pid = session('pid');
        }else{
            session('pid',$this->pid);
        }
        load_redis('set','userss',serialize(session('user')));
        //加载产品配置信息
        $proconf = get_proconf($this->pid,2);
        $script = &  load_wechat('Script',$this->pid,1);
        //获取JsApi使用签名，通常这里只需要传 $url参数  
        //设置统一分享链接
        $options = $script->getJsSign(U('Wechat/Index/show',array('pid'=>$this->pid,'u'=>$this->user)));
        $this->assign('ginfo',$this->ginfo)->assign('proconf',$proconf)->assign('options',$options);
    }
    /**
     * 微信开发几步走
     * 1、获取微信产品配置信息
     * 2、构建对应信息
     * 微信入口
     */
    function index(){
        //消息回复接口
        $wechat = & load_wechat('Receive',$this->pid,1);
        //dump($wechat);
        /* 验证接口 */
        if ($wechat->valid() === FALSE) {
            // 接口验证错误，记录错误日志
             // log_message('ERROR', "微信被动接口验证失败，{$wechat->errMsg}[{$wechat->errCode}]"); 
            // error_insert($wechat->errMsg.$wechat->errCode);
             // 退出程序
             exit($wechat->errMsg);
        }
        /* 获取粉丝的openid */
        $openid = $wechat->getRev()->getRevFrom();
        /* 记录接口日志，具体方法根据实际需要去完善 */
        // _logs();
        $url = U('Wechat/Index/ticket',array('openid'=>$openid,'pid'=>$this->pid));
        $user = & load_wechat('User',$this->pid,1);
        // 读取微信粉丝列表
        $result = $user->getUserInfo($openid);
        /* 分别执行对应类型的操作*/
        switch ($wechat->getRev()->getRevType()) {
            // 文本类型处理
             case WechatReceive::MSGTYPE_TEXT:
                  $keys = $wechat->getRevContent();
                  $wechat->text($tomsg)->reply();
                  //return _keys($keys);
             // 事件类型处理
             case WechatReceive::MSGTYPE_EVENT:
                    $event = $wechat->getRevEvent();
                    switch (strtolower($event['event'])) {
                        // 粉丝关注事件
                        case 'subscribe':
                            $uinfo = $this->stikcet($result);
                            if($uinfo != false){
                                $tomsg = $result['nickname']."很高兴在茫茫人海中与你相遇!\n我送你一张海潮游乐城<a href='".$url."'>".$uinfo['ticket']."体验劵，点击立即领取吧!</a>";
                            }else{
                                $tomsg = $result['nickname']."欢迎你再次回来.";
                            }
                           //发送购票链接
                           return $wechat->text($tomsg)->reply();
                        // 粉丝取消关注
                        case 'unsubscribe':
                            exit("success");
                    }
                break;
         } 
    }
    function stikcet($result = ''){
         //查询是否已经写入
        $db = D('HcWx');
        $uinfo = $db->where(array('openid'=>$result['openid']))->find();
        if($uinfo){
            return false;
        }else{
            $datas = array(
                'openid'    =>  $result['openid'],
                'nickname'  =>  $result['nickname'],
                'ticket'    =>  $this->get_ticket(),
                'createtime'=>  time(),
                'uptime'    =>  time(),
                'status'    =>  '1',
            );
            $db->add($datas);
            return $datas;
        }
        //构造链接门票
    }
    function ticket(){
        $ginfo = I('get.');
        $db = D('HcWx');
        $uinfo = $db->where(array('openid'=>$ginfo['openid']))->find();
        $user = & load_wechat('User',$this->pid,1);
        // 读取微信粉丝列表
        $result = $user->getUserInfo($ginfo['openid']);
        $this->assign('uinfo',$uinfo)->assign('result',$result)->display();
    }
    //核销
    function check(){
        $ginfo = I('get.');
        $db = D('HcWx');
        $updata = array('status'=>4,'uptime'=>time());
        $uinfo = $db->where(array('openid'=>$ginfo['openid'],'status'=>1))->save($updata);
        if($uinfo){
            $return = array('statusCode'=>200);
        }else{
            $return = array('statusCode'=>300);
        }
        die(json_encode($return));
    }    //页面初始化
    function wx_init($product_id){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $proconf = cache('ProConfig');
        $proconf = $proconf[$product_id][2];
        //微信jssdk 签名包
        $script = & load_wechat('Script',$product_id,1);

        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $jsapi = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 处理执行结果
        if($jsapi===FALSE){
            // 接口失败的处理
            echo $script->errMsg;
        }else{
            // 接口成功的处理
        }

        $this->assign('wechat',$proconf[$product_id][1]);
        $this->assign('jsapi',json_encode($jsapi))->assign('pid',$product_id)->assign('user',session('user'));
    }
    /**
     * @Author   zhoujing   <zhoujing@leubao.com>
     * @DateTime 2017-08-23
     * 厦门活动
     * @return   [type]     [description]
     */
    public function acty()
    {
        session('user',null);
        $user = session('user');
        if(empty($user['user']['openid'])){
           Wticket::xm_tologin($this->ginfo);
           $user = session('user');
        }
        $actid = 25;
        //判断是否已经在登录
        $this->check_login(U('Wechat/Index/acty',array('pid'=>$this->pid,'u'=>$this->user,'actid'=>$actid,'param'=>$this->param)));
        $plan = Wticket::getplan($this->pid);
        $param = array('pid'=>$this->pid);
        $goods_info = array_merge($plan,$user);
        //获取活动
        $info = D('Activity')->where(['id'=>$actid])->find();
        $info['param'] = json_decode($info['param'],true);
        //根据活动类型加载
        switch ($info['type']) {
            case '3':
                $idcard = $info['param']['info']['card'];
                $this->assign('idcard',json_encode($idcard));
                break;
            default:
                break;
        }

        $this->assign('actid',$actid);
        $this->assign('goods_info',json_encode($goods_info))->assign('ginfo',$this->ginfo)->assign('param',$param)->display();
    }
    /**
     * LubTicket 门票单品购买页面
     */
    function show(){
        session('user',null);//TODO  生产环境删除
        //判断用户是否登录   检查session 是否为空
        $user = session('user');
        if(empty($user['user']['openid']) || !empty($this->user)){
           Wticket::tologin($this->ginfo);
           $user = session('user');
        }
       // dump($user);dump($user);dump($user);dump($user);dump($this->ginfo);
        //判断是否已经在登录
        $this->check_login(U('Wechat/Index/show',array('pid'=>$this->pid,'u'=>$this->user,'param'=>$this->param)));
        //与数据比对、是否绑定渠道商\
        //根据当前用户属性  加载价格及座位
        $plan = Wticket::getplan($this->pid);
        $param = array('pid'=>$this->pid);
        $goods_info = array_merge($plan,$user);//dump($goods_info);
        $this->wx_init($this->pid);
        session('pid',$this->pid);
        $urls = Wticket::reg_link($user['user']['id'],$this->pid);
        $produt = D('Product')->where(['id'=>$this->pid])->cache(true)->field('type')->find();
        if($produt['type'] == 1){
            $template = 'show';
        }else{
            $template = 'scenic';
        }
        //限制单笔订单最大数
        $this->assign('goods_info',json_encode($goods_info))->assign('ginfo',$this->ginfo)->assign('uinfo',$user)
            ->assign('urls',$urls)->assign('param',$param)->display($template);
    }
    function check_login($url){
        $ginfo = I('get.');
        $user = session('user');
        if(empty($user['user']['openid']) && !isset($ginfo['code'])){
            //session('user',null);http://act.leubao.com/index.php?g=wechat&m=activity&a=act&act=131159&pid=67&u=
            $oauth = & load_wechat('Oauth',$ginfo['pid'],1);
            $urls = $oauth->getOauthRedirect($url, $state, 'snsapi_base');
            load_redis('set','check_login',date('Y-m-d H:i:s'));
            header('location:'. $urls);
        }elseif(empty($user['user']['openid'])){
            header('location:'. $url);
        }
    }
    /**
     * 订单详情
     */
    function order_info(){
        $info = D('Item/Order')->where(array('order_sn'=>$this->ginfo['sn']))->relation(true)->find();
        $info['info'] = unserialize($info['info']);
        //区域分类
        foreach ($info['info']['data'] as $key => $value) {
            $area[$value['areaId']]['area'] = $value['areaId'];
            $area[$value['areaId']]['num'] = $area[$value['areaId']]['num']+1;
        }
        $this->wx_init($this->pid);
        $this->assign('data',$info);
        $this->assign('area',$area);
        $this->display();
    }
    //微信支付成功通知
    function pay_notice(){
        $this->wx_init($this->pid);
        $this->assign('data',$info)->display();
    }
    //用户中心
    function uinfo(){
        
        $user = session('user');
        load_redis('set','user_wx',serialize($user));
        if(empty($user) || $user['user']['id'] == '2'){
            $this->redirect('Wechat/Index/login');
        }

        $this->check_login(U('Wechat/index/uinfo',array('pid'=>$this->pid,'u'=>$this->user)));
        
        $uid = $user['user']['id'];
        $info = M('User')->where(array('id'=>$uid))->field('id,nickname,cash,is_scene,type')->find();
        $this->wx_init($this->pid);
        $this->assign('data',$info)->display();
    }
    //我的订单
    function orderlist(){
        //加密参数
        $ginfo = I('get.');
        $url = U('Wechat/index/orderlist',['pid'=>$ginfo['pid']]);
        session('user',null);//TODO  生产环境删除
        
        //判断用户是否登录   检查session 是否为空
        $user = session('user');
        if(empty($user['user']['openid']) || !empty($this->user)){
           Wticket::tologin($this->ginfo);//dump($this->ginfo);
           $user = session('user');
        }
        
        $this->check_login($url);

        $uid = $user['user']['id'];
        if($uid <> 2){
           $list = M('Order')->where(array('status'=>array('in','1,9'),'user_id'=>$uid))->field('order_sn,status,createtime,money,plan_id,number')->limit('50')->order('createtime DESC')->select();//dump($list);
           $this->wx_init($this->pid); 
        }
        
        $this->assign('user', $user)->assign('data',$list)->display();
    }
    //推广
    function promote(){
        //加密参数
        $user = session('user');
        $uid = $user['user']['id'];
        $openid = $user['user']['openid'];
        $urls = Wticket::reg_link($uid,$this->pid);
        $base64_image_content = get_up_fxqr($openid,$this->pid);
        $this->wx_init($this->pid);
        $this->assign('qr',$base64_image_content)->assign('urls',$urls)->display();
    }
    //开放注册
    function reg(){
        if(IS_POST){
            $info = json_decode($_POST['info'],true);
            //校验验证码
            $code = load_redis('get','phone_'.$info['phone']);
            if($code <> $info['code']){
                $return = array(
                    'statusCode' => 300,
                    'msg' => $code.'验证码有误...'.$info['code'],
                ); 
                die(json_encode($return));
            }
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
                "type"  =>  $this->ginfo['type'] ? $this->ginfo['type'] : '1', //推广
                "cid"    => '0',
                "verify" => $verify,
                'phone' => $info['phone'],
                'email'  => '0',
                'role_id' => '0',
                'legally' => isset($info['legally']) ? $info['legally'] : 0,
                'groupid' => $info['group'],
                "password" => md5($info['password'].md5($verify)),
                'status' => '3',
            );
            if($info['openid']){
                $user_id = D('User')->add($data);
            }else{
                $return = array(
                    'statusCode' => 300,
                    'msg' => '获取授权失败,请退出重试...',
                );
                die(json_encode($return));
            }
            if(!empty($user_id)){
                $userdata = D('UserData')->add(array('user_id'=>$user_id,'wechat'=>'1','industry'=>industry($info['industry'],1)));
                $updata = array('user_id'=>$user_id);
                D('WxMember')->where(array('openid'=>$info['openid']))->save($updata);
                session('user',null);
                $url = Wticket::reg_link($user_id,$this->pid);
                $return = array('statusCode' => 200,'url' => $url); 
            }else{
                $return = array(
                    'statusCode' => 300,
                    'msg' => '注册失败...',
                );  
            }
            die(json_encode($return));
        }else{
            //判断是否已注册
            /*已注册返回*/
            Wticket::tologin($this->ginfo);
            $user = session('user');
            if(!$user['user']['openid']){
                $this->error("授权失败...");
            }
            switch (Wticket::is_reg($user['user']['openid'])) {
                case '400':
                    $this->wx_init($this->pid);
                    $url = Wticket::reg_link($user['user']['id'],$this->pid);
                    $this->assign('data',$user)->assign('url',$url)->assign('type',$this->ginfo['type'])->display();
                    break;
                case '300':
                    $this->error("您已经注册,正在等待审核");
                    break;
                case '200':
                    //调用登录
                    //
                    $this->redirect('Wechat/Index/uinfo');
                    break;
            }
        }
    }
    
    //注册二维码
    function reg_code()
    {
        $user = session('user');
        $uid = $user['user']['id'];
        if(empty($uid) || $uid == '2'){
            $this->error("您还没有登录",U('Wechat/index/login'));
        }
       // $image_file = SITE_PATH."d/upload/".'fxregu-'.$uid;
        $callback = U('Wechat/Index/reg',array('u'=>$uid,'type'=>$user['user']['fx'],'pid'=>$this->pid));
        // SDK实例对象
        $oauth = & load_wechat('Oauth',$this->pid,1);
        // 执行接口操作
        $urls = $oauth->getOauthRedirect($callback, 'alizhiyou', 'snsapi_userinfo');
        $base64_image_content = qr_base64($urls,'fxregu-'.$uid);
        $this->wx_init($this->pid);
        $this->assign('qr',$base64_image_content)->assign('urls',$urls)->display();
    }
    //微信框架页面，用于扫描后
    function view(){
        //跳转购买页面
        $url = U('Wechat/Index/show',array('u'=>$ginfo['u']));
        $urls = $this->api->get_authorize_url('snsapi_base',$url);
        $this->wx_init();
        $this->assign('url',$urls)->display();
    }
    //手机号验证
    function phone(){
        if(empty($this->ginfo)){
            $return = array(
                'statusCode' => 300
            ); 
            echo json_encode($return);
            exit; 
        }
        $db = M('User');
        $phone = $db->where(array('phone'=>$this->ginfo['phone']))->find();
        if($phone){
            $return = array('statusCode' => 300,'msg'=>'手机号已被注册...'); 
        }else{
            $legally = $db->where(array('phone'=>$this->ginfo['legally']))->find();
            if($legally){
               $return = array('statusCode' => 300,'msg'=>'导游证号已被注册...'); 
            }else{
               $return = array('statusCode' => 200);  
           }
        }
        die(json_encode($return));
    }
    //账号登录
    function login()
    {   
        if(IS_POST){
            $pinfo = $_POST;
            $pinfo = json_decode($pinfo['info'],true);
            if(empty($pinfo)){
                $return = array(
                    'statusCode' => 300,
                    'msg'        => '登录信息不能为空'
                ); 
                die(json_encode($return)); 
            }            
            $db = D('User');//
            $map = array('username'=>$pinfo['username'],'status'=>array('in','1,3'));
            $info = $db->where($map)->field('id,username,password,verify')->find();
            if(!$info){
                $return = array('statusCode' => 300,'msg' => '用户名或密码错误'); 
                die(json_encode($return)); 
            }else{
                $password = md5($pinfo['password'].md5($info['verify']));
                if (!empty($pinfo['password']) && $password != $info['password']) {
                    $return = array('statusCode' => 300,'msg' => '用户名或密码错误'); 
                    die(json_encode($return)); 
                }
                $uinfo = Wticket::get_auto_auth('','',$info['id']);
                if($uinfo){
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
                        case '5':
                            $type = 9;/*全员销售*/
                            $pay = '5';/*支持微信支付*/
                            $scene = '49';
                            break;
                    }
                    $user['user'] = array(
                        'id'      => $uinfo['id'],
                        'openid'  => $uinfo['wechat']['openid'],
                        'nickname'=> $uinfo['nickname'],
                        'maxnum'  => '30',
                        'guide'   => $uinfo['id'],
                        'qditem'  => $uinfo['cid'] ? $uinfo['cid']:'0',
                        'scene'   => $scene,
                        'channel' => '1',
                        'epay'    => $uinfo['group']['settlement'],
                        'pricegroup'=> $uinfo['group']['price_group'],
                        'wxid'      => $uinfo['wechat']['user_id'],//微信id
                        'fx'        => $uinfo['type'],
                        'promote'   => $uinfo['promote']
                    );
                    session('user',$user);
                    $return = array('statusCode' => 200,'msg' => '登录成功','url'=>U('Wechat/Index/show',array('pid'=>$this->pid,'u'=>$uinfo['id'])));
                }else{
                    $return = array('statusCode' => 300,'msg' => '登录失败,请联系管理员');
                }
                die(json_encode($return)); 
            }
        }else{
            $user = session('user');
            if(empty($user) || $user['user']['id'] == '2'){
                session('openid',$user['openid']);
                session('user',null);$this->display();
            }else{
                $this->redirect('Wechat/Index/uinfo');
            }
        }
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
            $this->wx_init($this->pid);
            $this->assign('money',$money)->display();
        }
    }
    //提现记录
    function mention_list(){
        $user = session('user');
        //获取当前用户可提金额
        $uid = $user['user']['id'];
        $data = M('Cash')->where(array('user_id'=>$uid))->field('sn,money,remark,status,createtime')->select();
        $this->wx_init($this->pid);
        $this->assign('data',$data)->display();
    }
    //支付成功提示页面
    function pay_success(){
        $sn = I('sn');
        $this->wx_init($this->pid);
        $this->assign('sn',$sn)->display();
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
                $updata = array('user_id'=>$uinfo['id'], 'channel'=>'1');
                if(M('WxMember')->where(array('openid'=>$pinfo['openid']))->save($updata)){
                    //更新用户微信状态
                    M('UserData')->where(array('user_id'=>$uinfo['id']))->setField('wechat',1);
                    //注销登录
                    session('user',null);
                    $return = array(
                        'statusCode' => 200,
                        'url' => U('Wechat/Index/uinfo',array('pid'=>$this->pid))
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
            Wticket::tologin($this->ginfo);
            $user = session('user');
            if(empty($user['user']['openid'])){
                $this->error("授权失败...");
            }
            $state = M('WxMember')->where(array('user_id'=>$user['user']['id'],'channel'=>1))->find();
            if($state){
                $this->error('已完成绑定请勿重复操作~', U('Wechat/Index/uinfo',array('pid'=>$this->pid)));
            }
            //查询是否已经绑定
            $this->assign('openid',$user['user']['openid'])->assign('type',$status)->display();
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
    public function check_card($card)
    {
        $map = [
            'createtime' => array('EGT', strtotime('2017-08-22')),
            'status'    =>  ['in','1,9'],
            'id_card'   =>  $card,
            'activity'  =>  '1'
        ];
        $count = D('Order')->where($map)->count();
        if($count == '0'){
            return true;
        }else{
            return false;
        }
    }
    public function act_order()
    {
        if(IS_POST){
            //创建订单
            $info = $_POST['info'];
            $pinfo = json_decode($info,true);
            //dump($pinfo);
            //判断身份证是否有使用过
            if($this->check_card($pinfo['info']['param'][0]['id_card'])){
                //判断数据的完整性
                $uinfo = session('user');
                $order = new Order();
                $sn = $order->mobile($info,$uinfo['user']['scene'],$uinfo['user']);
                if($sn != false){
                   // dump(U('Wechat/Index/order',array('sn'=>$sn,'pid'=>$this->pid)));
                    $return = array(
                        'statusCode' => 200,
                        'url' => U('Wechat/Index/order',array('pid'=>$this->pid,'sn'=>$sn)),
                    ); 
                }else{
                    $return = array(
                        'statusCode' => 300,
                        'msg' => '订单创建失败',
                    );  
                }
            }else{
                $return = array(
                        'statusCode' => 300,
                        'msg' => '该身份证已参加活动,不能重复参与',
                    );
            }

            die(json_encode($return));
        }else{
            $info = D('Item/Order')->where(array('order_sn'=>$this->ginfo['sn']))->relation(true)->find();
            $info['info'] = unserialize($info['info']);
            $this->wx_init($this->pid);
            if(in_array($info['status'],array('1','9'))){
                $this->error("订单状态不允许此项操纵");
            }else{
                //根据订单属性判断是否需要加载微信支付 散客和政企通过微信购票
                if($info['type'] == '1' || $info['type'] == '6' || $info['type'] == '8'){
                    $user = session('user');
                    if(empty($user)){
                       $user = Wticket::tologin($this->ginfo);
                    }
                    // 获取预支付ID
                    if($info['money'] == '0'){
                       $money = 0.1*100;
                    }else{
                       $money = $info['money']*100; 
                    }
                    //$money = 1;
                    $proconf = cache('ProConfig');
                    $proconf = $proconf[$this->pid][2];
                    $notify_url = $proconf['wx_url'].'index.php/Wechat/Notify/notify.html';
                    //产品名称
                    $product_name = product_name($this->pid,1);
                    $pay = & load_wechat('Pay',$this->pid);
                    $prepayid = $pay->getPrepayId($user['user']['openid'], $product_name, $info['order_sn'], $money, $notify_url, $trade_type = "JSAPI",'',1);
                    if($prepayid){
                        $options = $pay->createMchPay($prepayid);
                        //dump($options);
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
    //订单确认
    function order(){
        if(IS_POST){
            //创建订单
            $info = $_POST['info'];
            //判断数据的完整性
            $uinfo = session('user');
            $order = new Order();
            $sn = $order->mobile($info,$uinfo['user']['scene'],$uinfo['user']);
            if($sn != false){
                $return = array(
                    'statusCode' => 200,
                    'url' => U('Wechat/Index/order',array('pid'=>$this->pid,'sn'=>$sn)),
                ); 
            }else{
                $return = array(
                    'statusCode' => 300,
                    'url' => '',
                );  
            }
            die(json_encode($return));
        }else{
            $info = D('Item/Order')->where(array('order_sn'=>$this->ginfo['sn']))->relation(true)->find();
            $info['info'] = unserialize($info['info']);
            $this->wx_init($this->pid);
            if(in_array($info['status'],array('1','9'))){
                $this->error("订单状态不允许此项操纵");
            }else{
                //根据订单属性判断是否需要加载微信支付 散客和政企通过微信购票
                if($info['type'] == '1' || $info['type'] == '6' || $info['type'] == '8'){
                    $user = session('user');
                    if(empty($user)){
                       $user = Wticket::tologin($this->ginfo);
                    }
                    // 获取预支付ID
                    if($info['money'] == '0'){
                       $money = 0.1*100;
                    }else{
                       $money = $info['money']*100; 
                    }
                    //$money = 1;
                    $proconf = cache('ProConfig');
                    $proconf = $proconf[$this->pid][2];
                    $notify_url = $proconf['wx_url'].'index.php/Wechat/Notify/notify.html';
                    //产品名称
                    $product_name = product_name($this->pid,1);
                    $pay = & load_wechat('Pay',$this->pid);
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
    //景区订单
    function scenic_order(){
        if(IS_POST){
            //创建订单
            $info = $_POST['info'];
            //判断数据的完整性
            $uinfo = session('user');
            $order = new Order();
            $sn = $order->mobile($info,$uinfo['user']['scene'],$uinfo['user']);
            dump($order);
            if($sn != false){
                $return = array(
                    'statusCode' => 200,
                    'url' => U('Wechat/Index/scenic_order',array('pid'=>$this->pid,'sn'=>$sn)),
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
            $this->wx_init($this->pid);
            if(in_array($info['status'],array('1','9'))){
                $this->error("订单状态不允许此项操纵");
            }else{
                //根据订单属性判断是否需要加载微信支付 散客和政企通过微信购票
                if($info['type'] == '1' || $info['type'] == '6' || $info['type'] == '8'){
                    $user = session('user');
                    if(empty($user)){
                       $user = Wticket::tologin($this->ginfo);
                    }
                    // 获取预支付ID
                    if($info['money'] == '0'){
                       $money = 0.1*100;
                    }else{
                       $money = $info['money']*100; 
                    }
                    //$money = 1;
                    $proconf = cache('ProConfig');
                    $proconf = $proconf[$this->pid][2];
                    $notify_url = $proconf['wx_url'].'index.php/Wechat/Notify/notify.html';
                    //产品名称
                    $product_name = product_name($this->pid, 1);
                    $pay = & load_wechat('Pay', $this->pid);//dump($pay);dump($user);
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
    //授信额支付
    public function pay(){
        if(IS_POST){
            $info = $_POST['info'];
            $info = json_decode($info,true);
            //渠道商  支付且开始排座
            $oinfo = D('Item/Order')->where(array('order_sn'=>$info['sn']))->relation(true)->find();
            if(empty($info) || empty($oinfo)){echo json_encode(array('statusCode' => '300','msg' => $oinfo));return false;}
            $order = new Order();
            $status = $order->mobile_seat($info,$oinfo);
            $product_name = product_name($oinfo['product_id'],1);
           // 支付成功，发送模板消息
            if($status != false){
                //构造模板消息
                $user = session('user');
                $attach =  array(
                    'number'=>$oinfo['number'],
                    'product_name'=>$product_name,
                    'plan'=> planShow($oinfo['plan_id'],4,1),
                );
                $openid = $user['user']['openid'];
                $result = array(
                    'openid' => $openid,
                    'out_trade_no' => $info['sn'],
                    'attach' => serialize($attach),
                );
                $this->to_tplmsg($result,$oinfo['product_id']);
                $return = array(
                    'statusCode' => 200,
                    'url' => U('Wechat/Index/pay_success',array('sn'=>$sn,'pid'=>$this->pid)),
                ); 
            }else{
                $return = array(
                    'statusCode' => 300,
                    'msg' => $status.'9',
                );  
            }
            die(json_encode($return));
        }
    }
    /*请人代付*/
    function dfpay(){
        //构造支付连接
        $url = U('Wechat/Index/order',array('sn'=>$this->ginfo['sn'],'pid'=>$this->pid,'param'=>$this->param));
        // SDK实例对象
        $oauth = & load_wechat('Oauth',$this->pid,1);
        // 执行接口操作
        $urls = $oauth->getOauthRedirect($url, 'alizhiyou', 'snsapi_base');
        //生成支付二维码
        $qr = qr_base64($urls,$this->ginfo['sn']);
        $this->wx_init($this->pid);
        $this->assign('qr',$qr)->assign('url',$urls)->display();
    }
    /*政企客户更新支付方式*/
    function window_pay(){
        if(IS_POST){
            $info = $_POST['info'];
            $info = json_decode($info,true);
            //渠道商  支付且开始排座
            $oinfo = D('Item/Order')->where(array('order_sn'=>$info['sn']))->relation(true)->find();
            if(empty($info) || empty($oinfo)){die(json_encode(array('statusCode' => '300','msg' => "订单获取失败")));return false;}
            $order = new Order();
            $status = $order->mobile_seat($info,$oinfo);
            // 支付成功，发送模板消息
            if($status != false){
                //构造模板消息
                $user = session('user');
                $product_name = product_name($oinfo['product_id'],1);
                $attach =  array(
                    'number'=>$oinfo['number'],
                    'product_name'=>$product_name,
                    'plan'=> planShow($oinfo['plan_id'],4,1),
                );
                $openid = $user['user']['openid'];
                $result = array(
                    'openid' => $openid,
                    'out_trade_no' => $info['sn'],
                    'attach' => serialize($attach)
                );
                $this->to_tplmsg($result,$oinfo['product_id']);
                $return = array(
                    'statusCode' => 200,
                    'url' => U('Wechat/Index/pay_success',array('sn'=>$sn,'pid'=>$this->pid)),
                ); 
            }else{
                $return = array('statusCode' => 300,'msg' => "订单状态不允许此项操作");
            }
            die(json_encode($return));
        }
    }
        //解除绑定
    function remove(){
        //输入密码
        //解除绑定
        if(IS_POST){
            $pinfo = json_decode($_POST['info'],true);
            $user = session('user');
            $map = array('id'=>$user['user']['id'],'wechat'=>'1');
            //读取用户信息
            $uinfo = M('User')->where($map)->find();
            $pwd = md5($pinfo['password'].md5($uinfo['verify']));
            //验证用户密码
            if($uinfo['password'] == $pwd){
                //删除用户授权信息
                $updata = array('user_id'=>'0','channel'=>'0');
                D('WxMember')->where(array('openid'=>$info['openid']))->save($updata);
                //停用用户表
                $status = D('User')->where(array('id'=>$user['user']['id']))->setField('status','3');
                if($status){
                    session('user',null);
                    $url = U('Wechat/Index/show',array('pid'=>$this->pid));
                    // SDK实例对象
                    $oauth = & load_wechat('Oauth',$this->pid,1);
                    // 执行接口操作
                    $urls = $oauth->getOauthRedirect($url, 'alizhiyou', 'snsapi_base');
                    $return = array(
                        'statusCode' => 200,
                        'url' => $urls,
                    ); 
                }else{
                    $return = array(
                        'statusCode' => 300,
                        'msg' => '注销失败...',
                    );  
                }
            }else{
                $return = array(
                    'statusCode' => 300,
                    'msg' => '密码验证失败...',
                );  
            }
            
            die(json_encode($return));
        }else{
            $this->display();
        }
    }
    /**发送模板消息
     * {{first.DATA}}
     * 订单号：{{OrderID.DATA}}
     * 产品名称：{{PkgName.DATA}}
     * 使用日期：{{TakeOffDate.DATA}}
     * {{remark.DATA}}
     *      * {
     *      "touser":"OPENID",
     *       "template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
     *       "url":"http://weixin.qq.com/download",
     *       "topcolor":"#FF0000",
     *       "data":{
     *           "参数名1": {
     *           "value":"参数",
     *           "color":"#173177"     //参数颜色
     *       },
     *       "Date":{
     *           "value":"06月07日 19时24分",
     *           "color":"#173177"
     *       },
     *       "CardNumber":{
     *           "value":"0426",
     *           "color":"#173177"
     *      },
     *      "Type":{
     *          "value":"消费",
     *          "color":"#173177"
     *       }
     *   }
     * }
    */
    function to_tplmsg($info,$product_id){
        $proconf = get_proconf($product_id,2);
        $attach = unserialize($info['attach']);
        $template = array(
            'touser'=>$info['openid'],//指定用户openid
            'template_id'=>'8W2t7l0loiAdTl7l0U7OWb7qZC-keLqi1FRuRQYkNtI',
            'url'   =>  U('Api/Index/ticket',array('sn' => $info['out_trade_no'])),
            'topcolor'=>"#7B68EE",
            'data'=>array(
                'first'=>array('value'=>'您好，您的门票订单已预订成功。'."\n"),
                'keyword1' =>array('value'=>$info['out_trade_no'],'color'=>'#5cb85c'),
                'keyword2' =>array('value'=>$attach['product_name'],'color'=>'#5cb85c'),
                'keyword3' =>array('value'=>$info['out_trade_no'],'color'=>'#5cb85c'),
                'keyword4' =>array('value'=>$info['number'],'color'=>'#5cb85c'),
                'keyword5'=>array('value'=>$attach['plan']."\n",'color'=>'#5cb85c'),
                'remark'=>array('value'=>'查看详情'), 
            )
        );
        $sndMsg = & load_wechat('Receive',$product_id,1);
        $res = $sndMsg->sendTemplateMessage($template);
        //TODO  回传模板消息发送状态
    }
    public function scenic()
    {
        session('user',null);//TODO  生产环境删除
        //判断用户是否登录   检查session 是否为空
        $user = session('user');
        if(empty($user['user']['openid'])){
           Wticket::tologin($this->ginfo);
           $user = session('user');
        }
        //判断是否已经在登录
        $this->check_login(U('Wechat/Index/scenic',array('pid'=>$this->pid,'u'=>$this->user,'param'=>$this->param)));
        //与数据比对、是否绑定渠道商\
        //根据当前用户属性  加载价格及座位
        $plan = Wticket::getplan($this->pid);
        $param = array('pid'=>$this->pid);
        $goods_info = array_merge($plan,$user);
        //load_redis('set','goods_info',json_encode($goods_info));
        $this->wx_init($this->pid);
        session('pid',$this->pid);
        $urls = Wticket::reg_link($user['user']['id'],$this->pid);
        //限制单笔订单最大数
        $this->assign('goods_info',json_encode($goods_info))->assign('ginfo',$this->ginfo)->assign('uinfo',$user)
            ->assign('urls',$urls)->assign('param',$param)->display();
    }
    //电子门票
    function scenic_ticket()
    {
        $info = D('Item/Order')->where(array('order_sn'=>$this->ginfo['sn'],'product_id'=>44))->relation(true)->find();
        $info['info'] = unserialize($info['info']);
        //区域分类
        foreach ($info['info']['data'] as $key => $value) {
            $area[$value['areaId']]['area'] = $value['areaId'];
            $area[$value['areaId']]['num'] = $area[$value['areaId']]['num']+1;
        }
        $this->wx_init($this->pid);
        $this->assign('data',$info);
        $this->display();
    }
    //核销电子票
    function check_ticket(){
        //出票过程
        $pinfo = I('get.');
        $updata = ['status'=>9,'uptime'=>time(),'is_print'=>1];
        $status = D('Order')->where(['order_sn'=>$pinfo['sn'],'status'=>1])->setField($updata);
        if($status){
            $return = array('statusCode'=>200);
        }else{
            $return = array('statusCode'=>300);
        }
        die(json_encode($return)); 
    }
    public function coupons()
    {
        //判断是否登录
        $user = session('user');
        if(empty($user['user']['openid']) || !empty($this->user)){
           Wticket::tologin($this->ginfo);
           $user = session('user');
        }
        //判断是否已经在登录
        $this->check_login(U('Wechat/Index/coupons',array('pid'=>$this->pid,'u'=>$this->user,'param'=>$this->param)));
        //dump($user);
        //判断是否通过审核
        if($user['user']['id'] > 2){
            //判断是否已经有领取
            $where = [
                'user_id' => $user['user']['id']
            ];
            $info = D('Coupons')->where($where)->find();
            if(empty($info)){
                $param =  [
                        'effective' => [
                            'start' =>  '',
                            'end'   =>  '',
                        ],
                        'ticket' => [
                            'ticket'    =>  6,//
                            'price'     =>  '68',
                            'discount'  =>  '0'
                        ]
                    ];
                $add = [
                    'product_id'=>  $this->pid,
                    'user_id'   =>  $user['user']['id'],
                    'param'     =>  json_encode($param),
                    'number'    =>  5,
                    'create_time'=> time(),
                    'update_time'=> time(),
                    'status' => 1
                ];
                D('Coupons')->add($add);
                $info = D('Coupons')->where($where)->find();
            }
            if($info['number'] > 0 && $info['status']){
                $info['param'] = json_decode($info['param']);
                $this->assign('info',$info);
            }
        }
        $this->assign('user',$user['user'])->display();
    }
    public function useticket()
    {
        if(IS_POST){
            //创建订单
            $pinfo = json_decode($_POST['info'],true);
            $uinfo = session('user');
            $count = D('Coupons')->where(['user_id'=>$uinfo['user']['id']])->getField('number');
            if($count > 0){
               //读取销售计划
                $plan = D('Plan')->where(['plantime'=>strtotime($pinfo['plan_id']),'status'=>2])->getField('id');
                if(empty($plan)){
                    $return = array(
                        'statusCode' => 300,
                        'url' => '',
                        'msg' => '未找到销售计划'
                    );
                    die(json_encode($return));
                }
                $pinfo['plan_id'] = $plan;
                $info = json_encode($pinfo);
                //判断数据的完整性
                
                $order = new Order();
                $sn = $order->mobile($info,$uinfo['user']['scene'],$uinfo['user'],1);
                
                if($sn != false){
                    D('Coupons')->where(['user_id'=>$uinfo['user']['id']])->setDec('number',$pinfo['number']);
                    $return = array(
                        'statusCode' => 200,
                        'url' => U('Wechat/Index/pay_success',array('sn'=>$sn['order_sn'],'pid'=>$this->pid)),
                    ); 
                }else{
                    $return = array(
                        'statusCode' => 300,
                        'url' => '',
                        'msg' => $order->error
                    );  
                }
                die(json_encode($return)); 
            }else{
                
                $return = array(
                    'statusCode' => 300,
                    'url' => U('Wechat/Index/coupons',array('sn'=>$sn,'pid'=>$this->pid)),
                    'msg' => '体验券已使用'
                );
                die(json_encode($return));
                
            }
            
        }else{
            $user = session('user');
            if(empty($user) ){
               $user = Wticket::tologin($this->ginfo);
            }
            $ginfo = I('get.');
            $where = [
                'id'    =>  $ginfo['id']
            ];
            $goods_info = $user;
            $info = D('Coupons')->where($where)->find();
            $info['param'] = json_decode($info['param'],true);
            $this->assign('info',$info)
                ->assign('date',date('Y-m-d'))
                ->assign('mindate',date("Y-m-d",strtotime("-1 day")))
                ->assign('goods_info',json_encode($goods_info))
                ->display();
        }
        

    }
    //短信验证码
    public function send_code()
    {
        if(empty($this->ginfo)){
            $return = array(
                'statusCode' => 300
            );
            echo json_encode($return);
            exit; 
        }
        $db = M('User');
        $phone = $db->where(array('phone'=>$this->ginfo['phone']))->find();
        if($phone){
            $return = array('statusCode' => 300,'msg'=>'手机号已被注册...'); 
            die(json_encode($return));
        }
        //生成验证码
        $code = load_redis('get','phone_'.$this->ginfo['phone']);
        if(empty($code)){
            $code = genRandomString(4,'1');
            load_redis('setex','phone_'.$this->ginfo['phone'],$code,1800);
            //发送短信
            $info = [
                'phone' =>  $this->ginfo['phone'],
                'code'  =>  $code
            ];
            \Libs\Service\Sms::order_msg($info,'12');
        }

        $return = array(
            'statusCode' => 200,
            'msg'   =>  'ok'
        ); 
        echo json_encode($return);
    }
    public function map()
    {
        $this->display();
    }
    //新订单列表
    public function olist()
    {
        $user = session('user');
        $uid = $user['user']['id'];
        if($uid <> 2){
           $list = M('Order')->where(array('status'=>array('in','1,9'),'user_id'=>$uid))->field('order_sn,status,createtime,money,plan_id,number')->limit('30')->order('createtime DESC')->select();
           $this->wx_init($this->pid); 
        }
        
        $this->assign('data',$list)->display();
    }
    public function test_pay()
    {
        $user = session('user');
        if(empty($user['user']['openid']) || !empty($this->user)){
            Wticket::tologin($zinfo);
            $user = session('user');
        }
        //var_dump(U('Wechat/Index/test_pay',array('pid'=>I('get.pid'),'u'=>'')));
        //$this->check_login(U('Wechat/Index/test_pay',array('pid'=>I('get.pid'),'u'=>'')));
        //同步分账信息
        $settleInfo = [[
            'ledgerNo'  =>  '10033855673',//分账方编号
            'ledgerName'=>  '武夷山市天空之镜旅游管理有限公司',//分账方名称
            'amount'    =>  '0.01',//分账金额
            //'proportion'=>  ,//比例分账的比例，所有分账比例累加不能超过 1（100%） 0.23(含义：23%)
        ],
        ['ledgerNo'  =>  '10033677648',//分账方编号
        'ledgerName'=>  '武夷山市安畅旅游开发有限公司',//分账方名称
        'amount'    =>  '0.01',//分账金额
        ]];
        //异步分账
        // $a = [
        //     'sn'            =>  session_create_id(),//商户订单号
        //     'mch_id'        =>  '10033843358',//商户编号
        //     'uniqueOrderNo' =>  '',//要分账订单的易宝流水号
        //     'request_no'    =>  $serial_no,//本次流水号
        //     'settle_info'   =>  [],//要分账的信息，只支持按金额分账
        //     'is_thaw_amount'=>  TRUE,//是否解冻收单商户剩余可用金额 可选TRUE、FALSE 默认TRUE
        //     'notify_url'    =>  'http://cby.leubao.com/api.php/test/notify',//分账回调地址
        // ];
        $data = [
            'out_trade_no'  =>  session_create_id(),//商户订单号
            'mch_id'        =>  '10033843358',//商户编号
            'appid'         =>  'wxde733cb2b0c68a36',//公众号appid
            'total'         =>  '0.04',//金额
            'openid'        =>  $user['user']['openid'],//opneid
            'profit_sharing'=>  'REAL_TIME',//'REAL_TIME_DIVIDE',//资金处理类型DELAY_SETTLE("延迟结算"),REAL_TIME("实时订单");REAL_TIME_DIVIDE（” 实时 分账” ）SPLIT_ACCOUNT_IN("实时拆分入账");
            'settle_info'   =>  [
                'divideDetail'      =>  [],//$settleInfo,//'',//json_encode($settleInfo),
                'divideNotifyUrl'   =>  'http://cby.leubao.com/api.php/test/notify',
            ],//结算信息
            'title'         =>  '测试支付',//商品标题
            'description'   =>  '测试支付',//商品描述
            'client_ip'     =>  get_client_ip(),//客户端IP
            'notify_url'    =>  'http://cby.leubao.com/api.php/test/notify',
            'time_stamp'     =>  time(),
            'nonce_str'     =>  genRandomString(8,1),//随机字符串
            'sign'          =>  ''
        ];
        $key = 'WhvUMxDgYjufJcdez6oGw1RXT9i8tQrs';
        $data['sign'] = \Libs\Service\ArrayUtil::setPaymentSign($data, $key);
        $url = 'https://api.pay.xzusoft.cn/pay/gopay';
        if(!empty($user)){
            $res = json_decode(getHttpContent($url, 'POST', $data), true);
            var_dump($data,$res);
            $this->assign('data', $res['data']);
        }
        
        $this->display();
    }
}