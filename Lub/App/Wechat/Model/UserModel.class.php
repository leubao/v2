<?php

// +----------------------------------------------------------------------
// | LubTMP 微信模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------

namespace Wechat\Model;
use Common\Model\Model;

class UserModel extends Model {

    //自动验证
    protected $_auto = array(
        array('createtime', 'time', 1, 'function'),
    );
    public function login($identifier, $password = NULL)
    {
        if (empty($identifier)) {
            return false;
        }
        $map = array();
        //判断是uid还是用户名
        if (is_int($identifier)) {
            $map['id'] = $identifier;
        } else {
            $map['username'] = $identifier;
        }
        //强制场景为客户端
        $map['is_scene'] = 3;
        $map['status'] = '1';
        $uInfo = $this->where($map)->find();
        if (empty($uInfo)) {
            return false;
        }
        //密码验证
        if (!empty($password) && $this->hashPassword($password, $uInfo['verify']) != $uInfo['password']) {
            return false;
        }

        //查询所属分组信息
        $uInfo['group'] = M('CrmGroup')->where(array('id'=>$uInfo['groupid']))->field('id,name,price_group,type,settlement')->find();
        if($uInfo['group']['type'] == '1'){
            //查询所属商户相关信息
            $uInfo['crm'] = M('Crm')->where(array('id'=>$uInfo['cid']))->field('id,name,groupid,cash,level,f_agents')->find();
            // //开启代理商制度
            // $proconf = cache('ProConfig');
            // if($proconf['agent'] == '1'){
            //     $cid = money_map($uInfo['cid']);
            //     $uInfo['crm']['cash'] = balance($cid);
            // }
        }
        $wxuser = session('user');
        //读取session
        $user['user'] = array(
            'id'      => $uInfo['id'],
            'openid'  => $wxuser['user']['openid'],
            'nickname'=> $uInfo['nickname'],
            'maxnum'  => '30',
            'guide'   => $uInfo['id'],
            'qditem'  => $uInfo['cid'] ? $uInfo['cid']:'0',
            'scene'   => $wxuser['user']['scene'],
            'channel' => '1',
            'epay'    => $uInfo['group']['settlement'],
            'pricegroup'=> $uInfo['group']['price_group'],
            'wxid'      => $uInfo['wechat']['user_id'],//微信id
            'fx'        => $uInfo['type'],
            'promote'   => $uInfo['promote'],
            'fid'       => $wxuser['user']['promote'],
            'ctype'     =>  $uInfo['group']['type']
        );
        session('user', $user);
        return $uInfo;
        
    }
    /**
     * 获取用户信息
     * @param type $identifier 用户名或者用户ID
     * @return boolean|array
     */
    public function getuInfo($identifier, $password = NULL) {
    	if (empty($identifier)) {
            return false;
        }
        $map = array();
        //判断是uid还是用户名
        if (is_int($identifier)) {
           	$map['id'] = $identifier;
        } else {
            $map['username'] = $identifier;
        }
        //强制场景为客户端
        $map['is_scene'] = 3;
        $map['status'] = '1';
        $uInfo = $this->where($map)->find();
        if (empty($uInfo)) {
            return false;
        }
        //查询所属分组信息
        $uInfo['group'] = M('CrmGroup')->where(array('id'=>$uInfo['groupid']))->field('id,name,price_group,type,settlement')->find();
        if($uInfo['group']['type'] == '1'){
            //查询所属商户相关信息
            $uInfo['crm'] = M('Crm')->where(array('id'=>$uInfo['cid']))->field('id,name,groupid,cash,quota,level,f_agents')->find();
            //开启代理商制度
            $proconf = cache('ProConfig');
            if($proconf['agent'] == '1'){
                $cid = money_map($uInfo['cid']);
                $uInfo['crm']['cash'] = balance($cid);
            }
        }
        return $uInfo;
    }
    //认证切换
    function check_pwd($identifier, $password = NULL){
    	$map = array();
        //判断是uid还是用户名
        if (is_int($identifier)) {
           	$map['id'] = $identifier;
        } else {
            $map['username'] = $identifier;
        }
        //强制场景为客户端
        $map['is_scene'] = 3;
        $map['status'] = '1';
    	$uInfo = $this->where($map)->find();
	    if (empty($uInfo)) {
	        return false;
	    }
        //密码验证
        if (!empty($password) && $this->hashPassword($password, $uInfo['verify']) != $uInfo['password']) {
            return false;
        }
        return $uInfo;
    }
    
    
    /**
     * 更新登录状态信息
     * @param type $userId
     * @return type
     */
    public function loginStatus($userId) {
        $this->find((int) $userId);
        $this->last_login_time = time();
        $this->last_login_ip = get_client_ip();
        return $this->save();
    }
    /**
     * 对明文密码，进行加密，返回加密后的密文密码
     * @param string $password 明文密码
     * @param string $verify 认证码
     * @return string 密文密码
     */
    public function hashPassword($password, $verify = "") {
        return md5($password . md5($verify));
    }
}
?>