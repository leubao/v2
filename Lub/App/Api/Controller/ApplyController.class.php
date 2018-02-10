<?php
// +----------------------------------------------------------------------
// | LubTMP 年卡申办接口
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use \Api\Controller\TrustController;
use Libs\Service\YearCard;
class ApplyController extends TrustController{
	/**
	 * 首次会话取得会话ID
	 * @Author   zhoujing   <zhoujing@leubao.com>
	 * @DateTime 2017-11-02
	 */
	public function index()
	{
		$ginfo = I('get.');
        $token = session('token',$token);
		if(empty($token)){
			$token = \Libs\Service\LubToken::createToken();
            session('token',$token);
			//存储至Redis中
			load_redis('setex',$token,json_encode(['token'=>$token]),3600);
		}else{
			$session = json_decode(load_redis('get',$token),true);
            
			$token = $session['token'];
            $openid = session('openid');
		}
        //返回年卡的基本配置
        $base = [
            'money' =>  '1.00',
            'bjimg' =>  '',
            'area'  =>  '',
            'call'  => $this->config['call'],
        ];
		$return = ['status'=>true, 'code'=>10001, 'data'=>[ 'token'=>$token, 'openid'=> $openid, 'base'=> $base], 'msg'=>'ok'];
		die(json_encode($return));
	}
	/**
	 * @Author   zhoujing   <zhoujing@leubao.com>
	 * @DateTime 2017-10-31
	 * @return   json 
	 */
	public function apply()
	{
		if(IS_POST){
			$pinfo = I('post.');//dump($pinfo);
    		if(empty($pinfo['card'])){
    			$return = ['status' => false, 'code'  => 10003, 'msg' => '身份证号不能为空' ];
    			die(json_encode($return));
    		}
            $token = $_SERVER['HTTP_AUTHORIZATION'];
            $session = json_decode(load_redis('get',$token),true);
            $model = D('Crm/Member');
            /*验证手机号和验证码
            
            
            $code = \Libs\Service\LubToken::encryCode($pinfo['code'],$pinfo['phone']);
            if($code !== $session['code']){
                $return = ['status' => false, 'code'  => 10003, 'msg' => '验证码不正确或已过期' ];
                die(json_encode($return));
            }*/
            //验证身份证号
            $yearCard = new YearCard();
    		$check_card = $yearCard->check_year_card($pinfo['card']);
            if(!$check_card){
                $return = ['status' => false, 'code'  => 10003, 'msg' => $yearCard->error ];
                die(json_encode($return));
            }
           // dump($session);
            //存储请求数据
            $session['post'] = [
                'content' => $pinfo['content'],
                'phone'=> $pinfo['phone'],
                'card' => $pinfo['card']
            ];
            //当未完成支付时，也返回数据
            $info = $model->where(['idcard'=>$pinfo['card']])->field('id,nickname,phone,idcard,status')->find();
            if(!empty($info) && $info['status'] === 1){
                die(json_encode(['status' => true, 'code'  => 10003,'msg' => '您已经办理完年卡,请勿重复办理!' ]));
            }elseif(!empty($info)){
                die(json_encode(['status' => true, 'code'  => 10001,'msg' => '等待付款!' ]));
            }else{
                $data = [
                    'source'    =>  '5',//来源
                    'no-number' =>  date('YmdH').genRandomString(6,1),
                    'idcard'    =>  $pinfo['card'],
                    'nickname'  =>  $pinfo['content'],
                    'phone'     =>  $pinfo['phone'],
                    'openid'    =>  session('openid'),
                    'user_id'   =>  '0',//窗口时写入办理人
                    'thetype'   =>  '1', //凭证类型
                    'remark'    =>  $pinfo['remark'],//备注
                    'create_time'=> time(),
                    'status'    =>  2,//未支付或未续期
                ];
                try{ 
                    $model->add($data);
                }catch(Exception $e){ 
                    die(json_encode(['status' => false, 'code'  => 10003,'msg' => '办理失败!' ]));
                }
                die(json_encode(['status' => true, 'code'  => 10001,'msg' => 'ok!' ]));
            }
            //发起微信支付数据
            load_redis('setex',$token,json_encode($session),3600);
    		die(json_encode(['status' => true, 'code'  => 10001, 'data' => ['sn'=>get_order_sn(43)],'msg' => 'ok' ]));
		}else{
			$this->display();
		}
	}
    public function temp()
    {
        $ginfo = I('get.');
        $yearCard = new YearCard();
        $check_card = $yearCard->check_year_card($ginfo['card']);
        echo $yearCard->error;
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
               //dump($openid);
                //返回支付信息
                $pay = & load_wechat('Pay',43);
                $body = "梦里老家演艺小镇年卡";
                $out_trade_no = $count['no-number'];
                $total_fee = 100;
                $notify_url = 'http://dp.wy-mllj.com/api.php/apply/notify.html';

                $script = & load_wechat('Script',43);

                // 获取JsApi使用签名，通常这里只需要传 $ur l参数
                $config = $script->getJsSign('http://dp.wy-mllj.com/card/apply.html');
                // 获取预支付ID
                $prepayid = $pay->getPrepayId($openid, $body, $out_trade_no, $total_fee, $notify_url, $trade_type = "JSAPI",'',1);
                if($prepayid){
                    $options = $pay->createMchPay($prepayid);
                    $return = ['status' => true, 'code'  => 10001, 'data'=> ['config'=>$config,'payconfig'=>$options],'msg' => '您的年卡已成功办理' ];
                }else{
                    // 创建JSAPI签名参数包，这里返回的是数组
                    $return = ['status' => false, 'code'  => 10004,'msg' => $pay->errMsg.$pay->errCode];
                }
                
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
    

    /**
     * 云鹿票务分销平台
     * 平台放到
     * 全员分销重构
     * 分销方式 二维码和链接 以及小程序卡片
     * 注册->审核->分销码[分销编号]同步生成分销二维码和分销链接->下单->支付->进入分账系统->晚上结算至个人账号
     * 选择提现或余额
     * 
     */
   	
    /**
     * 检票日志记录器
     * 检票全面有swoole 接管
     * 基于IP地址和端口来判断
     * 剧场检票数据在闸机开启时一次性读入Redis中，检票时 直接通过键名来操作，默认写入过程中只写入键名默认键值为1，检票时写入键值
     * 检票时将键值更改为日志数据
     * 检票先获取键值是否存在，然后判断键值是否可用
     * 过期票问题 以场次创建键值文件夹   场次过期  马上销毁
     * 对于景区三天有效的，以日期创建
     */
    public function check_log()
    {
    	//检查闸机是否有效
    	$log = [
    		'class'		=>	'',//入园凭证类型1门票二维码2身份证3指纹
    		'data' 		=>	'',//入园凭证 二维码数据、身份证号、指纹
    		'ticket'	=>	'',//门票类型1、散客 2、年卡
    		'intime'	=>	'',//入园检票时间
    		'outtime'	=>	'',//出园检票时间
    		'staytime'	=>	'',//停留时长
    		'tao'		=>	'',//通道编号
    		'type'		=>	'',//1入园2出园
    	];
    	//写入Redis
    	//没10分钟写入一次数据库
    	//
    }
    /**
     * 发送验证码
     * @Company  承德乐游宝软件开发有限公司
     * @Author   zhoujing      <zhoujing@leubao.com>
     * @DateTime 2017-11-05
     * @return   [type]        [description]
     */
    public function tosms()
    {
        $pinfo = I('post.data');
        $token = json_decode(load_redis('get',$_SERVER['HTTP_AUTHORIZATION']),true);
        if(empty($token)){
            die(json_encode(['status'=>false,'code'=>'10004','msg'=>'会话异常,请刷新页面']));
        }
        $code = genRandomString(4,1);
        $token['code'] = \Libs\Service\LubToken::encryCode($code,$pinfo['phone']);
        load_redis('setex',$_SERVER['HTTP_AUTHORIZATION'],json_encode($token),3600);
        //链接短信接口 
        //\Libs\Service\Sms::toSms();
        die(json_encode(['status'=>true,'code'=>'10001','msg'=>'ok']));

    }
    /**
     * 年卡是否存在校验
     * @Author   zhoujing   <zhoujing@leubao.com>
     * @DateTime 2017-11-01
     * @return   [type]     [description]
     */
    public function card()
    {
    	if(IS_POST){
    		$pinfo = I('post.');
    		if(empty($pinfo['card'])){
    			$return = ['status' => false, 'code'  => 10003, 'msg' => '未找到有效身份证号' ];
    			die(json_encode($return));
    		}
            //校验身份证是否可以办理
            $yearCard = new YearCard();
            $check_card = $yearCard->check_year_card($pinfo['card']);
            if(!$check_card){
                $return = ['status' => false, 'code'  => 10003, 'msg' => $yearCard->error ];
                die(json_encode($return));
            }
    		$model = D('Crm/Member');
    		$map = [
    			'idcard' => $pinfo['card'],
                'status' => 1,
    		];
    		$count = $model->where($map)->count();
    		if($count == 0){
    			$return = ['status' => true, 'code'  => 10001, 'msg' => 'error' ];
    		}else{
    			$return = ['status' => false, 'code'  => 10004, 'msg' => '已完成年卡办理,请无需重复办理' ];
    		}
    		die(json_encode($return));
    	}
    }
    public function notify()
    {
        // 实例支付接口
    $pay = & load_wechat('Pay',43);

    // 获取支付通知
    $notifyInfo = $pay->getNotify();

    // 支付通知数据获取失败
    if($notifyInfo===FALSE){
        // 接口失败的处理
        echo $pay->errMsg;
    }else{
    //支付通知数据获取成功
    if ($notifyInfo['result_code'] == 'SUCCESS' && $notifyInfo['return_code'] == 'SUCCESS') {
    // 支付状态完全成功，可以更新订单的支付状态了
    // @todo 
    // 返回XML状态，至于XML数据可以自己生成，成功状态是必需要返回的。
    // <xml>
    //    return_code><![CDATA[SUCCESS]]></return_code>
    //    return_msg><![CDATA[OK]]></return_msg>
    // </xml>
        $model = D('Crm/Member');
        $info = $model->where(['no-number'=>$notifyInfo['out_trade_no'],'status'=>1])->field('id')->find();
        if(empty($info)){
            $model->where(['no-number'=>$notifyInfo['out_trade_no']])->save(['status'=>1,'update_time'=>time()]);
        }
        
        return xml(['return_code' => 'SUCCESS', 'return_msg' => 'DEAL WITH SUCCESS']);
        }
        }
    }
}