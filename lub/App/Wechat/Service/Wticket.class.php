<?php
/**
 * Wticket.php
 * 
 * 票务业务逻辑
 */
namespace Wechat\Service;
class Wticket {
	/*获取销售计划*/
    function getplan(){
        $product = M('Product')->where(array('status'=>1))->field('id')->select();
        $info['product'] = arr2string($product,'id');
        //根据当前用户判断   若为散客  读取配置项  微信的价格政策
        $user = session('user');
        if($user['user']['channel'] == '1'){
            //渠道
            $info['group']['price_group'] = $user['user']['pricegroup'];
        }else{
            //散客
            $info['group']['price_group'] = '4';
        }
        $info['scene'] = '4';
        $plan = \Libs\Service\Api::plans($info);
        foreach ($plan['plan'] as $key => $value) {
            $plans['plan'][] = array(
                'title' =>  $value['title'],
                'id'    =>  $value['id'],
                'num'   =>  $value['num'],
            );
            $plans['area'][$value['id']] = $value['param'];
        }
        return $plans;
    }
    //写入微信用户 从微信服务端拉取用户
    function add_wx_user($data,$promote){
        if(!empty($data)){
            //判断用户是否存在
            $db = M('WxMember');
            $uinfo = $db->where(array('openid'=>$data->openid))->find();
            if($uinfo){
                if($uinfo['user_id']){
                    return '2';
                }else{
                    return '1';
                }
            }else{
                $datas = array(
                    'openid'    =>  $data->openid,
                    'unionid'   =>  $data->unionid,
                    'sex'       =>  $data->sex,
                    'city'      =>  $data->city,
                    'province'  =>  $data->province,
                    'nickname'  =>  $data->nickname,
                    'promote'   =>  $promote,
                );
                $db->add($datas);
                
                return '1';
            }
        }else{
            //获取用户信息失败
            return false;
        }
    }
}