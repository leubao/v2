<?php
// +----------------------------------------------------------------------
// | LubTMP 渠道商的公共控制器
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use Home\Service\Partner;

class PublicController extends Base{
    protected function _initialize(){
        /*if($this->check_wap()){
            $this->redirect("Wap/Public/login"); 
        }*/   
    }   
	//后台登陆界面
    public function login() {
        /*如果已经登录*/
    	if (Partner::getInstance()->id) {
            $this->redirect('Home/Index/index');
        }
        $itemName = D("ConfigItem")->where(['varname'=>'item_name'])->cache('itemName',60)->getField('value');
        $this->assign('title', $itemName)->display();
    }
	
    //渠道登陆验证
    public function tologin() {
        //记录登陆失败者IP
        $ip = get_client_ip();
        $username = I("post.username", "", "trim");
        $password = I("post.password", "", "trim");
        $type  = I("post.type",0,intval);
        if (empty($username) || empty($password)) {
            $this->error("用户名或者密码不能为空，请重新输入！", U("Public/login"));
        }
        if(get_user_id()){
            $this->error("当前终端已存在登录用户,请退出之后再进行登录", U("Public/login"));
        }
        //渠道商登录
        if (Partner::getInstance()->login($username, $password)) {
            $forward = cookie("forward");
            if (!$forward) {
                $forward = U("Home/Index/index");
            } else {
                cookie("forward", NULL);
            }
            $tologin = array(
                'username' => $username,
                'ip' => $ip,
            );
            tag('tologin', $tologin);
            $this->redirect('Index/index');
        } else {
            $this->error("用户名或者密码错误，登陆失败！", U("Public/login"));
        }
    }

    //退出登陆
    public function logout() {
        if (Partner::getInstance()->logout()) {
            //手动登出时，清空forward
            cookie("forward", NULL);
            $this->success('注销成功！', U("Home/Public/login"));
        }
    }
    //忘记密码
    public function password(){
    	if(IS_POST){
    		$info = I('post.');
    		if(empty($info['type'])){
    			$this->error('参数错误!');
    		}
    		if($info['type'] == '1'){
    			//手机找回密码
    		}else{
    			//备用邮箱找回
    		}
    	}else{
    		$this->display();
    	}	
    }
    //修改密码
    public function changepass(){
        $this->display();
    }
}