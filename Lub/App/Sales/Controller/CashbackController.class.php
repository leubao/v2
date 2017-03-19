<?php
// +----------------------------------------------------------------------
// | LubTMP 全员/三级销售  佣金管理
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Sales\Controller;
use Common\Controller\ManageBase;
use Payment\Common\PayException;
use Payment\Client\Transfer;
use Payment\Client\Query;
class CashbackController extends ManageBase{
	protected function _initialize() {
        parent::_initialize();
    }
    //提现列表
    function index(){
        $ginfo = I('get.');
        if(!empty($ginfo['id'])){
            $map['user_id'] = $ginfo['id'];
        }
		$this->basePage('Cash',$map,array('id'=>'DESC'));
		$this->assign('ginfo',$ginfo)->display();
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
                $product = get_product('info');
                $data =  [
                    'trans_no' => $info['sn'],
                    'openid' => $info['openid'],
                    'check_name' => 'NO_CHECK',// NO_CHECK：不校验真实姓名  FORCE_CHECK：强校验真实姓名   OPTION_CHECK：针对已实名认证的用户才校验真实姓名
                    'payer_real_name' => '',
                    'amount' => $info['money'],
                    'desc' => $product['name'].'利润分享计划!',
                    'spbill_create_ip' => get_client_ip(),
                ];
                //发起支付
                $config = load_payment('wx_transfer',$product['id']);
                try {
                    $return = Transfer::run('wx_transfer', $config, $data);
                } catch (PayException $e) {
                    //load_redis('set','fkhs',$e->errorMessage());
                    $this->erun("ERROR:".$e->errorMessage());
                    exit;
                }
                if($return['return_code'] == 'SUCCESS' && $return['result_code'] == 'SUCCESS'){
                    //交易成功
                    //写入支付日志改变订单状态
                    $this->pay_suess($return,$info['money']);
                    /*发送模板消息和短信*/
                    $this->srun("支付成功...",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
                }else{
                    error_insert($return['err_code']);
                    $this->erun("ERROR:".$return['return_msg'].$return['err_code'].$return['err_code_des']);
                }
            }else{
                $this->erun("交易状态不允许此项操作");
            }
        }else{
            $ginfo = I('get.');load_redis('set','fkhs',$ginfo['id']);
            $info = $db->where(array('id'=>$ginfo['id']))->find();
            $this->assign('data',$info)->display();
        }
    }
    //微信返款查询是否已返利
    function check_back($sn){
        $data = [
            'trans_no' => $sn,
        ];
        $product_id = get_product('id');
        $config = load_payment('wx_transfer',$product_id);
        try {
            $ret = Query::run('wx_transfer', $config, $data);
        } catch (PayException $e) {
            error_insert($e->errorMessage());
            $this->erun("ERROR:".$e->errorMessage());
            exit;
        }
        return $ret;
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
        $this->assign('data',$info)->display();
    }
    /*支付成功*/   
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
}