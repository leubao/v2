<?php
// +----------------------------------------------------------------------
// | LubTMP 客户操作端
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

namespace Common\Controller;
use Item\Service\Partner;
use Item\Service\RBAC;

//定义售票前台
define('IN_ITEM', true);

class ItemBase extends LubTMP{
	//初始化
    protected function _initialize() {
        C(array(
            "USER_AUTH_ON" => true, //是否开启权限认证
            "USER_AUTH_TYPE" => 2, //默认认证类型 1 登录认证 2 实时认证
            "REQUIRE_AUTH_MODULE" => "", //需要认证模块
            "NOT_AUTH_MODULE" => "Public", //无需认证模块
            "USER_AUTH_GATEWAY" => U("Item/Public/login"), //登录地址
        ));
        if (false == RBAC::AccessDecision(MODULE_NAME)) {
            //检查是否登录
            if (false === RBAC::checkLogin()) {
                //跳转到登录界面
                redirect(C('USER_AUTH_GATEWAY'));
            }
            //没有操作权限
            $this->erun('您没有操作此项的权限！');
        }
        parent::_initialize();
        //取得所有产品信息
        $this->products = cache('Product');
        //取得当前产品信息
        $this->product = $this->products[$this->pid];
        //设置产品配置信息
        $this->procof = cache('ProConfig');
        //验证登录
        $this->competence();
        //绑定URl参数
        $this->navTabId = I('request.navTabId');
        $this->assign('navTabId',$this->navTabId);
        
        //所属公司及当前产品设置
        $this->pid = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
        $this->itemid = \Libs\Util\Encrypt::authcode($_SESSION['lub_imid'], 'DECODE');
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
        $userInfo = Partner::getInstance()->getInfo();//dump($userInfo);
        if (empty($userInfo)) {
            Partner::getInstance()->logout();
            return false;
        }
        //是否锁定
        if (!$userInfo['status']) {
            Partner::getInstance()->logout();
            $this->error('您的帐号已经被锁定！', U('Public/login'));
            return false;
        }
        return $userInfo;
    }
    
    /**
     * 客户端成功返回代码
     * @param $code int 返回状态码
     * @param $msg string 返回提示信息
     * @param $navTabId string 
     * @param $rel string
     * @param $callbackType string
     * @param $forwardUrl string
     */
    protected function srun($message = '', $navTab='', $rel='', $callBackType='', $forwordurl='', $code=200,$merge=null){
    	$return = array(
    		'statusCode' => $code,
    		'message'	=> $message,
    		'navTabId' => $navTab,
    		'rel'	=> $rel,
    		'callbackType' => $callBackType,
    		'forwardUrl' => $forwordurl,
    	);
        if($merge){
            $return = array_merge($return,$merge);
        }
        D('Item/Operationlog')->record($message, 1);
        $this->ajaxReturn($return);
    }
    /**
     * 客户端错误返回代码
     */
    protected function erun($message = '', $navTab='',$rel='',$callBackType='',$forwordurl='', $code=300){
    	 //D('Manage/Operationlog')->record($message, 0);
    	 $return = array(
    		'statusCode' => $code,
    		'message'	=> $message,
    		'navTabId' => $navTab,
    		'rel'	=> $rel,
    		'callbackType' => $callBackType,
    		'forwardUrl' => $forwordurl,
    	);
        if($merge){
            $return = array_merge($return,$merge);
        }
        D('Item/Operationlog')->record($message, 0);
        $this->ajaxReturn($return);
    }
    /*访问超时*/
    protected function ajaxlogin($message = '', $navTab='',$rel='',$callBackType='',$forwordurl='', $code=301){
    	$return = array(
    		'statusCode' => $code,
    		'message'	=> $message,
    		'navTabId' => $navTab,
    		'rel'	=> $rel,
    		'callbackType' => $callBackType,
    		'forwardUrl' => $forwordurl,
    	);
    	if($merge){
            $return = array_merge($return,$merge);
        }
        D('Item/Operationlog')->record($message, 0);
        $this->ajaxReturn($return);
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
        D('Item/Operationlog')->record($message, 0);
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
        D('Item/Operationlog')->record($message, 1);
        parent::success($message, $jumpUrl, $ajax);
    }
    //获取当前可售出场次
    function can_plan(){
        $plan = M('Plan')->where(array('status'=>2,'product_id'=>$this->pid))->field('id,product_id,param')->select();
        return $plan;
    }
}