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
        $list = $db->where(array('product_id'=>$this->pid,'type'=>$type))->select();
        foreach ($list as $k => $v) {
            $config[$v["varname"]] = $v["value"];
        }
        if(IS_POST){
            $product_id = $_POST["product_id"];
            if($product_id <> $this->pid){
                $this->erun('配置失败,请刷新页面重试...');
                return false;
            }
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
            $price = M('TicketGroup')->where(array('status'=>1))->field('id,name')->select();
           //dump($config['appid']);
            $this->api = new Api(
                array(
                    'appId' => $config['appid'],
                    'appSecret' => $config['appsecret'],
                    'get_access_token' => function(){
                        // 用户需要自己实现access_token的返回
                        return S('wechat_token');
                    },
                    'save_access_token' => function($token) {
                        // 用户需要自己实现access_token的保存
                        S('wechat_token', $token);
                    }
                )
            );

            $reg = $this->api->get_authorize_url('snsapi_userinfo',U('Wechat/Index/reg',array('pid'=>$this->pid)));
            $view = $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/show',array('pid'=>$this->pid)));
            $channel = $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/auth_channel',array('pid'=>$this->pid)));
            $active = $this->api->get_authorize_url('snsapi_base',U('Wechat/Index/acty',array('act'=>1,'pid'=>$this->pid)));
            $this->assign('price',$price)
                ->assign('view',$view)
                ->assign('reg',$reg)
                ->assign('acty',$active)
                ->assign('channel',$channel)
                ->assign("vo",$config)
                ->display(); 
        }
        
    }
    //新增账号
    function add(){
    	if(IS_POST){
    		if (D("Wechat/Wechat")->create()) {
                if (D("Wechat/Wechat")->add()) {
                    $this->srun("添加成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
                } else {
                    $this->erun("添加失败！");
                }
            } else {
                $error = D("Wechat/Wechat")->getError();
                $this->erun($error ? $error : '添加失败！');
            }
    	}else{
    		$this->display();
    	}
    }
    //编辑
    function edit(){
    	if(IS_POST){
            if (D("Wechat/Wechat")->create()) {
                if (D("Wechat/Wechat")->save()) {
                    $this->srun("更新成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
                } else {
                    $this->erun("更新失败！");
                }
            } else {
                $error = D("Wechat/Wechat")->getError();
                $this->erun($error ? $error : '添加失败！');
            }
    	}else{
            $id = I('get.id',0,intval);
            if(!empty($id)){
                $info = M('Wechat')->where(array('id'=>$id))->find();

                $this->assign('data',$info)->display();
            }else{
                $this->erun('参数错误');
            }
    	}
    }
    //删除
    function delete(){
        $ginfo = I('get.');
        if(empty($ginfo['id'])){
            $this->erun('参数错误!');
        }else{
        	if($ginfo['type'] == '1'){
				$status = M('WechatMenu')->where(array('id' => $ginfo['id']))->delete();
        	}else{
        		$status = M('Wechat')->where(array('id' => $ginfo['id']))->delete();
        	}
            if($status){
                $this->srun('删除成功',array('tabid'=>$this->menuid.MODULE_NAME));
            }else{
                $this->erun('参数错误!');
            }
        }
    }
    //账号详情
    function wxinfo(){
    	$id = I('id');
    	if(empty($id)){$this->erun('参数错误!');}else{
            $info = M('Wechat')->where(array('id'=>$id))->find();
            $info['config'] = unserialize($info['config']);
            $this->assign('data',$info)->display(); 
        }	
    }
    //功能管理
    function can(){
        $ginfo = I('get.');
        $this->assign('ginfo',$ginfo)->display();
    }
    //获取自定义菜单
    function menulist(){
    	if(IS_POST){
            C('TOKEN_ON',false);
            $data = $_POST;
            if (D("WechatMenu")->create($data)) {
                $id = I('id');
                if(!empty($id)){
                    $status = D("WechatMenu")->save($data);
                }else{
                    $status = D("WechatMenu")->add($data);
                }
                if($status != false){
                     $this->srun("添加/更新成功!",array('tabid'=>$this->menuid.MODULE_NAME));         
                }else{
                    $this->erun("添加/更新失败！");
                }
            }else{
                $this->erun(D("WechatMenu")->getError());
            }
    	}else{
    		$ginfo = I('get.');
    		$map = array(
    			'wechat_id' => $ginfo['id'],
    		);
	        $result = D("WechatMenu")->where($map)->order(array("sort" => "ASC"))->select();
	        foreach ($result as $k => $v) {
	            $data[] =  array(
                    "id"       => $v['id'],
                    "name"     => $v['name'],
                    "parentid" => $v['parentid'],
                    'wechat_id'=> $v['wechat_id'],
                    'status'   => $v['status'],
                    'type'     => $v['type'],
                    'sort'	   => $v['sort'],
                    'param'    => $v['param'],
	            );
	        }
	        $this->assign('menu',$data)->assign('wechat_id',$ginfo['id'])->display();
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
    
    //连接微信
    function link_wechat($wechat_id){
    	$info = M('Wechat')->where(array('id'=>$wechat_id))->find();
    	// 开发者中心-配置项-AppID(应用ID)
        $appId = $info['appid'];
        // 开发者中心-配置项-AppSecret(应用密钥)
        $appSecret = $info['appsecret'];
        // 开发者中心-配置项-服务器配置-Token(令牌)
        $token = $info['token'];
        // 开发者中心-配置项-服务器配置-EncodingAESKey(消息加解密密钥)
        $encodingAESKey = $info['encodingaeskey'];

        // wechat模块 - 处理用户发送的消息和回复消息
        $this->wechat = new Wechat(array(
            'appId' => $appId,
            'token' => 	$token,
            'encodingAESKey' =>	$encodingAESKey //可选
        ));
        // api模块 - 包含各种系统主动发起的功能
        $this->api = new Api(
            array(
                'appId' => $appId,
                'appSecret'	=> $appSecret,
                'get_access_token' => function(){
                    // 用户需要自己实现access_token的返回
                    return S('wechat_token');
                },
                'save_access_token' => function($token) {
                    // 用户需要自己实现access_token的保存
                    S('wechat_token', $token);
                }
            )
        );
    }
    //发布自定义菜单自定义菜单需要第二天，或重新关注才能生效！！！！
    function menu_send(){
    	$ginfo = I('get.');
    	if(empty($ginfo['id'])){
            $this->erun('参数错误!');
        }else{
        	$map = array(
	    		'wechat_id' =>  $ginfo['id'],
	    		'status'	=>	'1',
	    		'parentid'	=>	'0',
	    	);//dump($map);
	    	$db = M('WechatMenu');
            $this->link_wechat($ginfo['id']);
	    	$menu = $db->where($map)->select();
	    	foreach ($menu as $key => $value) {
	    		//判断是否有子菜单
	    		$sun_menu = $db->where(array('parentid'=>$value['id']))->select();
	    		if(empty($sun_menu)){
	    			$url = $this->api->get_authorize_url('snsapi_base',$value['param']);
	    			if($value['type'] == '1'){
		    			//分隔符
		    			if(empty($menulist)){
		    			   $menulist = '{"type":"click","name":"'.$value['name'].'","key":"'.$value['param'].'"}';
		    			}else{
		    			   $menulist = $menulist.','.'{"type":"click","name":"'.$value['name'].'","key":"'.$value['param'].'"}';
		    			}
	    			}else{
	    				if(empty($menulist)){
		    			   $menulist = '{"type":"view","name":"'.$value['name'].'","url":"'.$url.'"}';
		    			}else{
		    			   $menulist = $menulist.','.'{"type":"view","name":"'.$value['name'].'","url":"'.$url.'"}';
		    			}
	    			}
	    		}else{
	    			foreach ($sun_menu as $k => $v) {
	    				$url = $this->api->get_authorize_url('snsapi_base',$v['param']);
	    				if($v['type'] == '1'){
			    			if(empty($sub_menulist)){
			    			   $sub_menulist = '{"type":"click","name":"'.$value['name'].'","key":"'.$value['param'].'"}';
			    			}else{
			    			   $sub_menulist = $sub_menulist.','.'{"type":"view","name":"'.$value['name'].'","url":"'.$url.'"}';
			    			}
		    			}else{
		    				if(empty($sub_menulist)){
			    			   $sub_menulist = '{"type":"click","name":"'.$value['name'].'","key":"'.$value['param'].'"}';
			    			}else{
			    			   $sub_menulist = $sub_menulist.','.'{"type":"view","name":"'.$value['name'].'","url":"'.$url.'"}';
			    			}
		    			}
	    			}
	    			if(empty($menulist)){
	    			   $menulist = '{"name":"'.$value['name'].'","sub_button":['.$sub_menulist.']}';
	    			}else{
	    			   $menulist = $menulist.','.'{"name":"'.$value['name'].'","sub_button":['.$sub_menulist.']}';
	    			}
	    		}
	    	}
	    	$data = '{"button":['.$menulist.']}';//dump($data);
	    	$status = $this->api->create_menu($data);
	    	$status = objectToArray($status);
            if(empty($status[0])){
                $this->srun('发布成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
            }else{
                $this->erun('发布失败!错误代码:'.$status[0]['errcode']);
            }
        }	
    }
    //删除菜单
    function remove_menu(){
    	$this->link_wechat($ginfo['id']);
    	$status = $this->api->delete_menu();
    	$status = objectToArray($status);
    	if(empty($status[0])){
            $this->srun('删除成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
       	}else{
            $this->erun('删除失败!错误代码:'.$status[0]['errcode']);
        }
    }
    //微信页面
    function tpl(){
    	if(IS_POST){

    	}else{
    		$serverUrl = 'http://www.meihua.com';
    		$url = $serverUrl . '/index.php?m=Api&c=tmpls&a=lists&uid=1';
			$rt = getHttpContent($url,'GET');
			$tmpls = json_decode($rt, true);

			//dump($tmpls);
    		$this->assign('tmpls',$tmpls)->display();
    	}
    }
    //门票销售页面
    function show_ticket(){
        $db = M('Wechat');
        if(IS_POST){
            $pinfo = I('post.');
            $conf = array(
                'title' =>  $pinfo['title'],
                'desc'  =>  $pinfo['desc'],
                );
            $status = $db->where(array('id'=>$pinfo['id']))->setField('tpl',serialize($conf));
            if($status){
                $this->srun("添加成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
            } else {
                $this->erun("添加失败！");
            }
        }else{
            $ginfo = I('get.');
            $info = $db->where(array('id'=>$ginfo['id']))->field('id,tpl')->find();
            $info['tpl'] = unserialize($info['tpl']);
            dump($info);
            $this->assign('data',$info)->display();
        }
    }
    //模板消息
    function tplmsg(){
        if(IS_POST){

        }else{
            $ginfo = I('get.');
            $info = $db->where(array('id'=>$ginfo['id']))->field('id,config')->find();
            $info['config'] = unserialize($info['config']);
            $this->assign('data',$info)->display();
        }
    }
}