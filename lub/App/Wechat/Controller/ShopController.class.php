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
use Wechat\Service\Wechat;
use WeChat\Service\Wxpay;
use Wechat\Service\Api;


//微信支付
use Wechat\Service\Wxpay\WxPayApi;
use Wechat\Service\Wxpay\JsApiPay;
use Wechat\Service\Wxpay\WxPayConfig;
use Wechat\Service\Wxpay\WxPayUnifiedOrder;
use Wechat\Service\Wxpay\WxPayOrderQuery;
use Wechat\Service\Wxpay\WxPayException;
use Wechat\Service\Wxpay\WxPayNotify;
use Wechat\Controller\PayNotifyCallBackController;

class ShopController extends LubTMP {
    protected function _initialize() {
    	parent::_initialize();
    }
    //微商城首页
    function index(){
    	$this->display();
    }
    //商品详情页面
    function view(){
    	$this->display();
    }
    //价格日历
    function calendar(){
        $ginfo = I('get.');
        //产品id
        
        $this->display();
    }

}