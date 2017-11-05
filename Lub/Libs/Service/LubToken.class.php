<?php
// +----------------------------------------------------------------------
// | LubTMP  前后端分离  会话token
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;

use Common\Model\Model;
class LubToken extends \Libs\System\Service {
	/** 执行错误消息及代码 */
    public $error = '';
    /**
     * 生成会话token
     * @Company  承德乐游宝软件开发有限公司
     * @Author   zhoujing      <zhoujing@leubao.com>
     * @DateTime 2017-11-06
     * @param    string        $param                参数
     * @return   [type]                              [description]
     */
    public function createToken($param = '')
    {
    	$param = $param ? $param : genRandomString(8);
    	$token = session_create_id($param.'-');
    	return = $token;
    }
    /**
     * @Company  承德乐游宝软件开发有限公司
     * @Author   zhoujing      <zhoujing@leubao.com>
     * @DateTime 2017-11-06
     * @param    string        $code                 验证码
     * @param    string        $phone                接收手机号
     * @return   [type]                              [description]
     */
    public function encryCode($code = '', $phone = '')
    {
    	$phone  = substr($phone,1,4).$code.substr($phone,-2,5);
    	return md5($phone);
    }
}