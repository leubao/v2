<?php


namespace Home\Controller;


use Common\Controller\Base;
use Home\Service\Partner;
use Wechat\Service\Wticket;
use Libs\Service\Order;
class WechatController extends Base
{
    //当前用户可售的票型
    function index(){
        //读取当前用户所属分组
        $uinfo = Partner::getInstance()->getInfo();
        $map = ['group_id'=>['in',$uinfo['group']['price_group']],'status'=>1,'type'=>2];
        $ticket = D('TicketType')->where($map)->field('id,product_id,name,area,price,discount')->select();
        $product = D('Product')->where(['id'=>['in', array_column($ticket, 'product_id')]])->field('id,name')->select();
        $product = array_column($product, 'name', 'id');
        
        foreach ($ticket as $k => $v) {
            $v['product'] = $product[$v['product_id']];
            $tickets[] = $v;
        }
        $this->assign('ticket',$tickets);
        $this->assign('uinfo', $uinfo);
        $this->display();
    }
    //产品详情
    function show(){
        $ginfo = I('get.');
        if(!isset($ginfo['pid']) || empty($ginfo['pid'])){
            $this->error('参数错误~', U('Home/Wechat/index'));
        }
        if(!isset($ginfo['tid']) || empty($ginfo['tid'])){
            $this->error('参数错误~', U('Home/Wechat/index'));
        }
        $info = D('TicketType')->where(['status'=>1,'id'=>$ginfo['tid']])->field('id,name,price,discount')->find();
        if(empty($info)){
           $this->error('产品已下架~');
        }
        $product = D('Product')->where(['id'=>$ginfo['pid']])->field('id,name')->cache('product_'.$ginfo['pid'], 3600)->find();

        $uinfo = Partner::getInstance()->getInfo();
        $plan = $this->getplan($ginfo['pid'],$uinfo,[$ginfo['tid']]);
        //dump($plan);
        $global = array_merge($plan,$uinfo);//dump($global);
        $this->assign('product', $product)->assign('info', $info)->assign('global', json_encode($global))->display();
    }
    //创建订单
    function order(){
        if(IS_POST){
            $info = $_POST['info'];
            $uInfo = \Home\Service\Partner::getInstance()->getInfo();
            $order = new Order();
            //活动订单 套票
            $ginfo = I('get.act');
            //根据当前用户所属分组类型进行区分是政企还是企业或个人
            if($uInfo['group']['type'] == '3'){
                //政企
                $sn = $order->channel($info, 26, $uInfo, (int)$ginfo['act']);
            }else{
                //个人或企业
                $sn = $order->channel($info, 22, $uInfo, (int)$ginfo['act']);
            }
            if($sn != false){
                $return = array('statusCode' => '200','sn'=>$sn, 'url'=>U('Home/Wechat/order', array('sn'=>$sn)));
            }else{
                $return = array('statusCode' => '300','sn'=>$sn, 'msg'=>$order->error);
            }
            //记录售票员日报表
            die(json_encode($return));
            return true;
        }else{
            $ginfo = I('get.');
            $info = D('Item/Order')->where(array('order_sn'=>$ginfo['sn']))->relation(true)->find();
            $info['info'] = unserialize($info['info']);//dump($info);
            //$this->wx_init($this->pid);
            if(in_array($info['status'],array('1','9'))){
                $this->error("订单状态不允许此项操纵");
            }else{
                //根据订单属性判断是否需要加载微信支付 散客和政企通过微信购票
                $uInfo = \Home\Service\Partner::getInstance()->getInfo();
                // 获取预支付ID
                if($info['money'] == '0'){
                    $money = 0.1*100;
                }else{
                    $money = $info['money']*100;
                }
                //dump(cache('Config'));
                //$money = 1;
                $conf = cache('Config');
                $notify_url = $conf['siteurl'].'index.php/Wechat/Notify/notify.html';
                //产品名称
                $product_name = product_name($info['product_id'],1);
                $pay = & load_wechat('Pay', $info['product_id']);
                $prepayid = $pay->getPrepayId($uInfo['openid'], $product_name, $info['order_sn'], $money, $notify_url, $trade_type = "JSAPI",'',1);
                if($prepayid){
                    $options = $pay->createMchPay($prepayid);
                }else{
                    // 创建JSAPI签名参数包，这里返回的是数组
                    $this->assign('error',$pay->errMsg.$pay->errCode);
                }
                $this->assign('jsapi',$prepayid)->assign('wxpay',$options);
            }
            $this->assign('data',$info)->display();
        }
    }
    //支付订单
    function payment(){
        $pinfo = $_POST['info'];
        $pinfo = json_decode($pinfo,true);
        $oinfo = D('Item/Order')->where(array('order_sn'=>$info['sn']))->relation(true)->find();
        if(empty($info) || empty($oinfo)){echo json_encode(array('statusCode' => '300','msg' => $oinfo));return false;}
        $order = new Order();
        $status = $order->mobile_seat($info,$oinfo);
        $product_name = product_name($oinfo['product_id'],1);
        if($status != false){
            //构造模板消息
            $uInfo = \Home\Service\Partner::getInstance()->getInfo();
            $attach =  array(
                'number'=>$oinfo['number'],
                'product_name'=>$product_name,
                'plan'=> planShow($oinfo['plan_id'],4,1),
            );
            $openid = $uInfo['openid'];
            $result = array(
                'openid' => $openid,
                'out_trade_no' => $info['sn'],
                'attach' => serialize($attach),
            );
            //$this->to_tplmsg($result,$oinfo['product_id']);
            $return = array(
                'statusCode' => 200,
                'url' => U('Wechat/Index/pay_success',array('sn'=>$info['sn'],'pid'=>$this->pid)),
            );
        }else{
            $return = array(
                'statusCode' => 300,
                'msg' => $status.'9',
            );
        }
        die(json_encode($return));
        
//        if($pinfo['way']){
//
//        }
//        if($pinfo['way'] === ''){
//
//        }
//        if($pinfo['way'] === 'wxpay'){
//
//        }
    }
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
    //订单列表
    function orderlist(){
        $this->display();
    }
    function orderinfo(){
        $this->display();
    }

    /*获取销售计划
    * @Author   zhoujing                 <zhoujing@leubao.com>
    * @DateTime 2019-11-15T13:44:31+0800
    * @param    inr                   $pid                  产品id
    * @param    array               $ticket               返回票型
    * @return   [type]               [description]
    */
    function getplan($pid, $user, $ticket = array()){
        $product = M('Product')->where(array('status'=>1,'id'=>$pid))->field('id')->select();
        $info['product'] = arr2string($product,'id');
        //根据当前用户判断   若为散客  读取配置项  微信的价格政策
        $info['group']['price_group'] = $user['group']['price_group'];
        $info['scene'] = '2';
        $plan = \Libs\Service\Api::plans($info, '', '', $ticket);
        foreach ($plan['plan'] as $key => $value) {
            $plans['plan'][] = array(
                'title' =>  $value['title'],
                'id'    =>  $value['id'],
                'num'   =>  $value['num'],
            );
            if(empty($value['param'])){
                $plans['area'][$value['id']] = [];
            }else{
                $plans['area'][$value['id']] = $value['param'];
            }
            
        }
        return $plans;
    }
    /**
     * 授权绑定
     */
    public function auth()
    {
        
    }
}