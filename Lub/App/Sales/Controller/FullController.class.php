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
            if($this->procof['wechat_full'] == '1'){
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
            $this->assign("vo",$config);
            //获取价格分组
            $map = array('status'=>1,'type'=>4,'product_id'=>$product_id);
            $group = M('CrmGroup')->where($map)->field('id,name,price_group')->select();
            $oauth = & load_wechat('Oauth',$product_id,'1');
            // 执行接口操作
            $reg = $oauth->getOauthRedirect(U('Wechat/Index/reg',array('pid'=>$product_id,'type'=>8)), $state, 'snsapi_userinfo');
            //$reg = U('Wechat/Index/reg',array('pid'=>$product_id,'type'=>8));
            $this->assign('group',$group)->assign('reg',$reg)->display();
        }
    }
    //审核
    function audit(){
    	if(IS_POST){
    		$pinfo = I('post.');
            //所属分组，状态
            $data = array('id'=>$pinfo['id'],'type'=>$pinfo['type'],'groupid'=>$pinfo['groupid'],'status'=>$pinfo['status'],'channel'=>'1', 'remark'=>$pinfo['remark']);
           // $ticket = (string)\Wechat\Controller\WechatController::get_wechat_code($pinfo['id']);
            //链接微信获取专属推广二维码
            if(D('User')->save($data)){
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
    	
        $base64_image_content = get_up_fxqr($ginfo['id'], get_product('id'));
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
                'groupid'   =>  $pinfo['groupid'],
                'remark'    =>  $pinfo['remark'],
                'status'    =>  $pinfo['status'],
                'type'      =>  $pinfo['type'],
                'update_time' => time(),
            );
            $status = M('User')->where(array('id'=>$pinfo['id']))->save($data);
            D('UserData')->where(array('user_id'=>$pinfo['id']))->setField('industry',$pinfo['industry']);
            if($status){
                $this->srun('更新成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
            }else{
                $this->erun('更新失败!');
            }
        }else{
            $ginfo = I('get.');
            $info = D('Crm/UserView')->where(array('id'=>$ginfo['id']))->field('id,nickname,groupid,type,phone,industry')->find();
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