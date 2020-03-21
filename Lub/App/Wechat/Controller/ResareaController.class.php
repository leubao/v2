<?php
// +----------------------------------------------------------------------
// | LubTMP 微信活动支持 限制区域销售
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2015-8-25 
// +----------------------------------------------------------------------
namespace Wechat\Controller;
use Common\Controller\LubTMP;
use Wechat\Service\Wechat;
use WeChat\Service\Wxpay;
use Wechat\Service\Api;
use Wechat\Service\Wticket;
use Libs\Service\Order;
use Payment\Client\Charge;
class ResareaController extends LubTMP {

	public function index($id)
	{
		$ginfo = I('get.');
    /*
        $user = session('user');
        if(empty($user['user']['openid'])){
            if(isset($ginfo['code'])){
                $oauth = & load_wechat('Oauth',$ginfo['pid'],1);
                $wxauth = $oauth->getOauthAccessToken($ginfo['code']);
                if(!empty($wxauth['openid'])){
                    $user['user'] = array(
                        'id' => 2,
                        'openid' => $wxauth['openid'],
                        'maxnum' => '1',
                        'guide'  => '0',
                        'qditem' => '0',
                        'scene'  => '41',
                        'epay'   => '2',//结算方式1 票面价结算2 底价结算
                        'channel'=> '0',
                        'pricegroup'=>'9',
                    );
                    session('user',$user);
                    $user = session('user');
                }
                
            }
        }
        $this->kill_login($user['user']);
        $url = U('Wechat/Resarea/index',['pid'=>$ginfo['pid'],'act'=>$ginfo['act']]);
        $this->check_login($url);*/
        //根据活动拉取销售计划
        $info = M('Activity')->where(array('id'=>$actInfo['actId'],'status'=>1))->field('title,param,product_id')->find();


        $ticketType = F("TicketType".$info['product_id']);
        $ticket = $ticketType[$actInfo['killInfo']['ticket']];

        $this->assign('data',$info)->assign('param',$param['info'])->assign('ticket', $ticket)->assign('rule',$actInfo['killInfo']);
        $this->display();
	}
}