<?php
/**
 * Wticket.php
 * 
 * 票务业务逻辑
 */
namespace Wechat\Service;
class Wticket {
    /*获取销售计划*/
    function getplan($pid){
        $product = M('Product')->where(array('status'=>1,'id'=>$pid))->field('id')->select();
        $info['product'] = arr2string($product,'id');
        //根据当前用户判断   若为散客  读取配置项  微信的价格政策
        $user = session('user');
        $info['group']['price_group'] = $user['user']['pricegroup'];
        $info['scene'] = '4';
        $plan = \Libs\Service\Api::plans($info);
        foreach ($plan['plan'] as $key => $value) {
            $plans['plan'][] = array(
                'title' =>  $value['title'],
                'id'    =>  $value['id'],
                'num'   =>  $value['num'],
            );
            $plans['area'][$value['id']] = $value['param'];
        }
        return $plans;
    }
    /**
     * 生成专属连接
     * $callback为微信回跳地址（接口已经默认url_encode处理，授权成功会有$_GET['code']值，可用于下个步骤）
     * $state为重定向后会带上state参数（开发者可以填写a-zA-Z0-9的参数值，最多128字节）
     * $scope为应用授权作用域（snsapi_base | snsapi_userinfo）
     * @param  [type] $uid        [description]
     * @param  [type] $product_id 产品ID
     * @param  string $scope      [description]
     * @return [type]             [description]
     */
    function reg_link($uid = '',$product_id,$scope = 'snsapi_base',$state = 'alizhiyou'){
        $callback = U('Wechat/Index/show',array('u'=>$uid,'pid'=>$product_id));
        // SDK实例对象
        $oauth = & load_wechat('Oauth',$product_id,1);
        // 执行接口操作
        $urls = $oauth->getOauthRedirect($callback, $state, $scope);
        return $urls;
    }
        /**
     * 系统登录接口
     */
    //用户登录  $ginfo  微信授权信息
    function tologin($ginfo,$reg = ''){
        session('user',null);
        //记录推广人员
        $promote = $ginfo['u'];
        $oauth = & load_wechat('Oauth',$ginfo['pid'],1);
        $wxauth = $oauth->getOauthAccessToken($ginfo['code']);
        //新用户写入
        Wticket::add_wx_user($wxauth,$promote);
        $openid = $ginfo['openid'] ? $ginfo['openid'] : $wxauth['openid'];
        //保存openid
        session('openid',$openid);
        //写入必要的当前用户信息
        $uinfo = Wticket::get_auto_auth($openid,$promote);
        if(!empty($uinfo['wechat']) && empty($reg)){
            //根据当前登录用户获取支付方式、价格政策、可售数量、单笔订单最大量 30
            //判断是否是政企渠道用户
            switch ($uinfo['group']['type']) {
                case '1':
                    $type = 2;
                    $pay = '2';/*支持授信额支付*/
                    $scene = '42';
                    break;
                case '3':
                    $type = 4;/*政企渠道*/
                    $pay = '5';/*支持微信支付*/
                    $scene = '46';
                    break;
                case '4':
                    $type = 8;/*全员销售*/
                    $pay = '5';/*支持微信支付*/
                    $scene = '48';
                    break;
                case '5':
                    $type = 9;/*全员销售*/
                    $pay = '5';/*支持微信支付*/
                    $scene = '49';
                    break;
            }
            $user['user'] = array(
                'id'      => $uinfo['id'],
                'openid'  => $openid,
                'nickname'=> $uinfo['nickname'],
                'maxnum'  => '30',
                'guide'   => $uinfo['id'],
                'qditem'  => $uinfo['cid'] ? $uinfo['cid']:'0',
                'scene'   => $scene,
                'channel' => '1',
                'epay'    => $uinfo['group']['settlement'],
                'pricegroup'=> $uinfo['group']['price_group'],
                'wxid'      => $uinfo['wechat']['user_id'],//微信id
                'fx'        =>  $uinfo['type'],
                'promote'   => $uinfo['promote'],
                'activity'  => $ginfo['act'] ? $ginfo['act']:'0',
                'fid'       => $promote,
            );
        }else{
            $proconf = cache('ProConfig');
            $proconf = $proconf[$ginfo['pid']][2];
            //微信散客先写死 TODO
            $user['user'] = array(
                'id' => 2,
                'openid' => $openid,
                'maxnum' => '5',
                'guide'  => '0',
                'qditem' => '0',
                'scene'  => '41',
                'epay'   => '2',//结算方式1 票面价结算2 底价结算
                'channel'=> '0',
                'pricegroup'=>$proconf['wx_price_group'],
                'wxid'   => $uinfo['wechat']['user_id'],
            );
        }
        //缓存用户信息
        session('user',$user);
        return $user;
    }
    /*
    * 渠道商微信自动登录 
    * 微信号id
    * 当已经是渠道人员的人打开带推广的连接依然使用自己的渠道政策，当是散客的人员打开推广连接查询是否包含推广人，包含则执行推广人的价格政策
     */
    function get_auto_auth($open_id = '',$promote = '',$user_id = ''){
        //查询数据库是否已经绑定账号
        if(empty($open_id)){//error_insert('98');
            $map = array('user_id'=>$user_id);
        }else{
            $map = array('openid'=>$open_id);
        }
        $winfo = M('WxMember')->where($map)->field('user_id,openid,unionid,channel')->find();
        $db = M('User');
        if(!empty($winfo['user_id']) && $winfo['channel'] == '1'){
            $uInfo = $db->where(array('id'=>$winfo['user_id'],'status'=>'1'))->field('id,nickname,cid,groupid,type')->find();
        }elseif (!empty($promote)) {
            $uInfo = $db->where(array('id'=>$promote,'status'=>'1'))->field('id,nickname,cid,groupid,type')->find();
            $uInfo['promote'] = $promote;//推广标记
        }else{
            //当微信为散客时，默认用户为微信售票
            return '0';
        }
        if(!empty($uInfo)){
            //查询所属分组信息
            $uInfo['group'] = M('CrmGroup')->where(array('id'=>$uInfo['groupid']))->field('id,name,price_group,type,settlement')->find();
            if($uInfo['group']['type'] == '1'){
                //查询所属商户相关信息 企业
                $uInfo['crm'] = M('Crm')->where(array('id'=>$uInfo['cid']))->field('id,name,groupid,cash,quota,level,f_agents,agent')->find();
                if($uInfo['crm']['agent'] == '1'){
                    //开启代理商制度
                    $cid = money_map($uInfo['cid']);
                    $uInfo['crm']['cash'] = balance($cid);
                }
            }
            $uInfo['wechat'] = $winfo;
            return $uInfo;
        }
    }
    //写入微信用户 从微信服务端拉取用户
    function add_wx_user($data,$promote){
        if(!empty($data)){
            //判断用户是否存在
            $db = M('WxMember');
            $uinfo = $db->where(array('openid'=>$data['openid']))->find();
            if($uinfo){
                if($uinfo['user_id']){
                    return '2';
                }else{
                    return '3';
                }
            }else{
                $datas = array(
                    'openid'    =>  $data['openid'],
                    'unionid'   =>  $data['unionid'],
                    'sex'       =>  $data['sex'],
                    'city'      =>  $data['city'],
                    'province'  =>  $data['province'],
                    'nickname'  =>  $data['nickname'],
                    'promote'   =>  $promote,
                );
                $db->add($datas);
                return '1';
            }
        }else{
            //获取用户信息失败
            return false;
        }
    }
}