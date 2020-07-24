<?php
use Libs\Service\ReturnCode;
/**
 * 全局状态返回
 * @param string $code
 * @param array $data
 * @param string $msg
 * @return array
 */
function showReturnCode($status = false, $code = '1002', $data = [], $msg = '')
{
    $return_data = [
        'status' => $status,
        'code' => '0',
        'msg' => '未定义消息',
        'count'=> $code == 0 ? $count : 0,
        'data' => $data
    ];
    $return_data['code'] = $code;
    if(!empty($msg)){
        $return_data['msg'] = $msg;
    }else if (isset(ReturnCode::$return_code[$code]) ) {
        $return_data['msg'] = ReturnCode::$return_code[$code];
    }
    //当错误时记录日志 TODO
    die(json_encode($return_data));
    //return json($return_data);
}

function ticketStatus($value='')
{
    $arr = [
        0 => '未售出',
        2 => '未使用',
        99 => '已使用'
    ];
    return $arr[$value];
}
//获取订单门票
function getOrderTicket($plan_id, $sn)
{
    $plan = F('Plan_'.$plan_id);
    if(empty($plan)){
        $plan = D('Plan')->where(['id'=>$plan_id])->field('seat_table')->find();
    }
    $field = [
        'id',
        'idcard',
        'checktime',
        'sale',
        'status'
    ];
    $ticket = D($plan['seat_table'])->where(['order_sn'=>$sn])->field($field)->select();
    foreach ($ticket as $k => $v) {
        $sale = array_merge($v, unserialize($v['sale']));
        unset($sale['sale']);
        $return[] = $sale;
    }
    return $return;
}