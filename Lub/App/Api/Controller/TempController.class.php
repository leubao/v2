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
class TempController extends ApiBase {
  public function index()
  {
        $list = D('IdcardLog')->field('idcard')->order('id desc')->select();
        foreach ($list as $key => $value) {
          $ab[] = $value['idcard'];
        }
        dump(array_count_values($ab));
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
        'plan'  =>  '3413',
        'sn'    =>  get_order_sn('9999'),
        'oinfo' =>  array('0'=>array('areaId'=>'151','priceid'=>'34','price'=>'0.01','num'=>'1')),
        'crm'   =>  array('contact'=>'联系人','phone'=>'18631451216'),
        'param' =>  array('remark'=>'备注..')
      );
      $post['data'] = json_encode($post);
      $aa = $this->curl_server($url,$post);
      dump(json_encode($aa));
    }
    //测试通用order
    function c_booking_order(){
      $url = "http://ticket.leubao.com/api.php?a=api_booking_order";
      $post = array(
        'appid' => '26628',
        'appkey'=> '8613f25b1f2691c8a1db85f1cb095d29',
        'money' =>  '0.1',
        'product_id' => '41',
        'datetime'  =>  '2017-02-28',
        'sn'    =>  get_order_sn('9999'),
        'oinfo' =>  array(array('priceid'=>'34','price'=>'0.1','num'=>'1')),
        'crm'   =>  array('contact'=>'联系人','phone'=>'18631451216','id_card'=>'1304231988909171234'),
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