<?php
// +----------------------------------------------------------------------
// | LubTMP 商户用户服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

namespace Home\Service;

class Partner {

    //存储用户uid的Key
    const UidKey = 'lub_uid';
    //存储分销商网点ID
    const channelId = 'lub_channelid';
    
    //超级管理员角色id
    const administratorRoleId = 1;

    //当前登录会员详细信息
    private static $uInfo = array();

    /**
     * 连接后台用户服务
     * @staticvar \Item\Service\Cache $systemHandier
     * @return \Item\Service\Cache
     */
    static public function getInstance() {
        static $handier = NULL;
        if (empty($handier)) {
            $handier = new Partner();
        }
        return $handier;
    }

    /**
     * 魔术方法
     * @param type $name
     * @return null
     */
    public function __get($name) {
        //从缓存中获取
        if (isset(self::$uInfo[$name])) {
            return self::$uInfo[$name];
        } else {
            $uInfo = $this->getInfo();
            if (!empty($uInfo)) {
                return $uInfo[$name];
            }
            return NULL;
        }
    }

    /**
     * 获取当前登录用户资料
     * @return array 
     */
    public function getInfo() {
        if (empty(self::$uInfo)) {
            self::$uInfo = $this->getuInfo($this->isLogin());
        }
        //dump(self::$uInfo);
        return !empty(self::$uInfo) ? self::$uInfo : false;
       
    }
	
    /**
     * 检验用户是否已经登陆
     * @return boolean 失败返回false，成功返回当前登陆用户基本信息
     */
    public function isLogin() {
        $userId = \Libs\Util\Encrypt::authcode(session(self::UidKey), 'DECODE');
        if (empty($userId)) {
            return false;
        }

        return (int) $userId;
    }

    //登录后台
    public function login($identifier, $password) {
        if (empty($identifier) || empty($password)) {
            return false;
        }
        //验证
        $uInfo = $this->getuInfo($identifier, $password);
        if (false == $uInfo) {
            //记录登录日志
            $this->record($identifier, $password, 0);
            return false;
        }
        //记录登录日志
        $this->record($identifier, $password, 1);
        //注册登录状态
        $this->registerLogin($uInfo);
        return true;
    }

    /**
     * 检查当前用户是否超级管理员
     * @return boolean
     */
    public function isAdministrator() {
        $uInfo = $this->getInfo();
        if (!empty($uInfo) && $uInfo['role_id'] == self::administratorRoleId) {
            return true;
        }
        return false;
    }

    /**
     * 注销登录状态
     * @return boolean
     */
    public function logout() {
        session('[destroy]');
        return true;
    }

    /**
     * 记录登陆日志
     * @param type $identifier 登陆方式，uid,username
     * @param type $password 密码
     * @param type $status 
     */
    private function record($identifier, $password, $status = 0) {
        //登录日志
        D('Manage/Loginlog')->addLoginLogs(array(
       	 	"is_scene" => '3',
            "username" => $identifier,
            "status" => $status,
            "password" => $status ? '密码保密' : $password,
            "info" => is_int($identifier) ? '用户ID登录' : '用户名登录',
        ));
    }

    /**
     * 注册用户登录状态
     * @param array $uInfo 用户信息
     */
    private function registerLogin(array $uInfo) {
        //写入session
        session(self::UidKey, \Libs\Util\Encrypt::authcode((int) $uInfo['id'], ''));
        //更新状态
        D('Home/User')->loginStatus((int) $uInfo['id']);
        //注册权限
        \Home\Service\RBAC::saveAccessList((int) $uInfo['id']);
    }

    /**
     * 获取用户信息
     * @param type $identifier 用户名或者用户ID
     * @return boolean|array
     */
    private function getuInfo($identifier, $password = NULL) {
        if (empty($identifier)) {
            return false;
        }
        
        return D('Home/User')->getuInfo($identifier, $password);
    }
	
     
    /**
     * 管理员授权
     */
    function authoLogin(){
    	
    }
}