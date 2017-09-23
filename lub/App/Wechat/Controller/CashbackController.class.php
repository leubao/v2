<?php
// +----------------------------------------------------------------------
// | LubTMP 全员销售  佣金管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19 
// +----------------------------------------------------------------------
namespace Wechat\Controller;
use Common\Controller\ManageBase;
use Wechat\Service\Wechat;
use WeChat\Service\Wxpay;
use Wechat\Service\Api;
//微信企业付款
use Wechat\Service\Wxpay\WxPayment;
use Wechat\Service\Wxpay\WxPayApi;

use Wechat\Service\Utils\SHA1;
class CashbackController extends ManageBase{
	protected function _initialize() {
        parent::_initialize();
        /*
        $proconf = cache('ProConfig');
       // $proconf = $proconfs['41'];
        // 开发者中心-配置项-AppID(应用ID)
        $this->appId = $proconf['appid'];
        //受理商ID，身份标识
        $this->mchid = $proconf['mchid'];
        //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
        $this->mchkey = $proconf['mchkey'];*/
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

    /**
     * 审核处理 提现
     * https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers
     *  "mch_appid":"wxe062425f740c30d8",
        "mchid":"10000098",
        "nonce_str":"3PG2J4ILTKCH16CQ2502SI8ZNMTM67VS",
        "partner_trade_no":"100000982014120919616",
        "openid":"ohO4Gt7wVPxIT1A9GjFaMYMiZY1s",
        "check_name":"OPTION_CHECK",
        "re_user_name":"张三",
        "amount":"100",
        "desc":"节日快乐!",
        "spbill_create_ip":"10.2.3.10",
        "sign":"C97BDBACF37622775366F38B629F45E3"
     */
    function back(){
        $db = M('Cash');
    	if(IS_POST){
    		//保存remak
    		$pinfo = I('post.');
    		//发起支付
    		$info = $db->where(array('id'=>$pinfo['id'],'status'=>3))->find();
    		if(!empty($info)){
    			$db->where(array('id'=>$pinfo['id']))->save(array('win_remark'=>$pinfo['remark'],'userid'=>get_user_id()));
    			$return = $this->get_pay_weixin($info);
    			if($return['return_code'] == 'SUCCESS' && $return['result_code'] == 'SUCCESS'){
    				/*if(){
    					
    				}else{
    					$this->erun("ERROR:".$return['err_code'].$return['err_code_des']);
    				}*/
                    //交易成功
                    //写入支付日志改变订单状态
                    $this->pay_suess($return,$info['money']);
                    /*发送模板消息和短信
                    $tomsg =  array(
                        'sn' => $info['sn'],
                        'phone' => get_phone($info['user_id']),
                        'money' => $info['money'],
                        );
                    \Libs\Service\Sms::order_msg($tomsg,9);*/
                    $this->srun("支付成功...",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
    			}else{
                    error_insert($return['err_code']);
    				$this->erun("ERROR:".$return['return_msg'].$return['err_code'].$return['err_code_des']);
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
    //发送红包
    public function package() {
    //  include_once ('./WeixinRedPacket/WxHongBaoHelper.php');
        // //测试的OpenID
        $re_openid = "oaWZ5s1kjrtJ7RdYcmALZg8QSwpk";
        // //红包金额单位是分所以得乘以100
        $price = 50 * 100;
        
        //组装数据
        $wxHongBaoHelper = new WxHb();
        $wxHongBaoHelper->setParameter ( "nonce_str", $this->great_rand () ); //随机字符串，丌长于 32 位
        $wxHongBaoHelper->setParameter ( "mch_billno", $this->app_mchid . date ( 'YmdHis' ) . rand ( 1000, 9999 ) ); //订单号
        $wxHongBaoHelper->setParameter ( "mch_id", $this->app_mchid ); //商户号
        $wxHongBaoHelper->setParameter ( "wxappid", $this->app_id );
        $wxHongBaoHelper->setParameter ( "send_name", '今日重庆' ); //红包发送者名称
        $wxHongBaoHelper->setParameter ( "re_openid", $re_openid ); //相对于医脉互通的openid
        $wxHongBaoHelper->setParameter ( "total_amount", $price ); //付款金额，单位分
        $wxHongBaoHelper->setParameter ( "total_num", 1 ); //红包収放总人数
        $wxHongBaoHelper->setParameter ( "wishing", '猴年吉祥' ); //红包祝福语
        $wxHongBaoHelper->setParameter ( "client_ip", '219.153.65.50' ); //调用接口的机器 Ip 地址
        $wxHongBaoHelper->setParameter ( "act_name", '重报集团' ); //活劢名称
        $wxHongBaoHelper->setParameter ( "remark", '新年快乐！' ); //备注信息
        //生成xml并且生成签名
        $postXml = $wxHongBaoHelper->create_hongbao_xml ( $this->api_key );
        //var_dump ( $postXml );
        //exit;
        //提交请求
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $responseXml = $wxHongBaoHelper->curl_post_ssl ( $url, $postXml );
        $responseObj = simplexml_load_string ( $responseXml, 'SimpleXMLElement', LIBXML_NOCDATA );
        //转换成数组
        $responseArr = ( array ) $responseObj;
        
        $return_code = $responseArr ['return_code'];
        $result_code = $responseArr ['result_code'];
        //判断是否红包是否发送成功
        if ($return_code == "SUCCESS" && $result_code == "SUCCESS") {
            //dump ( $responseArr );
            echo "SUCCESS";
        } else {
            echo "发送失败";
           // dump ( $responseArr );
        }
    
    }
    /*发起支付*/
    function get_pay_weixin($info){
        $money = $info['money']*100;
    	//发起支付
        $cp = new WxPayment;
        $cp->setReOpenid($info['openid']);
        $cp->setMchid($this->mchid);
        $cp->setMchAppid($this->appId);//这些都写死在class里面
        $cp->setApiKey($this->mchkey);
        $cp->setTotalAmount($money);
        $cp->setRemark('《梦里老家》利润分享计划!');
        //$cp->setActName('活动名称');
        $cp->setCheckName('NO_CHECK');
        $cp->setPartnerTradeNo($info['sn']);
        $data = $cp->ComPay();//dump($data);
        if($data){
           return $data;
        }else{
            return $cp->error();
        }
    	
    }
	/*支付成功*/        
	function pay_suess($data,$money){
		//改变订单状态
		$s1 = M('Cash')->where(array('sn'=>$data["partner_trade_no"]))->setField('status',1);
		//记录微信支付
        $pay_log = array(
        	'out_trade_no' =>	$data['payment_no'], //微信支付单号
        	'money'		   =>	$money,
        	'order_sn'	   =>	$data["partner_trade_no"],
        	'param'		   =>	serialize($data),
        	'status'	   =>	'1',
        	'type'		   =>	'2',
        	'pattern'	   =>   '2',
        	'create_time'  =>	time(), 
        	'update_time'  =>	strtotime($data['payment_time']),
        	);
        $s2 = M('Pay')->add($pay_log);
        if(!$s1 || !$s2){
        	error_insert('400026');
        }
        return true;
	}
    //生成微信提现记录
    function cashback(){
        //查询客户分组为个人的分组
        //拉取个人账户有余额 全员分享计划+余额不为0
        $list = M('User')->where()->relation(true)->select();
        
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
}