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
}