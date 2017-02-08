<?php

// +----------------------------------------------------------------------
// | LubTMP 手机端
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------

namespace Common\Controller;
use Wap\Service\Partner;
use Libs\Service\Operate;
use Wap\Service\RBAC;
define('IN_WAP', true);
class Wap extends LubTMP {

	/*初始化*/
    protected function _initialize() {
        C(array(
            "USER_AUTH_ON" => true, //是否开启权限认证
            "USER_AUTH_TYPE" => 2, //默认认证类型 1 登录认证 2 实时认证
            "REQUIRE_AUTH_MODULE" => "", //需要认证模块
            "NOT_AUTH_MODULE" => "Public", //无需认证模块
            "USER_AUTH_GATEWAY" => U("Wap/Public/login"), //登录地址
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
        $this->competence();//dump(Partner::getInstance()->getInfo());
        //$this->assign("SUBMENU_CONFIG", json_encode(D("Home/Menu")->getMenuList()));
        $this->assign('USER_INFO', json_encode(Partner::getInstance()->getInfo()));   
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