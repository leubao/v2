<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Libs\Service\Checkin;
class Api extends \Libs\System\Service {
    
    /**
     * 读取产品及价格信息
     * @param $proId array 产品ID集合
     */
    private function product($proId = false){
    	foreach ($proId as $key=>$val){
    		$info[$val] = D('Item/Product')->where(array('id'=>$val))->relation(true)->find();
    	}
    	return $info;
    }
    
    /**
     * 根据计划读取票型价格
     * @param $planid int 计划ID 
     */
    function planType($planid = false){
    	$info = D('Item/Plan')->where(array('id'=>$planid,'status'=>'2'))->find();
    	return $info;
    }

    /**
    * 获取销售计划
    * @param $product string 产品id 集合
    * @param $param array 当前APP的全部信息
    * return array  
    */
    function plans($param = null){
        if(empty($param)){return false;}
        $product = $param['product'];
        $proArr = explode(',', $product);
        foreach ($proArr as $k=>$v){
            $list[$v] = M('Product')->where(array('id'=>$v,'status'=>1))->field('name as productname')->select();
            if($list[$v] != false){
                $list[$v]['plan'] = M('Plan')->where(array('product_id'=>$v,'status'=>2))->order('plantime ASC')->field(array('id,plantime,starttime,endtime,games,param,product_id,seat_table'))->select();
            }
        }
        $list = array_filter($list);
        //重构参数信息 获取票价信息根据场次读取价格分组信息
        foreach ($list as $key => $value) {
            foreach ($value['plan'] as $ke => $valu) {
                $valu['param'] = area_price(unserialize($valu['param']),$valu['seat_table'],$param['group']['price_group'],$param['scene']);
                $valu['title'] = planShow($valu['id'],4,1);
                $valu['product_id'] = $valu['product_id'];
                $valu['num'] = M(ucwords($valu['seat_table']))->where(array('status'=>array('in','0')))->count();
                
                $plan[] = $valu;
            }
            $value['plan'] = $plan;
            $info = $value;
        }
        return $info;
    }
    
    /**
     * 时间场次验证
     * $plan 检票场次
     */
    function timeCheck($plan){
        if(empty($plan)){ return false;}
        //获取系统日期
        $datetime = date('Ymd');
        //日期
        $plantime = date('Ymd',$plan['plantime']);
        //检票基准时间
        $starttime = date('H:i',$plan['starttime']);
        //检票时间
        $start = date('H:i',strtotime("$starttime -40 minute"));
        $end = date('H:i',strtotime("$starttime +50 minute"));
        if($datetime == $plantime){
            //判断日期
            $totime = date('H:i');
            if($start <= $totime && $totime <= $end){
                //判断时间
                return true;

            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    //验证APP 信息
    function check_app($appid,$appkey){
        $info = D('Api/App')->where(array('appid'=>$appid))->relation(true)->field('id,appid,appkey,product,crm_id,is_pay')->find();
        $str = $info['appid'].$info['id'].$info['appkey'];
        $md5_key = md5($str);
        if($md5_key == $appkey){
            //查询所属分组的相关信息
            $crm = F('Crm');
            $info['groupid'] = $crm[$info['crm_id']]['groupid'];
            $info['group'] = M('CrmGroup')->where(array('id'=>$info['groupid']))->field('id,price_group,type,product_id,settlement')->find();
            $info['scene'] = '4';
            return $info;
        }else{
            return false;
        }
    }
}