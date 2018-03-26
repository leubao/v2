<?php
//是否显示微信解除按钮
function show_disabled($param){
	if(empty($param)){
		$this->error("参数错误");
	}
	if(M('UserData')->where(array('user_id'=>$param))->getField('wechat') == '1'){
		echo " ";
	}else{
		echo "disabled";
	}
}
//判断当前充值商户是否是当前商户的子商户
function check_crm_child($id = '')
{
    $info = D('Crm')->where(['id'=>$id,'status'=>'1'])->field('id,f_agents')->find();
    if(empty($info)){
    	return false;
    }
    $uinfo = Home\Service\Partner::getInstance()->getInfo();
    if((int)$info['f_agents'] === (int)$uinfo['crm']['id']){
    	return true;
    }else{
    	return false;
    }
}
