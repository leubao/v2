<?php
// +----------------------------------------------------------------------
// | LubTMP 全员销售
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19 
// +----------------------------------------------------------------------
namespace Sales\Controller;
use Common\Controller\ManageBase;
use \Wechat\Service\Api;
class FullController extends ManageBase{
	protected function _initialize() {
        parent::_initialize();
        $proconf = cache('ProConfig');
        // 开发者中心-配置项-AppID(应用ID)
        $this->appId = $proconf[$this->pid]['2']['appid'];
        // 开发者中心-配置项-AppSecret(应用密钥)
        $this->appSecret = $proconf[$this->pid]['2'];
        $this->api = new Api(
            array(
                'appId' => $this->appId,
                'appSecret' => $this->appSecret,
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
    //销售人员列表 登录场景为4
    function index(){
        $phone = I('phone');
        $legally = I('legally');
        $user = I('user_id');
        if(!empty($phone)){$map['phone'] = $phone;}
        if(!empty($legally)){$map['legally'] = $legally;}
        if(!empty($user)){$map['id'] = $user;}
        $map['is_scene'] = '4';
    	$this->basePage('User',$map,array('id'=>'DESC'));
		$this->display();
    }
    //全员销售设置
    function setfull(){
        $db = M("ConfigProduct");   //产品设置表 
        $type = '8';
        $product_id = (int)get_product('id');
        $list = $db->where(array('product_id'=>$product_id,'type'=>$type))->select();
        foreach ($list as $k => $v) {
            $config[$v["varname"]] = $v["value"];
        }
        if(IS_POST){
            $pinfo = $_POST;
            //判断是否开启多级分销
            if($pinfo['wechat_full'] == '1'){
                //新建多级返利表  作为票型表的扩展 
                $dbs = M('TicketType');
                //根据提交过来的票型分组拉去所有票型
                $ticket = $dbs->where(array('group_id'=>$pinfo['price_group_full']))->field('id,param')->select();
                foreach ($ticket as $key => $value) {
                    $param = unserialize($value['param']);
                    $param['full'] = $pinfo['tic'][$value['id']];
                    $sataus = $dbs->where(array('id'=>$value['id']))->setField('param',serialize($param));
                }
            }
            if (empty($pinfo) || !is_array($pinfo)) {
                $this->erun('配置数据不能为空！');
                return false;
            }
            $diff_key = array_diff_key($config,$pinfo);
            foreach ($pinfo as $key => $value) {
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
                        $ginfo["product_id"] = $this->pid;
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
            $this->assign("vo",$config);
            //获取价格分组
            $group = M('CrmGroup')->where(array('status'=>1,'type'=>4,'product_id'=>(int)$this->pid))->field('id,name,price_group')->select();
            /*TODO 需要优化*/
            $proconf = cache('ProConfig');
            $proconf = $proconf[$product_id][2];
            $api = new \Wechat\Service\Api(
                array(
                    'appId' => $proconf['appid'],
                    'appSecret' => $proconf['appsecret'],
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
            $reg = $api->get_authorize_url('snsapi_userinfo',U('Wechat/Index/reg',array('pid'=>$this->pid,'type'=>8)));
            $this->assign('group',$group)->assign('reg',$reg)->display();
        }
    }
    //新详情匹配
    function new_param($value='')
    {
        # code...
    }
    //审核
    function audit(){
    	if(IS_POST){
    		$pinfo = I('post.');
    		//所属分组，状态
    		$data = array('id'=>$pinfo['id'],'groupid'=>$pinfo['groupid'],'status'=>$pinfo['status'],'remark'=>$pinfo['remark']);
            $ticket = (string)\Wechat\Controller\WechatController::get_wechat_code($pinfo['id']);
            //链接微信获取专属推广二维码
    		if(D('User')->save($data) && D('WxMember')->where(array('user_id'=>$pinfo['id']))->setField('ticket',$ticket)){
    			//发送审核通过的短信 
    			//\Libs\Service\Sms::order_msg(array('title'=>$pinfo['name'],'phone'=>$pinfo['phone']),8);
    			$this->srun("审核成功",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
    		}else{
    			$this->erun("审核失败");
    		}
    	}else{
    		$ginfo = I('get.');
           // \Wechat\Controller\WechatController::get_wechat_code($pinfo['id']);
    		//获取用户信息
    		$info = D('Item/User')->where(array('id'=>$ginfo['id']))->field(array('verify','password'),true)->relation(true)->find();
    		$group = M('CrmGroup')->where(array('status'=>1,'type'=>4))->field('id,name')->select();
        	$this->assign('data',$info)->assign('group',$group)->display();
    	}
    }
    
    //停用
    function disable(){
    	$ginfo = I('get.');
    	$db = M('User');
    	$info = $db->where(array('id'=>$ginfo['id']))->field('status')->find();
    	if($info['status'] == '3'){
    		$this->erun('未完成审核,不能执行此项操作!');
    	}
    	if($info['status'] == '1'){
			//停用
			$user_start = $db->where(array('id'=>$ginfo['id']))->setField('status',0);
		}else{
			//启用
			$user_start = $db->where(array('id'=>$ginfo['id']))->setField('status',1);
		}
		if($user_start){
			$this->srun('停用成功!',array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$this->erun('停用失败!');
		}
    }
    //二维码
    function qrcode(){
    	$ginfo = I('get.');
    	if(empty($ginfo['id'])){
    		$this->erun("参数错误!");
    	}
    	//生成二维码
        $image_file = SITE_PATH."d/upload/".'u-'.$ginfo['id'];
        //二维码是否已经生成
        if(!file_exists($image_file)){
            //构造链接
            $url = U('Wechat/Index/show',array('u'=>$ginfo['id']));
            $urls = $this->api->get_authorize_url('snsapi_base',$url);
        }
        $base64_image_content = qr_base64($urls,'u-'.$ginfo['id']);
    	$this->assign('qr',$base64_image_content)->assign('id',$ginfo['id'])->display();
    }
    /**
     * 编辑
     * @return [type] [description]
     */
    function edit(){
        if(IS_POST){
            $pinfo = I('post.');
            if(empty($pinfo['id'])){
                $this->erun("参数错误!");
            }
            $data = array(
                'nickname'  =>  $pinfo['nickname'],
                'phone'     =>  $pinfo['phone'],
                'groupid'   =>  $pinfo['groupid'],
                'remark'    =>  $pinfo['remark'],
            );
            $status = M('User')->where(array('id'=>$pinfo['id']))->save($data);
            if($status){
                $this->srun('更新成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
            }else{
                $this->erun('更新失败!');
            }
        }else{
            $ginfo = I('get.');
            $info = M('User')->where(array('id'=>$ginfo['id']))->field('id,nickname,groupid,phone')->find();
            $group = M('CrmGroup')->where(array('status'=>1,'type'=>4))->field('id,name')->select();
            $this->assign('group',$group)->assign('data',$info)->display();
        }
    }
    //收入
    function income(){
    	$this->display();
    }
    /*二维码下载 TODO  不可用*/
    function public_qrdown(){
    	$ginfo = I('get.');
    	if(empty($ginfo['id'])){
    		$this->erun("参数错误!");
    	}
		$uid = 'u-'.$ginfo['id'];
    	//生成二维码
    	$image_file = SITE_PATH."d/upload/".$uid;
		$filename=realpath($image_file); //文件名
		$date=date("Ymd-H:i:m");
		Header( "Content-type:  application/octet-stream "); 
		Header( "Accept-Ranges:  bytes "); 
		Header( "Accept-Length: " .filesize($filename));
		header( "Content-Disposition:  attachment;  filename= {$date}.png"); 
		echo file_get_contents($filename);
		readfile($filename); 
    }
    /**
     * 多级分销
     */
    
}