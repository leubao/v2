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