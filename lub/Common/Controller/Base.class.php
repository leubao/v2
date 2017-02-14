<?php
// +----------------------------------------------------------------------
// | LubTMP 渠道商
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------
namespace Common\Controller;
use Home\Service\Partner;
use Home\Service\RBAC;
use Libs\Service\Operate;
define('IN_HOME', true);
class Base extends LubTMP {

	/*初始化*/
    protected function _initialize() {
        C(array(
            "USER_AUTH_ON" => true, //是否开启权限认证
            "USER_AUTH_TYPE" => 2, //默认认证类型 1 登录认证 2 实时认证
            "REQUIRE_AUTH_MODULE" => "", //需要认证模块
            "NOT_AUTH_MODULE" => "Public", //无需认证模块
            "USER_AUTH_GATEWAY" => U("Home/Public/login"), //登录地址
        ));
        if (false == RBAC::AccessDecision(MODULE_NAME)) {
            //检查是否登录
            if (false === RBAC::checkLogin()) {
                //跳转到登录界面
                redirect(C('USER_AUTH_GATEWAY'));
            }
            //没有操作权限
            $this->error('您没有操作此项的权限！');
        }
        parent::_initialize();
        //验证登录
        $this->competence();
        $product = I('get.productid',0,intval);
        //dump($this->pro_conf($product));
        $this->assign("SUBMENU_CONFIG", json_encode(D("Home/Menu")->getMenuList()));
        $this->assign('USER_INFO', json_encode($this->senuInfo()));
        $this->assign('PRO_CONF',json_encode($this->pro_conf($product)));
        $this->assign('proconf',$this->pro_conf($product));
    }
    /**
     * 返回产品配置信息
     * 去除敏感信息
     */
    public function pro_conf($product){
        $unset = array(
            'alipay_email'=>'',
            'alipay_partner'=>'',
            'alipay_key'=>'',
            'aliwappay_email'=>'',
            'aliwappay_partner'=>'',
            'aliwappay_key'=>'',
            'plan_start_time'=>'', 
            'plan_end_time'=>'',
            'ticket_sms'=>'',
            'win_subtract'=>'',
            'channel_quota'=>'',
            'channel_time'=>'',
            'print_seat_custom'=>'',
            'print_seat'=>'',
            'webpay'=>'',
            'area_sms'=>'',
            'crm_sms'=>'',
            'print_remrak'=>'',
            'print_field'=>'',
            'appsecret'=>'',
            'token'=>'',
            'encoding'=>'',
            'mchkey'=>'',
            'mchid'=>'',
            'wxurl'=>'',
            'tplmsg_order_id'=>'',
            'tplmsg_order_remark'=>'',
            'page_title'=>'',
        );
        $proconf = cache('ProConfig');
        $return = array_diff_key($proconf[$product]['1'],$unset);
        return $return;
    }
    /*返回去除敏感信息的客户信息
    *@param $uinfo 包含敏感信息的
    */
    public function senuInfo(){
        $uinfo =Partner::getInstance()->getInfo();
        $unset = array(
            'id'=>'',
            'username'=>'',
            'password'=>'',
            'last_login_time'=>'',
            'last_login_ip'=>'',
            'verify'=>'',
            'email'=>'', 
            'remark'=>'',
            'create_time'=>'',
            'update_time'=>'',
            'status'=>'',
            'is_scene'=>'',
            'role_id'=>'',
            'info'=>'',
            'rpassword'=>'',
            );
        $return = array_diff_key($uinfo,$unset);
        return $return;
    } 
	/**
     * 验证登录
     * @return boolean
     */
    private function competence() {
        //检查是否登录
        $uid = (int) Partner::getInstance()->isLogin();
        if (empty($uid)) {
            return false;
        }
        //获取当前登录用户信息
        $uInfo = Partner::getInstance()->getInfo();
        if (empty($uInfo)) {
            Partner::getInstance()->logout();
            return false;
        }
        //是否锁定
        if (!$uInfo['status']) {
            Partner::getInstance()->logout();
            $this->error('您的帐号已经被锁定！', U('Public/login'));
            return false;
        }
        return $uInfo;
    }
	/**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    final public function error($message = '', $jumpUrl = '', $ajax = false) {
        D('Home/Operationlog')->record($message, 0);
        parent::error($message, $jumpUrl, $ajax);
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    final public function success($message = '', $jumpUrl = '', $ajax = false) {
        D('Home/Operationlog')->record($message, 1);
        parent::success($message, $jumpUrl, $ajax);
    }
    //判断浏览器类型
    function check_wap() {  
        if (isset($_SERVER['HTTP_VIA'])) return true;  
        if (isset($_SERVER['HTTP_X_NOKIA_CONNECTION_MODE'])) return true;  
        if (isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])) return true;  
        if (strpos(strtoupper($_SERVER['HTTP_ACCEPT']),"VND.WAP.WML") > 0) {  
            // Check whether the browser/gateway says it accepts WML.  
            $br = "WML";  
        } else {  
            $browser = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';  
            if(empty($browser)) return true;
            $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');  
                  
            $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');  
                  
            $found_mobile=$this->checkSubstrs($mobile_os_list,$browser) ||  
                      $this->checkSubstrs($mobile_token_list,$browser); 
            if($found_mobile)
                $br ="WML";
            else $br = "WWW";
        }  
        if($br == "WML") {  
            return true;  
        } else {  
            return false;  
        }
    }
    function checkSubstrs($list,$str){
        $flag = false;
        for($i=0;$i<count($list);$i++){
            if(strpos($str,$list[$i]) > 0){
                $flag = true;
                break;
            }
        }
        return $flag;
    } 
    /*根据当前登录用户信息获取渠道商列表
    return $channel array 一维数组*/
    function get_channel(){
        $crm = Partner::getInstance()->crm;
        $channel = channel($crm['id'],$crm['level']);
        return $channel;

    }
    //返回渠道商列表
    function get_channel_list(){
        return M('Crm')->where(array('id'=>array('in',$this->get_channel())))->select();
    }
    /*根据当前登录用户信息获取渠道商下所有员工
    return $user array 一维数组*/
    function get_channel_user(){
        $crm = Partner::getInstance()->crm;
        $user = channel_user($crm['id'],$crm['level']);
        return $user;
    }
    //返回渠道商员工列表
    function get_channel_user_list(){
        return D('Home/User')->where(array('id'=>array('in',$this->get_channel_user())))->select();
    }
}