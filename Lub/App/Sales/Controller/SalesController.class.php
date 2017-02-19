<?php
// +----------------------------------------------------------------------
// | LubTMP 销售模块
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Sales\Controller;
use Common\Controller\ManageBase;
class SalesController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
	}
	function index(){
		$phone = I('phone');
        $legally = I('legally');
        $user = I('user_id');
        $type = I('type');
        $status = I('status');
        if(!empty($phone)){$map['phone'] = $phone;}
        if(!empty($legally)){$map['legally'] = $legally;}
        if(!empty($user)){$map['id'] = $user;}
        if(!empty($type)){$map['type'] = $type;}
        if(!empty($status)){$map['status'] = $status;}
        $map['is_scene'] = '4';
    	$this->basePage('User',$map,array('id'=>'DESC'));
		$this->assign('map',$map)->display();
	}
	//全员
	/**
	 * 三级分销设置
	 * @return [type] [description]
	 */
	function level3(){
		$db = M("ConfigProduct");   //产品设置表 
		$type = '9';
		$product_id = (int)get_product('id');
		$list = $db->where(array('product_id'=>$product_id,'type'=>$type))->select();
		foreach ($list as $k => $v) {
			$config[$v["varname"]] = $v["value"];
		}
		if(IS_POST){
			$pinfo = $_POST;
            //判断是否开启多级分销
            if($pinfo['wechat_level_3'] == '1'){
                //新建多级返利表  作为票型表的扩展 
                $dbs = M('TicketType');
                //根据提交过来的票型分组拉去所有票型
                $ticket = $dbs->where(array('group_id'=>$pinfo['price_group_level']))->field('id,param')->select();
                foreach ($ticket as $key => $value) {
                    $param = unserialize($value['param']);
                    $level3 = array(
                        'l1' => $pinfo['tic'][$value['id']][0],
                        'l2' => $pinfo['tic'][$value['id']][1],
                        'l3' => $pinfo['tic'][$value['id']][2],
                    );
                    $param['level3'] = $level3;
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
            $group = M('CrmGroup')->where(array('status'=>1,'type'=>4,'product_id'=>$product_id))->field('id,name,price_group')->select();
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
            $reg = $api->get_authorize_url('snsapi_userinfo',U('Wechat/Index/reg',array('pid'=>$this->pid,'type'=>9)));
            $this->assign('group',$group)->assign('reg',$reg)->display();
		}
	}
	
}