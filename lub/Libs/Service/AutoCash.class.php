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
                    //主包日志
                    $mainData[] = [
                        'sn'        => get_order_sn('6'),
                        'user_id'   => $data['id'],
                        'datetime'  => $datetime,
                        'createtime'=> time(),
                        'uptime'    => time(),
                        'money'     => $data['cash'],
                        'remark'    => '系统自动创建',
                        'pay_type'  => '5',
                        'status'    => '6',//分包中
                    ];
                }
            }
            //批量写入提现数据$mainData
            if(!empty($mainData)){
                $cash = D('cash')->addAll($mainData);
                if($cash){
                    //重置用户余额
                    foreach ($mainData as $k => $va) {
                        AutoCash::up_user_cash($va['user_id'],$va['money']);
                    }
                    $red = D('cash')->where(['status'=>6])->field('id,sn,user_id,money')->select();
                    foreach ($red as $ka => $ve) {
                       //判断是否需要分包
                        $pack = AutoCash::create_pack($ve);
                        $redData = empty($redData) ? $pack : array_merge($redData,$pack); 
                    }
                    $red = $model->table(C('DB_PREFIX').'cash_log')->addAll($redData);
                    if($red){
                        return '200';
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }
    }
    //构建返利包
    public function create_pack($data)
    {   
        $num = 0;
        if($data['cash'] > 495){
            $num = (int)floor($data['cash']/495);
            $mod = $this->Kmod($data['cash'],495);
            $redArr[] = array_merge([$mod],str_split(str_repeat(495, $num),3));
            foreach ($redArr as $k => $v) {
               $return[] = AutoCash::create_data($data, $v, $k);
            }
        }else{
            $return[] = AutoCash::create_data($data); 
        }
        return $return;
    }
    //取余
    function Kmod($bn, $sn)
    {
        //fmod() 函数返回除法的浮点数余数
        return intval(fmod(floatval($bn), $sn));
    }
    static function create_data($data, $cash = 0, $k)
    {
        $datetime = date('Y-m-d H:i:s');
        $return = array(
            'no'        => $data['sn'].$k,
            'cash_id'   => $data['id'],
            'wx_no'     => '0',
            'open_id'   => AutoCash::get_openid($data['user_id']),
            'issuetime' => $datetime,
            'updatetime'=> $datetime,
            'money'     => $cash > 0 ? $cash : $data['money'],
            'remark'    => '系统自动创建',
            'status'    => '3',//待审核
        );
        return $return;
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