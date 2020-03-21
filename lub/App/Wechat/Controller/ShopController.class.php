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
class ShopController extends LubTMP {

    protected function _initialize() {
        parent::_initialize();
        $this->ginfo = I('get.');
        
        $this->user = session('user');
        $this->param = $this->ginfo['param'];
        session('pid', $this->ginfo['pid']);
        //加载产品配置信息
        $proconf = get_proconf($this->ginfo['pid'],2);
        // $script = & load_wechat('Script',$this->ginfo['pid'],1);
        // //获取JsApi使用签名，通常这里只需要传 $url参数  
        // //设置统一分享链接
        // $options = $script->getJsSign(U('Wechat/Shop/special',['pid'=>$this->ginfo['pid'],'tid'=>$this->ginfo['tid'],'u'=>$this->ginfo['u']]));

        $this->assign('ginfo', $this->ginfo)->assign('proconf',$proconf)->assign('options',$options);

    }
    public function index()
    {
        $user = session('user');
        if(empty($user['user']['openid']) || !empty($this->user)){
            $zinfo['pid'] = $this->ginfo['pid'];
            Wticket::tologin($zinfo);
            $user = session('user');
        }
        $this->check_login(U('Wechat/Shop/index',['pid'=>$this->ginfo['pid'],'u'=>$this->user]));
        //单景区模式 读取票型
        $list = D('TicketType')->where(['status'=>1,'group_id'=>$user['user']['pricegroup']])->field('id,name,price,discount')->order('sort asc')->select();
        // $list = D('Product')->where(['status'=>1])->relation(true)->field('id,name')->order('sorting asc')->select();
        $this->assign('list',$list)->display();
    }

    public function view()
    {
        
        $ginfo = I('get.');
        if(empty($ginfo['pid']) || empty($ginfo['tid'])){
            $this->error('产品不存在!', U('Wechat/Shop/index',['pid'=>$this->ginfo['pid']]));
        }
        $user = session('user');
        if(empty($user['user']['openid'])){
            $zinfo['pid'] = $this->ginfo['pid'];
            Wticket::tologin($zinfo);
            $user = session('user');
            $url = $this->getUrl($user, U('Wechat/Shop/view',['pid'=>$this->ginfo['pid'],'tid'=>$ginfo['tid'],'u'=>$ginfo['u']]));

            $this->check_login($url);    
        }
        
        
        //根据活动拉取销售计划
        //$info = D('Product')->where(array('id'=>$ginfo['pid'],'status'=>1))->relation(true)->field('id,name')->find();
        $info = D('TicketType')->where(['status'=>1,'id'=>$ginfo['tid']])->field('id,name,price,discount')->find();
        if(empty($info)){
           $this->error('产品已下架~');
        }

        $plan = Wticket::getplan($ginfo['pid'],[$ginfo['tid']]);

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
            $info = $_POST['info'];
            //$this->check_login(U('Wechat/Shop/index',['pid'=>$this->ginfo['pid'],'tid'=>$ginfo['tid'],'u'=>$this->user]));//dump($info);
            //判断数据的完整性
            $uinfo = session('user');load_redis('setex','debug', json_encode($uinfo), 3600);
            $order = new Order();//dump($uinfo);
            $sn = $order->mobile($info,$uinfo['user']['scene'],$uinfo['user']);
          
            if($sn != false){
                $return = array(
                    'statusCode' => 200,
                    'url' => U('Wechat/Shop/create_order',array('sn'=>$sn)),
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
                    $proconf = $proconf[$info['product_id']][2];
                    $notify_url = 'http://dp.wy-mllj.com/index.php/Wechat/Notify/notify.html';
                    //产品名称
                    $product_name = product_name($info['product_id'], 1);
                    $pay = & load_wechat('Pay', $info['product_id']);
                    $prepayid = $pay->getPrepayId($user['user']['openid'], $product_name, $info['order_sn'], $money, $notify_url, $trade_type = "JSAPI", '', 1);
                   
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
        $sns = putIdToCode([$sn], 8);
        $this->assign('sn',$sn)->assign('sns', $sns)->display();
    }
    public function orderlist()
    {
        $user = session('user');
        if(empty($user['user']['openid'])){
            $zinfo['pid'] = $this->ginfo['pid'];
            Wticket::tologin($zinfo);
            $user = session('user');
            $url = $this->getUrl($user, U('Wechat/Shop/orderlist',['pid'=>$this->ginfo['pid'],'u'=>$this->user]));

            $this->check_login($url);  
        }
        

        // $list = M('Order')->where(array('status'=>array('in','1,9'),'openid'=>$user['user']['openid']))->field('take,phone,order_sn,status,product_id,createtime,money,plan_id,number')->limit('50')->order('createtime DESC')->select();
        $list = M('Order')->where(array('status'=>array('in','1,9'),'user_id'=>$user['user']['id']))->field('take,phone,order_sn,status,product_id,createtime,money,plan_id,number')->limit('50')->order('createtime DESC')->select();
        foreach ($list as $k => &$v) {
            $v['sns'] = putIdToCode($v['order_sn'], 8);
        }
        $this->assign('data',$list)->display();
    }
    function order_info(){
        $sn = I('sn');
        $user = session('user');
        if(empty($user['user']['openid']) || !empty($this->user)){
            $zinfo['pid'] = $this->ginfo['pid'];
            Wticket::tologin($zinfo);
            $user = session('user');
        }
        $this->check_login(U('Wechat/Shop/order_info',['pid'=>$this->ginfo['pid'],'sn'=>$sn,'u'=>$this->user]));
        $info = D('Item/Order')->where(array('order_sn'=>$sn))->relation(true)->find();
        $info['info'] = unserialize($info['info']);
        $info['sns'] = putIdToCode($sn, 8);
        $this->assign('data',$info);
        $this->display('orderinfo');
    }
    //活动
    function act()
    {
        $ginfo = I('get.');
        if(empty($ginfo['act'])){
            $this->error('页面不存在!');
        }
        $user = session('user');
        if(empty($user['user']['openid']) || !empty($this->user)){
            $zinfo['pid'] = $this->ginfo['pid'];
            Wticket::tologin($zinfo);
            $user = session('user');
        }
        $this->check_login(U('Wechat/Shop/act',['pid'=>$this->ginfo['pid'],'act'=>$ginfo['act'],'u'=>$this->user]));

        //根据活动拉取销售计划
        $info = M('Activity')->where(array('id'=>$ginfo['act'],'status'=>1))->find();
        $param = json_decode($info['param'], true);
        if((int)$info['type'] === 3){
            //限制区域销售
            $ticketType = F('TicketType'.$info['product_id']);
            $ticket = $ticketType[$param['info']['ticket']];
        }
        $info = [
            'id'    => $info['id'],
            'title' => $info['title'],
            'remark'=> $info['remark'],
            'param' => $param,
            'ticket'=> $ticket,
        ];
        $tpl = 'area_sale';
        $plan = Wticket::getplan($ginfo['pid'],[$param['info']['ticket']]);

        $user = session('user');
        $global = array_merge($plan,$user);
        $this->assign('global',json_encode($global));

        $this->assign('data', $info)->display($tpl);
    }
    //非常日期
    public function special()
    {
        $ginfo = I('get.');
        if(empty($ginfo['pid']) || empty($ginfo['tid'])){
            $this->error('产品不存在!', U('Wechat/Shop/special',['pid'=>$this->ginfo['pid'],'tid'=>$ginfo['tid'],'u'=>$ginfo['u']]));
        }
    //session('user',null);
        $user = session('user');
        if(!in_array($this->ginfo['tid'], ['566','567'])){
            $url = $this->getUrl($user, U('Wechat/Shop/special',['pid'=>$this->ginfo['pid'],'tid'=>$ginfo['tid'],'u'=>$ginfo['u']]));
        }else{
            $url = $this->getUrl($user);
        }
        
        //当前用户没有登录
        if(empty($user['user']['openid'])){
            $this->check_login($url);
        }
        //判断当前分享用户是否是登录用户
        if(isset($ginfo['u']) && $ginfo['u'] != $user['user']['id']){
            $this->check_login($url);
        }
        if(empty($ginfo['u']) && !empty($user['user']['channel'])){
            if(in_array($this->ginfo['tid'], ['566'])){
                $ginfo['tid'] = 567;
            }
            $url = $this->getUrl($user, U('Wechat/Shop/special',['pid'=>$this->ginfo['pid'],'tid'=>$ginfo['tid'],'u'=>$user['user']['id']]));

            header('location:'. $url);
        }
        //设置销售计划
        $info = D('TicketType')->where(['status'=>1,'id'=>$ginfo['tid']])->field('id,name,price,discount')->find();
        if(empty($info)){
           $this->error('产品已下架~');
        }
        $plan = Wticket::getplan($ginfo['pid'],[$ginfo['tid']]);

        $script = & load_wechat('Script', $this->ginfo['pid'], 1);
        //获取JsApi使用签名，通常这里只需要传 $url参数
        $options = $script->getJsSign($url);

        $global = array_merge($plan, $user);
        $tPlan = $plan['plan'][0];
        $aPlan = $plan['area'][$tPlan['id']][0];
        $this->assign('global', json_encode($global));
        $this->assign('ginfo',$ginfo);
        $this->assign('plan', $tPlan);
        $this->assign('area', $aPlan);
        $this->assign('product', $info);
        $this->assign('urls', $url)->assign('user', $user);
        $this->assign('options', $options);
        $this->getGoods($ginfo['tid']);
        $this->display();
    }
    public function getGoods($tid)
    {
        $data = [
            '566' => [
                'title'     => '家庭微度假套餐',
                'desc'      => '浪漫双人行&温馨亲子游',
                'price'     => '299',
                'oprice'    => '1065',
                'tag'       => ['抢购日期 2020年3月9日—3月31日'],
                'thumb'     => 'http://dp.wy-mllj.com/d/299/banner.jpg',
                'content'   => '<img src="http://dp.wy-mllj.com/d/299/01.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/02.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/03.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/04.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/05.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/06.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/07.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/08.jpg" alt="">',
                'know'      =>  [
                    [
                        'icon'      =>  'layui-icon-heart-fill',
                        'title'     =>  '优待政策',
                        'content'   =>  '小朋友1.2米以下免票'
                    ],
                    [
                        'icon'      =>  'layui-icon-rmb',
                        'title'     =>  '退票须知',
                        'content'   =>  '以上特价产品一经售出概不退款'
                    ],
                    [
                        'icon'      =>  'layui-icon-date',
                        'title'     =>  '有效期',
                        'content'   =>  '2020年12月31日'
                    ],
                    [
                        'icon'      =>  'layui-icon-tree',
                        'title'     =>  '预订方式',
                        'content'   =>  '活动套餐购买支付成功后,需提前预约，景区将根据房间实际库存满足游客预订需求；预约电话 0793—7377777'
                    ],
                    [
                        'icon'      =>  'layui-icon-log',
                        'title'     =>  '特别说明',
                        'content'   =>  '以上产品如遇十一黄金周需加价使用'
                    ]
                ]
            ],
            '567' => [
                'title'     => '家庭微度假套餐',
                'desc'      => '浪漫双人行&温馨亲子游',
                'price'     => '299',
                'oprice'    => '1065',
                'tag'       => ['抢购日期 2020年3月9日—3月31日'],
                'thumb'     => 'http://dp.wy-mllj.com/d/299/banner.jpg',
                'content'   => '<img src="http://dp.wy-mllj.com/d/299/01.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/02.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/03.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/04.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/05.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/06.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/07.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/299/08.jpg" alt="">',
                'know'      =>  [
                    [
                        'icon'      =>  'layui-icon-heart-fill',
                        'title'     =>  '优待政策',
                        'content'   =>  '小朋友1.2米以下免票'
                    ],
                    [
                        'icon'      =>  'layui-icon-rmb',
                        'title'     =>  '退票须知',
                        'content'   =>  '以上特价产品一经售出概不退款'
                    ],
                    [
                        'icon'      =>  'layui-icon-date',
                        'title'     =>  '有效期',
                        'content'   =>  '2020年12月31日'
                    ],
                    [
                        'icon'      =>  'layui-icon-tree',
                        'title'     =>  '预订方式',
                        'content'   =>  '活动套餐购买支付成功后,需提前预约，景区将根据房间实际库存满足游客预订需求；预约电话 0793—7377777'
                    ],
                    [
                        'icon'      =>  'layui-icon-log',
                        'title'     =>  '特别说明',
                        'content'   =>  '以上产品如遇十一黄金周需加价使用'
                    ]
                ]
            ],
            '568' => [
                'title'     => '爱心义卖！￥9.9抵￥100预售【梦里老家】演出门票',
                'desc'      => '9.9抵100，限时抢购',
                'price'     => '9.9',
                'oprice'    => '',
                'tag'       => ['义卖时间2020年3月15日至4月10日'],
                'thumb'     => 'http://dp.wy-mllj.com/d/9/banner1.jpg',
                'content'   => '<img src="http://dp.wy-mllj.com/d/9/01.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/9/02.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/9/03.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/9/04.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/9/05.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/9/06.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/9/07.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/9/08.jpg" alt="">
                                <img src="http://dp.wy-mllj.com/d/9/09.jpg" alt="">',
                'know'      =>  [
                    
                ]
            ],
        ];
        $this->assign('data', $data[$tid]);
    }

    public function auth()
    {
        $zinfo['pid'] = $this->ginfo['pid'];
        $zinfo['u'] = $this->ginfo['u'];
        Wticket::tologin($zinfo);
        $user = session('user');
        if(!empty($user['user']['openid'])){

            $callback = session('callback');
            if(empty($callback)){
                $url = U('Wechat/Shop/index',['pid' => $this->ginfo['pid']]);
            }else{
                $url = $callback;//$this->getUrl($user);
            }
            header('location:'. $url);
        }
    }
    //判断是否登录
    function check_login($url){
        $ginfo = I('get.');
        session('user',null);
        $user = session('user');
        //存储跳转URL
        session('callback', $url);

        $authUrl = U('Wechat/Shop/auth', $this->ginfo);
        if(!isset($ginfo['code'])){
            $oauth = & load_wechat('Oauth', $this->ginfo['pid'], 1);

            $urls = $oauth->getOauthRedirect($authUrl, 'mllj', 'snsapi_base');
            header('location:'. $urls);

        }elseif(empty($user['user']['openid'])){
            header('location:'. $authUrl);
        }
        return true;
    }
    function cklogin()
    {
        $user = session('user');
        if(!$user || $user['user']['id'] == '2'){
            $urls = U('wechat/Shop/login');
            header('location:'. $urls);
        }
    }
    protected function getUrl($user, $url = '')
    {
        if(empty($url)){
            if(isset($user['user']) && !empty($user['user']['channel'])){
                $url = U('Wechat/Shop/special',['pid'=>$this->ginfo['pid'],'tid'=>567,'u'=>$user['user']['id']]);
            }else{
                $url = U('Wechat/Shop/special',['pid'=>$this->ginfo['pid'],'tid'=>$this->ginfo['tid'],'u'=>$this->ginfo['u']]);
            }
        }
        
        return $url;
    }
}