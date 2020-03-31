<?php
/**
 * 			
 */
class Wxlogin
{
	
	public function kill_login($uinfo)
    {
        if(!empty($uinfo['openid'])){
            if(D('KillLog')->where(['openid' => $uinfo['openid']])->field('id')->find()){
                D('KillLog')->where(['openid' => $uinfo['openid']])->setInc('login', 1);
            }else{
                $data = [
                    'openid'        =>  $uinfo['openid'],
                    'status'        =>  0,
                    'login'         =>  1,
                    'create_time'   =>  time(),
                    'update_time'   =>  time(),
                ];
                D('KillLog')->add($data);
            }
        }
        
       return true;
    }
}