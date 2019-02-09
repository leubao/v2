<?php
// +----------------------------------------------------------------------
// | LubTMP 运营短信
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Libs\Service\Sms;
class Leadersms extends \Libs\System\Service {
	/*发送短信
  */
 /**
  * @Company  承德乐游宝软件开发有限公司
  * @Author   zhoujing      <zhoujing@leubao.com>
  * @DateTime 2018-07-20
  * @param    array        $plan                 销售计划
  * @param    int       $type                 1场次结束后统一发送2T+1推送3定时推送
  * @param    array     $param   其它辅助参数
  * @return   [type]                              [description]
  */
	function send_sms($plan = null,$type = 1,$param = []){
      $start = '0';
      if(empty($plan)){
        return false; 
      }
      switch ($plan['product_type']) {
        case '1':
          //剧场
          $count = M(ucwords($plan['seat_table']))->where(array('status'=>array('in','2,99')))->count();
          $area = Leadersms::area($plan);
          $channel = Leadersms::channel($plan['id']);
          $where = array('status'=>array('in','2,99'));
          $type = '7';
          $start = '1';
          break;
        case '2':
          //景区 T+1发送
          if($type == 2){
            $channel = Leadersms::channel($plan['id']);
            $where = array('status'=>array('in','2,99'),'plan_id'=>array('in',$plan['plan_id']));
            $start = '2';
          }else{
            $channel = Leadersms::channel($plan['id']);
            $where = array('status'=>array('in','2,99'),'plan_id'=>$plan['id']);
            $start = '1';
          }
          $ids = $plan['id'];
          $table = 'Scenic';
          $type = '10';
          break;
        case '3':
          //判断是否最后一个场次
          $db = M('Plan');
          $counts = $db->where(array('plantime'=>$plan['plantime'],'status'=>'2','product_id'=>$plan['product_id']))->count();
          if($counts == '0'){
            if($type == 2){
              $ids = $plan['id'];
              $start = '2';
            }else{
              //拉取当日计划集合
              $planList = $db->where(array('plantime'=>$plan['plantime'],'product_id'=>$plan['product_id']))->field('id')->select();
              $ids = arr2string($planList,'id');
              $start = '1';
            }
            //漂流
            $channel = Leadersms::channel($ids);
            $where = array('status'=>array('in','2,99'),'plan_id'=>array('in',$ids));
            $type = '10';
            $table = 'Drifting';
          }
          break;
      }
      
      if($start == '1'){
          //$count = M(ucwords($plan['seat_table']))->where($where)->count();
          $count = M('Order')->where(array('plan_id'=>array('in',$ids),'status'=>array('in','1,7,9')))->sum('number');
          //获取所有票型
         if($count != 0){
          //获取发送人列表
          $list = M('LeaderSms')->where(array('status'=> array('in','1,3')))->field('id,name,phone')->select();
          foreach ($list as $ke => $valu) {
              $info = array('phone'=>$valu['phone'],'title'=>planShows($plan['id']),'num'=>$count,'area'=>$area,'channel'=>$channel,'product'=>productName($plan['product_id'],1));dump($info);
                Sms::order_msg($info,$type);
           }
         }
      }
      if($start == '2'){
        $count = M('Order')->where(array('plan_id'=>array('in',$ids),'status'=>array('in','1,7,9')))->sum('number');
        if($count != 0){
          $list = M('LeaderSms')->where(array('status'=> array('in','1,3')))->field('id,name,phone')->select();

          foreach ($list as $ke => $valu) {
              $info = array('phone'=>$valu['phone'],'title'=>$param['day'],'num'=>$count,'area'=>$area,'channel'=>$channel,'product'=>productName($plan['product_id'],1));dump($info);
                Sms::order_msg($info,$type);
           }
        }
      }

       
	}
	//按区域获取已售数
  function area($plan){
      $area = unserialize($plan['param']);
      foreach ($area['seat'] as $key => $value) {
          $info[$value] = M(ucwords($plan['seat_table']))->where(array('status' => array('in','2,99'),'area'=>$value))->count();
          $msg = $msg.areaName($value,1).$info[$value].',';
      }
      return $msg;
  }
  //按创建场景获取已售数
  function channel($plan){
      $db = M('Order');
      //散客
      $info['scat'] = $db->where(array('plan_id'=>array('in',$plan),'type'=>1,'status'=>array('in','1,7,9')))->sum('number');
      //政企
      $info['enter'] = $db->where(array('plan_id'=>array('in',$plan),'type'=>6,'status'=>array('in','1,7,9')))->sum('number');
      //旅行社
      $info['channel'] = $db->where(array('plan_id'=>array('in',$plan),'type'=>4,'status'=>array('in','1,7,9')))->sum('number');
      $scat = $info['scat'] ? $info['scat'] : 0;
      $enter = $info['enter'] ? $info['enter'] : 0;
      $channel = $info['channel'] ? $info['channel'] : 0;

      $msg = "渠道".$channel."人,政企".$enter."人,散客".$scat."人";
      return $msg;
  }
}