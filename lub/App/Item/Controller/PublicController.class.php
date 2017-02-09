<?php
// +----------------------------------------------------------------------
// | LubTMP 
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
use Item\Service\Partner;
class PublicController extends ManageBase{
    function _initialize(){
        parent::_initialize();
    }
    // 左侧页面
    public function menu() {
        $parentid=I('get.tid', 0, intval);
        $menu = D("Item/Menu")->getMenuList();
        $leftemu = $menu[$parentid];
        $this->assign('leftmenu',$leftemu);
        $this->display();
    }
    // 登录检测
    public function checkLogin() {
        //记录登陆失败者IP
        $ip = get_client_ip();
        $username = I("post.username", "", "trim");
        $password = I("post.password", "", "trim");
        $code = I("post.code", "", "trim");
        if (empty($username) || empty($password)) {
            $this->error("用户名或者密码不能为空，请重新输入！", U("Item/Public/login"));
        }
        if (empty($code)) {
            $this->error("请输入验证码！", U("Item/Public/login"));
        }
        //验证码开始验证
        if (!$this->verify($code)) {
            $this->error("验证码错误，请重新输入！", U("Item/Public/login"));
        }
        if (Partner::getInstance()->login($username, $password)) {
            $forward = cookie("forward");
            if (!$forward) {
                $forward = U("Item/Index/index");
            } else {
                cookie("forward", NULL);
            }
            //增加登陆成功行为调用
            $admin_public_tologin = array(
                'username' => $username,
                'ip' => $ip,
            );
            tag('admin_public_tologin', $admin_public_tologin);
            //记录登录状态  防止重复登录
            //M('LockUser')->add(array('user_id'=>));
           $this->redirect('Item/Index/index');
        } else {
            $this->error("用户名或者密码错误，登陆失败！", U("Public/login"));
        }
    }
    // 后台首页 查看系统信息
    public function main() {
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
            );
        $this->assign('info',$info);
        $this->display();
    }
    //后台登陆界面
    public function login() {
        //如果已经登录
        if (Partner::getInstance()->id) {
            $this->redirect('Item/Index/index');
        }
        $this->display();
    }
    //退出登陆
    public function logout() {
        if (Partner::getInstance()->logout()) {
            //手动登出时，清空forward
            cookie("forward", NULL);
            $this->success('注销成功！', U("Item/Public/login"));
            //$this->srun('注销成功！',U("Item/Public/login"));
        }
    }
    
    //弹窗登录
    public function login_dialog(){
        if(IS_POST){
            //记录登陆失败者IP
            $ip = get_client_ip();
            $username = I("post.username", "", "trim");
            $password = I("post.password", "", "trim");
            $code = I("post.code", "", "trim");
            if (empty($username) || empty($password)) {
                $this->erun("用户名或者密码不能为空，请重新输入！", U("Item/Public/login"));
            }
            if (empty($code)) {
                $this->erun("请输入验证码！", U("Item/Public/login"));
            }
            //验证码开始验证
            if (!$this->verify($code)) {
                $this->erun("验证码错误，请重新输入！", U("Item/Public/login"));
            }
            if (Partner::getInstance()->login($username, $password)) {
                $forward = cookie("forward");
                if (!$forward) {
                    $forward = U("Item/Index/index");
                } else {
                    cookie("forward", NULL);
                }
                //增加登陆成功行为调用
                $public_tologin = array(
                    'username' => $username,
                    'ip' => $ip,
                );
                tag('public_tologin', $public_tologin);
                $this->srun('登录成功!');
            }
        }else{
            $this->display();
        }   
    }
}