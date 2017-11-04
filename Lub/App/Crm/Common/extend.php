<?php
// +----------------------------------------------------------------------
// | LubTMP  客户扩展函数
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
/**
 * @Author   zhoujing   <zhoujing@leubao.com>
 * @DateTime 2017-10-31
 * @param    string     $param                类型参数
 * @return   string
 */
function memberType($param = '', $type = '1')
{
	switch ($param) {
		case '1':
			$return = '按次计费';
			break;
		case '2':
			$return = '年卡';
			break;
		case '3':
			$return = '时间段计费';
			break;
		case '4':
			$return = '身份识别';
			break;
		default:
			$return = '未知';
			break;
	}
	if($type == 1){
		echo $return;
	}else{
		return $return;
	}
}