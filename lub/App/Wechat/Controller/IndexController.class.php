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
    }
    function get_ticket()
    {
        $product = array(
            '1'     =>      '过山车',
            '2'     =>      '摩天环车',
            '3'     =>      '冲浪旋艇',
            '4'     =>      '摩天轮',
            '5'     =>      '水陆大战',
            '6'     =>      '欢乐旅行',
            '7'     =>      '疯狂老鼠',
            '8'     =>      '飞椅',
            '9'     =>      '迷你穿梭',
            '10'    =>      '碰碰船',
            '11'    =>      '挖掘机',
            '12'    =>      '逍遥水母',
            '13'    =>      '升降飞机',
            '14'    =>      '体能乐园',
            '15'    =>      '海盗船',
            '16'    =>      '鬼城',
            '17'    =>      '旋风骑士',
            '18'    =>      '弹跳机',
            '19'    =>      '碰碰车',
            '20'    =>      '高空飞翔',
            '21'    =>      '双人飞天',
            '22'    =>      '嘉年华',
            '23'    =>      '超级飞碟',
            '24'    =>      '太空漫步',
            '25'    =>      '激流勇进',
            '26'    =>      '转马',
            '27'    =>      '狂呼',
            '28'    =>      '迪斯科转盘',
            '29'    =>      '大摆锤',
            '30'    =>      '喷球车',
            '31'    =>      '袋鼠跳',
            '32'    =>      '手摇船',
            '33'    =>      '五D影院'
        );
        $bh = rand(1,33);
        return $product[$bh];
    }
    //页面初始化
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
        //判断是否已经在登录
        $this->check_login(U('Wechat/Index/acty',array('pid'=>$this->pid,'u'=>$this->user,'param'=>$this->param)));
        $plan = Wticket::getplan($this->pid);
        $param = array('pid'=>$this->pid);
        $goods_info = array_merge($plan,$user);

        $this->assign('goods_info',json_encode($goods_info))->assign('ginfo',$this->ginfo)->assign('param',$param)->display();
    }
    /**
     * LubTicket 门票单品购买页面
     */
    function show(){
        session('user',null);//TODO  生产环境删除
        //判断用户是否登录   检查session 是否为空
        $user = session('user');
        if(empty($user['user']['openid'])){
           Wticket::tologin($this->ginfo);
           $user = session('user');
        }
        //dump($user);dump($user);dump($user);dump($user);
        //判断是否已经在登录
        $this->check_login(U('Wechat/Index/show',array('pid'=>$this->pid,'u'=>$this->user,'param'=>$this->param)));
        //与数据比对、是否绑定渠道商\
        //根据当前用户属性  加载价格及座位
        $plan = Wticket::getplan($this->pid);
        load_redis('set','goods_info_plan',json_encode($plan));
        $param = array('pid'=>$this->pid);
        $goods_info = array_merge($plan,$user);
        load_redis('set','goods_info',json_encode($goods_info));
        $this->wx_init($this->pid);
        session('pid',$this->pid);
        $urls = Wticket::reg_link($user['user']['id'],$this->pid);
        //限制单笔订单最大数
        $this->assign('goods_info',json_encode($goods_info))->assign('ginfo',$this->ginfo)->assign('uinfo',$user)
            ->assign('urls',$urls)->assign('param',$param)->display();
    }
    function check_login($url){
        $user = session('user');

        if(empty($user['user']['openid']) && !$this->ginfo['code']){
            //session('user',null);
            $oauth = & load_wechat('Oauth',$this->pid,1);
            $urls = $oauth->getOauthRedirect($url, $state, 'snsapi_base');
            //load_redis('set','check_login',date('Y-m-d H:i:s'));
            //header("Location:" . $urls);
            //redirect($urls);
            header('location:'. $urls);
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
        $user = session('user');
        $uid = $user['user']['id'];
        $list = M('Order')->where(array('status'=>array('in','1,9'),'user_id'=>$uid))->field('order_sn,status,createtime,money,plan_id')->limit('10')->select();
        $this->wx_init($this->pid);
        $this->assign('data',$list)->display();
    }
    //推广
    function promote(){
        //加密参数
        $user = session('user');
        $uid = $user['user']['id'];
        $openid = $user['user']['openid'];
        $urls = Wticket::reg_link($uid,$this->pid);
        $base64_image_content = get_up_fxqr($openid);
        $this->wx_init($this->pid);
        $this->assign('qr',$base64_image_content)->assign('urls',$urls)->display();
    }
    //开放注册
    function reg(){
        if(IS_POST){
            $info = json_decode($_POST['info'],true);
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
                'legally' => $info['legally'],
                'groupid' => $info['group'],
                "password" => md5($info['password'].md5($verify)),
                'status' => '3',
            );
            if($info['openid']){
                $user_id = D('User')->add($data);
            }
            if(!empty($user_id)){
                $userdata = D('UserData')->add(array('user_id'=>$user_id,'wechat'=>'1','industry'=>industry($info['industry'],1)));
                $updata = array('user_id'=>$user_id,'channel'=>'1');
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
            //已注册返回
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
            Wticket::tologin($this->ginfo);
            $user = session('user');
            if(!$user['user']['openid']){
                $this->error("授权失败...");
            }
            //查询是否已经绑定
            $this->assign('data',$user['user']['openid'])->assign('type',$status)->display();
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
                
                $sn = \Libs\Service\Order::mobile($info,$uinfo['user']['scene'],$uinfo['user']);
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
            $sn = \Libs\Service\Order::mobile($info,$uinfo['user']['scene'],$uinfo['user']);
            if($sn != false){
               // dump(U('Wechat/Index/order',array('sn'=>$sn,'pid'=>$this->pid)));
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
                    //存储微信支付日志
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
            $status = \Libs\Service\Order::mobile_seat($info,$oinfo);
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
            $status = \Libs\Service\Order::mobile_seat($info,$oinfo);
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
     * 
    */
    function to_tplmsg($info,$product_id){
        $proconf = get_proconf($product_id,2);
        $attach = unserialize($info['attach']);
        $template = array(
            'touser'=>$info['openid'],//指定用户openid
            'template_id'=>$proconf['wx_tplmsg_order_id'],
            'url'   =>  U('Wechat/Index/order_info',array('sn' => $info['out_trade_no'])),
            'topcolor'=>"#7B68EE",
            'data'=>array(
                'first'=>array('value'=>'您好，您的门票订单已预订成功。'."\n"),
                'OrderID' =>array('value'=>$info['out_trade_no'],'color'=>'#5cb85c'),
                'PkgName'=>array('value'=>$attach['product_name'],'color'=>'#5cb85c'),
                'TakeOffDate'=>array('value'=>$attach['plan']."\n",'color'=>'#5cb85c'),
                'remark'=>array('value'=>$proconf['wx_tplmsg_order_remark']), 
            )
        );
        $sndMsg = & load_wechat('Receive',$product_id,1);

        $res = $sndMsg->sendTemplateMessage($template);
        //TODO  回传模板消息发送状态
    }
    /*微信API 相关处理  后期完善
	文本消息
    */
    function _keys($keys){
	    global $wechat;
        error_insert("111微信被动接口验证失败");
	    // 这里直接原样回复给微信(当然你需要根据业务需求来定制的)
	    return $wechat->text($keys)->reply();
	}
	/**
	 * 事件消息
	 * @param  [type] $event [description]
	 * @return [type]        [description]
	 */
	function _event($event) {
	    global $wechat;
	    switch ($event) {
	        // 粉丝关注事件
	        case 'subscribe':
	           return $wechat->text('欢迎关注公众号dd！')->reply();
	        // 粉丝取消关注
	        case 'unsubscribe':
	            exit("success");
	        // 点击微信菜单的链接
	        case 'click': 
	            return $wechat->text('你点了菜单链接！')->reply();
	        // 微信扫码推事件
	        case 'scancode_push':
	        case 'scancode_waitmsg':
	                $scanInfo = $wechat->getRev()->getRevScanInfo();
	                return $wechat->text("你扫码的内容是:{$scanInfo['ScanResult']}")->reply();
	        // 扫码关注公众号事件（一般用来做分销）
	        case 'scan':
	             return $wechat->text('欢迎关注公众号！')->reply();
	    }
	}
	/**
	 * 图片消息
	 * @return [type] [description]
	 
	function _images(){
	    //global $wechat;
	　  //$wechat 中有获取图片的方法
	    //return $wechat->text('您发送了一张图片过来')->reply();
	}*/
	/**
	 * 位置消息
	 */
	function _location(){

	}
	/**
	 * 其它消息
	 */
	function _default(){

	}
	/**
	 * 日志
	 */
	function _logs(){

	}
}