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
use EasyWeChat\Foundation\Application;
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
		if(empty($ginfo['token'])){
			$token = \Libs\Service\LubToken::createToken();
			//存储至Redis中
			load_redis('setex',$token,json_encode(['token'=>$token]),3600);
		}else{
			$session = json_decode(load_redis('get',$ginfo['token']),true);
			$token = $session['token'];
			$openid = $session['openid'] ? $session['openid'] : 0;
		}
		$return = ['status'=>true, 'code'=>10001, 'data'=>[ 'token'=>$token, 'openid'=> $openid], 'msg'=>'ok'];
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
			$pinfo = I('post.');
    		if(empty($pinfo['card'])){
    			$return = ['status' => false, 'code'  => 10003, 'msg' => '身份证号不能为空' ];
    			die(json_encode($return));
    		}
            //验证手机号和验证码
            $token = $_SERVER['HTTP_AUTHORIZATION'];
            $session = json_decode(load_redis('get',$token),true);
            $code = \Libs\Service\LubToken::encryCode($pinfo['code'],$pinfo['phone']);
            if($code !== $session['code']){
                $return = ['status' => false, 'code'  => 10003, 'msg' => '验证码不正确或已过期' ];
                die(json_encode($return));
            }
            //验证身份证号
            $yearCard = new YearCard();
    		$check_card = $yearCard->check_year_card($pinfo['card']);
            if(!$check_card){
                $return = ['status' => false, 'code'  => 10003, 'msg' => $yearCard->error ];
                die(json_encode($return));
            }
            //存储请求数据
            $session['post'] = [
                'name' => $pinfo['name'],
                'phone'=> $pinfo['phone'],
                'card' => $pinfo['card']
            ];
            load_redis('setex',$token,json_encode($session),3600);
    		die(json_encode(['status' => true, 'code'  => 10001, 'msg' => 'ok' ]));
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
    			'cardid' => $pinfo['card']
    		];
    		$count = $model->where($map)->count();
    		if($count == 0){
    			$return = ['status' => true, 'code'  => 10001, 'msg' => 'error' ];
    		}else{
    			$return = ['status' => false, 'code'  => 10004, 'msg' => '已完成年卡办理' ];
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
    	$pinfo = I('post.');
    	$options = get_wechat(41);
    	$app = new Application($options);
    	if(empty($ginfo['code'])){
    		$callback = 'http://ticket.leubao.com/api.php?m=apply&a=oauth&token='.$_SERVER['HTTP_AUTHORIZATION'];
    		$response = $app->oauth->scopes(['snsapi_base'])->redirect($callback);
    		$return = ['status'=>true, 'code'=>10001, 'data'=>['auth'=>$response->getTargetUrl()], 'msg'=>ok];
    		echo json_encode($return);
    	}else{
			$oauth = $app->oauth;
			$user = $oauth->user();
			$uinfo = $user->toArray();
			$token = json_decode(load_redis('get',$ginfo['token']),true);
			$token['openid'] = $uinfo['id'];
			load_redis('setex',$ginfo['token'],json_encode($token),3600);
			$targetUrl = 'http://ticket.leubao.com/card/apply.html?token='.$ginfo['token'];
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
    			$return = ['status' => false, 'code'  => 10003, 'msg' => 'error' ];
    			die(json_encode($return));
    		}
    		$model = D('Crm/Member');
    		$map = [
    			'cardid' => $pinfo['card']
    		];
    		$count = $model->where($map)->count();
    		if($count == 0){
    			$return = ['status' => true, 'code'  => 10001, 'msg' => 'error' ];
    		}else{
    			$return = ['status' => false, 'code'  => 10004, 'msg' => '已完成年卡办理' ];
    		}
    		die(json_encode($return));
    	}
    }
}