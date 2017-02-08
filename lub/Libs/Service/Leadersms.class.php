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
	/*发送短信*/
	function send_sms($plan = null){
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
          //景区
          $channel = Leadersms::channel($plan['id']);
          $where = array('status'=>array('in','2,99'),'plan_id'=>$plan['id']);
          $type = '10';
          $start = '1';
          break;
        case '3':
          //判断是否最后一个场次
          $db = M('Plan');
          $counts = $db->where(array('plantime'=>$plan['plantime'],'status'=>'2','product_id'=>$plan['product_id']))->count();
          if($counts == '0'){
            //拉取当日计划集合
            $planList = $db->where(array('plantime'=>$plan['plantime'],'product_id'=>$plan['product_id']))->field('id')->select();
            $ids = arr2string($planList,'id');
            //漂流
            $channel = Leadersms::channel($ids);
            $where = array('status'=>array('in','2,99'),'plan_id'=>array('in',$ids));
            $type = '10';
            $start = '1';
          }
          break;
      }
      if($start == '1'){
          $count = M(ucwords($plan['seat_table']))->where($where)->count();
          //获取所有票型
         if($count != '0'){
          //获取发送人列表
          $list = M('LeaderSms')->where(array('status'=> array('in','1,3')))->field('id,name,phone')->select();
          foreach ($list as $ke => $valu) {
              $info = array('phone'=>$valu['phone'],'title'=>planShows($plan['id']),'num'=>$count,'area'=>$area,'channel'=>$channel,'product'=>productName($plan['product_id'],1));
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
      $msg = "渠道".$info['channel'].",政企".$info['enter'].",散客".$info['scat'];
      return $msg;
  }
}