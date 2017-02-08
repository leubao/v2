<?php
// +----------------------------------------------------------------------
// | LubTMP API　接口　接口服务
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------
namespace Common\Controller;
define('IN_API', true);
use Libs\Service\Api;
class ApiBase extends \Think\Controller {
    /*初始化*/
    protected function _initialize() {
        //验证appid
    }
	public function index(){
        $return = array(
	    		'code'	=> 404,
	    		'info'	=>	'',
	    		'msg'	=> '你太淘气了，快回去....',
	    );
	    echo json_encode($return);
    }
    /**
     * 记录检票终端信息
     * @param $code 识别号
     * @param $type 监票终端类型
     * @param $status 状态
     * @param $info 请求数据
     * @param $data 返回数据
     */
    public function recordLogindetect($code, $type, $status, $info = "" , $data = "") {
        M("Checklog")->add(array(
            "code" => $code,
            'type' => $type,
            "datetime" => time(),
            "ip" => get_client_ip(),
            "status" => $status,
            "info" => serialize($info),
            "data" => serialize($data),
        ));
    }
    
}