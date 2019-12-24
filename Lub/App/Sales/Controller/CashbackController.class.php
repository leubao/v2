<?php
// +----------------------------------------------------------------------
// | LubTMP 全员/三级销售  佣金管理
// | 商户支付方式1打卡2支付宝转账3财务取现4微信企业转账5微信红包
// |状态1提现成功3待审核4驳回5微信红包待领取
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Sales\Controller;
use Common\Controller\ManageBase;
use Payment\Common\PayException;
use Payment\Client\Transfer;
use Payment\Client\Red;
use Payment\Client\Query;
class CashbackController extends ManageBase{
	protected function _initialize() {
        parent::_initialize();
    }
    //提现列表
    function index(){
        $sn = I('sn');
        $user_id = I('user_id');
        $status = I('status');
        $starttime = I('starttime');
        $endtime = I('endtime') ? I('endtime') : date('Y-m-d',time());
        $this->assign('starttime',$starttime)
            ->assign('endtime',$endtime);
        if(!empty($sn)){$map['sn'] =  array('like','%'.$sn.'%');}
        if(!empty($status)){
            $map['status'] = $status;
        }
        if (!empty($starttime) && !empty($endtime)) {
            $starttime = strtotime($starttime);
            $endtime = strtotime($endtime) + 86399;
            $map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
        }else{
            //默认显示当天的订单
            $starttime = strtotime(date("Ymd"));
            $endtime = $starttime + 86399;
            $map['createtime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
        }
        if(!empty($user_id)){$map['user_id'] = $user_id;}
        if(empty($user_id) && empty($status) && empty($sn)){
            //$map['status'] = 3;
        }
		$this->basePage('Cash',$map,array('id'=>'DESC'));
		$this->assign('ginfo',$ginfo)->assign('status',$status)->display();
    }
    //提现审核
    function back(){
        $db = M('Cash');
        if(IS_POST){
            //保存remak
            $pinfo = I('post.');
            //发起支付
            $info = $db->where(array('id'=>$pinfo['id'],'status'=>3))->find();
            if(!empty($info)){
                $db->where(array('id'=>$pinfo['id']))->save(array('win_remark'=>$pinfo['remark'],'userid'=>get_user_id()));
                $itemCof = get_item_conf('1');
                if(empty($itemCof)){$this->erun("商户配置信息获取失败,请重新登录...");}
                //dump($itemCof);
                
                //微信企业付款
                if($itemCof['rebate_pay'] == '1'){
                    $postData = $this->pay_transfer($info,$product);
                    /*发起支付*/
                    $config = load_payment('wx_transfer');
                    try {
                        $return = Transfer::run('wx_transfer', $config, $postData);
                    } catch (PayException $e) {
                        load_redis('set','fkhs',serialize($e));
                        $this->erun("ERROR:".$e->errorMessage().$return['err_code']);
                        exit;
                    }
                    if($return['return_code'] == 'SUCCESS' && $return['result_code'] == 'SUCCESS'){
                        //交易成功,写入支付日志改变订单状态
                        $this->pay_suess($return,$info['money']);
                        $this->srun("支付成功...",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
                    }else{
                        error_insert($return['err_code']);
                        $this->erun("ERROR:".$return['return_msg'].$return['err_code'].$return['err_code_des']);
                    }
                }
                //微信普通红包
                if($itemCof['rebate_pay'] == '2'){
                    /*注意200的限额,根据模板设置金额*/
                    $state = true;
                    if($info['money'] > 1000){
                        //大于200拆分多个红包
                        $redNum = 1;
                        $redInfo = [];
                        $redInfo[] = [
                            'money' => $info['money']%1000,
                            'sn'    => $info['sn'],
                            'openid'=> $info['openid'],
                        ];
                        $num = (int)floor($info['money']/1000);//dump($num);
                        for ($i = $num; $i <> 0; $i--) {
                            $redInfo[] = [
                                'money' => '1000',
                                'sn'    => $info['sn'].'I'.$redNum,
                                'openid'=> $info['openid'],
                            ];
                            $redNum += 1;
                        }
                        //构建红包基础数据,并发送红包
                        foreach ($redInfo as $k => $v) {
                            if((int)$v['money'] > 0){
                                $ret = $this->pay_red($v,$itemCof,$info['sn']);
                                if($ret['return_code'] != 'SUCCESS' && $ret['result_code'] != 'SUCCESS'){
                                    $state = false;
                                }
                            }
                        }
                    }else{
                        $ret = $this->pay_red($info,$itemCof,$info['sn']);
                    }
                    
                    // $ret = $this->check_back($info['sn'],2);dump($ret);
                    // if($ret['is_success'] === 'T'){
                    //     $ret['response']['reason']
                    // }
                    // $ret = $this->pay_red($info, $itemCof, $info['sn']);
                    if($ret['return_code'] === 'SUCCESS' && $ret['result_code'] === 'SUCCESS' && $state){
                        $this->srun($ret['err_code_des'],array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
                    }else{
                        $this->erun($ret['err_code_des']);
                    }
                }
            }else{
                $this->erun("交易状态不允许此项操作");
            }
        }else{
            $ginfo = I('get.');
            $info = $db->where(array('id'=>$ginfo['id']))->find();
            $this->assign('data',$info)->display();
        }
    }
    //微信返款查询是否已返利
    function check_back($sn,$type){
        //微信企业付款
        if((int)$type === 1){
            $data = [
                'trans_no' => $sn,
            ];
            $config = load_payment('wx_transfer');
            try {
                $ret = Query::run('wx_transfer', $config, $data);
            } catch (PayException $e) {
                error_insert($e->errorMessage());
                $this->erun("ERROR:".$e->errorMessage());
                exit;
            }
        }
        //微信红包发送
        if((int)$type === 2){
            $config = load_payment('wx_red');
            $data = [
                'mch_billno' =>  $sn,
                'bill_type'  =>  'MCHT',
                'sub_appid'  =>  $config['sub_appid'],
                'sub_mch_id' =>  $config['sub_mch_id']
            ];
            $ret = Query::run('wx_red', $config, $data);
            try {
                $ret = Query::run('wx_red', $config, $data);
                return $ret;
            } catch (PayException $e) {
                error_insert($e->errorMessage());
                $this->erun("ERROR:".$e->errorMessage());
                exit;
            } 
        }
        return $ret;
    }
    public function public_check_backe()
    {
        $sn = I('sn');
        $config = load_payment('wx_red');
        $data = [
            'mch_billno' => $sn,
            'bill_type'  => 'MCHT',
            'sub_appid'  =>  $config['sub_appid'],
            'sub_mch_id' =>  $config['sub_mch_id']
        ];
        $ret = Query::run('wx_red', $config, $data);
        try {
            $ret = Query::run('wx_red', $config, $data);
            if($ret['return_code'] === 'SUCCESS' && $ret['result_code'] === 'SUCCESS'){
                //发放成功，已领取
                if($ret['status'] === 'RECEIVED'){
                    M('Cash')->where(array('sn'=>$ret["mch_billno"]))->setField(['status'=>1,'uptime'=>time()]);
                   // $this->srun('发放成功,已领取');
                }
                //已退款或发放失败
                if(in_array($ret['status'],['FAILED','REFUND'])){
                    //更新新的单号 remark
                    $info = M('Cash')->where(['sn'=>$ret["mch_billno"]])->field('id,remark')->find();
                    $up = [
                        'status'    =>  3,
                        'uptime'    =>  time(),
                        'sn'        =>  get_order_sn($info['id']),
                        'remark'    =>  $info['remark'].'历史单号:'.$ret["mch_billno"]
                    ];
                    M('Cash')->where(array('id'=>$info['id']))->setField($up);
                   // $this->erun('发放失败,请重新发放...');
                }
                //未领取  或者发放中  都继续写入查询中
                if(in_array($ret['status'], ['SENDING','SENT','RFUND_ING'])){
                    load_redis('lpush','red_list',$ret["mch_billno"]);
                    
                   // $this->srun('发放成功,待领取...');
                }

            }else{
               // $this->erun("ERROR:".$ret['err_code_des'].$ret['return_msg']);
            }
            $this->assign('data',$ret);
            $this->display();
        } catch (PayException $e) {
            error_insert($e->errorMessage());
            $this->erun("ERROR:".$e->errorMessage());
            exit;
        }
    }
    /**
     * 发放补贴
     */
    function subsidies(){
        if(IS_POST){
            $pinfo = I('post.');
            //构造写入数据
            $postData = array(
                'sn' => $pinfo['sn'],
                'user_id' => $pinfo['uid'],
                'openid'  => '',
                'userid'  => get_user_id(),
                'createtime'=>  time(),
                'uptime'    => time(),
                'money' =>  $pinfo['money'],
                'remark'=>  $pinfo['remark'],
                'pay_type'=> $pinfo['pay_type'],
                'status'=>'1',
            );
            if(M('Cash')->add($postData)){
                $return = array('statusCode' => 200,'url'=>$url); 
            }else{
                $return = array('statusCode' => 300); 
            }
            die(json_encode($return));
        }else{
            $ginfo = I('get.');
            if(empty($ginfo['id'])){
                $this->erun("参数错误,请在客户管理中选择客户执行此项操作");
            }
            $info = D('Item/User')->where(array('id'=>$ginfo['id']))->field(array('password','username','verify'),true)->find();
            $this->assign('ginfo',$ginfo)
                ->assign('sn',get_order_sn())
                ->assign('data',$info)->display();
        }
    }
    //提现订单详情
    function public_cashinfo(){
        $ginfo = I('get.');
        if(empty($ginfo['sn'])){
            $this->erun("参数错误...");
        }
        $info = M('Cash')->where(array('sn'=>$ginfo['sn']))->find();
        //若红包存在子订单则列出所有子订单
        $redlist = D('Pay')->where([''=>$ginfo['sn']])->select();
        $this->assign('list',$redlist)->assign('data',$info)->display();
    }
    //微信企业付款
    function pay_transfer($info,$product){
        $postData = [
            'trans_no' => $info['sn'],
            'openid' => $info['openid'],
            'check_name' => 'NO_CHECK',// NO_CHECK：不校验真实姓名  FORCE_CHECK：强校验真实姓名   OPTION_CHECK：针对已实名认证的用户才校验真实姓名
            //'payer_real_name' => '',
            'amount' => $info['money'],
            'desc' => $product['name'].'利润分享计划!',
            'spbill_create_ip' => get_client_ip(),
            
        ];
        return $postData;
    }
    /**
     * @Company  承德乐游宝软件开发有限公司
     * @Author   zhoujing      <zhoujing@leubao.com>
     * @DateTime 2018-04-13
     * @param    array        $info                发送数据包
     * @param    array        $itemCof             配置数据包
     * @param    string       $sn                  订单号
     * @return   
     */
    function pay_red($info,$itemCof,$sn){
        //读取红包模板
        //dump($itemCof);
        $redTpl = D('RedTpl')->where(['id'=>$itemCof['red_tpl']])->field('create_time,user_id,id,status',true)->find();
        
        if(empty($redTpl)){
            $this->erun('未找到红包模板,请设置');
        }
        $config = load_payment('wx_red');
        //先查询是否已经返利
        
        $postData = [
            'mch_billno'        =>  $info['sn'],//商户订单号
            'send_name'         =>  $redTpl['send_name'],//商户名称
            're_openid'         =>  $info['openid'],//用户openid
            'total_amount'      =>  $info['money'],//付款金额
            'total_num'         =>  '1',//红包发放总人数
            'wishing'           =>  $redTpl['wishing'], //'感谢参与'.$product['name'].'利润分享计划！',//红包祝福语
            'client_ip'         =>  get_client_ip(),//Ip地址
            'act_name'          =>  $redTpl['act_name'],//"利润分享计划",//活动名称
            'remark'            =>  $redTpl['remark'],//'感谢参与'.$product['name'].'利润分享计划！',//备注
            'scene_id'          =>  $redTpl['scene_id'],
            'sub_appid'         =>  $config['sub_appid'],
            'sub_mch_id'        =>  $config['sub_mch_id']
        ];

        try {
            $ret = Red::run('wx_red', $config, $postData);
            if($ret['return_code'] === 'SUCCESS' && $ret['result_code'] === 'SUCCESS'){
               $this->pay_red_susess($ret,$sn); 
            }else{
               //记录错误日志 
            }
        } catch (PayException $e) {
            //error_insert($e->errorMessage());
            $this->erun("ERROR:".$e->errorMessage());
            exit;
        }
        return $ret;
    }

    /*企业付款模式支付成功*/   
    /**
     * ["return_code"] => string(7) "SUCCESS"
        ["return_msg"] => array(0) {
        }
      ["nonce_str"] => string(32) "v5qcs3fshmsfwco8ycmcy7l7mp7y0ako"
      ["result_code"] => string(7) "SUCCESS"
      ["partner_trade_no"] => string(12) "703172665590"
      ["payment_no"] => string(28) "1000018301201703196752314542"
      ["payment_time"] => string(19) "2017-03-19 00:18:42"
    */     
    function pay_suess($data,$money){
        //改变订单状态
        $s1 = M('Cash')->where(array('sn'=>$data["partner_trade_no"]))->setField('status',1);
        //记录微信支付
        $pay_log = array(
            'out_trade_no' =>   $data['payment_no'], //微信支付单号
            'money'        =>   $money,
            'order_sn'     =>   $data["partner_trade_no"],
            'param'        =>   serialize($data),
            'status'       =>   '1',
            'type'         =>   '2',
            'pattern'      =>   '2',
            'create_time'  =>   time(), 
            'update_time'  =>   strtotime($data['payment_time']),
        );
        $s2 = M('Pay')->add($pay_log);
        if(!$s1 || !$s2){
            error_insert('400026');
        }
        return true;
    }
    /*红包的发送成功
    * 红包的状态
    * @param array 红包数据
    */
    function pay_red_susess($data,$sn = '')
    {
        $map = [
            'rebate_sn'     =>  $sn,
            'red_sn'        =>  $data["mch_billno"]
        ];
        $db = D('RedList');
        $status = $db->where($map)->field('id')->find();
        if(!$status){
            //改变订单状态 微信红包默认待领取
            $s1 = M('Cash')->where(array('sn'=>$sn))->setField(['status'=>5,'uptime'=>time()]);
            //写入查询队列  支付日志
            load_redis('lpush','red_list',$data["mch_billno"]);
            $pay_log = [
                'rebate_sn'     =>  $sn,//提现单号
                'red_sn'        =>  $data["mch_billno"],//发送红包的单号
                'money'         =>  $data['total_amount']/100,
                're_openid'     =>  $data['re_openid'],//领取人
                'status'        =>  '1',
                'type'          =>  '2',//微信普通红包
                'out_trade_no'  =>  $data['send_listid'], //微信支付
                'is_scene'      =>  '1',//窗口
                'user_id'       =>  get_user_id(),
                'create_time'   =>  time(), 
                'update_time'   =>  time(),
            ];
            $s2 = $db->add($pay_log);
            if(!$s1 || !$s2){
                error_insert('400026');
            }
        }
        
        return true;
    }
    /**
     * 红包领取超时 重发
     * 更换订单号 作废原有记录 并关联关系和备注
     * @return [type] [description]
     */
    function resetred(){
        $ginfo = I('get');
        //判断记录状态，读取记录内容
        $map = [
            'id'      =>    $ginfo['id'],
            'status'  =>    '',
        ];
        $model = D('Manage/Pay');
        $info = $model->where($map)->find();
        if(!empty($info)){
            $param = unserialize($info['param']);
            //组装红包数据
            
            //发送红包申请
            //记录红包日志
        }else{
            $this->erun("记录查询失败");
        }
    }
}