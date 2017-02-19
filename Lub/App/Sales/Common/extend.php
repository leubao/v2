<?php
// +----------------------------------------------------------------------
// | LubTMP  系统扩展函数  分销模块
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
/**
 * 分销类型
 * @param  int $param 1推广2全员销售3三级分销
 * @return [type]        [description]
 */
function sales_type($param = ''){
	switch ($param) {
		case '1':
			$return = "推广";
			$label = "info";
			break;
		case '2':
			$return = "全员销售";
			$label = "default";
			break;
		case '3':
			$return = "三级分销";
			$label = "success";
			break;
	}
	echo "<span class='label label-".$label."'>".$return."</span>";
}
?>