<?php
// +----------------------------------------------------------------------
// | LubTMP ajax 检测
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
use Item\Service\Partner;
class CheckController extends ManageBase{
	protected function _initialize() {
	 	parent::_initialize();
	 }
	/**
	 * 检测名称是否存在
	 * tb 表名称
	 * mp 条件
	 * na 名称
	 */
	function public_check_name(){
		$ginfo = I('get.');
		if(empty($ginfo['ta'])){
			return false;
		}
		switch ($ginfo['ta']){
			case 11:
				$map = array('product_id'=>$ginfo['mp'],'name'=>$ginfo['name'],'status'=>'1');
				$return = $this->check_name2('TicketGroup',$map);
				break;
			case 12:
				$map = array('product_id'=>$ginfo['mp'],'name'=>$ginfo['name'],'status'=>'1');
				$return = $this->check_name2('TicketSingle',$map);
				break;
			case 13:
				$map = array('product_id'=>$ginfo['mp'],'name'=>$ginfo['name'],'status'=>'1');
				$return = $this->check_name2('TicketType',$map);
				break;
			case 14:
				$map = array('product_id'=>$ginfo['mp'],'name'=>$ginfo['name']);
				$return = $this->check_name2('ChannelSeat',$map);
				break;
			case 15:
				$map = array('product_id'=>$ginfo['mp'],'name'=>$ginfo['name']);
				$return = $this->check_name2('Pwd',$map);
				break;
			case 16:
				$map = array('product_id'=>$ginfo['mp'],'name'=>$ginfo['name']);
				$return = $this->check_name2('LeaderSms',$map);
				break;
			case 17:
				$map = array('username'=>$ginfo['username']);
				$return = $this->check_name2('User',$map);
				break;
			case 18:
				$map = array('name'=>$ginfo['name']);
				$return = $this->check_name2('Place',$map);
				break;
			case 19:
				$map = array('phone'=>$ginfo['phone']);
				$return = $this->check_name2('User',$map); 
				break;
			case 20:
				$map = array('legally'=>$ginfo['legally']);
				$return = $this->check_name2('User',$map);
				break;
			case 31:
				$map = ['idcard'=>$ginfo['idcard'],'activity_id'=>$ginfo['actid']];
				$return = $this->check_name2('IdcardLog',$map);
				break;
			case 51:
				if(!empty($ginfo['id'])){
					$map = ['idcard'=>$ginfo['idcard'],'id'=>['neq',$id],'status'=>['gt',0]];
				}else{
					$map = ['idcard'=>$ginfo['idcard'],'status'=>['gt',0]];
				}
				
				$return = $this->check_name2('Member',$map);
				$msg = $return ? '年卡已存在' : 'ok';
				break;
		}
		if(empty($return)){
			$this->ajaxReturn(array('ok'=>$msg ? $msg : '名称可用','state'=>'ok'),json);
		}else{
			$this->ajaxReturn(array('error'=>$msg ? $msg : '已存在','state'=>'error'),json);
		}
	}
	private function check_name2($table,$map){
		$db = D("$table");
		$status = $db->where($map)->find();
		return $status;
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
				$return = verifyIdCard($ginfo);
				// $map = ['idcard'=>trim($ginfo['idcard']),'activity_id'=>$ginfo['actid']];
				// $count = D('IdcardLog')->where($map)->count();
				// if($count > 0){
				// 	//读取活动
				// 	$actInfo = D('Activity')->getActInfo($ginfo['actid']);
				// 	$number = $actInfo['param']['info']['number'];
				// 	if($number > 0 && $number >= $count){
				// 		$return = 1;
				// 	}else{
				// 		$return = 0;
				// 	}
				// }
				break;	
		}
		if($return > 0){
			die(json_encode(['msg'=>'身份证号可用','status'=>true]));
		}else{
			die(json_encode(['msg'=>$msg ? $msg : '已存在','status'=>false]));
		}
	}
}