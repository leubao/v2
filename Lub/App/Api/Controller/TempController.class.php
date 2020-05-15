<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务测试端口 Hprose  客户端
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Common\Controller\ApiBase;
use Libs\Service\Api;
use Libs\Service\Order;
use Common\Model\Model;
use Libs\Service\Report;
use Libs\Service\ArrayUtil;

class TempController extends ApiBase {

  function tongpiao(){
    //1、基于主体读取统票订单
   //  $list = D('Order')->where(['activity'=>58,'createtime'=>['gt',strtotime('20200501')],'status'=>['in', ['1','9']]])->field('id,order_sn,number,money')->select();
   //  $olist = D('OrderData')->where(['oid'=>['in', array_column($list, 'id')]])->select();
   // dump($list);
   //  foreach ($olist as $k => $v) {
   //    $info = unserialize($v['info']);
   //    $ids = array_column($info['data'], 'id');
   //    $ids = array_unique($ids);
   //    $data = [];
   //    foreach ($info['data'] as $key => $value) {

   //      if(in_array($value['id'], $ids)){
   //        $ids = array_diff($ids, [$value['id']]);
   //        $data[] = $value;
   //        dump($value);
   //      }
   //    }
   //    $info['data'] = $data;
   //    dump($v);
   //    dump($info);
   //    D('OrderData')->where(['oid'=>$v['oid']])->setField('info', serialize($info));
   //    //dump($info);
   //  }
    //2、重新计算统票总数
    // foreach ($list as $k => $v) {
    //   $dlist = D('Drifting')->where(['order_sn'=>$v['order_sn']])->select();
    //   dump($dlist);
    //   if($v['number'] === count($dlist)){
    //     echo $v['number'].'='.$count.'='.$v['order_sn'].'<br />';
    //   }
    // }
    //3、更新总数
  }

  function get_temp_seat(){
    //读取模板
    $area = D('Area')->where(['status'=>1,'template_id'=>32])->field('id,name,seat,seats,seatid')->select();
    $row = [];
    $col = [];
    $seatid = [];
    foreach ($area as $k => $v) {
      $seat = unserialize($v['seats']);
      $rows = str_replace("','", ",", $seat['rows']);
      $cols = str_replace("','", ",", $seat['columns']);
      if(!empty($row)){
        $row = array_merge($row, explode(',', $rows));
      }else{
        $row = explode(',', $rows);
      }

      if(!empty($col)){
        $col = array_merge($col, explode(',', $cols));
      }else{
        $col = explode(',', $cols);
      }

      //合并已存在的座位
      if(empty($seatid)){
        $seatid = unserialize($v['seatid']);
      }else{
        $seatid = array_merge($seatid, unserialize($v['seatid']));
      }
      
    }dump($seatid);
    //去重
    $row = array_unique($row);
    $col = array_unique($col);
    //dump($row);dump($col);
    //排序
    sort($row);
    sort($col);  
    //获取首尾
    //判断是否区分单双号 TODO
    $odd = [];
    $even = [];
    $rowList = [];
    $colList = [];
    foreach ($col as $k => $v) {
      if($v%2 !== 0){
        $odd[] = $v;
      }else{
        $even[] = $v;
      }
    }
    //奇数降序，
    asort($odd);
    //偶数升序
    arsort($even);
    //集合
    $colList = array_merge($even,$odd);
    //判断朝向 TODO
    $rowList = rsort($rowList);
    //生成座位
    foreach ($rowList as $key => $value) {
      foreach ($colList as $k => $v) {
        //判断是否存在座位
        
        $seatList[$value][] = [
          'row'     => $value,  
          'col'     => $v,
          'is_seat' => $is_seat,
          'bgColor' => $bgColor,
          'seat'    => $value.'-'.$v,
        ];
      }
    }

  }
  function get_seat_list(){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, sign, timestamp, Authorization, X-Requested-With, token');
    $row = 35;
    $col = 50;
    $rowList = [];
    $colList = [];
    $odd = [];
    $even = [];
    //格式化列
    for ($i=1; $i < $col+1; $i++) { 
      if($i%2 !== 0){
        $odd[] = $i;
      }else{
        $even[] = $i;
        
      }
    }
    //奇数降序，
    asort($odd);
    //偶数升序
    arsort($even);
    //集合
    $colList = array_merge($even,$odd);
    //格式化行
    for ($i=1; $i < $row+1; $i++) { 
      $rowList[] = $i;
    }
    rsort($rowList);
    //TODO判断是从上到下 还是从下道上
   
    //生成座位
    foreach ($rowList as $key => $value) {
      foreach ($colList as $k => $v) {
        if($v%2 === 0 || $value%2 !== 0){
          $is_seat = true;
        }else{
          $is_seat = false;
        }
        if($value%2 === 0 && $v%2 === 0){
          $bgColor = 'red';
        }
        $seatList[$value][] = [
          'row'     => $value,  
          'col'     => $v,
          'is_seat' => $is_seat,
          'bgColor' => $bgColor,
          'seat'    => $value.'-'.$v,
        ];
      }
    }
    rsort($seatList);
    $return = [
      "code" => 200,
      "data" => [
        'row' =>  $rowList,
        'col' =>  $colList,
        'seat_list' => $seatList
      ],
      "msg" => 'ok'
    ];
    die(json_encode($return));
  }
  //获取销售计划
  function get_plan_list()
  {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, sign, timestamp, Authorization, X-Requested-With, token');
    $plan = D('Plan')->where(['status'=>2])->field('id,plantime,starttime,endtime,games')->select();
    foreach ($plan as $k => $v) {
      $v['title'] = planShow($v['id'], 1, 1);
    }
    $return = [
      "code" => 200,
      "data" => $plan,
      "msg" => 'ok'
    ];
    die(json_encode($return));
  }
  public function cs_link()
  {
      // $start = 1;
      // $end = 100;
      // $row = 31;
      // $map = [
      //   'status' => ['in','1,9'],
      //   'pay'    => 5,
      //   'product_id' => 44,
      //   'type' => 8,
      //   'id' => ['gt','117060']
      // ];
      // $order = D('order')->where($map)->field('order_sn,id')->select();
      // dump($order);
        //短信发送测试
        // $msg = [
        //   'phone' => '13463652179',
        //   'sn'  =>  '12312312',
        //   'product' => '青龙大瀑布',
        //   'title' => '青龙瀑布0801',
        //   'remark' => '青龙大瀑布',
        //   'num' => '22'
        // ];
        //  \Libs\Service\Sms::order_msg($msg,'1');
      
      // foreach ($order as $key => $value) {
      //   load_redis('lpush','PreOrder',$value['order_sn']);
      // }
      //批量生成报表
      // $dateList = getDateFromRange('20200301','20200424');
      // foreach ($dateList as $key => $value) {
      //   $date = date('Ymd',strtotime($value));
        
      //   $return = \Libs\Service\Report::report($date); 
      //   echo $date .'='.$return;
      // }
      // $list = [

      // ];
      // $sn = '3181192757201';
      // $order = D('order_data')->where(['order_sn'=>$sn])->find();
      // $info = unserialize($order['info']);
      // foreach ($info['data'] as $key => $v) {
      //   $v['priceid'] = 567;
      //   $v['discount'] = 269;
      //   $data[] = $v;
      //   $idx[] = $v['id'];
      // }
      // $info['data'] = $data;
      // D('order_data')->where(['order_sn'=>$sn])->setField('info', serialize($info));
      // D('scenic')->where(['order_sn'=>$sn])->setField('price_id', 567);
      // dump($order);
      // for ($i = $start; $i < $end; $i++) {
      //   if($i%2 == 0){
      //     $a[] = $i;
      //   }else{
      //     $b[] = $i;
      //   }
      //   // for ($rr = 1; $rr < $row; $rr++) { 
      //   //   if($i%2 == 0){
      //   //     $seatArr[$rr][$i] = $rr.'-'.$i;
      //   //   }else{
      //   //     $seatArr[$rr][$i] = $rr.'-'.$i;
      //   //   }
      //   // }
      // }
      // rsort($b);
      // $com = array_merge($b,$a);
      // for ($rr = 1; $rr < $row; $rr++) { 
      //   foreach ($com as $key => $value) {
      //     $seatArr[$value][$rr] = $rr.'-'.$value;
      //   }
      // }
      // //var_dump($a,$b);
      // dump(array_merge($b,$a));
      // echo json_encode($seatArr);
      // $sn = I('get.sn');
      // $ticket = \Libs\Service\Ticket::createTicket($sn);
      
      // $qr = \Libs\Service\Encry::getQrData($ticket['sns']);
      // var_dump($ticket, $qr);
      // $plan = D('Plan')->where(['status'=>2])->select();

      // //dump($plan);
      // foreach ($plan as $k => $v) {
      //   $product = D('Product')->where(['id'=>$v['product_id']])->field('id,idCode')->cache('productData', 3600)->find();
      //   $param = unserialize($v['param']);
      //   //dump($param);
      //   if(in_array($v['status'], ['1','3','4'])){
      //     $status = false;
      //   }else{
      //     $status = true;
      //   }
      //   $plans[] = [
      //     'id'        =>  $v['id'],
      //     'plantime'  =>  $v['plantime'],
      //     'starttime' =>  $v['starttime'],
      //     'endtime'   =>  $v['endtime'],
      //     'ticket'    =>  $param['ticket'],
      //     'status'    =>  $status
      //   ];
      //   $postData = [
      //     'product' =>  $product['idcode'],
      //     'plan'    =>  $plans
      //   ];
      //   vendor('Hprose.HproseHttpClient');
      //   $client = new \HproseHttpClient('https://api.leubao.com/open/yunlu/start', false);
      //   // 或者采用
      //   //$data = ['time' => date('Y-m-d H:i:s'),'work' => '99'];
      //   $result = $client->post_plan($postData);
      //   dump($result);
        //删除已过期的Redis
        // $plantime = strtotime('2020-01-01');
        // $plan = D('Plan')->where(['plantime'=>['lt',$plantime],'status'=>'4'])->field('id,seat_table')->select();
        // foreach ($plan as $k => $v) {
        //   $name_group = 'seat_group_'.$v['seat_table'];
        //   load_redis('delete', $name_group);
        //   $seat_table = 'seat_'.$v['seat_table'];
        //   load_redis('delete', $seat_table);
        // }
      
        
    // $plan = D('Plan')->where(['status'=>2])->field('id,product_id,plantime')->select();
    // // dump($plan);
    // foreach ($plan as $k => $v) {
    //   $starttime = date('Y-m-d', $v['plantime']). '20:30:00';
    //   $endtime = date('Y-m-d', $v['plantime']). '21:30:00';
    //   //$endtime = strtotime($endtime);dump($endtime);
    //   $updata = [
    //     'starttime' =>  strtotime($starttime),
    //     'endtime'   =>  strtotime($endtime)
    //   ];
    //   D('Plan')->where(['id'=>$v['id']])->save($updata);
    // }

    // $sn = '9738411';
    // $wechat = & load_wechat('Extends',100,1);dump($wechat);
    // $snTosecret = putIdToCode($sn, 8);
    // $sn = getCodeToId($snTosecret, 8);
    // $lurl = U('Api/Index/ticket',['tid'=>$snTosecret]);
    // $surl = $wechat->getShortUrl($lurl);
    // dump($surl);
    // $list = D("Order")->field('order_sn,product_id')->select();
    // foreach ($list as $k => $v) {
    //   D('Scenic')->where(['order_sn'=>$v['order_sn']])->setField('product_id', $v['product_id']);
    // }
    //删除已过期的Redis
    // $plantime = strtotime('2019-07-01');
    // $plan = D('Plan')->where(['plantime'=>['gt',$plantime],'status'=>'4'])->field('id,seat_table')->select();
    // foreach ($plan as $k => $v) {
    //   $name_group = 'seat_group_'.$v['seat_table'];
    //   load_redis('delete', $name_group);
    //   $seat_table = 'seat_'.$v['seat_table'];
    //   load_redis('delete', $seat_table);
    //}
    // $url = 'https://api.pro.alizhiyou.com/sgqibU2WZ8.txt';
    // $status = getHttpContent($url, 'GET');
    // $url = 'https://api.leubao.com/ctrip';
    // $status = getHttpContent($url, 'POST');
    // dump($status);
    //  $sn = '907011372167120';
    //  $snTosecret = putIdToCode($sn, 8);
    // $qrInfo = ['18512','5911','3722','1','8'];
    //   (int)$position = array_key_last($qrInfo);
    //   dump($qrInfo);dump($position);
    //     $string = '';
    //     foreach ($qrInfo as $k => $v) {
    //         if($k < $position){
    //             $string .= $v;
    //         }
    //     }

    //  dump(creatCheckDigit($string));
    // $scret = getCodeToId($snTosecret, 8);
    // dump($scret);
    // $lurl = U('Api/Index/ticket',['tid'=>$snTosecret]);
    // dump($lurl);
  }
  public function index()
  {
     
        $nextId = '46704';
        
        $date = date('Ymd');
        //load_redis('delete', 'notice_sn_'.$date);
        $datetime = strtotime($date);
      //获取API下单的订单号
        $olist = D('Order')->where(['createtime'=>['gt', $datetime], 'status'=>9, 'addsid'=>5])->field('id,order_sn,number')->order('id desc')->select();
        //二维数组转一维数组
        $nSnArr = array_column($olist,'order_sn');
        //获取今天已通知的sn
        $snList = load_redis('lrange','notice_sn_'.$date,0,-1);
        dump($snList);
        if(empty($snList)){
            $diffSn = $nSnArr;
            //判断
            $hdate = date('Ymd', strtotime('-1 day'));
            $hSn = load_redis('lsize', 'notice_sn_'.$hdate);
            if($hSn > 0){
              load_redis('delete', 'notice_sn_'.$date);
            }
        }else{
            //取得差集
            $diffSn = array_diff($nSnArr,$snList);
        }
        foreach ($olist as $k => $v) {
            if(in_array($v['order_sn'], $diffSn)){
                //load_redis('lpush','notice_sn_'.date('Ymd'), $v['order_sn']);
            }
        }
        //dump($diffSn);
  }
  /**
     * 设置签名
     * @author helei
     */
    public function setSign($data)
    {
        $values = ArrayUtil::removeKeys($data, ['sign','appkey']);

        $values = ArrayUtil::arraySort($values);

        $signStr = ArrayUtil::createLinkstring($values);

        $values['sign'] = $this->makeSign($signStr,$data['appkey']);
        return $values;
    }
    /**
     * 签名算法实现 目前签名算法支持md5
     * @param string $signStr 签名字符串
     * @param string $appeky app秘钥
     * @return string
     */
    protected function makeSign($signStr,$appkey)
    {
        $signStr .= '&key=' . $appkey;
        $sign = md5($signStr);
        return strtoupper($sign);
    }
        //测试计划接入
    function c_plan(){
      $url = "http://ticket.leubao.com/api.php?a=api_plan";
      $post = array(
        'appid' => '26628',
        'appkey'=> '8613f25b1f2691c8a1db85f1cb095d29',
      );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump(json_encode($aa));
    }
    //测试order
    function c_order(){
      $url = "http://ticket.leubao.com/api.php?a=api_order";
      $post = array(
        'appid' => '26628',
        'appkey'=> '8613f25b1f2691c8a1db85f1cb095d29',
        'money' =>  '0.01',
        'plan'  =>  '3659',
        'sn'    =>  get_order_sn('3299'),
        'oinfo' =>  array('0'=>array('areaId'=>'151','priceid'=>'34','price'=>'0.01','num'=>'1')),
        'crm'   =>  array('contact'=>'联系人','phone'=>'18631451216'),
        'param' =>  array('remark'=>'备注..')
      );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
    }

    //测试通用order
    function c_booking_order(){
      $url = "http://ticket.leubao.com/api.php?a=api_booking_order";
      $post = array(
        'appid' => '26628',
        'appkey'=> '8613f25b1f2691c8a1db85f1cb095d29',
        'money' =>  '0.01',
        'product_id' => '41',
        'datetime'  =>  '2018-09-30',
        'sn'    =>  get_order_sn('13999'),
        'oinfo' =>  array(array('priceid'=>'34','price'=>'0.01','num'=>'1')),
        'crm'   =>  array('contact'=>'联系人','phone'=>'18631451216','id_card'=>''),
        'param' =>  array('remark'=>'备注..')
      );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
    }
    //测试库存查询
    function c_sku(){
      $url = "http://tickets.leubao.com/api.php?a=api_sku";
      $url = "http://new.leubao.com/api.php?a=api_plan";
      $post = array(
        'appid' => '26628',
        'appkey'=> '8613f25b1f2691c8a1db85f1cb095d29',
        'plan'  =>  '86',
        'area' =>  '89',
        );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);

    }
    //测试订单查询 type 1 order_sn 票务系统订单号查询 2 app_sn 查询  3 根据order_sn 查询订单
    function c_query_order(){
      $url = "http://tickets.leubao.com/api.php?a=api_query_order";
      $url = "http://new.leubao.com/api.php?a=api_plan";
      $post = array(
        'appid' => '39989',
        'appkey'=> 'c922b084221663d43ef62e54142923a7',
        'type'  =>  '3',
        'sn' =>  '50824141140608',
      );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
    }
    //短信重发
    function c_tosms(){
      $url = "http://tickets.leubao.com/api.php?a=api_sms";
      $url = "http://new.leubao.com/api.php?a=api_plan";
      $post = array(
        'appid' => '39989',
        'appkey'=> 'c922b084221663d43ef62e54142923a7',
        'sn' =>  '50701141140620',
        );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
    }
    //自助机dayin
    function c_print(){
      $url = "http://new.leubao.com/api.php?a=api_plan";
      $post = array(
        'appid' => '65535',
        'appkey'=> 'a646ce13e4c01f42b8ac2a0ca879069',
        'sn' =>  '51111143165',
        'phone'=>'18631451216',
       // 'card'  => '4',
        'type' => '1',
        );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
      dump(json_decode($aa));
    }
    //测试api 退票
    function c_refund(){
      /*
      $db = D('User');
      $list = $db->where(array('groupid'=>2))->field('id')->select();
      foreach ($list as $k => $v) {
        $status = D('Order')->where(array('user_id'=>$v['id']))->field('id')->find();
        if(!$status){
          $del[] = $v['id'];
        }
      }
      $delid = implode(',',$del);
      $db->where(array('id'=>array('in',$delid)))->delete();
      D('UserData')->where(array('user_id'=>array('in',$delid)))->delete();
      */
      $url = "http://ticket.leubao.com/api.php?a=api_refund";
      $post = array(
        'appid' => '26628',
        'appkey'=> '8613f25b1f2691c8a1db85f1cb095d29',
        'sn'    =>  '710131341323774',
        'type'  => '1',
      );
      $post['data'] = json_encode($post);
      //dump($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
      //dump(json_encode($aa));
    }
    //测试网络
    function c_network(){
      $url = "http://www.yxpttk.com/api.php?a=api_check_network";
      $post = array(
        'appid' => '26628',
        'appkey'=> '8613f25b1f2691c8a1db85f1cb095d29',
      );
      $orderNo = time() . rand(1000, 9999);
      // 订单信息
      $payData = [
          'body'    => 'test body',
          'subject'    => 'test subject',
          'order_no'    => $orderNo,
          'timeout_express' => time() + 600,// 表示必须 600s 内付款
          'amount'    => '0.01',// 单位为元 ,最小为0.01
          'return_param' => '123',

          // 支付宝公有
          'goods_type' => 1,
          'store_id' => '',

          // 条码支付
          'operator_id' => '',
          'terminal_id' => '',// 终端设备号(门店号或收银设备ID) 默认值 web
          'alipay_store_id' => '',
          'scene' => 'bar_code',// 条码支付：bar_code 声波支付：wave_code
          'auth_code' => '1231212232323123123',

          // web支付
          'qr_mod' => '',//0、1、2、3 几种方式
          'paymethod' => 'creditPay',// creditPay  directPay

          'client_ip' => '127.0.0.1',

          'openid' => 'ohQeiwnNrAg5bD7EVvmGFIhba--k',
          'product_id' => '123',
      ];
      try {
          $ret = Payment\Client\Charge::run($channel, $config, $payData);
      } catch (Payment\Common\PayException $e) {
          echo $e->errorMessage();
          exit;
      }
      //load_redis('lpush','WechatPayOrder','70301190632334');
      /*
      $len = load_redis('lsize','test','1212211212');
      load_redis('set','work','qqqqq');
      $sn = load_redis('get','work');
      //$sn = load_redis('rPop','test');
      load_redis('setex','t2i','1221',60);
      //判断队列的长度
      //load_redis('delete','work');
      dump($len);
      dump($sn);
      //下单测试数据
      {"crm":{"contact":"测试","phone":"18631451216","id_card":"350783199304213022"},"datetime":"2017-04-2","money":143,"product_id":"41","oinfo":[{"priceid":"33","num":"1","price":143}],"sn":"16088221778323","appid":"38642","appkey":"3aaa5ed4f614668ba10f4dc807b23541"}
      
      $whoops = new \Whoops\Run();
      $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
      $whoops->register();

      // 测试未捕获的异常
      $this->division(10, 0);
      
     
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);

      $payData = [
          "order_no"  => '201612311430',
          "amount"  => '10.00',// 单位为元 ,最小为0.01
          "client_ip" => '127.0.0.1',
          "subject" => 'test',
          "body"  => 'test wap pay',
          "show_url"  => 'https://helei112g.github.io/',// 支付宝手机网站支付接口 该参数必须上传 。其他接口忽略
          "extra_param" => '',
      ];
      dump($this->pid);
      $config = load_payment('alipay',$this->pid);*/
      //dump($config);
      /*
      $charge = new ChargeContext();
      try {
          // 支付宝即时到帐接口  新版本，不再支持该方式
          //$type = Config::ALI_CHANNEL_WEB;

          // 支付宝 手机网站支接口
          $type = Config::ALI_CHANNEL_WAP;

          // 支付宝 移动支付接口
          //$type = Config::ALI_CHANNEL_APP;

          // 支付宝  扫码支付
          //$type = Config::ALI_CHANNEL_QR;

          $charge->initCharge($type, $config);

          // 微信 扫码支付
          //$type = Config::WX_CHANNEL_QR;

          // 微信 APP支付
          //$type = Config::WX_CHANNEL_APP;

          // 微信 公众号支付
          //$type = Config::WX_CHANNEL_PUB;

          //$charge->initCharge($type, $wxconfig);
          $ret = $charge->charge($payData);
      } catch (PayException $e) {
          echo $e->errorMessage();exit;
      }
      if ($type === Config::ALI_CHANNEL_APP) {
          echo $ret;exit;
      } elseif ($type === Config::ALI_CHANNEL_QR) {
          $url = \Payment\Utils\DataParser::toQRimg($ret);// 内部会用到google 生成二维码的api  可能有些同学反应很慢
          echo "<img alt='支付宝扫码支付' src='{$url}' style='width:150px;height:150px;'/>";exit;
      } elseif ($type === Config::WX_CHANNEL_QR) {
          $url = \Payment\Utils\DataParser::toQRimg($ret);
          echo "<img alt='微信扫码支付' src='{$url}' style='width:150px;height:150px;'/>";exit;
      } elseif ($type === Config::WX_CHANNEL_PUB) {
          $json = $ret;
          var_dump($json);
      } elseif (stripos($type, 'wx') !== false) {
          var_dump($ret);exit;
      } elseif (stripos($type, 'ali') !== false) {
          // 跳转支付宝
          header("Location:{$ret}");
      }*/
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump($aa);
    }
    //更新客源地统计
    function ky(){
      $db = M('ReportData');
      //读取渠道订单
      $map = array(
        'status' => '1',
        'type'    => '4',
        );
      $list = $db->where($map)->field('order_sn')->limit('1,20')->select();
      //读区地区信息
      foreach ($list as $key => $value) {
        $oinfo = M('OrderData')->where(array('order_sn'=>$value['order_sn']))->field('info')->find();
        $info = unserialize($oinfo['info']);
        dump($info['param'][0]['tour']);
        //更新订单地区
        $status = $db->where(array('order_sn'=>$value['order_sn']))->setField('region',$info['param'][0]['tour']);
        if($status){
          echo $value['order_sn']."ok<br />";
        }else{
          echo $value['order_sn']."error<br />";
        }
      }
    }
    //查询花费和返佣不匹配的订单
    function with_fill(){
      //查询所有渠道订单
      $list = M('Order')->where(array('type'=>array('in','8,9'),'status'=>array('in','1,9,7,8')))->limit('1,500')->field('order_sn')->order('id DESC')->select();
      //匹配返佣订单
      foreach ($list as $k => $v) {
        $status = M('TeamOrder')->where(array('order_sn' => $v['order_sn']))->find();
        if(!$status){
          load_redis('lpush','PreOrder',$v['order_sn']);
        }
      }
      //echo "string";*/
      
      /*查询所有退单、判断是否返还票款
      $list = M('TicketRefund')->where(array('re_type'=>1,'status'=>3))->field('id,order_sn')->select();
      $db = M('CrmRecharge');
      foreach($list as $k=>$v){
            $status = $db->where(array('order_sn'=>$v['order_sn'],'type'=>2))->find();
          if(!$status){
            $statu = $db->where(array('order_sn'=>$v['order_sn'],'type'=>4))->find();
            if(!$statu){
              dump($v['order_sn']);
            }
            
          }
      }*/
      //删除7、8、9月报表数据
      /*
      $map= array('datetime'=>array(array('EGT', '20160701'), array('ELT', '20160930'), 'AND'));
      $status = M('ReportData')->where($map)->delete();
      dump($status);
      
      for ($i=10; $i < 31; $i++) {
        $datetime = '201609'.$i;
        $status = \Libs\Service\Report::report($datetime);
        //dump($status);
      }*/

    }
    //生成sql语句
    function sqlshow(){
      //$map = array('status'=>array('neq','4'));
      //$list = M('TeamOrder')->where($map)->field('order_sn')->select();
      //dump($list);
      //按订单返佣
      //foreach ($list as $key => $value) {
          //$info[$key] = \Libs\Service\Rebate::rebate($value,1);
      //}
      $info = D('Item/Order')->where(array('order_sn'=>'70314158721843'))->relation(true)->find();
      $info['info'] = unserialize($info['info']);
      dump($info);
      /*
      $map = array(
        'order_sn' => $sn,
        //'status' => '9',
        'type'  => array('in','2,4'),
        //'subtract' => '1',
      );
      $info = D('Item/Order')->where($map)->relation(true)->find();
      $info['info'] = unserialize($info['info']);
      //dump($info);
      $rebate = $this->rebate($info['info']['data'],$info['product_id']);
      $teamData = array(
        'order_sn'    => $sn,
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
      return $in_team;
    */
    }
    //重构订单详情 241
    public function reset_order($sn='')
    {
      if(!empty($sn)){
        $info = D('Item/Order')->where(array('order_sn'=>$sn))->relation(true)->find();
        $info['info'] = unserialize($info['info']);
        $ticketType = F("TicketType".$info['product_id']);
        foreach ($info['info']['data'] as $k => $v) {
          $ticket = $ticketType['241'];
          $data[] = [
            "areaId" => $v['areaId'],
            "priceid" => $ticket['id'],
            "price" => $ticket['price'],
            "discount" => $ticket['discount'],
            "seatid" => $v['seatid'],
          ];
        }
        $info['info']['data'] = $data;
        /*更新金额
        D('Item/Order')->where(array('order_sn'=>$sn))->setField('money','140');
        //返还多扣金额
        $cid = money_map($info['channel_id']);
        $money= '140';
        $crmData = array('cash' => array('exp','cash+'.$money),'uptime' => time());
        $c_pay = D("crm")->where(array('id'=>$cid))->setField($crmData);
        $datas = array(
          'cash'    =>  $money,
          'user_id' =>  '1',
          'crm_id'  =>  $cid,
          'createtime'=>  time(),
          'type'    =>  '1',
          'order_sn'  =>  $info['order_sn'],
          'balance' =>  balance($cid),
          'remark'  =>  '返还'.$sn.'错误扣款'
        );
        $c_pay2 = D('CrmRecharge')->add($datas);*/
        //更新详情
        //D('OrderData')->where(array('order_sn'=>$sn))->setField('info',serialize($info['info']));
        dump($info);
        dump($data);
      }
      
    }
    public function tianchong()
    {
        for($i;$i<500;$i++){
          load_redis('lpush','check_order_41',get_order_sn('891'));
        }
    }
    function sy(){
      \Libs\Service\Check::check_ticket_order_tag();
    }
    //生成结算数据
    public function js($starttime = '', $endtime = '')
    {
        //$return = Report::months2($starttime);
      
        $begintime = strtotime($starttime);$endtime = strtotime($endtime);
        for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
            echo date('H:i:s') ."<br />";
            usleep(500000);
            echo date("Ymd", $start), "<br />";
            $datetime = date("Ymd", $start);
            //D('ReportSum')->where(['plantime'=>$datetime])->delete();
            $return = Report::months2($datetime);
            usleep(500000);
            dump($return);
           // echo $return."<br />";;
        }
        
        //dump($return);
    }
    public function correction()
    {
        //读取单条记录之后所有相关记录  
        //将其余额全部机上6763
        $where = [
          'id'    =>  ['egt',59028],
          'crm_id'=>  '20'
        ];
        //$list = D('CrmRecharge')->where($where)->field('id,crm_id')->select();
        //echo count($list).'<br>';
        //echo D('CrmRecharge')->where($where)->setInc('balance',6763);
    }
    /*
    向服务端发送验证请求
    @param $url string 服务器URL
    @param $post_data array 需要提交的数据
    */
    private function curl_server($url,$post_data){
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch,CURLOPT_POST,1);
      curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
      $output = curl_exec($ch);
      curl_close($ch);
      return $output;
  }
}