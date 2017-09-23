<?php
// +----------------------------------------------------------------------
// | LubTMP 微信前台
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2015-8-25 
// +----------------------------------------------------------------------
namespace Wechat\Controller;
use Common\Controller\LubTMP;
use Wechat\Service\Wticket;
use \Wechat\WechatReceive;
class ShopController extends LubTMP {
    protected function _initialize() {
    	parent::_initialize();
        $this->ginfo = I('get.');
        $this->pid = $this->ginfo['pid'];
        if(empty($this->pid) && empty(session('pid'))){
            $this->error("参数错误");
        }
        if(empty($this->pid)){
            $this->pid = session('pid');
        }else{
            session('pid',$this->pid);
        }
        //暂存访问的URL
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        session('oaurl',$url);
    }
    //微商城首页
    function index(){
    	$this->display();
    }
    //判断是否已经授权
    public function getOauth($value='')
    {
        $oauth = session('oauth');
        if(!$oauth['open_id']){
            //正常处理完成，返回原链接
            $rurl = session('oaurl');
            header("Location:" . $rurl);
        }
    }

    //产品单页
    public function show()
    {
        $this->display();
    }

}