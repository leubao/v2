<?php
use Libs\Service\ReturnCode;
/**
 * 全局状态返回
 * @param string $code
 * @param array $data
 * @param string $msg
 * @return array
 */
function showReturnCode($status = false, $code = '1002', $data = [], $count = '', $msg = '')
{
    $return_data = [
        'status' => $status,
        'code' => '0',
        'msg' => '未定义消息',
        'count'=> $code == 0 ? $count : 0,
        'data' => $code == 0 ? $data : []
    ];
    $return_data['code'] = $code;
    if(!empty($msg)){
        $return_data['msg'] = $msg;
    }else if (isset(ReturnCode::$return_code[$code]) ) {
        $return_data['msg'] = ReturnCode::$return_code[$code];
    }
    //当错误时记录日志 TODO
    //return json_encode($return_data);
    return json($return_data);
}
/**/
