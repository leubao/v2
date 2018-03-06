<?php
// +----------------------------------------------------------------------
// | LubTMP 系统第三方支付配置
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

namespace Manage\Controller;

use Common\Controller\ManageBase;

class PayController extends ManageBase {
	//配置第三方支付
	function index()
	{
		//$db = M("ConfigProduct");   //产品设置表 
        $db = M("ConfigItem");   //产品设置表 
		$item_id = (int)get_item('id');
		$list = $db->where(array('item_id'=>$item_id))->select();
		foreach ($list as $k => $v) {
			$config[$v["varname"]] = $v["value"];
		}
		if(IS_POST){
			$pinfo = $_POST;
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
                $count = $db->where(array("varname"=>$key,'type'=>$type,'item_id'=>$item_id))->count();
                $ginfo = array();   
                if ($count == 0) {//此前无此配置项
                    if($key!="__hash__"&&$key!="item_id"&&$key!='type'){
                        $ginfo["varname"] = $key;
                        $ginfo["value"]   = trim($value);
                        $ginfo["item_id"] = $item_id;
                        $add = $db->add($ginfo);
                    }
                }else{
                    if ($db->where(array("varname" => $key,'item_id'=>$item_id))->save($saveData) === false) {
                        $this->erun("更新到{$key}项时，更新失败！");
                        return false;
                    }                   
                }
            }
            //更新未选择的复选框
            foreach ($diff_key as $key => $value) {
                $saveData = array();
                $saveData["value"] = '0';
                $saveData["item_id"] = $item_id;
                if ($db->where(array("varname" => $key))->save($saveData) === false) {
                    $this->erun("更新到{$key}项时，更新失败！");
                    return false;
                }
            }
            D('Common/Config')->config_cache();
            $this->srun("配置成功!", array('tabid'=>$this->menuid.MODULE_NAME)); 
		}else{
            //加载几个文件路径
            $path = [
                'w_cert' => SITE_PATH.'pay/wxpay/'.$item_id.'/apiclient_cert.pem',
                'w_key'  => SITE_PATH.'pay/wxpay/'.$item_id.'/apiclient_key.pem',
            ];
			$this->assign("vo",$config)->assign('path',$path)->display();
		}
	}
}