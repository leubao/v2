<?php

/**
 * jsonRPC服务
 * @Author: IT Work
 * @Date:   2019-11-22 20:16:34
 * @Last Modified by:   IT Work
 * @Last Modified time: 2019-11-22 21:40:29
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

}