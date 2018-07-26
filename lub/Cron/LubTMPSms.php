<?php

// +----------------------------------------------------------------------
// | LubTMP  运营短息的发送
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\Sms;
class LubTMPSms {
	/*计划任务错误代码
	*120001 订单拆解失败，原因：此天已拆解，抹去旧数据失败
	*/
    //任务主体
    public function run($cronId) {
    	//获取发送人列表
        //$list = M('LeaderSms')->where(array('status'=> array('in','1,3')))->field('id,name,phone')->select();
        //根据日期获取销售额
        $datetime = strtotime(date('Ymd',strtotime("-1 day")));
        //读取产品列表
        $product = M('Product')->where(['status'=>1])->field('id,item_id')->select();
        $itemConf = cache('ItemConfig');
        foreach ($product as $key => $value) {
            
            if((int)$itemConf[$value['item_id']]['1']['send_msg'] === 2){
                $map = array('plantime'=>$datetime,'product_id'=>$value['id']);
                $planList = M('Plan')->where($map)->field('id')->select();
                $plan_id = arr2string($planList,'id');
                //dump($plan_id);
                if(!empty($plan_id)){
                    $param = [
                        'day' => date('Y年m月d日',$datetime),
                        'product_id'   =>   $value['id']
                    ];
                    $plan = ['product_type'=>2,'plan_id'=>$plan_id];
                    \Libs\Service\Leadersms::send_sms($plan,2,$param);
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
        //散客
        $info['scat'] = M('Order')->where(array('plan_id'=>$plan['id'],'type'=>1,'status'=>array('in','1,7,9')))->sum('number');
        //政企
        $info['enter'] = M('Order')->where(array('plan_id'=>$plan['id'],'type'=>6,'status'=>array('in','1,7,9')))->sum('number');
        //旅行社
        $info['channel'] = M('Order')->where(array('plan_id'=>$plan['id'],'type'=>4,'status'=>array('in','1,7,9')))->sum('number');
        $msg = "渠道".$info['channel'].",政企".$info['enter'].",散客".$info['scat'];
        return $msg;
    }

}
