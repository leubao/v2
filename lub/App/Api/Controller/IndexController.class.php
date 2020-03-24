<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务 Hprose  客户端
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Common\Controller\ApiBase;
use Libs\Service\Api;
use Libs\Service\Order;
use Libs\Service\Refund;
use Common\Model\Model;

use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Client\Query;


class IndexController extends ApiBase {
  
    //获取场次信息
    function api_plan(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);

      if($appInfo != false){
        //获取销售计划
        $info = Api::plans($appInfo,'', $pinfo['datetime']);
        $return = array(
            'code'  => 200,
            'info'  => $info,
            'msg' => 'OK',
          );
        }else{
          $return = array('code' => 401,'info' => '','msg' => '认证失败');
        }
      }else{
        $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    //获取订单列表数据
    public function api_order_list()
    {
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo != false){
          $date = time();
          //获取上次执行时间
          $htime = F('SynchronizationTime');
          if(empty($htime)){
            $htime = strtotime('-15 minute');
          }
          $map = array(
            'createtime' => array(
              array('EGT', $htime), 
              array('ELT', $date), 
              'AND' 
            ),
            'product_type' => 1,
            'status' => ['in', ['1','9']]
          );
          $list = [];//->where($map)
          $field = [
            'id',
            'take',
            'phone',
            'order_sn',
            'plan_id',
            'createtime',
            'number',
            'uptime'
          ];
          $list = D('Order')->where(['product_type' => 1,'type'=>2,'status' => ['in', ['1','9']]])->field($field)->relation(true)->limit(10)->order('id desc')->select();
          //获取销售计划
          //$olanId = array_column($list, 'plan_id');
          //获取表
          
          foreach ($list as $k => $v) {
            $info = unserialize($v['info']);
            $ticket = [];
            foreach ($info['data'] as $ke => $va) {
              $ticket[] = [
                'area'   => areaName($va['areaId'], 1),
                'price'  => $va['price'],
                'idcard' => isset($va['idcard']) ? $va['idcard'] : '',
                'seat'   => seatShow($va['seatid'], 1)
              ];
            }
            $v['plan_id'] = planShow($v['plan_id'], 1,1);
            $v['info'] = $ticket;
            unset($v['id']);
            $lists[] = $v;
          }
          $return = array(
            'code'  => 200,
            'info'  => $lists,
            'msg' => 'OK',
          );
          echo json_encode($return);
        }else{
          $return = array('code' => 401,'info' => '','msg' => '认证失败');
        }
      }else{
        $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
    }
    //api 订单写入
    function api_order(){
      if(IS_POST){
          $pinfo = $_POST['data'];
          $pinfo = json_decode($pinfo,true);
          $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']); 
          if($appInfo != false){
              if(!empty($pinfo['sn'])){
                //判断是否已下单
                $sn = $this->query_order(array('sn'=>$pinfo['sn'],'type'=>1));//dump($sn);
                if($sn != false){
                  //已经下单直接返回
                  $return = array(
                      'code'  => 201,
                      'info'  => $sn,
                      'seat'  => sn_seat($sn),
                      'msg'   => 'OK',
                    );
                }else{
                  //组合订单数据
                  $info = $this->order_info($pinfo,$appInfo);
                  //TODO API团队和API散客暂时按照支付方式来分  只记录不付费的51 API散客 52 API团队
                  if($appInfo['is_pay'] == '1'){
                    $scena = '51';
                  }else{
                    $scena = '52';
                  }
                  $order = new Order();
                  $reOrder = $order->orderApi($info,$scena,$appInfo);
                  if($reOrder){
                    $return = array(
                      'code'  => 200,
                      'info'  => $reOrder['order_sn'],
                      'seat'  => sn_seat($reOrder['order_sn']),
                      'msg'   => 'OK',
                    );
                  }else{
                    $return = array('code' => 403,'info' => $order->error,'msg' => '订单提交失败');
                  }
                }
              }else{
                $return = array('code' => 409,'info' => '','msg' => '终端标识不存在');
              }
          }else{
              $return = array('code' => 401,'info' => '','msg' => '认证失败');
          }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    //库存查询
    function api_sku(){
      if(IS_POST){
          $pinfo = $_POST['data'];
          $pinfo = json_decode($pinfo,true);
          $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
          if($appInfo != false){
            $info = sku($pinfo['plan'],$pinfo['area']);
            if($info != false){
              $return = array('code' => 200,'info' => $info,'msg' => 'OK');
            }else{
              $return = array('code' => 407,'info' => '','msg' => '查询失败');
            }
          }else{
            $return = array('code' => 401,'info' => '','msg' => '认证失败');
          }
      }else{
        $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    /*订单查询
    参数 appid appkey sn type 1 终端sn 2 系统SN 3 纯系统取票
    */
    function api_query_order(){
        if(IS_POST){
            $pinfo = $_POST['data'];
            $pinfo = json_decode($pinfo,true);
            $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
            if($appInfo != false){
              //订单查询 不存在返回false  存在返回订单详细信息
              $sn = $this->query_order(array('sn'=>$pinfo['sn'],'type'=>$pinfo['type']));
              if($sn != false){
                $return = array('code' => 200,'info' => $sn,'seat'=>sn_seat($sn),'state'=>$this->query_state($sn),'msg' => 'OK');
              }else{
                $return = array('code' => 407,'info' => '','msg' => '查询失败');
              }
            }else{
              $return = array('code' => 401,'info' => '','msg' => '认证失败');
            }
        }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
        }
        $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
        die(json_encode($return));
    }
    /**
     * 查询订单状态
     * @param  string $order_sn 订单号
     * 0为作废订单1正常2为渠道版订单未支付情况3已取消5已支付但未排座6政府订单7申请退票中9门票已打印11窗口订单创建成功但未排座
     * @return 订单状态信息 100预订成功 200已取票 300取消中 400作废订单[不可用订单] 500 等待景区确认
     */
    function query_state($order_sn = ''){
      $state = D('Order')->where(['order_sn'=>$order_sn])->getField('status');
      switch ($state) {
        case '1':
          return '100';
          break;
        case '9':
          return '200';
          break;
        case '7':
          return '300';
          break;
        case '3':
          return '400';
          break;
        case '11':
          return '400';
          break;
        case '0':
          return '400';
          break;
        case '5':
          return '500';
          break;
        default:
          return '400';
          break;
      }
    }
    /*
    * 短信重发  只支持系统订单号
    *
    */
    function api_sms(){
      if(IS_POST){
          $pinfo = $_POST['data'];
          $pinfo = json_decode($pinfo,true);
          $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
          if($appInfo != false){
            if(Order::repeat_sms($pinfo['sn']) != false){
              $return = array('code' => 200,'info' => '','msg' => '发送成功');
            }else{
              $return = array('code' => 408,'info' => '','msg' => '发送失败');
            }
          }else{
            $return = array('code' => 401,'info' => '','msg' => '认证失败');
          }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      die(json_encode($return));
    }
    /*
    *API订单查询
    */
    function query_order($data = null){
      if(empty($data)){return false;}
      switch ($data['type']) {
        case '1':
          //通过APP_SN 查询
          $order_sn = M('ApiOrder')->where(array('app_sn'=>$data['sn']))->find();
          if(!empty($order_sn)){
            return $order_sn['order_sn'];
          }else{
            return false;
          }
          break;
        case '2':
          //通过票务系统订单号查询
          $order_sn = M('ApiOrder')->where(array('order_sn'=>$data['sn']))->find();
          if($order_sn){
            return $order_sn['order_sn'];
          }else{
            return false;
          }
          break;
        case '3':
          //通过票务系统订单号查询
          $order_sn = M('Order')->where(array('order_sn'=>$data['sn']))->find();
          if($order_sn){
            return $order_sn['order_sn'];
          }else{
            return false;
          }
          break;
        default:
          
          break;
      }
    }
    /**
     * API接口退票 
     * @param $type int 1 整单退 2退其中几张
     * @param $sn  订单号
     * @param $seat string 多个用‘,’分开 
     * @return true|false
     */
    function api_refund(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        $type = $pinfo['type'] ? $pinfo['type'] : '1';
        if($appInfo != false){
          switch ($type) {
            case '1':
              $status = $this->refund($pinfo['sn'],$appInfo);
              break;
            case '2':
              break;
          }
          if($status){
            $return = array('code' => 200,'info' => ['sn'=>$pinfo['sn']],'msg' => '退票成功');
          }else{
            $return = array('code' => 401,'info' => '','msg' => '认证失败1');
          }
        }else{
          $return = array('code' => 401,'info' => '','msg' => '认证失败0');
        }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    /**
     * 退单操作
     * @return [type] [description]
     */
    private function refund($sn,$uinfo){
      //查询订单状态
      $info = M('Order')->where(array('order_sn'=>$sn,'user_id'=>$uinfo['id']))->field('order_sn as sn,plan_id,status')->find();
      if($info['status'] == '1'){
        //判断手续费的事
        $poundage = $this->cost_rules($info['plan_id']);
        return \Libs\Service\Refund::refund($info,1,'','',$poundage,5);
      }
      //执行退票操作
    }
    /**
     * 手续费规则
     * 演出当天三点之前任意退
     * 三点之后扣手续费交易额的20%
     * @param  string $value [description]
     * @return [type]        [description]
     */
    function cost_rules($plan_id){
      //获取当天的场次
      if(in_array($plan_id,array_column(get_today_plan(), 'id'))){
        //判断是否过三点
        if(date('H') > '14'){
          return '3';
        }else{
          return '1';
        }
      }else{
        return '1';
      }
    } 
    /*
    *订单取票
    *$type 取票方式 1 手机号码+订单号 2身份证 3微信
    */
    function api_print(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo != false && $pinfo['type'] != false){
          //加锁
          $sn = $pinfo['type'] == '1' ? $pinfo['sn'] : get_sn_api($pinfo['card']);
          $lock_sn = load_redis('get','lock_'.$sn);
          if(!empty($lock_sn)){
            die(json_encode(['code' => 415,'info' => '','msg' => '订单锁定中...']));
          }
          switch ($pinfo['type']) {
            case '1':
              if($this->print_check($pinfo['sn'],$pinfo['phone']) != false){
                $ticket_info = $this->ticket_info($pinfo['sn'],$appInfo['id'],'1');
                if($ticket_info != false){
                  if($ticket_info['code'] == '211'){
                    $return = array('code' => 211,'info' => $ticket_info,'msg' => '请完成支付');
                  }else{
                    $return = array('code' => 200,'info' => $ticket_info,'msg' => '门票信息获取成功');
                  }
                }else{
                  $return = array('code' => 411,'info' => '','msg' => '门票信息获取失败1');
                }
              }else{
                $return = array('code' => 410,'info' => '','msg' => '取票密码错误');
              }
              break;
            case '2':
              $ticket_info = $this->ticket_info($pinfo['card'],$appInfo['id'],'2');
              if($ticket_info != false){
                $return = array('code' => 200,'info' => $ticket_info,'msg' => '门票信息获取成功');
              }else{
                $return = array('code' => 411,'info' => '','msg' => '门票信息获取失败2');
              }
              break;
          }
        }else{
          $return = array('code' => 401,'info' => '','msg' => '认证失败');
        }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      die(json_encode($return));
    }
    /*
    * 身份证取票
     */
    function api_print_card(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo != false && $pinfo['type'] != false){
          switch ($pinfo['type']) {
            case '2':
              $order_list = $this->order_list($pinfo['card']);
              if($order_list != false){
                $return = array('code' => 200,'info' => $order_list,'msg' => '订单列表获取成功');
              }else{
                $return = array('code' => 411,'info' => '','msg' => '订单列表获取失败');
              }
              break;
          }
        }else{
          $return = array('code' => 401,'info' => '','msg' => '认证失败');
        }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    /*
    * 根据订单号返回要打印信息 禁止取政企订单
    * @param $sn 订单号 
    * @param $type 1、手机号+订单号 2、身份证取票
    * @param $card 身份证取票
    */
    function ticket_info($sn = null,$channel_id = null,$type = '1'){
      switch ($type) {
        case '1':
          if(empty($sn) || sn_length($sn) == false){return false;}
          $map = array('order_sn'=>$sn,'status'=>array('in','1,6'));
          break;
        case '2':
          //TODO  身份证号码校验
          if(checkIdCard($sn) != false){
            $map = array('id_card'=>$sn,'status'=>'1');
          }else{
            return false;
          }
          break;
      }
      //排除禁止打印的订单
      $map['is_print'] = 0;
      $info = M('Order')->where($map)->field('plan_id,product_id,order_sn,status,money,number,take,type,pay,phone')->find();
      if(empty($info)){
        return false;
      }
      if($info['status'] == '1'){
        if(in_array($info['pay'],['2','4','5'])){
          //授信额、支付宝、微信支付
          $plan = F('Plan_'.$info['plan_id']);
          if(empty($plan)){return false;}
          $list = M(ucwords($plan['seat_table']))->where(array('status'=>2,'order_sn'=>$info['order_sn'],'print'=>array('eq',0)))->select();
          foreach ($list as $k=>$v){
            $info[] = re_print($plan['id'],$plan['encry'],$v);
          }
          //锁定时间根据门票数量来确定
          $time = (int)$info['number']*3;
          load_redis('setex','lock_'.$sn,'警告:订单正在出票,稍后再试...',$time);
        }
        if(in_array($info['pay'],['1','3','6'])){
          //return false;
          
          if($info['money'] == '0'){
            return false;
            //return ['code' => 415,'info' => '','msg' => '订单金额不允许当前操作'];
          }
          $payQr = $this->getPayQr($info);
          if($payQr['code'] == 500){
            return false;
          }elseif($payQr['code'] == 200){
            $info['code'] = '211';
            $info['qrurl'] = $payQr['info'];
            $info['pid'] = $info['product_id'];
          }else{
            return false;
          }
          //现金、签单
        }
        return $info;
      }
    }
    /**
     * 获取支付二维码
     * @param  string $sn [description]
     * @return [type]     [description]
     */
    public function getPayQr($info='') 
    {
      $product = D('Product')->where(['id'=>$info['product_id']])->field('id,name,item_id')->find();
      $payData = [
        'subject' => $product['name']."门票",
        'body'    => planShow($info['plan_id'],1,1).$product['name']."门票",
        'order_no'    => $info['order_sn'],
        'timeout_express' => time() + 600,// 表示必须 600s 内付款
        'amount'      => $info['money'],// 单位为元 ,最小为0.01
        'return_param' => [],
        'product_id'    =>  $info['product_id'],
        // 支付宝公有
        'goods_type' => 1,
        'store_id' => '',
        'client_ip' => get_client_ip()
      ];
      /**/
      $qr = load_redis('get','qr_sn_'.$info['order_sn']);
      //发起一次查询 
      $return = \Api\Service\Apipay::orderquery('wx_charge',$product['item_id'],['out_trade_no'=>$info['order_sn'],'sub_appid'=>'wx9e7b571701cc6601','sub_mch_id'=>'1390172002'],2);
      if($return['state'] == 'SUCCESS'){
        return ['code' => 500,'msg' => '订单已支付成功,请勿重复操作'];
      }
      $qr = \Api\Service\Apipay::get_pay_qr('wx_qr',$product['item_id'],$payData);
      $sData = serialize(['product_id'=>$info['product_id'],'code_url'=>$qr,'paytype'=>$pinfo['paytype'],'sn'=>$info['order_sn']]);
      load_redis('setex','qr_sn_'.$info['order_sn'],$sData,'9000');

      return array('code' => 200,'info' => $qr);
    }
    /**
     * 自助机付款
     * 需要传递订单号和支付类型 目前支持alipay wxpay
     * @return [type] [description]
     */
    function api_payment(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $sn = $pinfo['sn'];
        if(empty($sn) || sn_length($sn) == false){
          $return = array('code' => 414,'info' => '','msg' => '提交失败');die(json_encode($return));
        }
        $map = array('order_sn'=>$sn,'status'=>array('in','1,6'));
        $info = M('Order')->where($map)->field('plan_id,order_sn,status,number,take,pay,product_id,money')->find();
        if(empty($info)){
          die(json_encode(['code' => 414,'info' => '','msg' => '未找到失败']));
        }
        if($info['money'] == '0'){
          die(json_encode(['code' => 415,'info' => '','msg' => '订单金额不允许当前操作']));
        }
        if(in_array($info['status'],['1','9']) && !in_array($info['pay'],['1','3'])){
          $return = array('code' => 412,'info' => '','msg' => '订单已完成支付');
        }else{
          $product = D('Product')->where(['id'=>$info['product_id']])->field('id,name,item_id')->find();
          $payData = [
            'subject' => $product['name']."门票",
            'body'    => planShow($info['plan_id'],1,1).$product['name']."门票",
            'order_no'    => $info['order_sn'],
            'timeout_express' => time() + 600,// 表示必须 600s 内付款
            'amount'      => $info['money'],// 单位为元 ,最小为0.01
            //'amount'      => 0.01,// 单位为元 ,最小为0.01
            'return_param' => [],
            'product_id'    =>  $info['product_id'],
            // 支付宝公有
            'goods_type' => 1,
            'store_id' => '',
            'client_ip' => get_client_ip()
          ];
          if($pinfo['paytype'] == 'alipay'){
            $qr = \Api\Service\Apipay::get_pay_qr('ali_qr',$product['item_id'],$payData);
            $sData = serialize(['product_id'=>$info['product_id'],'code_url'=>$qr,'paytype'=>$pinfo['paytype'],'sn'=>$info['order_sn']]);
            load_redis('setex','qr_sn_'.$info['order_sn'],$sData,'9000');
            $return = array('code' => 200,'info' => $qr,'msg' => '等待客户扫码...');
          }elseif($pinfo['paytype'] == 'wxpay'){
            $qr = load_redis('get','qr_sn_'.$info['order_sn']);
            if($qr){
              //发起一次查询
              $queryData = [
                'out_trade_no'=>$info['order_sn']
              ];
              $return = \Api\Service\Apipay::orderquery('wx_charge',$product['item_id'],$queryData,2);
              if($return['state'] == 'SUCCESS'){
                die(json_encode(['code' => 200,'msg' => '订单已支付成功,请勿重复操作']));
              }
            }
            $qr = \Api\Service\Apipay::get_pay_qr('wx_qr',$product['item_id'],$payData);
            $sData = serialize(['product_id'=>$info['product_id'],'code_url'=>$qr,'paytype'=>$pinfo['paytype'],'sn'=>$info['order_sn']]);
            load_redis('setex','qr_sn_'.$info['order_sn'],$sData,'9000');
            $return = array('code' => 200,'info' => $qr,'msg' => '等待客户扫码...');
          }else{
            $return = array('code' => 413,'info' => $qr,'msg' => '不被支持的支付方式');
          }
        }
        die(json_encode($return,JSON_UNESCAPED_UNICODE));
      }
    }
    //前端轮询
    function query_pay_order() 
    {
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $sn = $pinfo['sn'];
        $pid = $pinfo['pid'];
        if(empty($sn) || empty($pid)){
          $return = ['code' => 404,'info' => '','msg' => '缺少必要的查询信息'];
        }else{
          $product = D('Product')->where(['id'=>$pinfo['pid']])->field('id,name,item_id')->find();
          if($pinfo['paytype'] == 'wxpay'){
            $queryData = [
                'out_trade_no'=>$sn
              ];
            $return  = \Api\Service\Apipay::orderquery('wx_charge',$product['item_id'],$queryData);
          }
          if($pinfo['paytype'] == 'alipay'){
            $return  = \Api\Service\Apipay::orderquery('ali_charge',$product['item_id'],['out_trade_no'=>$sn]);
          }
          if($return['state'] == 'SUCCESS'){
            $info = \Api\Service\Apipay::up_order($sn);
            if($info['state'] == 'SUCCESS'){
              //单号和手机号
              $return = array('code' => 200,'info' => ['sn'=>$sn,'phone'=>$info['phone']],'msg' => '支付完成,开始打印门票...');
            }else{
              $return = array('code' => 300,'info' => $qr,'msg' => $info['msg']);
            }
          }else{
            $return = array('code' => 300,'info' => '300','msg' => $return['msg']);
          }
        }
      }else{
        $return = array('code' => 404,'info' => '','msg' => '服务器拒绝连接');
      }
      die(json_encode($return));
    }

    /*
    * 根据身份号码获取订单列表
    */
   function order_list($sn = null){
    load_redis('setex', 'snnss', $sn .'1091', 37000);
      if(empty($sn) || checkIdCard($sn) == false){error_insert('400019');return false;}
      $map = array('id_card'=>$sn,'status'=>'1','pay'=>array('in','2,4,5'),'plan_id'=>array('in',normal_plan()));
      $list = M('Order')->where($map)->field('order_sn,phone,number,plan_id')->order('plan_id')->select();
      if(!empty($list)){
        foreach ($list as $key => $value) {
          $data[] = array(
            'order_sn' => $value['order_sn'],
            'phone' => $value['phone'],
            'number'=>$value['number'],
            'title'=>planShow($value['plan_id'],1,1), 
          );
        }
        return $data;
      }else{
        error_insert('400020');
        return false;
      }    
   }
    /* 更新座椅状态 自助取票机打印门票
    *  $plan 计划id
    *  $seat 座位ID
    *  $priceid 价格id 
    *  $sn 订单号码
    *  $type 1 更新单张状态  2 整单打印完成
    */
    function api_seat_status(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);//dump($pinfo);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo != false){
          $plan = F('Plan_'.$pinfo['plan']);
          if(empty($plan)){
            $return = array('code' => 413,'info' => '','msg' => '场次已过期');
            return false;
          }
          //更新门票打印状态
          $model = new Model();
          $model->startTrans();
          $sn =  $pinfo['sn'];
          //判断订单类型
          $order_type = order_type($sn);
          if($pinfo['type'] == '1'){
            $map = array('order_sn'=>$sn,'id'=>$pinfo['id'],'print'=>array('eq',0));
            $up_print = $model->table(C('DB_PREFIX'). $plan['seat_table'])->where($map)->setInc('print',1); 
            $up_order = true;
            $remark = "打印".$pinfo['seat']."单号".$sn;
            $type = '1';
          }else{
            //更新订单状态
            $up_order = $model->table(C('DB_PREFIX'). order)->where(array('order_sn'=>$sn))->setField('status',9);
            $up_print = true;
            load_redis('delete','lock_'.$sn);
            $remark = "打印定单".$sn."完结";
            $type = '3';
          }
          if($up_print && $up_order){
            //记录打印日志
            print_log($sn,$appInfo['id'],$type,$order_type['channel_id'],$remark,1,6);
            $model->commit();//提交事务
            $return = array('code' => 200,'info' => $pinfo['seat'],'msg' => '状态更新成功');
          }else{
            $model->rollback();//事务回滚
            $return = array('code' => 412,'info' => '','msg' => '状态更新失败');
          }
        }else{
          $return = array('code' => 401,'info' => '','msg' => '认证失败');
        }
      }else{
        $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);
    }
    //验证取票密码 取票凭证 手机号+订单号
    function print_check($sn,$phone){
      if(empty($sn) && empty($phone)){
        return false;
      }else{
        $info = M('Order')->where(array('order_sn'=>$sn,'phone'=>$phone))->field('order_sn,phone')->find();
        if($info){
          $pwd = $this->than_pwd($sn,$phone);
          $pwds = $this->than_pwd($info['order_sn'],$info['phone']);
          if($pwd == $pwds){
            return true;
          }else{
            return false;
          }
        }else{
          return false;
        }
      }
    }
    function than_pwd($sn,$phone){
      $phone = substr($phone,4,6);
      $sn    = substr($sn,7);
      $pwd = md5(($sn+$phone)%256);
      return $pwd;
    }
    //自助机网络状态检测
    function api_check_network(){
      if(IS_POST){
        $return = array('code' => 200,'msg' => '网络正常');
        die(json_encode($return));
      } 
    }
    /**
     * 通用订单接口
     * 产品id
     * appid 
     * appkey 
     * 日期
     * 票型
     * 数量
     * 金额
     * 订单号
     * @return [type] [description]
     */
    function api_booking_order(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']); 
        if($appInfo != false){
          if(!empty($pinfo['sn'])){
            //判断是否已下单
            $sn = $this->query_order(array('sn'=>$pinfo['sn'],'type'=>1));//dump($sn);
            if($sn != false){
                //已经下单直接返回
                $return = array(
                  'code'  => 201,
                  'info'  => $sn,
                  'seat'  => sn_seat($sn),
                  'msg'   => 'OK',
                );
            }else{
              //组合订单数据
              $info = $this->booking_order($pinfo,$appInfo);
              if($info['error'] == '1'){
                $return = array('code' => 406,'msg' => '销售配额不足');
              }else{
                //TODO API团队和API散客暂时按照支付方式来分  只记录不付费的51 API散客 52 API团队
                if($appInfo['is_pay'] == '1'){
                  $scena = '51';
                }else{
                  $scena = '52';
                }
                $order = new Order;
                $reOrder = $order->orderApi($info,$scena,$appInfo,$this->if_seat($pinfo['seat']));
                if($reOrder){
                  $return = array(
                    'code'  => 200,
                    'info'  => array('plan'=>planShow($info['plan_id'],1,1)),
                    'sn'    => $reOrder['order_sn'],
                    'seat'  => sn_seat($reOrder['order_sn']),
                    'msg'   => 'OK',
                  );
                }else{
                  $return = array('code' => 403,'info' => '','msg' => '订单提交失败'.$order->error);
                }
              }
            }
          }else{
            $return = array('code' => 409,'info' => '','msg' => '终端标识不存在');
          }
        }else{
          $return = array('code' => 401,'info' => $pinfo,'msg' => '认证失败');
        }

      }else{
        $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      die(json_encode($return));
    }

    /**
     * 根据日期获取场次  TODO是否指定座位
     * 组合订单数据 多票型情况 ->根据票型获取区域ID
     *  
     */
    function booking_order($pinfo,$appInfo){
      $proList = cache('Product');
      $product = $proList[$pinfo['product_id']];
      $plan = get_date_plan($pinfo['datetime'],'1','2',$pinfo['product_id'],2);
      $number = 0;
      foreach ($pinfo['oinfo'] as $k => $v) {
        $oinfo[] = array(
          'areaId'=>get_ticket_area($v['priceid'],$pinfo['product_id']),
          'priceid'=>$v['priceid'],
          'price'=>$v['price'],
          'num'=>$v['num'],
        );
        $number += $v['num'];
      }
      //判断配额
      $quota = $this->check_quota($plan['id'],$pinfo['product_id'],$appInfo['crm_id'],$number);
      if($quota == false){
          $info = [
            'error' => '1'
          ];
          return $info;
      }
      //重构请求数据
      $info = array(
        'subtotal'  =>  $pinfo['money'],
        'plan_id'   =>  $plan['id'],
        'checkin'   => '1',
        'app_sn'    =>  $pinfo['sn'],
        'data'      =>  $oinfo,
        'id_card'   =>  $pinfo['crm']['id_card'],
        'crm'       =>  array('0'=>array('guide'=>$appInfo['id'],'qditem'=>$appInfo['crm_id'],'phone'=>$pinfo['crm']['phone'],'contact'=>$pinfo['crm']['contact'])),
        'param'     =>  array('0'=>array('tour'=>'0','settlement'=>$appInfo['group']['settlement'],'remark'=>$pinfo['param']['remark'],'id_card'=>$pinfo['crm']['id_card'])),
      );
      return $info;
    }
    /**
     * 下预订订单
     * 类似渠道版的超量申请
     */
    public function api_pre_order()
    {
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo != false && $pinfo['type'] != false){
          switch ($pinfo['type']) {
            case '2':
              $order_list = $this->order_list($pinfo['card']);
              if($order_list != false){
                $return = array('code' => 200,'info' => $order_list,'msg' => '订单列表获取成功');
              }else{
                $return = array('code' => 411,'info' => '','msg' => '订单列表获取失败1');
              }
              break;
          }
        }else{
          $return = array('code' => 401,'info' => '','msg' => '认证失败');
        }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      echo json_encode($return);      
    }
    /**
     * 判断配额
     * @return [type] [description]
     */
    function check_quota($plan_id = '',$product_id = '',$channel_id = '', $number = ''){
      return \Libs\Service\Quota::quota($plan_id,$product_id,$channel_id,$number);
    }
    /**
     * 判断排座方式
     */
    function if_seat($param = 'auto'){
      if($param == 'auto'){
        return '1';
      }
      switch ($param) {
        case 'auto':
          return '1';
          break;
        case 'manual':
          return '2';
          break;
        default:
          return '1';
          break;
      }
    }
    /**
     * api 接口通知取票状态
     * 通过订单号查询取票状态
     * @return [type] [description]
     */
    function api_notice(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo != false){
            //获取订单号
          }else{
            $return = array('code' => 401,'info' => '','msg' => '认证失败');
          }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      die(json_encode($return));
    }
    /**
     * 查询订单状态 条件为订单号
     * 1、查询订单状态
     * 2、查询支付记录状态，若两者状态存在歧义，马上强制屏幕弹窗报警
     */
    function confirm_pay(){
      $sn = I('get.sn');
      if(empty($sn)){return false;}
      $map = array(
        'order_sn' => $sn,
        'status'   => '1',
        );
      $o_status = D('Item/Order')->where($map)->find('id');
      $p_status = D('Pay')->where($map)->find('id');
      if(empty($o_status) || empty($p_status)){
        return false;
      }else{
        return true;
      }
    }
    /**
     * 自助机获取付款二维码
     * 解决微信支付商户号重复的情况
     * 判断先前是否有提交订单，如果有则读取先前提交的信息，若没有则重新创建
     */
    function api_pay_qr(){
      if(IS_POST){
        $pinfo = $_POST['data'];
        $pinfo = json_decode($pinfo,true);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo != false){
            //获取订单号
            $map = [
              'order_sn'=>  $pinfo['sn'],
              'type'    =>  ['in','1,6'],
              'addsid'  =>  ['in','3,7'],/*网站和自助机*/
              'status'  =>  ['in','6,11']
            ];
            $oinfo = D('Item/Order')->where($map)->field('order_sn,product_id,money')->find();
            $config = load_payment('wx_qr',$oinfo['product_id']);
            try {
                $ret = Transfer::run('wx_qr', $config, $data);
            } catch (PayException $e) {
                $this->erun("ERROR:".$e->errorMessage());
                error_insert($e->errorMessage());
                exit;
            }
          }else{
            $return = array('code' => 401,'info' => '','msg' => '认证失败');
          }
      }else{
          $return = array('code' => 404,'info' => '','msg' => '服务起拒绝连接');
      }
      $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
      die(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 支付扫码页面
     * @return [type] [description]
     */
    function paypage(){
      $ginfo = I('get.');
      if(!$ginfo['sn']){
        $this->error("参数错误");
      }
      //判断是否已经完成支付
      $info = D('Order')->where(['order_sn'=>$ginfo['sn']])->field('pay,status')->find();
     // dump($info);
      $status = '0';
      if(!in_array($info['pay'],['1','3','6']) || $info['status'] == '9'){
          $status = '1';
      }
      $this->assign('sn',$ginfo['sn'])->assign('pid',$ginfo['pid'])->assign('status',$status)->display();
    }
    /**
     * 扫码支付通知接口
     */
    function paynotify(){

      //判断通知来路微信还是支付宝
      $pay = & load_wechat('Pay');
      // 获取支付通知
      $notifyInfo = $pay->getNotify(); 

      // 支付通知数据获取失败
      if($notifyInfo===FALSE){
          // 接口失败的处理
          echo $pay->errMsg;
      }else{
          //支付通知数据获取成功
           if ($notifyInfo['result_code'] == 'SUCCESS' && $notifyInfo['return_code'] == 'SUCCESS') {
              // 支付状态完全成功，可以更新订单的支付状态了
              // 1、更新订单状态，查看是否需要后续操作，如排座  
              // 此处有两种情况
              //更新缓存存储状态
              S('pay'.$notifyInfo['out_trade_no'],'200',300);
              // $sn = \Libs\Service\Order::sweep_pay_seat();
              // 2、更新网银支付日志
              $uppaylog = array('status'=>1,'out_trade_no'=>$notifyInfo['transaction_id']);
              $paylog = D('Manage/Pay')->where(array('order_sn'=>$notifyInfo['out_trade_no'],'type'=>2))->save($uppaylog);
              // 3、返回信息
              // @todo 
              // 返回XML状态，至于XML数据可以自己生成，成功状态是必需要返回的。
              // <xml>
              //    return_code><![CDATA[SUCCESS]]></return_code>
              //    return_msg><![CDATA[OK]]></return_msg>
              // </xml>
              return xml(['return_code' => 'SUCCESS', 'return_msg' => 'DEAL WITH SUCCESS']);
           }
      }
    }
    
    //构造订单请求
    //$pinfo1 = '{"subtotal":288,"checkin":1,"data":[ {"areaId":21,"priceid":27,"price":288,"num":"1"} ],"param":[{"guide":"测试","qditem":"爱上大声地","phone":18631451216,"contact":"啊实打实"},{"cash":288,"card":0,"alipay":0}]}';
    /*
    array(5) {
  ["subtotal"] => int(814)
  ["checkin"] => int(1)
  ["data"] => array(3) {
    [0] => array(4) {
      ["areaId"] => int(88)
      ["priceid"] => int(1)
      ["price"] => int(218)
      ["num"] => string(1) "1"
    }
    [1] => array(4) {
      ["areaId"] => int(89)
      ["priceid"] => int(10)
      ["price"] => int(298)
      ["num"] => string(1) "1"
    }
    [2] => array(4) {
      ["areaId"] => int(89)
      ["priceid"] => int(4)
      ["price"] => int(298)
      ["num"] => string(1) "1"
    }
  }
  ["crm"] => array(1) {
    [0] => array(4) {
      ["guide"] => int(2)
      ["qditem"] => int(10)
      ["phone"] => int(18634151216)
      ["contact"] => string(2) "sa"
    }
  }
  ["param"] => array(1) {
    [0] => array(2) {
      ["tour"] => int(18)
      ["remark"] => string(11) "sadadadasda"
    }
  }
} */ 
    function order_info($pinfo,$appInfo){
        $info = array(
          'subtotal'  =>  $pinfo['money'],
          'plan_id'   =>  $pinfo['plan'],
          'checkin'   => '1',
          'app_sn'    =>  $pinfo['sn'],
          'data'      =>  $pinfo['oinfo'],
          'crm'       =>  array('0'=>array('guide'=>$appInfo['id'],'qditem'=>$appInfo['crm_id'],'phone'=>$pinfo['crm']['phone'],'contact'=>$pinfo['crm']['contact'])),
          'param'     =>  array('0'=>array('tour'=>'0','settlement'=>$appInfo['group']['settlement'],'remark'=>$pinfo['param']['remark'])),
        );
        //dump($pinfo['param']);
        return $info;
    }

    //校验传递过来的数据
    function format_seat($pinfo,$appInfo){
        $plan = F('Plan_'.$pinfo['plan']);
        //重组座位
        $seat = Order::area_group($pinfo['oinfo'],$plan['product_id'],$appInfo['group']['settlement']);
        
        $ticketType = F("TicketType".$plan['product_id']);
        foreach ($seat['area'] as $k => $v) {
          foreach ($v['seat'] as $ke => $va) {
            $price = $ticketType[$va['priceid']];
            $money += $va['num']*$price['price'];
            $moneys += $va['num']*$price['discount'];
          }
        }
        if(bccomp((float)$pinfo['money'],(float)$moneys,2) == 0){
          return $seat;
        }else{
          return false;
        }
    }
    /**
     * 阿里支付网关
     */
    function alipay_gateway()
    {
      # code...
    }

    
    
    
   /*更新订单票型
      $sn = "61002164916451,61002165541576,61002165521993,61002165536913,61002165561076,61002165562356,61002165594474,61002165562829, 61002165560148,61002165588071,61002165574850";
      $map = array(
        'order_sn' => array('in',$sn),
      );
      $list = D('Item/Order')->where($map)->relation(true)->select();
      
      foreach ($list as $key => $value) {
        $info = unserialize($value['info']);
        $money = 98*$value['number'];
        foreach ($info['data'] as $ke => $va) {
            $data[$value['order_sn']][] = array(
              'ciphertext' => $va['ciphertext'],
              'priceid' => '33',
              'price' => "98.00",
              'discount' => "98.00",
              'id' => $va['id'],
              'plan_id' => $va['plan_id'],
              'child_ticket' => ''
            );
        }
        $infos = array(
          'subtotal'  =>  $money,
          'data'      =>  $data[$value['order_sn']],
          'crm'       =>  $info['crm'],
          'pay'       =>  '1',
          'param'     =>  $info['param'],
          'child_ticket' => ''
          );
        $status1 = M('Order')->where(array('order_sn'=>$value['order_sn']))->setField('money',$money);
        $status2 = M('OrderData')->where(array('order_sn'=>$value['order_sn']))->setField('info',serialize($infos));
        dump($status1);dump($status2);
      }*/
      //dump($info);
      /*
    //  $sn = "60306141189367,60306141131724,60306141130886,60306141152431,60306141198810,60306141174687";
      $sns = explode(',', $sn);
      //dump($sns);
      foreach ($sns as $key => $value) {
        $map = array(
          'order_sn' => $value,
          //'status' => '9',
          'type'  => array('in','2,4'),
          //'subtract' => '1',
        );
        $info = D('Item/Order')->where($map)->relation(true)->find();
        $info['info'] = unserialize($info['info']);
        //dump($info);
        $rebate = $this->rebate($info['info']['data'],$info['product_id']);
        $teamData = array(
          'order_sn'    => $value,
          'plan_id'     => $info['plan_id'],
          'product_type'  => $info['product_type'],//产品类型
          'product_id'  => $info['product_id'],
          'user_id'     => $info['user_id'],
          'money'     => $rebate,
          'guide_id'    => $info['info']['crm'][0]['guide'],
          'qd_id'     => $info['info']['crm'][0]['qditem'],
          'status'    => '1',
          'number'    => $info['number'],
          'type'      => $info['type'],//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
          'createtime'  => time(),
          'uptime'    => time(),
        );
        $in_team = D('TeamOrder')->add($teamData);
        dump($in_team);
      }
      //dump($info);*/
    
    //计算补贴金额
    function rebate($seat,$product_id){
      $ticketType = F("TicketType".$product_id);
      foreach ($seat as $k=>$v){
        //计算订单返佣金额
        $rebate += $ticketType[$v['priceid']]['rebate'];
      }
      return $rebate;
    }
    //校验余额
    function check_yu(){
      //按一级渠道商读取资金往来明细189
      $list = M('CrmRecharge')->where(array('crm_id'=>array('in',agent_channel('189'))))->select();
      //echo count($list);
      //指定渠道商初始金额
      $money = '0';
      //通过循环纠正单次消费后的余额
      //分类（1：充值；2：花费3:返佣4：退票5:提现）
      foreach ($list as $key => $value) {
        switch ($value['type']) {
          case '1':
            $money1 += $value['cash'];
            break;
          case '2':
            $money2 -= $value['cash'];
            break;
          case '3':
            $money3 += $value['cash'];
            break;
          case '4':
            $money4 += $value['cash'];
            break;
          case '5':
            $money5 -= $value['cash'];
            break;
        }
        /*
        $status = M('CrmRecharge')->where(array('id'=>$value['id']))->save(array('balance'=>$money,'remark'=>'o'));
        if($status){
          echo $value['id'] .'su<br />';
        }else{
          echo $value['id'] .'er<br />';
        }*/

      }
        echo $money1 .'<br />';
        echo $money2 .'<br />';
        echo $money3 .'<br />';
        echo $money4 .'<br />';
        echo $money5 .'<br />';
        echo $money1+$money2+$money3+$money4+$money5;
    }
    function report(){
      $datetime= date('Ymd');
      Report::report($datetime);
    }
    /**
     * 农行充值
     * @return [type] [description]
     */
    function abc_notify(){
      //判断是否成功
      //if()
        
    }
    //测试批量汇总
    function cs_sum(){
      $datetime = '20160816';

      $list = \Libs\Service\ReportSum::summary($datetime);
      //dump($list);
    }
  /*****************************第三方支付******************************/
  public function notify(){
    //use Payment\Common\PayException;
   // use Payment\Client\Notify;
  }
  public function ticket()
  {
    $sn = I('get.tid');
    if(empty($sn)){
      $class = 'error';
    }else{
      $sn = getCodeToId($sn, 8);
      $ticket = \Libs\Service\Ticket::createTicket($sn[0], 2);
      if($ticket['status']){
        $class = 'success';
      }else{
        $class = 'error';
      }
    }
    $this->assign('class',$class);
    $this->assign('ticket',$ticket);
    $this->display();
  }
  public function cancel()
  {
    if(IS_POST){
      $pinfo = I('post.');
      if($pinfo['type'] == 'sn'){
        $order = D('Order')->where(['order_sn'=>$pinfo['sn']])->field('plan_id,number,status')->find();
        if(empty($order)){
          $return = [
            'status'=> false,
            'code'  => 1000,
            'data'  => [],
            'msg'   => '未找到有效订单信息'
          ];
          die(json_encode($return)); 
        }
        $count = D('Scenic')->where(['order_sn'=>$pinfo['sn'],'status'=>2])->count();
        if($count > 0){
          $ticket = D('Scenic')->where(['order_sn'=>$pinfo['sn'],'status'=>2])->field('order_sn,price_id,ciphertext,plan_id,id')->select();
          if(!empty($ticket)){
            foreach ($ticket as $k => $v) {
              $tickets[] = [
                'sn'    =>  $v['order_sn'],
                'price' =>  ticketName($v['price_id'],1),
                'plan'  =>  planShow($v['plan_id'],2,1),
                'ticket'=>  $v['ciphertext'], 
                'id'    =>  $v['id'],
              ];
            }
          }
        }
        $return = [
          'status'=> true,
          'code'  => 0,
          'data'  => [
            'sn'    => $pinfo['sn'],
            'plan'  => planShow($order['plan_id'],2,1),
            'number'=> $order['number'],
            'count' => $count,
            'ticket'=> $tickets ? $tickets : []
          ]
        ];
        die(json_encode($return));
      }
    }else{
      $this->display();
    }
  }
  public function checkin()
  {
    if(IS_POST){
      $pinfo = I('post.');
      $upTicket = [
        'status'    =>  99,
        'checktime' =>  time()
      ];
      if($pinfo['type'] == 'all'){
        //全部核销
        $ticket = D('Scenic')->where(['order_sn'=>$pinfo['sn'],'status'=>2])->setField($upTicket);
        $order = D('Order')->where(['order_sn'=>$pinfo['sn']])->setField('status',9);
        if($ticket && $order){
          $count = D('Scenic')->where(['order_sn'=>$pinfo['sn'],'status'=>99])->count();
          $return = [
            'status'=> true,
            'code'  => 0,
            'data'  => [
              'count' => $count,
            ],
            'msg'   =>  '成功核销'.$ticket.'张'
          ];
          die(json_encode($return));
        }else{
          $return = [
            'status'=> false,
            'code'  => 1000,
            'data'  => [
              'count' => $count,
            ],
            'msg'   =>  '核销失败,请重试'
          ];
          die(json_encode($return));
        }
      }
      if($pinfo['type'] == 'small'){
        
        foreach ($pinfo['info'] as $k => $v) {
          $ticId[] = $v['id'];
        }
        $id = implode(',',$ticId);
        $where = [
          'order_sn'  =>  $pinfo['sn'],
          'id'        =>  ['in',$id]
        ];
        $count = D('Scenic')->where(['order_sn'=>$pinfo['sn'],'status'=>2])->count();
        if($count == 0){
          $return = [
            'status'=> false,
            'code'  => 1000,
            'data'  => [
              'count' => $count,
            ],
            'msg'   =>  '核销失败,未找到可核销的门票'
          ];
          die(json_encode($return));
        }
        $number = count($pinfo['info']);
        $ticket = D('Scenic')->where($where)->setField($upTicket);
        if($count == $number){
          $order = D('Order')->where(['order_sn'=>$pinfo['sn']])->setField('status',9);
        }
        $more = $count - $number;
        if($ticket){
          $return = [
            'status'=> true,
            'code'  => 0,
            'data'  => [
              'count' => $number,
            ],
            'msg'   =>  '成功核销'.$number.'张,剩余可核销'.$more."张"
          ];
          die(json_encode($return));
        }else{
          $return = [
            'status'=> false,
            'code'  => 0,
            'data'  => [],
            'msg'   =>  '核销失败,请重试'
          ];
          die(json_encode($return));
        }
      }
    }
  }
  public function getCodeToId()
  {
    $info = I('post.');
    load_redis('set', '22', json_encode($info));
    $qr = \Libs\Service\Encry::getQrData($info['content']);
    die(json_encode($qr));
  }
}