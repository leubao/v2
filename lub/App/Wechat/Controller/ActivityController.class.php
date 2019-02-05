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
class ActivityController extends LubTMP {
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
       
        foreach ($plan_list as $key => $value) {
            //获取销售计划
            $plan = F('Plan_'.$value['plan_id']);
            if(!empty($plan)){
                $table = ucwords($plan['seat_table']);
                //dump($value['plan_id']);
               
                //根据活动设定的区域进行加载
                foreach ($param['info'] as $k => $v) {
                    
                    $area_num = area_count_seat($table,array('area'=>$v['area'],'status'=>'0'),1);
                    //判断当前剩余数量与渠道给定数量的大小
                    if($area_num > $v['quota']){
                        //展示渠道剩余数量
                        //查询已经消耗的数量
                        $quota_nums = M('QuotaUse')->where(array('channel_id'=>$ginfo['act'],'type'=>2,'plan_id'=>$value['plan_id'],'area_id'=>$v['area']))->getField('number');
                        $num = $v['quota'] - $quota_nums;
                    }else{
                        //展示实际数量
                        $num = $area_num;
                    }
                    $seat[$value['plan_id']][] = array(
                            'area'=>$v['area'],
                            'name'=>areaName($v['area'],1),
                            'priceid'=>$v['price'],
                            'money'=>$ticketType[$v['price']]['discount'],
                            'moneys'=>$ticketType[$v['price']]['discount'],
                            'num'   =>  $num
                        );
                    //$v['param'] = $seat;
                   // $v['title'] = planShow($value['plan_id'],3,1);
                    
                   // $plan[] = $v;
                   //$area_data = array();
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
        $wxauth = $this->get_openid();
        $user['user'] = array(
                'id' => 2,
                'openid' => $wxauth->openid,
                'maxnum' => '3',
                'guide'  => $ginfo['act'],
                'qditem' => $ginfo['act'],//活动id
                'scene'  => '41',
                'epay'   => '1',
                'channel'=> '0',
            
            );
        $goods_info = array(
        'plan' => $plan_data,
        'area' => $seat,
        'user' => $user['user'],
        );
        session('user',$user);
        //TODO 加载页面模板
        $this->assign('goods_info',json_encode($goods_info))
             ->display();
    }
    //活动订单
    function acty_order(){
        if(IS_POST){
            //创建订单
            $info = json_decode($_POST['info'],true);
            //读取当前活动票型
            $param = session('param');
            $ticketType = F('TicketType'.$param['product']);
            //获取当前区域活动配置
            $area_set = $param['info'][$info['data'][0]['areaId']];
            //重新构造请求订单
            $info['data'] = array(
                array('areaId'=>$info['data'][0]['areaId'],'priceid'=>$info['data'][0]['priceid'],'price'=>$ticketType[$area_set['price']]['discount'],'num'=>'1'),
                 array('areaId'=>$info['data'][0]['areaId'],'priceid'=>(int)$ticketType[$area_set['prices']]['id'],'price'=>$ticketType[$area_set['prices']]['discount'],'num'=>'2'),
                );
            $info['subtotal'] =  $ticketType[$area_set['price']]['discount'];
            //增加活动标记
            $info['param'][0]['activity'] = $info['crm'][0]['qditem'];
            $info['param'][0]['area'] = $info['data'][0]['areaId'];
            $info = json_encode($info);
          //  dump($info);
            //提交订单请求
            $uinfo = session('user');//dump($uinfo);
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
    
    public function kill()
    {
        $ginfo = I('get.');
    
        $user = session('user');
        if(empty($user['user']['openid'])){
            if(isset($ginfo['code'])){
                $oauth = & load_wechat('Oauth',$ginfo['pid'],1);
                $wxauth = $oauth->getOauthAccessToken($ginfo['code']);
                if(!empty($wxauth['openid'])){
                    $user['user'] = array(
                        'id' => 2,
                        'openid' => $wxauth['openid'],
                        'maxnum' => '1',
                        'guide'  => '0',
                        'qditem' => '0',
                        'scene'  => '41',
                        'epay'   => '2',//结算方式1 票面价结算2 底价结算
                        'channel'=> '0',
                        'pricegroup'=>'9',
                    );
                    session('user',$user);
                    $user = session('user');
                }
                
            }
        }
        $this->kill_login($user['user']);
        $url = U('Wechat/Activity/kill',['pid'=>$ginfo['pid'],'act'=>$ginfo['act']]);
        $this->check_login($url);
        $actInfo = $this->getActInfo();
        //根据活动拉取销售计划
        $info = M('Activity')->where(array('id'=>$actInfo['actId'],'status'=>1))->field('title,param,product_id')->find();
        
        /*
        
        $param = json_decode($info['param'], true);
        $ticketType = F("TicketType".$info['product_id']);
        $ticket = $param['info']['tciket'];
        $ticket = $ticketType[$ticket];
        $rule = $param['info']['rule'];
        foreach ($rule as $k => $v) {
            $this->check_kill($v);
        }
        dump($rule);
        $rule = load_redis('get','kill_'.$pinfo['act']);*/

        $ticketType = F("TicketType".$info['product_id']);
        $ticket = $ticketType[$actInfo['killInfo']['ticket']];

        $this->assign('data',$info)->assign('param',$param['info'])->assign('ticket', $ticket)->assign('rule',$actInfo['killInfo']);
        $quota = load_redis('get', $actInfo['actName']);
        if(empty($actInfo['killInfo']) || $quota <= 0){
            if($time > '1600'){
                $datetime = strtotime('+1 day');
                $datetime = date('Y-m-d', $datetime);
            }else{
                $datetime = date('Y-m-d');
            }
            $starttime = $datetime.' 16:30:00';

            $time = strtotime($datetime.' 16:30:00').'000';
            $this->assign('time',$time)->assign('starttime',$starttime);
            $this->display('kill_error');
        }else{
            $this->display();
        }
    }
    //判断是否开始 
    //判断是否登录
    function check_login($url){
        $ginfo = I('get.');
        $user = session('user');
        if(empty($user['user']['openid']) && !isset($ginfo['code'])){
            //session('user',null);
            $oauth = & load_wechat('Oauth',$ginfo['pid'],1);
            $urls = $oauth->getOauthRedirect($url, $state, 'snsapi_base');
            load_redis('set','check_login',date('Y-m-d H:i:s'));
            header('location:'. $urls);
        }
    }
    
    public function killOrder()
    {
        if(IS_POST){
            $pinfo = I('post.');
            $actInfo = $this->getActInfo();
            //获取秒杀是否存在 判断库存
            $stock = load_redis('get',$actInfo['actName']);
            if(empty($stock) || $stock === 0 || $stock < 0){
                $return = array(
                    'statusCode' => 4001,
                    'data'   => '',
                    'msg'    => '亲,你来晚了,这一波抢完了,等下一波吧!'
                ); 
                die(json_encode($return));
            }
            $user = session('user');
            //判断当前用户是否已经参与【比对用户[，openID】，支付成功的
            $mInfo = D('KillLog')->where(['openid'=>$user['user']['openid'],'status'=>2])->find();
            
            if($mInfo){
                $return = array(
                    'statusCode' => 4002,
                    'data'   => '',
                    'msg'    => '亲,你已经参加过活动了哦!'
                ); 
                die(json_encode($return));
            }
            //创建虚拟订单
            $sn = get_order_sn($pinfo['plan_id'],1);
            $virtual = [
                'order_sn'  =>  $sn,
                'product'   =>  $pinfo['product'],
                'contact'   =>  $pinfo['username'],
                'mobile'    =>  $pinfo['phone'],
                'plan_id'   =>  $pinfo['plan_id'],
                'number'    =>  $pinfo['number'],
                'ticket'    =>  $pinfo['ticket'],
                'money'     =>  $pinfo['money'],
                'actid'     =>  $pinfo['act'],
                'openid'    =>  $user['user']['openid'],
            ];
            load_redis('setex','kill_order_'.$sn, json_encode($virtual), 3600);
            $return = array(
                'statusCode' => 200,
                'url' => U('Wechat/activity/killorder',array('pid'=>$pinfo['product'],'sn'=>$sn)),
            ); 
            die(json_encode($return));
        }else{
            $ginfo = I('get.');
            $user = session('user');
            $virtual = json_decode(load_redis('get','kill_order_'.$ginfo['sn']), true);
            // 获取预支付ID
            if($virtual['money'] == '0'){
               $money = 0.1*100;
            }else{
               $money = $virtual['money']*100; 
            }
            if($user['user']['openid'] == 'oBQ9fwSOcWSGk-i6GzWHZhikUiL8'){
                $money = 0.01*100;
            }
            //向微信支付下单
            $proconf = cache('ProConfig');
            $proconf = $proconf[$virtual['product']][2];
            $notify_url = $proconf['wx_url'].'index.php/Wechat/Notify/notify.html';
            //产品名称
            $product_name = product_name($virtual['product'],1);
            $pay = & load_wechat('Pay',$virtual['product']);

            $prepayid = $pay->getPrepayId($user['user']['openid'], $product_name, $virtual['order_sn'], $money, $notify_url, $trade_type = "JSAPI",'',1);
            if($prepayid){
                $options = $pay->createMchPay($prepayid);
            }else{
                // 创建JSAPI签名参数包，这里返回的是数组
                $this->assign('error',$pay->errMsg.$pay->errCode);
            }
            $this->assign('jsapi',$prepayid)->assign('wxpay',$options)->assign('data',$virtual);
            $this->display('kill_order');
        }
    }
    //秒杀等待开始
    public function kill_error()
    {
        $time = strtotime('2019-01-30 14:00:00').'000';
        $this->assign('time',$time)->assign('starttime','2019年1月30日 14:00');
        $this->display();
    }
    //登录
    public function kill_login($uinfo)
    {
        if(!empty($uinfo['openid'])){
            if(D('KillLog')->where(['openid' => $uinfo['openid']])->field('id')->find()){
                D('KillLog')->where(['openid' => $uinfo['openid']])->setInc('login', 1);
            }else{
                $data = [
                    'openid'        =>  $uinfo['openid'],
                    'status'        =>  0,
                    'login'         =>  1,
                    'create_time'   =>  time(),
                    'update_time'   =>  time(),
                ];
                D('KillLog')->add($data);
            }
        }
        
       return true;
    }
    //判断库存
    public function killQuota()
    {
        $actInfo = $this->getActInfo();
        //获取秒杀是否存在 判断库存
        $stock = load_redis('get',$actInfo['actName']);
        if(empty($stock) || $stock <= 0){
            $return = array(
                'statusCode' => 4001,
                'data'   => '',
                'msg'    => '亲,你来晚了,这一波抢完了,等下一波吧!'
            ); 
            die(json_encode($return));
        }
    }
    //判断当前创建活动
    public function getActInfo()
    {
        /**
         *定义访问时间
         *14:00-14:30 1元秒杀   10张
         *14:31-15:00 9.9元秒杀 10张
         *15:01-15:30 1元秒杀   10张
         *15:31-16:00 9.9元秒杀 10张
         */
        $today = date('Ymd'); 
        $time = date('Hi');
        if($time >= '1400' && $time <= '1430'){
            $actName = 'kill_quota_'.$today.'1400';
            $quota = load_redis('get', $actName);
            $killInfo = [
                'actid' =>  16,
                'plan_id'  =>  1219,
                'plan'  =>  planShow('1219',1,1),
                'ticket'=>  502,
                'quota' =>  $quota ? $quota : 10
            ];
            $actId = 16;
        }
        if($time >= '1431' && $time <= '1500'){
            $actName = 'kill_quota_'.$today.'1431';
            $quota = load_redis('get', $actName);

            $killInfo = [
                'actid' =>  17,
                'plan_id'  =>  1219,
                'plan'  =>  planShow('1219',1,1),
                'ticket'=>  503,
                'quota' =>  $quota ? $quota : 10
            ];
            $actId = 17;
        }
        if($time >= '1601' && $time <= '1630'){
            $actName = 'kill_quota_'.$today.'1501';
            $quota = load_redis('get', $actName);

            $killInfo = [
                'actid' =>  16,
                'plan_id'  =>  1219,
                'plan'  =>  planShow('1219',1,1),
                'ticket'=>  502,
                'quota' =>  $quota ? $quota : 10
            ];
            $actId = 16;
        }
        if($time >= '1631' && $time <= '1700'){
            $actName = 'kill_quota_'.$today.'1531';
            $quota = load_redis('get', $actName);

            

            $killInfo = [
                'actid' =>  17,
                'plan_id'  =>  1219,
                'plan'  =>  planShow('1219',1,1),
                'ticket'=>  503,
                'quota' =>  $quota ? $quota : 10
            ];
            $actId = 17;
        }
        // $user = session('user');
        // if($user['user']['openid'] == 'oBQ9fwSOcWSGk-i6GzWHZhikUiL8'){
        //     $actName = 'kill_quota_'.$today.'1400';
        //     $quota = load_redis('get', $actName);

        //     if(empty($quota) && $quota <> 0){
        //         load_redis('setex', $actName, 1, 1800);
        //     }

        //     $killInfo = [
        //         'actid' =>  16,
        //         'plan_id'  =>  1219,
        //         'plan'  =>  planShow('1219',1,1),
        //         'ticket'=>  502,
        //         'quota' =>  $quota ? $quota : 10
        //     ];
        //     $actId = 16; 
        // }
        
        return ['killInfo'=>$killInfo, 'actId'=>$actId, 'actName'=>$actName];
    }
    //支付
    public function killPay()
    {
        //支付完成,创建订单,减少库存
        $ginfo = I('get.');
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $script = &  load_wechat('Script',$ginfo['pid'],1);
        $options = $script->getJsSign($url);
        $this->assign('options',$options);

        $actInfo = $this->getActInfo();
        //获取秒杀是否存在 判断库存

        //削减库存
        $user = session('user');
        if(!load_redis('get',$ginfo['sn'])){
            $order = load_redis('get', 'kill_order_'.$ginfo['sn']);
            load_redis('lpush', 'kill_order', $order);
            load_redis('decrby', $actInfo['actName'],1);
            //更新抢票人
            $data = ['status'=>2,'update_time'=>time()];
            D('KillLog')->where(['openid' => $user['user']['openid']])->save($data);
        }
        load_redis('set', $ginfo['sn'],1);
        //存入待处理订单，秒杀结束后统一下单
        $this->display('kill_pay');
    }
    //创建活动计划
    public function test()
    {
        // $dateList = getDateFromRange('2019-01-31','2019-02-08');
        // dump($dateList);
        // $actName = [];
        // foreach ($dateList as $k => $v) {
        //     $actName = [
        //         'kill_quota_'.date('Ymd', strtotime($v)).'1400',
        //         'kill_quota_'.date('Ymd', strtotime($v)).'1431',
        //         'kill_quota_'.date('Ymd', strtotime($v)).'1600',
        //         'kill_quota_'.date('Ymd', strtotime($v)).'1631'
        //     ];
        //     if(!empty($actName1)){
        //         $actName1 = array_merge($actName1, $actName);
        //     }else{
        //         $actName1 = $actName;
        //     }
        // }
        // dump($actName1);
        // foreach ($actName1 as $key => $value) {
        //     load_redis('set', $value, 9);
        // }
       // $order = load_redis('rPop', 'kill_order');
        //load_redis('lpush','kill_orders', $order);
        // dump($order);
        //
        for ($i=0; $i < 50; $i++) {
            $this->setTicketOrder();
        }
    }
    //转化实际订单
    public function setTicketOrder()
    {
        $virtual = json_decode(load_redis('rPop', 'kill_order'), true);
        load_redis('lpush','kill_orders', json_encode($virtual));
        // $order = '{"order_sn":"901301121942046","product":"43","contact":"\u80e1\u5174\u7fa4","mobile":"15067997630","plan_id":"1219","number":"1","ticket":"503","money":"9.90","actid":"17","openid":"oBQ9fwfOJ9KZIpLzoDxbqvKKfGr0"}';
        //$virtual = json_decode($order,true);
        //查询微信支付是否正常收款
        $pay = & load_wechat('Pay',$virtual['product']);
        $payBack = $pay->queryOrder($virtual['order_sn']);
        if($payBack['return_code'] === 'SUCCESS' && $payBack['trade_state'] === 'SUCCESS'){
            //组合数据
            $ticketType = F("TicketType".$virtual['product']);
            $ticket = $ticketType[$virtual['ticket']];

            $orderData = [
                'order_sn'  =>  $virtual['order_sn'],
                'subtotal'  =>  $virtual['money'],
                'plan_id'   =>  $virtual['plan_id'],
                'checkin'   =>  1,
                'sub_type'  =>  0,
                'type'      =>  1,
                'data'      =>  [
                    [
                        'areaId'=>$ticket['area'],
                        'priceid'=>$virtual['ticket'],
                        'price'=>$ticket['discount'],
                        'num'=>$virtual['number']
                    ]
                ],
                'crm'       =>  [
                    [
                        'guide'     =>  0,
                        'qditem'    =>  0,
                        'phone'     =>  $virtual['mobile'],
                        'contact'   =>  $virtual['contact'],
                        'memmber'   =>  ''
                    ]
                ],
                'pay'       =>  [
                    [   
                        'cash'  =>  0, 
                        'card'  =>  0, 
                        'alipay'=>  0 
                    ]
                ],
                'param'     =>  [
                    [
                        'remark'    => $virtual['remark'] ? $virtual['remark'] : '无', 
                        'id_card'   => '', 
                        'activity'  => $virtual['actid'], 
                        'settlement'=> 2,
                        'is_pay'    => 1
                    ]
                ]
            ];
            //写入订单
            $order = new Order();
            $uinfo = [
                'id' => 2,
                'maxnum' => '1',
                'guide'  => '0',
                'qditem' => '0',
                'scene'  => '41',
                'epay'   => '2',//结算方式1 票面价结算2 底价结算
                'channel'=> '0',
                'pricegroup'=>'9',
            ];
            $sn = $order->quick(json_encode($orderData), 41, $uinfo);
            if($sn){
                echo 'sucess' . $sn['order_sn'] . '<br />';
                dump($sn);
            }else{
                echo 'error' . $virtual['order_sn'] . '<br />';
            }
        }else{
            echo 'errorpay' . $virtual['order_sn'] . '<br />';
        }
        
        
    }
}