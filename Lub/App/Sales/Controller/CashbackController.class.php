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
        if(!empty($sn)){$map['sn'] =  array('like','%'.$sn.'%');}
        if(!empty($status)){
            $map['status'] = $status;
        }
        if(!empty($user_id)){$map['user_id'] = $user_id;}
        if(empty($user_id) && empty($status) && empty($sn)){
            $map['status'] = 3;
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
                if(empty($itemCof)){$this->erun("商户配置信息获取失败,请重新登录...");}//dump($itemCof);
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
                    /*注意200的限额,根据模板设置金额
                    if($info['money'] > 200){
                        //大于200拆分多个红包
                        $redNum = 1;
                        $redInfo[] = [
                            'money' => $info['money']%200,
                            'sn'    => $info['sn'],
                            'openid'=> $info['openid'],
                        ];
                        $num = (int)floor($info['money']/200);
                        for ($i = $num; $i <> 0; $i--) {
                            $redInfo[] = [
                                'money' => '200',
                                'sn'    => $info['sn'].'-'.$redNum,
                                'openid'=> $info['openid'],
                            ];
                            $redNum += 1;
                        }//dump($redInfo);
                        //构建红包基础数据,并发送红包
                        foreach ($redInfo as $k => $v) {
                            $postData = $this->pay_red($v,$itemCof);
                        }
                    }else{
                        $postData = $this->pay_red($info,$itemCof);
                    }*/
                    $postData = $this->pay_red($info,$itemCof);
                    $this->pay_red_susess($info);
                    $this->srun("红包创建成功,等待领取...",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
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
        //若红包存在子订单则列出所有子订单
        $this->assign('data',$info)->display();
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
    //微信红包返款
    function pay_red($info,$itemCof){
        //读取红包模板
        //dump($itemCof);
        $redTpl = D('RedTpl')->where(['id'=>$itemCof['red_tpl']])->field('create_time,user_id,id,status',true)->find();
        
        if(empty($redTpl)){
            $this->erun('未找到红包模板,请设置');
        }
        $config = load_payment('wx_red');
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

        //dump($config);
        try {
            $ret = Red::run('wx_red', $config, $postData);
        } catch (PayException $e) {
            error_insert($e->errorMessage());
            $this->erun("ERROR:".$e->errorMessage());
            exit;
        }
        return $ret;
        /*
        $wishing = '感谢参与'.$product['name'].'利润分享计划！';
        $actname = "利润分享计划";
        $remark = '感谢参与'.$product['name'].'利润分享计划！';
        $scene_id = 'PRODUCT_6';
        $pay = & load_wechat('Pay',$this->pid,1);
        // 调用方法
        $result = $pay->sendRedPack($info['openid'], $info['money'], $info['sn'], $product['name'], $wishing, $actname, $remark,'1',$scene_id);
        if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
            return true;
        }else{
            $pay->errMsg;
            return false;
        }*/
        //return $postData;
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
    */
    function pay_red_susess($data)
    {
        //改变订单状态 微信红包默认待领取
        $s1 = M('Cash')->where(array('sn'=>$data["sn"]))->setField('status',5);
        //写入查询队列  支付日志
        payLog();
        //轮询支付日志红包部分
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