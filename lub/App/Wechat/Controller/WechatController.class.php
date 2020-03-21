<?php
// +----------------------------------------------------------------------
// | LubTMP 微信管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19 
// +----------------------------------------------------------------------
namespace Wechat\Controller;
use Common\Controller\ManageBase;
use Wechat\Service\Wechat;
use Wechat\Service\Api;
class WechatController extends ManageBase{
	protected function _initialize() {
        parent::_initialize();
    }
	//微信账号设置
	function index(){
        $db = M("ConfigProduct");   //产品设置表 
        $type = '2';
        $product_id = (int)get_product('id');
        $list = $db->where(array('product_id'=>$product_id,'type'=>$type))->select();
        foreach ($list as $k => $v) {
            $config[$v["varname"]] = $v["value"];
        }
        if(IS_POST){
            $data = $_POST;
            if (empty($data) || !is_array($data)) {
                $this->erun('配置数据不能为空！');
                return false;
            }
            $diff_key = array_diff_key($config,$data);
            foreach ($data as $key => $value) {
                if (empty($key)) {
                    continue;
                }
                $saveData = array($config,);
                $saveData["value"] = trim($value);
                $count = $db->where(array("varname"=>$key,'type'=>$type,'product_id'=>$product_id))->count();
                $ginfo = array();   
                if ($count == 0) {//此前无此配置项
                    if($key!="__hash__"&&$key!="product_id"&&$key!='type'){
                        $ginfo["varname"] = $key;
                        $ginfo["value"]   = trim($value);
                        $ginfo["product_id"] = $product_id;
                        $ginfo["type"]  =   $type;
                        $add = $db->add($ginfo);
                    }
                }else{
                    if ($db->where(array("varname" => $key,'product_id'=>$product_id,'type'=>$type))->save($saveData) === false) {
                        $this->erun("更新到{$key}项时，更新失败！");
                        return false;
                    }                   
                }
            }
            //更新未选择的复选框
            foreach ($diff_key as $key => $value) {
                $saveData = array();
                $saveData["value"] = '0';
                $saveData["product_id"] = $product_id;
                if ($db->where(array("varname" => $key,'type'=>$type))->save($saveData) === false) {
                    $this->erun("更新到{$key}项时，更新失败！");
                    return false;
                }
            }
            D('Common/Config')->config_cache();
            $this->srun("配置成功!", array('tabid'=>$this->menuid.MODULE_NAME));    
        }else{
            //获取价格分组
            $price = M('TicketGroup')->where(array('status'=>1,'product_id'=>$product_id))->field('id,name')->select();
            // SDK实例对象
            $oauth = & load_wechat('Oauth',$product_id,1);
            // 执行接口操作
            $reg = $oauth->getOauthRedirect(U('Wechat/Index/reg',array('pid'=>$product_id)), $state, 'snsapi_userinfo');
            $view = $oauth->getOauthRedirect(U('Wechat/Index/show',array('pid'=>$product_id)), $state, 'snsapi_base');
            $channel = $oauth->getOauthRedirect(U('Wechat/Index/auth_channel',array('pid'=>$product_id)), $state, 'snsapi_base');
            $active = $oauth->getOauthRedirect(U('Wechat/Index/acty',array('pid'=>$product_id,'act'=>1)), $state, 'snsapi_base');
            $uinfo = $oauth->getOauthRedirect(U('Wechat/Index/uinfo',array('pid'=>$product_id)), $state, 'snsapi_base');
            $uorder = $oauth->getOauthRedirect(U('Wechat/Index/orderlist',array('pid'=>$product_id)), $state, 'snsapi_base');
            $this->assign('price',$price)
                ->assign('view',$view)
                ->assign('reg',$reg)
                ->assign('acty',$active)
                ->assign('channel',$channel)
                ->assign('uinfo',$uinfo)
                ->assign('uorder',$uorder)
                ->assign("vo",$config)
                ->display(); 
        }
        
    }
    //获取专属关注二维码//获取微信公众平台专属推广二维码
    function get_wechat_code($user_id){
        //获取userID的加密数据
        $api = new Api(array(
                'appId' => $this->procof['appid'],
                'appSecret' => $this->procof['appsecret']
            ));
        $scene_id = '9'.$user_id;
        /*拉去所有用户
        $userList = $api->get_user_list();
        
        //写入数据库
        $userList = $userList[1]->data;
        foreach ($userList->openid as $key => $value) {
            $data[] = array('openid' => $value);
            //dump($api->get_user_info($value,'zh_CN'));
        }
        D('WxMember')->addAll($data);*/
        $return = $api->create_qrcode($scene_id,604800);
        //生成二维码
        return $return[1]->ticket;
    }
}