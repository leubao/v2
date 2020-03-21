<?php

/**
 * jsonRPC服务
 * @Author: IT Work
 * @Date:   2019-11-22 20:16:34
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-01-07 16:28:12
 */
namespace Trust\Controller;
use Think\Controller\JsonRpcController;
use Trust\Service\CheckIn;
class JrpcController extends JsonRpcController {

    public function index(){
        return 'Hello, JsonRPC!';
    }
    // 支持参数传入
    public function test($name=''){
        return "Hello, {$name}!";
    }
    //订单查询
    public function post_query($data)
    {
    	
    }
    //门票查询
    public function post_ticket_query($data)
    {
    	
    }
    //门票核销
    public function post_checkin($data)
    {
    	return CheckIn::closeTicket($data);
    }
    //接收发号器推送过来的单号
    public function post_sn($data)
    {
    	//1、接收发号服务推送过来的单号
    	//2、作废过期的单号
    }
}