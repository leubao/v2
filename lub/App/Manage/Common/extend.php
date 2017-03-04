<?php
// +----------------------------------------------------------------------
// | LubTMP  系统扩展函数
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
/**
 * 第三方支付状态
 * @param  [type] $param [description]
 * @return [type]        [description]
 */
function pay_status($param)
{
    switch ($param) {
        case 0:
            $msg = "已作废";
            $status = "danger";
            break;
        case 1:
            $msg = "支付成功";
            $status = "success";
            break;
        case 3:
            $msg = "等待支付";
            $status = "warning";
            break;
        case 4:
            $msg = "已退款";
            $status = "default";
            break;
        
    }
    $return = "<span class='label label-".$status."'>".$msg."</span>";
    echo $return;

}
?>