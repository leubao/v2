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
/**
 * 判断文件是否存在
 * @param  string $path 路径
 * @param  int $type 类型
 */
function if_file($path,$type = ''){
    if (file_exists($path)) {
        $msg = "已上传";
        $status = "success";
        $boot = true;
    }else{
        $msg = "未上传";
        $status = "danger";
        $boot = false;
    }
    if($type == '1'){
        return $boot;
    }else{
        $return = "<span class='label label-".$status."'>".$msg."</span>";
        echo $return;
    }
}
function cronName($param = '')
{
    echo M('Cron')->where(['cron_id'=>$param])->getField('subject');
}
?>