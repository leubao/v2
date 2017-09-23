<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Think\Controller;
class SharingController extends Controller {
	//
    public function index(){
        $return = array(
	    		'code'	=> 404,
	    		'info'	=>	'',
	    		'msg'	=> '你太淘气了，快回去....',
	    );
	    echo json_encode($return);
    }
    function temp(){
    	$starttime = '2017-04-10 16:23:09';
    	$endtime = date('Y-m-d H:i:s');
    	$res = timediff($starttime,$endtime,'hour');
    	echo $res['hour']/48;
    	echo "<br/>";
    	echo 96%48;
    	$where = ['status'=>1,'level'=>16];
    	$list = D('Crm/Crm')->where($where)->field('id,cash,product_id')->select();
    	dump($list);
    	dump($res);
    }
}