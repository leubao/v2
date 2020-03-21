<?php
/**
 * Wticket.php
 * 
 * 票务业务逻辑
 */
namespace Wechat\Service;
class Wticket {
    /*获取销售计划
    * @Author   zhoujing                 <zhoujing@leubao.com>
    * @DateTime 2019-11-15T13:44:31+0800
    * @param    inr                   $pid                  产品id
    * @param    array                    $ticket               返回票型
    * @return   [type]                                         [description]
    */
    function getplan($pid, $ticket = array()){
        $product = M('Product')->where(array('status'=>1,'id'=>$pid))->field('id')->select();
        $info['product'] = arr2string($product,'id');
        //根据当前用户判断   若为散客  读取配置项  微信的价格政策 
        $user = session('user');
        $info['group']['price_group'] = $user['user']['pricegroup'];
        $info['scene'] = '4';
        $plan = \Libs\Service\Api::plans($info, '', '', $ticket);
        foreach ($plan['plan'] as $key => $value) {
            $plans['plan'][] = array(
                'title' =>  $value['title'],
                'id'    =>  $value['id'],
                'num'   =>  $value['num'],
            );
            if(empty($value['param'])){
                $plans['area'][$value['id']] = [];
            }else{
                $plans['area'][$value['id']] = $value['param'];
            }
            
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
        //$callback = U('Wechat/Index/show',array('u'=>$uid,'pid'=>$product_id));
        //返回到活动
        $callback = U('Wechat/Activity/act',array('u'=>$uid,'pid'=>$product_id,'act'=>'131159'));
        // SDK实例对象
        $oauth = & load_wechat('Oauth',$product_id,1);
        // 执行接口操作
        $urls = $oauth->getOauthRedirect($callback, $state, $scope);
        return $urls;
    }
    /**
     * 印象大红袍厦门活动
     */
    function xm_tologin($ginfo,$reg = '')
    {
        session('user',null);
        $oauth = & load_wechat('Oauth',$ginfo['pid'],1);//dump($ginfo);
        $wxauth = $oauth->getOauthAccessToken($ginfo['code']);//dump($wxauth);
        //新用户写入
        Wticket::add_wx_user($wxauth,$promote,$ginfo['pid']);
        $openid = $ginfo['openid'] ? $ginfo['openid'] : $wxauth['openid'];
        //保存openid
        session('openid',$openid);
        //写入必要的当前用户信息
        $uinfo = Wticket::get_auto_auth($openid,$promote);
        $user['user'] = array(
            'id' => 2,
            'openid' => $openid,
            'maxnum' => '1',
            'guide'  => '0',
            'qditem' => '0',
            'scene'  => '41',
            'epay'   => '2',//结算方式1 票面价结算2 底价结算
            'channel'=> '0',
            'pricegroup'=>'9',
            'wxid'   => $uinfo['wechat']['user_id'],
        );
        session('user',$user);
        return true;
    }
    /**
     * 系统登录接口
     */
    //用户登录  $ginfo  微信授权信息
    function tologin($ginfo,$reg = ''){
        //session('user',null);
        //记录推广人员
        $promote = $ginfo['u'];
        $oauth = & load_wechat('Oauth',$ginfo['pid'],1);
        $wxauth = $oauth->getOauthAccessToken($ginfo['code']);
        //新用户写入
        Wticket::add_wx_user($wxauth,$promote,$ginfo['pid']);
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
                'fx'        => $uinfo['type'],
                'promote'   => $uinfo['promote'],
                'fid'       => $promote,
                'ctype'     =>  $uinfo['group']['type']
            );
        }else{
            $proconf = cache('ProConfig');
            $pid = session('pid');
            $proconf = $proconf[$pid][2];
            if(!empty($uinfo['id']) && !empty($uinfo['group']['type'])){
                //二维码推广或分享购买链接
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
                        $type = 9;/*三级销售*/
                        $pay = '5';/*支持微信支付*/
                        $scene = '49';
                        break;
                }
                $user['user'] = array(
                    'id' => 2,
                    'openid' => $openid,
                    'maxnum' => '30',
                    'guide'  => $uinfo['id'],
                    'qditem' => $uinfo['cid'] ? $uinfo['cid']:'0',
                    'scene'  => $scene,
                    'epay'   => $uinfo['group']['settlement'],//结算方式1 票面价结算2 底价结算
                    'channel'=> '0',
                    'fx'     => $uinfo['type'],
                    'pricegroup'=> $uinfo['group']['price_group'],
                    'wxid'   => $uinfo['wechat']['user_id'],
                    'fxlink' => '1',//标记通过分享链接过来
                    'ctype'     =>  $uinfo['group']['type']
                );
            }else{
                //微信散客先写死 TODO
                $user['user'] = array(
                    'id' => 2,
                    'openid' => $openid,
                    'maxnum' => '30',
                    'guide'  => '0',
                    'qditem' => '0',
                    'scene'  => '41',
                    'epay'   => '2',//结算方式1 票面价结算2 底价结算
                    'channel'=> '0',
                    'pricegroup'=>$proconf['wx_price_group'],
                    'wxid'   => $uinfo['wechat']['user_id'],
                );
            }   
        }        //缓存用户信息
        session('user',$user);
        return true;
    }
    //存储用户信息
    function storage_user($uinfo){
        load_redis('set','ppp',serialize($uinfo));
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
                'fid'       => $promote,
            );
        }else{
            $proconf = cache('ProConfig');
            $pid = session('pid');
            $proconf = $proconf[$pid][2];
            if(!empty($uinfo['id']) && !empty($uinfo['group']['type'])){
                //二维码推广或分享购买链接
                switch ($uinfo['group']['type']) {
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
                    'id' => 2,
                    'openid' => $openid,
                    'maxnum' => '30',
                    'guide'  =>  $uinfo['id'],
                    'qditem'  => $uinfo['cid'] ? $uinfo['cid']:'0',
                    'scene'  => $scene,
                    'epay'   => $uinfo['group']['settlement'],//结算方式1 票面价结算2 底价结算
                    'channel'=> '0',
                    'pricegroup'=>$uinfo['group']['price_group'],
                    'wxid'   => $uinfo['wechat']['user_id'],
                    'fxlink' => '1',//标记通过分享链接过来
                );
            }else{
                //微信散客先写死 TODO
                $user['user'] = array(
                    'id' => 2,
                    'openid' => $openid,
                    'maxnum' => '30',
                    'guide'  => '0',
                    'qditem' => '0',
                    'scene'  => '41',
                    'epay'   => '2',//结算方式1 票面价结算2 底价结算
                    'channel'=> '0',
                    'pricegroup'=>$proconf['wx_price_group'],
                    'wxid'   => $uinfo['wechat']['user_id'],
                );
            }   
        }
        //缓存用户信息
        session('user',$user);
        return true;
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
        //if(!empty($winfo['user_id']) && $winfo['channel'] == '1'){
        if(!empty($winfo['user_id'])){
            $uInfo = $db->where(array('id'=>$winfo['user_id'],'status'=>'1'))->field('id,nickname,cid,groupid,type')->find();
        }elseif (!empty($promote) && empty($winfo['channel'])) {
            
            $uInfo = $db->where(array('id'=>$promote,'status'=>'1'))->field('id,nickname,cid,groupid,type')->find();
            //$uInfo['promote'] = $promote;//推广标记
        }
        //load_redis('set','222',$promote.serialize($uInfo));
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
            //load_redis('set','sql',$db->_sql());
            //load_redis('set','uinfo',serialize($uInfo));
            return $uInfo;
        }else{
            //load_redis('set','2','221');
            //当微信为散客时，默认用户为微信售票
            return '0';
        }
    }
    //写入微信用户 从微信服务端拉取用户
    function add_wx_user($data,$promote,$product_id){
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
                $userinfo = Wticket::get_wechat_user_info($data['openid'],$product_id);
                $datas = array(
                    'openid'    =>  $data['openid'],
                    'unionid'   =>  $userinfo['unionid'],
                    'headimgurl'=>  $userinfo['headimgurl'],
                    'sex'       =>  $userinfo['sex'],
                    'city'      =>  $userinfo['city'],
                    'province'  =>  $userinfo['province'],
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
    //异步获取关注者的详细信息
    function get_wechat_user_info($openid,$product_id){
        // 实例微信粉丝接口
        $user = & load_wechat('User',$product_id,1);
        // 读取微信粉丝列表
        $result = $user->getUserInfo($openid);
        // 处理创建结果
        if($result===FALSE){
            // 接口失败的处理
            //echo $user->errMsg;
        }else{
            // 接口成功的处理
            return $result;
        }
    }
    //判断是否已经注册
    function is_reg($openid){
        //读取是否存在user_id
        //$db = D('Wechat/WxMemberView');
        $wx = D('WxMember')->where(['openid'=>$openid])->find();
        $info = D('User')->where(['id'=>$wx['user_id']])->find();
        if(empty($info['status'])){
            //可注册
            return '400';
        }elseif($info['status'] == '3'){
            //已经注册 待审核
            return '300';
        }else{
            //跳转到个人中心
            return '200';
        }
    }
}