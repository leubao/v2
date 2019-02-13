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
		case '8':
			$return = "全员销售";
			$label = "default";
			break;
		case '9':
			$return = "三级分销";
			$label = "success";
			break;
	}
	echo "<span class='label label-".$label."'>".$return."</span>";
}
/**
 * 编号
 */
function user_coding($param = ''){
	
	echo 'M'.sprintf("%05d",$param);
}
/**
 * 返回提现状态
 * 1提现成功3待审核4驳回
 */
function pay_cash_back_status($param = ''){
	switch ($param) {
        case 0:
            echo "<span class='label label-danger'>已作废</span>";
            break;
        case 4:
            echo "<span class='label label-danger'>驳回</span>";
            break;
        case 3:
            echo "<span class='label label-success'>待审核</span>";
            break;
        case 5:
            echo "<span class='label label-warning'>发放中</span>";
            break;
        case 6:
            echo "<span class='label label-warning'>分包中</span>";
            break;
        case 1:
            echo "<span class='label label-default'>完结</span>";
            break;
    }
}
function userMobile($param,$type=NULL){
    if(!empty($param)){
        $name = M('User')->where(array('id'=>$param))->getField('phone');
    }else{
        $name = "未知";
    }
    if($type){
        return $name ? $name : "未知";
    }else{
        echo $name;
    }
}
?>