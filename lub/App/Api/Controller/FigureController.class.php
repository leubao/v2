<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务 实时票图
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Think\Controller;
class FigureController extends Controller{
	//加载所有区域及座位
	function index(){
		$ginfo = I('pid');
		$today = strtotime(date('Ymd'));
		$plan = M('Plan')->where(array('plantime'=>array('lt',$today)))->select();
		if(empty($ginfo)){
			//默认加载当天的第一场
			$plan_id = M('Plan')->where(array('plantime'=>$today,'status'=>2,'games'=>1))->field('id')->getField();
		}else{
			$plan = explode('_',$ginfo);
			$plan_id = $plan['0'];
		}	
		//读取座椅模板
		$seat = F('Seat_'.$plan_id);
		if(empty($seat)){
			$this->error('未找座椅模板....');
		}
		//
		
		$this->assign('plan',$plan)
			->assign('seat',$seat)
			->assign('')
			->display();
	}
	//加载座椅状态  数据缓存   30秒更新一次
	//统一IP地址1分钟内请求超过30次  则禁止该IP地址15分钟不能刷新  并提示不要平凡刷新
	//返回数据格式为JSON
	function seats(){
		
	}

}