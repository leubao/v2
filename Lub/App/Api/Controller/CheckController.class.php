<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务 
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Common\Controller\LubTMP;
use Api\Service\Api;

class CheckController extends LubTMP{
	//判断是否已经授权
    function _initialize(){
       
	}
	
	//检测身份证是否可用
	public function public_check_idcard()
	{
		$ginfo = I('get.');
		if(empty($ginfo['ta'])){
			return false;
		}
		switch ($ginfo['ta']){
			case 31:
				$map = ['idcard'=>$ginfo['idcard'],'activity_id'=>$ginfo['actid']];
				$return = $this->check_name2('IdcardLog',$map);
		}
		if(empty($return)){
			die(json_encode(['msg'=>'名称可用','status'=>true]));
		}else{
			die(json_encode(['msg'=>'名称已存在','status'=>false]));
		}
	}
	private function check_name2($table,$map){
		$db = D("$table");
		$status = $db->where($map)->find();
		return $status;
	}
}