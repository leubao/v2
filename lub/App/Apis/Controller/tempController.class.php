<?php


//政企退票产生的无效充值
public function enterprise()
{
...}
//查询所有代订订单且作废的
        $map = array(
          'type' => '6',
          'status' => '0'
          );
        $list = D('Item/Order')->where($map)->field('id,order_sn')->select();
        //删除退票还款记录
        //foreach ($list as $key => $value) {
          //$status[] = D('CrmRecharge')->where(array('type'=>'4','order_sn'=>$value['order_sn']))->field('type,order_sn')->select();
         // $status[] = D('CrmRecharge')->where(array('type'=>'4','order_sn'=>$value['order_sn']))->delete();
        //}
        //
//无扣费但退票产生退款
function tuikuan{
  $sn = "50418141101129,50904141195031,50818141279680,51024141203267,51024141275375";
        $sns = explode(',', $sn);
        
        foreach ($sns as $key => $value) {
          //dump($value);
          $map=array(
            'order_sn' => $value,
            'status'  => '0',
            );
          //查询订单金额
          $info = D('Item/Order')->where($map)->field('id,order_sn,money,channel_id')->find();
          //充值
          //获取扣费条件
          $cid = money_map($info['channel_id']);
          //先消费后记录
          $c_pay = D('Crm')->where(array('id'=>$cid))->setDec('cash',$info['money']);
          $data = array(
            'cash'    =>  $info['money'],
            'user_id' =>  '1',
            'crm_id'  =>  $cid,
            'createtime'=>  time(),
            'type'    =>  '2',
            'order_sn'  =>  $info['order_sn'],
            'balance' =>  balance($cid),
            'remark' => "订单".$info['order_sn']."没有扣款，现补扣"
          );
          $c_pay2 = D('CrmRecharge')->add($data);
          dump($c_pay);
          dump($c_pay2);
        }
}
//有补贴但无消费的情况
function y_butie(){
   $sn = "50410141138444,50410141193473,50411141128721,50411141146584,50424141175661,50704141155787,50716141136329,50718141265010,50502141180004,50804141158840,50806141160485,50807141356658,50806141244853,50812141140251,50812141160718,50813141139566,50719141257727,50719141210276,50723141116718,50724141234320,50724141255120,50724141293407,50730141272545,50731141167260,50802141265221,50804141129273,50804141155018,50803141243929,50814141134466,50814141125164,50817141253395,50817141269042,50822141158290,50827141164959,50904141156014,51002141386465,51002141209096,51003141195946,51004141107263,51004141397585,51005141103905,51013141124102,51018141120496,51024141183841,51024141225725,51024141227453,51025141102828,50814141124549";
        $sns = explode(',', $sn);
        
        foreach ($sns as $key => $value) {
          dump($value);
          $map=array(
            'order_sn' => $value,
            'status'  => '9',
            );
          //查询订单金额
          $info = D('Item/Order')->where($map)->field('id,order_sn,money,channel_id')->find();
          //获取扣费条件
          $cid = money_map($info['channel_id']);
          //先消费后记录
          $c_pay = D('Crm')->where(array('id'=>$cid))->setDec('cash',$info['money']);
          $data = array(
            'cash'    =>  $info['money'],
            'user_id' =>  '1',
            'crm_id'  =>  $cid,
            'createtime'=>  time(),
            'type'    =>  '2',
            'order_sn'  =>  $info['order_sn'],
            'balance' =>  balance($cid),
            'remark' => "订单".$info['order_sn']."没有扣款，现补扣,补贴已发放"
          );
          $c_pay2 = D('CrmRecharge')->add($data);
          dump($c_pay);
          dump($c_pay2);
        }
}
//超量申请的订单  未退款
function chaoliang(){
  $map = array(
          'type' => '4',
          'status' => '5'
          );
        $list = D('Item/Order')->where($map)->field('id,order_sn,channel_id')->select();

        foreach ($list as $k => $v) {
          $info = D('CrmRecharge')->where(array('type'=>'2','order_sn'=>$v['order_sn']))->find();
          if($info){
            //获取扣费条件
            $cid = money_map($info['crm_id']);
            //先消费后记录
            $c_pay = D('Crm')->where(array('id'=>$cid))->setInc('cash',$info['cash']);
            $data = array(
              'cash'    =>  $info['cash'],
              'user_id' =>  '1',
              'crm_id'  =>  $cid,
              'createtime'=>  time(),
              'type'    =>  '4',
              'order_sn'  =>  $info['order_sn'],
              'balance' =>  balance($cid),
              'remark' => "预订单".$info['order_sn']."未处理，现退款"
            );
            $c_pay2 = D('CrmRecharge')->add($data);
            $order = D('Item/Order')->where(array('order_sn'=>$info['order_sn']))->setField('status','0');
            dump($c_pay);
            dump($c_pay2);
            dump($order);
            //dump($data);
          }
          
          dump($v['order_sn']);
        }
}
//查询花费和返佣不匹配的订单
    function with_fill(){
      //查询所有渠道订单
      $list = M('Order')->where(array('addsid'=>2,'type'=>4,'status'=>array('in','1,9')))->limit('1,1000')->field('order_sn')->order('id DESC')->select();
      //匹配返佣订单
     // dump($list);
      dump(count($list));
      foreach ($list as $k => $v) {
        $status = M('TeamOrder')->where(array('order_sn' => $v['order_sn']))->find();
        if(!$status){
          $data[] = $v['order_sn'];
        }
      }
      //echo "string";
      dump($data);
    }
//核减订单删除返佣  补写返利程序
function ret(){
        $sn = "60210141313661,60210141306656";
        $sns = explode(',', $sn);
        
        foreach ($sns as $key => $value) {
          $map = array(
            'order_sn' => $value,
            'status' => '9',
            'type'  => '4',
            'subtract' => '1',
          );
          $info = D('Item/Order')->where($map)->relation(true)->find();
          $info['info'] = unserialize($info['info']);
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
        dump($info);
}
//计算补贴金额
function rebate($seat,$product_id){
      $ticketType = F("TicketType".$product_id);
      foreach ($seat as $k=>$v){
        //计算订单返佣金额
        $rebate += $ticketType[$v['priceid']]['rebate'];
      }
      return $rebate;
    }
/*更新客源地统计 start*/
$db = M('ReportData');
      //读取渠道订单
      $map = array(
        'status' => '1',
        'type'    => '4',
        );
      $list = $db->where($map)->field('order_sn')->select();
      //读区地区信息
      foreach ($list as $key => $value) {
        $oinfo = M('OrderData')->where(array('order_sn'=>$value['order_sn']))->field('info')->find();
        $info = unserialize($oinfo['info']);
        //dump($info['param'][0]['tour']);
        //更新订单地区
        $status = $db->where(array('order_sn'=>$value['order_sn']))->setField('region',$info['param'][0]['tour']);
        if($status){
          echo $value['order_sn']."ok<br />";
        }else{
          echo $value['order_sn']."error<br />";
        }
      }
/*更新客源地统计 end*/

/*批量删除数据 starat*/
    //按照场次id  进行删除
    $plan = "256,257,258,259,260,261";
    $plans = explode(',',$plan);
    //删除返佣
    $r = M('TeamOrder')->where(array('plan_id'=>array('in',$plan)))->delete();
    //删除退票日志
    $t_r = M('TicketRefund')->where(array('plan_id'=>array('in',$plan)))->delete();
    //删除授信记录
    //删除报表数据
    $report = M('ReportData')->where(array('plan_id'=>array('in',$plan)))->delete();
    //删除配额
    $quota = M('QuotaUse')->where(array('plan_id'=>array('in',$plan)))->delete();
    foreach ($plans as $key => $value) {
      //删除订单
      $oid = M('Order')->where(array('plan_id'=>$value))->field('id')->select();
      $ids = arr2string($oid,'id');
      $s_o = M('Order')->where(array('id'=>array('in',$ids)))->delete();
      $s_o_d = M('OrderData')->where(array('oid'=>array('in',$ids)))->delete();
    }
    //剧院产品删除座位表
    
    //删除场次信息
    $p_s = M('Plan')->where(array('id'=>array('in',$plan)))->delete();
/*批量删除数据 end*/
?>