<?php
// +----------------------------------------------------------------------
// | LubTMP  充值支付
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use Libs\Service\Operate;
use Home\Service\Partner;
use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Config;
use Payment\Client\Query;
class PaymentController extends Base{

	public function index()
	{	

		$uinfo = Partner::getInstance()->getInfo();
    	$crm =  D('Crm')->where(['id'=>$uinfo['cid']])->field('id,cash,name')->find();
    	$this->assign('crm',$crm)->display('create');
	}
	//创建页面
	public function create()
	{
		if(IS_POST){
			try {
                $pinfo = I('post.');
                $money = $pinfo['money'];
              
                if (bccomp($money, 500) === -1) {
                    //$this->error('支付金额不能低于 500 元',U('home/pay/to_up_pay'));
                    $return = [
				    	'status'	=>	300,
				    	'msg'		=>	'单次充值金额不能低于 500 元'
				    ];
				    die(json_encode($return));
                }
                $uinfo = Partner::getInstance()->getInfo();

                $crm =  D('Crm')->where(['id'=>$uinfo['cid']])->field('id,cash,name')->find();
                if((int)$pinfo['type'] === 5){
                	//微信支付
                }
                $wxConfig = load_payment('wx_qr',$uinfo['item_id']);
                $sn = get_order_sn($crm['id']);
                // 订单信息
				$payData = [
				    'body'    => itemName($uinfo['item_id'], 1).'门票款',
				    'subject' => itemName($uinfo['item_id'], 1).'门票款',
				    'order_no'    => $sn,
				    'timeout_express' => time() + 600,// 表示必须 600s 内付款
				    'amount'    => $money,
				    'return_param' => '',
				    'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
				    'product_id' => $uinfo['item_id'],

				    // 如果是服务商，请提供以下参数
				    'sub_appid' => $wxConfig['sub_appid'],//微信分配的子商户公众账号ID
				    'sub_mch_id' => $wxConfig['sub_mch_id'],// 微信支付分配的子商户号
				];
				try {
				    $ret = Charge::run('wx_qr', $wxConfig, $payData);
				    if($ret['result_code'] === 'SUCCESS' && $ret['return_code'] === 'SUCCESS'){
						$return = [
					    	'status'	=>	200,
					    	'qr'		=>	$ret['code_url'],
					    	'sn'		=>	$sn
					    ];
					    //记录充值日志
	                    $log = [
	                        'tyint'     => '1',
	                        'addsid'    => '2',
	                        'cash'      => $money,
	                        'order_sn'  => $sn,
	                        'crm_id'    => $crm['id'],
	                        'type'      => 1,
	                        'pay'       => '7',
	                        'balance'   => '',
	                        'remark'    => $pinfo['remark'],
	                        'user_id'   => get_user_id('id'),
	                    ];
	                    load_redis('setex','pay_'.$sn,json_encode($log),'14200');
					    die(json_encode($return));
				    }else{
				    	$return = [
					    	'status'	=>	300,
					    	'msg'		=>	$ret['return_msg']
					    ];
					    echo json_encode($return);
				    }
				} catch (PayException $e) {
					$return = [
				    	'status'	=>	300,
				    	'msg'		=>	$e->errorMessage()
				    ];
				    echo json_encode($return);
				    exit;
				}

            } catch (PayException $e) {
                echo $e->errorMessage();
                exit;
            }
		}else{
			$uinfo = Partner::getInstance()->getInfo();
        	$crm =  D('Crm')->where(['id'=>$uinfo['cid']])->field('id,cash,name')->find();
        	$this->assign('crm',$crm)->display();
		}
	}
	//微信支付
	public function wxpay()
	{
		
	}
	//支付宝支付
	public function alipay()
	{
		
	}
	public function query($sn)
	{
		$uinfo = Partner::getInstance()->getInfo();
		$config = load_payment('wx_qr',$uinfo['item_id']);
		$payData = [
			'out_trade_no'=>$sn,
			'sub_appid' => $config['sub_appid'],
			'sub_mch_id' => $config['sub_mch_id']
		];
        $ret = Query::run('wx_charge', $config, $payData);
        load_redis('setex','qr_paoy_'.$payData['out_trade_no'],json_encode($ret),'3600');

        if($ret['result_code'] === 'SUCCESS' && $ret['return_code'] === 'SUCCESS'){
            if(in_array(strtoupper($ret['trade_state']), ['REFUND','CLOSED','REVOKED','PAYERROR'])){
                $return = ['status'=>'400','msg'=>'支付超时,请重新提交...'];
            }
            if(in_array(strtoupper($ret['trade_state']), ['NOTPAY','USERPAYING'])){
                $return = ['status'=>'300','msg'=>'等待支付...'];
            }
            //当返回全部成功时,更新订单状态
            if(strtoupper($ret['trade_state']) == 'SUCCESS'){
            	if($ret['amount'] == $log['cash']){

            	}
                $log = json_decode(load_redis('get','pay_'.$sn), true);
                $payLog = [
                	'out_trade_no'	=>	$ret['transaction_id'],
                	'money'			=>  $log['cash'],
                	'order_sn'		=>	$sn,
                	'type'			=>	2,
                	'pattern'		=>  1,
                	'scene'			=>	2,
                	'user_id'		=>  get_user_id(),
                ];
                M('Pay')->add($payLog);
                $this->topUp($log);
                $return = ['status'=>'200','msg'=>'支付成功'];
            }
        }
        if($ret['is_success'] == 'F'){
            $return = ['status'=>'400','msg'=>$ret['error']];
        }
        die(json_encode($return));
	}
	public function topUp($log)
	{
		$model = new \Think\Model();
		$model->startTrans();
		//判断是企业还是个人1企业4个人
		$crmData = array('cash' => array('exp','cash+'.$log['cash']),'uptime' => time());

		$c_pay = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$log['crm_id']))->setField($crmData);

		//充值成功后，添加一条充值记录
		$data = array(
			'order_sn'  =>  $log['order_sn'],
			'cash'		=>	$log['cash'],
			'user_id'	=>	get_user_id(),
			'crm_id'	=>	$log['crm_id'],
			'createtime'=>	time(),
			'type'		=>	'1',
			'balance'	=>  balance($log['crm_id'],1),
			'tyint'		=>	1,//客户类型1企业4个人
			'remark'	=>	'自助充值'.$log['remark'],
		);		
		$recharge = $model->table(C('DB_PREFIX')."crm_recharge")->add($data);
		if($c_pay && $recharge){
			$model->commit();//成功则提交
			load_redis('delete', 'pay_'.$sn);
		}else{
			$model->rollback();//不成功，则回滚
			load_redis('lpush','pay_err',json_encode($log));
		}
	}
}