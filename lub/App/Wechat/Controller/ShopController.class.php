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
        $proconf = get_proconf(169,2);
        $script = &  load_wechat('Script',169,1);
        //获取JsApi使用签名，通常这里只需要传 $url参数  
        //设置统一分享链接
        $options = $script->getJsSign(U('Wechat/Shop/index',['pid'=>$this->ginfo['pid'],'u'=>$this->user]));

        $this->assign('ginfo', $this->ginfo)->assign('proconf',$proconf)->assign('options',$options);

    }
    public function index()
    {
        $user = session('user');
        if(empty($user['user']['openid']) || !empty($this->user)){
            $zinfo['pid'] = 169;
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
        if(empty($user['user']['openid']) || !empty($this->user)){
            $zinfo['pid'] = 169;
            Wticket::tologin($zinfo);
            $user = session('user');
        }
        $this->check_login(U('Wechat/Shop/view',['pid'=>$this->ginfo['pid'],'tid'=>$ginfo['tid'],'u'=>$this->user]));
        
        //根据活动拉取销售计划
        //$info = D('Product')->where(array('id'=>$ginfo['pid'],'status'=>1))->relation(true)->field('id,name')->find();
        $info = D('TicketType')->where(['status'=>1,'id'=>$ginfo['tid']])->field('id,name,price,discount')->find();
        if(empty($info)){
           $this->error('产品已下架~');
        }

        $plan = Wticket::getplan($ginfo['pid'],[$ginfo['tid']]);

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
            $info = $_POST['info'];
            $this->check_login(U('Wechat/Shop/index',['pid'=>$this->ginfo['pid'],'tid'=>$ginfo['tid'],'u'=>$this->user]));//dump($info);
            //判断数据的完整性
            $uinfo = session('user');
            $order = new Order();
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
            //var_dump($info);
           // $this->wx_init($this->pid);
            if(in_array($info['status'],array('1','9'))){
                $this->error("订单状态不允许此项操纵");
            }else{
                //根据订单属性判断是否需要加载微信支付 散客和政企通过微信购票
                if(in_array($info['type'],['1','2','6','8'])){
                    $user = session('user');
                    // 获取预支付ID
                    // if($info['money'] == '0'){
                    //    $money = 0.1*100;
                    // }else{
                    //    $money = $info['money']*100; 
                    // }
                    //$money = 1;
                    // $proconf = cache('ProConfig');
                    // $proconf = $proconf['169'][2];
                    $notify_url = 'http://cby.leubao.com/index.php/Wechat/Notify/notify.html';
                    //产品名称
                    $product_name = product_name($info['product_id'], 1);
                    // $pay = & load_wechat('Pay', '169');
                     //$prepayid = $pay->getPrepayId($user['user']['openid'], $product_name, $info['order_sn'], $money, $notify_url, $trade_type = "JSAPI", '', 1);
                    // 获取单票型id
                    $priceid = array_keys($info['info']['data']['area'])[0];
                    $options = $this->getPrepayId($info['order_sn'],$user['user']['openid'], $product_name, $info['money'], $priceid);
                    // if($prepayid){
                    //     $options = $pay->createMchPay($prepayid);
                    // }else{
                    //     // 创建JSAPI签名参数包，这里返回的是数组
                    //     $this->assign('error',$pay->errMsg.$pay->errCode);
                    // }
                    $this->assign('wxpay',$options);
                }
            }
            $this->assign('data',$info)->display();
        }
    }
    public function getPrepayId($sn, $openid, $product_name, $money,$priceid)
    {
        /**
         * 分账规则
         * @var [type]
         */
        $rule = D('Routing')->where(['ticket_id'=>$priceid,'status'=>1])->find();
        if(empty($rule)){
            $settleInfo = [];
            $profit = 'REAL_TIME';
        }else{
            //同步分账信息
            if((int)$rule['type'] === 1){
                $settleInfo = [
                    [
                        'ledgerNo'  =>  $rule['mch_id'],//分账方编号
                        'ledgerName'=>  $rule['mch_name'],//分账方名称
                        'amount'    =>  $rule['rule'],//分账金额
                    ]
                ];
            }else{
                //比例分账的比例，所有分账比例累加不能超过 1（100%） 0.23(含义：23%)
                $settleInfo = [
                    [
                        'ledgerNo'  =>  $rule['mch_id'],//分账方编号
                        'ledgerName'=>  $rule['mch_name'],//分账方名称
                        'proportion'=>  $rule['rule']
                    ]
                ];   
            }
            $profit = 'REAL_TIME_DIVIDE';
        }
        $config = [
            'mch_id' =>  '10033843358',
            'appid'  =>  'wxde733cb2b0c68a36',
            'key'    => 'WhvUMxDgYjufJcdez6oGw1RXT9i8tQrs'
        ];
        $data = [
            'out_trade_no'  =>  $sn,//商户订单号
            'mch_id'        =>  $config['mch_id'],//商户编号
            'appid'         =>  $config['appid'],//公众号appid
            'total'         =>  $money,//金额
            'openid'        =>  $openid,//opneid
            'profit_sharing'=>  $profit,//'REAL_TIME_DIVIDE',//资金处理类型DELAY_SETTLE("延迟结算"),REAL_TIME("实时订单");REAL_TIME_DIVIDE（” 实时 分账” ）SPLIT_ACCOUNT_IN("实时拆分入账");
            'settle_info'   =>  [
                'divideDetail'      =>  $settleInfo,//'',//json_encode($settleInfo),
                'divideNotifyUrl'   =>  'http://cby.leubao.com/api.php/notify/settle_notify',
            ],//结算信息
            'title'         =>  $product_name,//商品标题
            'description'   =>  $product_name,//商品描述
            'client_ip'     =>  get_client_ip(),//客户端IP
            'notify_url'    =>  'http://cby.leubao.com/api.php/notify/pay_notify',
            'time_stamp'    =>  time(),
            'nonce_str'     =>  genRandomString(8,1),//随机字符串
            'sign'          =>  ''
        ];
        $key = $config['key'];//'WhvUMxDgYjufJcdez6oGw1RXT9i8tQrs';
        $data['sign'] = \Libs\Service\ArrayUtil::setPaymentSign($data, $key);
        $url = 'https://api.pay.xzusoft.cn/pay/gopay';
        $res = json_decode(getHttpContent($url, 'POST', $data), true);
        $param = array(
            'appid'=>$config['mch_id'],
            'mch_id'=>$config['appid'],
            'openid'=>$openid,
            'total_fee'=>$money,
            'settle_info' => $settleInfo
        );
        payLog($money,$sn,3,2,1,$param);
        // 
        return $res['data'];
    }
    public function pay_success()
    {
        $sn = I('sn');
        $sns = putIdToCode($sn, 8);
        $this->assign('sn',$sn)->assign('sns', $sns)->display();
    }
    public function orderlist()
    {
        $user = session('user');
        if(empty($user['user']['openid']) || !empty($this->user)){
            $zinfo['pid'] = 169;
            Wticket::tologin($zinfo);
            $user = session('user');
        }
        $this->check_login(U('Wechat/Shop/orderlist',['pid'=>$this->ginfo['pid'],'u'=>$this->user]));
        $list = M('Order')->where(array('status'=>array('in','1,9'),'openid'=>$user['user']['openid']))->field('take,phone,order_sn,status,product_id,createtime,money,plan_id,number')->limit('50')->order('createtime DESC')->select();
        foreach ($list as $k => &$v) {
            $v['sns'] = putIdToCode($v['order_sn'], 8);
        }
        $this->assign('data',$list)->display();
    }
    function order_info(){
        $sn = I('sn');
        $user = session('user');
        if(empty($user['user']['openid']) || !empty($this->user)){
            $zinfo['pid'] = 169;
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
    //判断是否登录
    function check_login($url){
        $ginfo = I('get.');
        $user = session('user');
        if(empty($user['user']['openid']) && !isset($ginfo['code'])){
            $oauth = & load_wechat('Oauth',169,1);
            $urls = $oauth->getOauthRedirect($url, 'cby', 'snsapi_base');
            //dump($urls);
            header('location:'. $urls);
        }elseif(empty($user['user']['openid'])){
            header('location:'. $url);
        }
    }
    function cklogin()
    {
        $user = session('user');
        if(!$user || $user['user']['id'] == '2'){
            $urls = U('wechat/Shop/login');
            header('location:'. $urls);
        }
    }
    public function test_pay()
    {
        $user = session('user');
        if(empty($user['user']['openid']) || !empty($this->user)){
            Wticket::tologin($zinfo);
            $user = session('user');
        }
        $this->check_login(U('Wechat/Index/test_pay',array('pid', I('get.pid'))));
        //同步分账信息
        // $settleInfo = [[
        //     'ledgerNo'  =>  ,//分账方编号
        //     'ledgerName'=>  ,//分账方名称
        //     'amount'    =>  ,//分账金额
        //     'proportion'=>  ,//比例分账的比例，所有分账比例累加不能超过 1（100%） 0.23(含义：23%)
        // ]];
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
            'sn'            =>  session_create_id(),//商户订单号
            'mch_id'        =>  '10033843358',//商户编号
            'appid'         =>  'wxde733cb2b0c68a36',//公众号appid
            'total'         =>  '0.01',//金额
            'openid'        =>  $user['user']['openid'],//opneid
            'settle_info'   =>  [],//结算信息
            'title'         =>  '测试支付',//商品标题
            'description'   =>  '测试支付',//商品描述
            'client_ip'     =>  get_client_ip(),//客户端IP
            'notify_url'    =>  'http://cby.leubao.com/api.php/test/notify',
            'timestamp'     =>  time(),
            'nonce_str'     =>  genRandomString(8,1),//随机字符串
            'sign'          =>  ''
        ];
        $key = '111111111';
        $data['sign'] = \Libs\Service\ArrayUtil::setPaymentSign($data, $key);
        $url = 'https://api.msg.alizhiyou.cn/pay/gopay';
        if(!empty($user)){
            $res = json_decode(getHttpContent($url, 'POST', $data), true);
            var_dump($data,$res);
            $this->assign('data', $res['data']);
        }
        
        $this->display();
    }
}