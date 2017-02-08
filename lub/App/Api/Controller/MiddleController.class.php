<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务 景区中间层
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Common\Controller\ApiBase;
use Libs\Service\Api;
class MiddleController extends ApiBase {
    //返回当前可检票场次
    function today_plan(){
        if(IS_POST){
            $pinfo = I('post.');
            $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
            if($appInfo == false){
                $return = array('code' => 401,'info' => '','msg' => '认证失败');
                $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo['appid']);
                echo json_encode($return);
                return false;
            }
            $datetime = date('Ymd');
            if($pinfo['datetime'] <> $datetime){
                $return = array('code' => 405, 'info' => '', 'msg' => '中间层服务器已过期',);
                $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$info);
                echo json_encode($return);
                return false;
            }else{
                $map = array(
                    'plantime'  =>  strtotime($pinfo['datetime']),
                    'starttime' =>  array(array('EGT', $pinfo['start']), array('ELT', $pinfo['end']), 'AND'),
                    'status'    =>  '2',
                );
                $data = M('Plan')->where($map)->field('id,games,product_id,plantime,starttime,endtime,status,param')->select();
                foreach ($data as $key => $value) {
                    $data[$key] = $value;
                    $data[$key]['param'] = serialize($this->format_plan($value['param']));
                }
                if(!empty($data)){
                    $return = array(
                        'code'  => 200,
                        'info'  => $data,
                        'msg'   => 'ok',
                    );
                    $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
                    echo json_encode($return);
                    return true;
                }else{
                    $return = array(
                        'code'  => 406,
                        'info'  =>  '',
                        'msg'   => '未找到可用计划',
                    );
                    $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
                    echo json_encode($return);
                    return false;
                }
            }
        }else{
            $return = array(
                'code'  => 409,
                'info'  =>  '',
                'msg'   => '系统不支持的请求方式',
            );
            $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
            echo json_encode($return);
            return false;
        }
    }
    //处理计划参数信息
    function format_plan($param){
        $info = unserialize($param);
        foreach ($info['seat'] as $v) {
           $data[] = array('id'=>$v,'name'=>areaName($v,1),'num'=>areaSeatCount($v,1));
        }
        return $data;
    }
    //获取座位信息表 返回字段包括 id Seat  status print order_sn  下方数据时直接下放加密信息  中间层不解密  直接比对密文
    function get_seat_data(){
    	if(IS_POST){
            $pinfo = I('post.');
            $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
            if($appInfo == false){
                $return = array('code' => 401,'info' => '','msg' => '认证失败');
                $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
                echo json_encode($return);
                return false;
            }else{
                $plan = F('Plan_'.$pinfo['id']);
                $list = M(ucwords($plan['seat_table']))->where(array('status'=>2,'middle'=>array('notin','2')))->field('id,order_sn,print,area,seat')->select();
                foreach ($list as $key => $value) {
                   $data[] = $this->re_print($plan['id'],$plan['encry'],$value);
                   M(ucwords($plan['seat_table']))->where(array('id'=>$value['id']))->setField('middle','2');
                }
                if(!empty($data)){
                    $return = array(
                        'code'  => 200,
                        'info'  => $data,
                        'msg'   => 'ok',
                    );
                    echo json_encode($return);
                    return true;
                }else{
                    $return = array(
                        'code'  => 408,
                        'info'  =>  '',
                        'msg'   => '未找到已售出座位',
                    );
                    echo json_encode($return);
                    return false;
                }
            }
        }
    }
    //二维码信息加密处理  直接返回密文
    /*
    返回打印数据
    $plan_id 计划id
    $encry 加密常量
    $data 待处理的数据
    */
    private function re_print($plan_id,$encry,$data){
        $code = \Libs\Service\Encry::encryption($plan_id,$data['order_sn'],$encry,$data['area'],$data['seat'],$data['print'],$data['id']);
        $sn = $code."^#";
        $info = array('qrcode' => $sn,'plan' => $plan_id, 'seat' => $data['seat'],'sid' => $data['id'],'status'=>'1','area'=>$data['area']);
        return $info;
    }
    //接收反传递回来的数据 并更新座位表
    function post_seat_data(){
        $pinfo = I('post.');
        $info = unserialize($pinfo['data']);
        $appInfo = Api::check_app($pinfo['appid'],$pinfo['appkey']);
        if($appInfo == false){
            $return = array('code' => 401,'info' => $pinfo['data'],'msg' => '认证失败');
            $this->recordLogindetect($pinfo['appid'],9,$return['code'],$return,$pinfo);
            echo json_encode($return);
            return false;
        }else{
            $plan = F('Plan_'.$pinfo['id']);
           // $seat = unserialize($pinfo['data']);
            if(!empty($seat)){
                foreach ($seat as $value) {
                    $data = M(ucwords($plan['seat_table']))->where(array('id'=>$value['sid'],'seat'=>$value['seat']))->setField(array('status'=>'99','checktime'=>$value['uptime']));
                }
            }
            $return = array(
                'code'  => 200,
                'info'  => $info,
                'msg'   => 'ok',
            );
            echo json_encode($return);
            return true;
        }
    }
}