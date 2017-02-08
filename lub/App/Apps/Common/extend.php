<?php
// +----------------------------------------------------------------------
// | LubTMP  扩展函数
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
//短信网关
function gateway($param, $type = null){
	switch ($param) {
		case '1':
			$return = "bechtech";
			break;
		case '2':
			$return = "SendCloud";
			//http://sendcloud.sohu.com/
			break;
		case '3':
			$return = "云通讯";
			//http://sendcloud.sohu.com/
			break;
		case '4':
			$return = "聚合网络";
			//http://sendcloud.sohu.com/
			break;
		default:
			# code...
			break;
	}
	if($type){
		return $return;
	}else{
		echo $return;
	}
}
?>