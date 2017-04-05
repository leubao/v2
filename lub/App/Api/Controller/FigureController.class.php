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
	//处理单笔订单返利问题
	function one_rebate($sn = ''){
		if(empty($sn)){
			$this->error('单号有误');
		}
		$model = D('TeamOrder');
		$info = $model->where(array('order_sn'=>$sn,'status'=>1))->find();
		if(!empty($info)){
			$status = \Libs\Service\Rebate::rebate($info);
			echo $status;
		}else{
			echo "未找到订单";
		}
	}
	/**
	 * 导出订单的下单商户  电话  数量
	 * http://www.yx513.net/api.php?m=Figure&a=exp_order&starttime=20170101&endtime=2010331
	 */
	function exp_order($starttime = ' ',$endtime = '')
	{
		$start_time = strtotime($starttime);
        $end_time = strtotime($endtime) + 86399;
		$map = [
			'createtime' => [['GT',$start_time],['LT',$end_time],'AND'],
			'status' 	  => ['in','1,7,9'],
			'type'	  	  => ['in','2,4,6']
		];
		$list = D('Api/OrderView')->where($map)->field('name,phone,number,channel_id')->select();
		$headArr = array(
			'channel_id'		=>	'单号ID',
   			'name'		=>	'渠道商',
   			'phone'		=>	'电话',
   			'number'	=>	'数量',
   		);
   		$filename = "订单记录";
   		return \Libs\Service\Exports::getExcel($filename,$headArr,$list);
   		exit;
	}
	/**
	 * 批量更新全员销售头像,且下载到本地
	 */
	function batch_wx(){
		$pid = '41',

		$model = D('WxMember');
		//获取列表
		$list = $model->where(['channel'=>1])->field('openid,user_id')->select();
		$user = & load_wechat('User',$pid,1);
		
		foreach ($list as $k => $v) {
			$result = $user->getUserInfo($v['openid']);
			// 读取微信粉丝列表
			if($result===FALSE){
			    // 接口失败的处理
			    $user->errMsg;
			}else{
			    // 接口成功的处理
			    $model->where(['openid'=>$v['openid']])->setField('headimgurl',$result['headimgurl']);
			    //下载图片到指定文件夹
			    $logo_path = SITE_PATH."d/upload/viplogo/";
        		$logo_path = \Libs\Util\Upload::getImage($result['headimgurl'],$logo_path,'u-logo-'.$ginfo['id'].'.png');
			    //生成新的二维码
			    $image_file = SITE_PATH."d/upload/".'u-'.$v['user_id'];
			    $url = U('Wechat/Index/show',array('u'=>$v['user_id'],'pid'=>$pid));
			    qr_base64($url,'u-'.$v['user_id'],$logo_path['save_path']);
			}
		}
		//批量下载logo
		//批量生成二维码
	}
}