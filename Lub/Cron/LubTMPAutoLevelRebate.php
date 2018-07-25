<?php

// +----------------------------------------------------------------------
// | LubTMP  分级别返利
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\Rebate;
class LubTMPAutoLevelRebate {
    //任务主体
    public function run($cronId) {
    	//默认计算当天的订单
        //$datetime= date('Ymd',strtotime("-1 day"));
        /**/
        $date = $this->getDateFromRange('2018-06-01','2018-07-04');
        foreach ($date as $k => $v) {
            //$this->rebate($v);
            //$this->report($v,2);
           // dump($v);
        }
        //$this->rebate($datetime);
    }
    //批量重置报表
    public function report($datetime,$type = 1)
    {
        if($type == 1){
            $map['plantime'] = $datetime;
            //删除报表
            $status = M('ReportData')->where($map)->delete();
            if($status){
                echo '删除成功';
            }else{
                echo '删除错误';
            }
        }
        if($type == 2){
            //生成报表
            $status = M('ReportData')->where(['plantime'=>$datetime])->count();
            if($status == 0){
                $stat = \Libs\Service\Report::report($datetime);
                if($stat == '200'){
                    echo '生成成功';
                }else{
                    echo '重置失败';
                }
            }else{
                echo '已生成';
            }
            
            
        }
    }
    public function rebate($datetime)
    {
        $plan = M('Plan')->where(['plantime'=>strtotime($datetime)])->field('id')->select();
        if(!empty($plan)){
            
            $map = [
                'plan_id'   =>  ['in',array_column($plan,'id')],
                'type'      =>  4,
                'status'    =>  ['in',['1','6','7','9']]
            ];//dump($map);
            $orderList = D('Order')->where($map)->field('order_sn')->select();//dump($orderList);
            if(!empty($orderList)){
                foreach ($orderList as $key => $va) {
                    $info = D('Item/Order')->where(['order_sn'=>$va['order_sn']])->relation(true)->find();
                    $ticketType = F('TicketType'.$info['product_id']);
                    $model = D('Item/TeamOrder');
                    if(!$model->where(array('order_sn'=>$va['order_sn']))->getField('id')){

                        $info['info'] = unserialize($info['info']);
                        $discount = 0;
                        $rebate = 0;

                        //获取顶级扣款ID
                        $cid = money_map($info['channel_id']);
                        //应该扣款，实际扣款
                        $where = ['crm_id'=>$cid,'type'=>2,'order_sn'=>$va['order_sn']];
                        $cash = M('CrmRecharge')->where($where)->getField('cash');

                        //计算返利金额
                        foreach ($info['info']['data'] as $k => $v) {
                          $discount += $ticketType[$v['priceid']]['discount'];
                        }
                        
                        if($cash > $discount){
                            $rebate = $cash - $discount;
                        }
                        
                        if($rebate > 0){
                            $teamData[] = array(
                              'order_sn'    => $info['order_sn'],
                              'plan_id'     => $info['plan_id'],
                              'subtype'     => '0',
                              'money'       => $rebate,
                              'product_type'=> $info['product_type'],//产品类型
                              'product_id'  => $info['product_id'],
                              'user_id'     => $info['user_id'],
                              'number'      => $info['number'],
                              'guide_id'    => $info['info']['crm'][0]['guide'],
                              'qd_id'       => $cid,
                              'status'      => '1',
                              'type'        => 2,//窗口团队时可选择，渠道版时直接为渠道商TODO 渠道版导游登录时
                              'userid'      => '0',
                              'uptime'      => time(),
                              'createtime'  => time()
                            );
                        }
                        
                    }
                }  
            }/*
            $map = array(
                'plan_id' => ['in',array_column($plan,'id')],
                'status'  => array('neq','4'),
            );
            
            $list = M('TeamOrder')->where($map)->select();
            //按订单返佣
            foreach ($list as $key => $value) {
               $info[$key] = Rebate::rebate($value,1);
               
            }
            //dump($list);
            dump($info);*/
        }
       
        /* */ 
        if(!empty($teamData)){
            $status = $model->addAll($teamData);
            if($status){
                return $status;
            }else{
                //load_redis('lpush','Error_PreOrder',$info['order_sn'].'E1');
                return false;
            }
        }
        
    }
    /**
     * 获取指定日期段内每一天的日期
     * @param  Date  $startdate 开始日期
     * @param  Date  $enddate   结束日期
     * @return Array
     */
    function getDateFromRange($startdate, $enddate){

        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);

        // 计算日期段内有多少天
        $days = ($etimestamp-$stimestamp)/86400+1;

        // 保存每天日期
        $date = array();

        for($i=0; $i<$days; $i++){
            $date[] = date('Ymd', $stimestamp+(86400*$i));
        }

        return $date;
    }
}
