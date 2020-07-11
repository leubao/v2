<?php

/**
 * @Author: IT Work
 * @Date:   2020-06-24 09:53:31
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-06-24 10:28:45
 */
namespace Trust\Model;


use Common\Model\RelationModel;
class UserModel extends RelationModel {
	/**
     * 获取用户信息
     * @param type $identifier 用户名或者用户ID
     * @return boolean|array
     */
    public function getuInfo($identifier, $isLogin = true, $password = '') {
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
        $uInfo = $this->where($map)->field('email,create_time,update_time,remark,last_login_time,last_login_ip', true)->find();
        if (empty($uInfo)) {
            return false;
        }
        //密码验证
        if($isLogin){
        	if (!empty($password) && $this->hashPassword($password, $uInfo['verify']) != $uInfo['password']) {
            	return false;
        	}
        }
        
        //过滤敏感信息
        $uInfo = array_diff_key($uInfo, ['password'=>'','verify'=>'','legally'=>'','uptime'=>'']);
        //查询所属分组信息
        $group = M('CrmGroup')->where(array('id'=>$uInfo['groupid']))->field('id,name,price_group,type,settlement,param')->find();
        $group['param'] = json_decode($group['param'], true);
        $uInfo['group'] = $group;

        if($uInfo['group']['type'] <> '4'){
            //查询所属商户相关信息
            $crm = M('Crm')->where(array('id'=>$uInfo['cid']))->field('id,name,groupid,cash,level,agent,itemid,f_agents,param')->find();
            $param = json_decode($crm['param'],true);
            unset($crm['param']);
            $uInfo['crm'] = $crm;
            $uInfo['param'] = $param;
            //TODO 不开启代理制度
            if($crm['agent'] == '1'){
                
            }
            //判断是否开启多级扣款   开启时 显示自己的授信额度
            $itemConf = cache('ItemConfig');
            if(!$itemConf[$crm['itemid']]['1']['level_pay']){
                $cid = money_map($uInfo['cid']);
                $uInfo['crm']['cash'] = balance($cid);
            }
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