<?php
// +----------------------------------------------------------------------
// | LubTMP 自动提现
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
class AutoCash extends \Libs\System\Service {
	/**
     * 批量生成提现申请
     */
    function cach_all(){
        $datetime = date('Ymd',strtotime("-1 day"));
        $map = array(
            'status' => '1',
            'cash'  =>  array('neq','0'),
            'datetime' => $datetime,
        );
        //查询余额不为0的客户
        $list = M('User')->where($map)->field('id,nickname,cash')->select();
        if(!empty($list)){
            //构造提现数据
            foreach ($list as $key => $v) {
                if(AutoCash::check_cash($v['id'],$v['cash'],$datetime) == '200'){
                    $postData[] = array(
                        'sn'        => get_order_sn('6'),
                        'user_id'   => $v['id'],
                        'openid'    => AutoCash::get_openid($v['id']),
                        'datetime'  => $datetime,
                        'createtime'=> time(),
                        'uptime'    => time(),
                        'money'     => $v['cash'],
                        'remark'    => '系统自动创建',
                        'pay_type'  => '5',
                        'status'    => '3',//待审核
                    );
                }
            }
            //查询该客户是否存在原有未处理的提现申请。存在即作废
            //批量写入提现数据
            if(!empty($postData)){
                if(M('Cash')->addAll($postData)){
                    //重置用户余额
                    foreach ($postData as $k => $va) {
                        AutoCash::up_user_cash($va['user_id'],$va['money']);
                    }
                    return '200';
                }else{
                    error_insert("400078");
                    return false;
                }
            }else{
                return true;
            }
        }
    }
    /**
     * 查询是否存在未处理的提现申请
     * @param $userid 角色id
     * 存在即作废
     */
    function check_cash($userid,$cash,$datetime){
        $map = array('user_id'=>$userid,'status'=>'3','datetime'=>$datetime);
        $count = M('Cash')->where($map)->getField('id');
        if(empty($count)){
            return '200';
        }else{
           // return AutoCash::invalid($userid,$count,$cash);
           return '400';
        }
    }
    /**
     * 获取用户的openid
     * @param $id int 用户id
     * @return [type] [description]
     */
    function get_openid($id){
        if(empty($id)){
            return false;
        }
        $openid = M('WxMember')->where(array('user_id'=>$id))->getField('openid');
        return $openid;
    }
    /**
     * 更新操作
     * @param $string 要作废的数据集ID
     */
    function invalid($userid,$cash_id,$cash){
        $map['id'] = $cash_id;
        $map['status'] = '3';
        $status = M('Cash')->where($map)->save(array('money'=>array('exp','money+'.$cash),'createtime'=>time()));
        if($status){
            AutoCash::up_user_cash($userid,$cash);
            return '300';
        }else{
            //记录错误集合
            //且跳过
            error_insert('409'.$id);
            return '300';
        }
    }
    /**
     * 更新客户可用余额
     */
    function up_user_cash($userid,$cash){
        if(!empty($userid)){
            $up = D('User')->where(array('id'=>$userid))->setDec('cash',$cash);
            return '200';
        }else{
            error_insert('408'.$userid);
            return '300';
        }
    }
}