<?php
// +----------------------------------------------------------------------
// | LubTMP 微信前台
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2015-8-25 
// +----------------------------------------------------------------------
namespace Wechat\Controller;
use Common\Controller\LubTMP;
class ApplyController extends LubTMP {
	
	public function apply()
	{
		$this->display();
	}
	/**
	 * 开通确认
	 * @Author   zhoujing   <zhoujing@leubao.com>
	 * @DateTime 2017-10-31
	 */
	public function confirm()
	{
		if(IS_POST){
			$pinfo = I('post.');
    		if(empty($pinfo['card'])){
    			$return = ['status' => false, 'code'  => 10003, 'msg' => 'error' ];
    			die(json_encode($return));
    		}
    		$model = D('Crm/Member');
    		$map = [
    			'idcard' => $pinfo['card'],
                'status' => 2
    		];
    		$count = $model->where($map)->field('id,no-number')->find();
            //dump($count);
    		if(!empty($count)){
    			//$return = ['status' => true, 'code'  => 10001, 'msg' => 'error' ];
                //判断是否已经支付
                //$session = json_decode(load_redis('get',$ginfo['token']),true);
                //dump($session);
                //$token = $session['token'];
                $openid = session('openid');
               // dump($openid);
                //返回支付信息
                $pay = & load_wechat('Pay',43);
                $body = "梦里老家演艺小镇年卡办理";
                $out_trade_no = $count['no-number'];
                $total_fee = 1;
                $notify_url = 'http://dp.wy-mllj.com/';
                // 获取预支付ID
                $prepayid = $pay->getPrepayId($openid, $body, $out_trade_no, $total_fee, $notify_url, $trade_type = "JSAPI",'',1);
                $options = $pay->createMchPay($prepayid);
                $return = ['status' => true, 'code'  => 10001, 'data'=> ['config'=>$options],'msg' => '您的年卡已成功办理' ];
                //dump($options);
    		}else{
    			$return = ['status' => false, 'code'  => 10004, 'msg' => '您的年卡已成功办理' ];
    		}
    		die(json_encode($return));
		}
        
	}
	/**
	 * 微信授权 授权回调页面
	 * @Author   zhoujing   <zhoujing@leubao.com>
	 * @DateTime 2017-10-31
	 */
    public function oauth()
    {
    	$ginfo = I('get.');
        $oauth = & load_wechat('Oauth',43,1);
        $token = json_decode(load_redis('get',$_SERVER['HTTP_AUTHORIZATION']),true);
        if(!empty($token['openid'])){
            $return = ['status'=>true, 'code'=>10001, 'data'=>['openid'=>$token['openid']], 'msg'=>ok];
            die(json_encode($return));
        }
    	if(empty($ginfo['code'])){
            $callback = 'http://dp.wy-mllj.com/api.php?m=apply&a=oauth&token='.$_SERVER['HTTP_AUTHORIZATION'];
            $urls = $oauth->getOauthRedirect($callback, $state, 'snsapi_base');
    		$return = ['status'=>true, 'code'=>10003, 'data'=>['auth'=>$urls], 'msg'=>ok];
    		echo json_encode($return);
    	}else{
            $result = $oauth->getOauthAccessToken();
            $token = json_decode(load_redis('get',$ginfo['token']),true);
            $token['openid'] = $result['openid'];
            session('openid',$result['openid']);
            session('token',$token['token']);
            load_redis('setex',$ginfo['token'],json_encode($token),3600);
            $targetUrl = 'http://dp.wy-mllj.com/card/apply.html?token='.$token['token'];
            header('location:'. $targetUrl);
    	}
    }
}