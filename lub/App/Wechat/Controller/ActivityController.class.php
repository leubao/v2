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


    public function xm()
    {
        //获取场次
        $this->display();
    }
    
}