<?php


namespace Home\Controller;


use Common\Controller\Base;

class WechatController extends Base{
{
    //当前用户可售的票型
    function index(){
        $this->display();
    }
    //产品详情
    function show(){
        $this->display();
    }
    //创建订单
    function order(){
        if(IS_POST){
        
        }else{
            $this->display();
        }
    }
    //订单列表
    function orderlist(){
        $this->display();
    }

}