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

	public function login()
	{
		if(IS_POST){
			$pinfo = I('post.');
			if(empty($pinfo['pwd'])){
				die(json_encode(['statusCode'=>300,'msg'=>'查询密码不能为空~']));
			}
			if($pinfo['pwd'] == '168168'){
				session('fugure', 'welcome');
				die(json_encode(['statusCode'=>200,'msg'=>'ok']));
			}else{
				die(json_encode(['statusCode'=>300,'msg'=>'查询密码有误~']));
			}
		}else{
			session('fugure',null);
			$this->display();
		}
	}
	//加载所有区域及座位
	function index(){
		$ginfo = I('get.');
		$product = (string)base_convert($ginfo['pid'],16,10);		//产品类型
		switch ($ginfo['type']) {
			case '1':
				//今天时间戳
				$today = strtotime(date('Y-m-d'))."-1";
				$where = [
					'product_id'=>$product,
					'status'=>2
				];
				$plan = D('Plan')->where($where)->order('plantime ASC,games ASC')->select();
				$type = $pinfo['type'] ? $pinfo['type'] : '1';
				//剧场
				$template = 'index';
				break;
			case '2':
				//景区
				$today = date('Y-m-d');
				$template = 'drifting';
				$type = $pinfo['type'] ? $pinfo['type'] : '1';
				break;
			case '3':
				//漂流
				$today = date('Y-m-d');
				$template = 'drifting';
				$type = $pinfo['type'] ? $pinfo['type'] : '1';
				break;
		}//dump($plan);
		$this->assign('plan',$plan)
		     ->assign('today',$today)
		     ->assign('type',$type)
		     ->assign('pid',$ginfo['pid'])
		     ->assign('product',productName($product,1))
		     ->display($template);
	}
	//改变场次或日期
	function change_plan()
	{
		if(IS_POST){
			$pinfo = json_decode($_POST['info'],true);
			$param = I('get.param',0,intval) ? I('get.param',0,intval) : '3';
			$product = (string)base_convert(I('get.pid'),16,10);
			// if(!session('fugure')){
			// 	$return = ['statusCode' => 300];
			// }else{
			// 	$return = \Libs\Service\Api::get_plan($product,$pinfo,$param);
			// }
			$return = \Libs\Service\Api::get_plan($product,$pinfo,$param);
			die(json_encode($return));
		}
	}
	//加载座椅状态  数据缓存   30秒更新一次
	//统一IP地址1分钟内请求超过30次  则禁止该IP地址15分钟不能刷新  并提示不要平凡刷新
	//返回数据格式为JSON
	function seats(){
		
	}
	//指定时间段过期场次
	function check_rebate_plan($starttime = ' ',$endtime = ''){
		$start_time = strtotime($starttime);
        $end_time = strtotime($endtime);
		$map = [
			'plantime' => [['EGT',$start_time],['ELT',$end_time],'AND'],
			'status'   => '4'
		];
		//获取已过期的场次
		$plan = M('Plan')->where($map)->field('id')->select();
		$model = D('TeamOrder');
		$where = [
			'status'=>1,
			'plan_id' => ['in',arr2string($plan,'id')]
		];
		$list = $model->where($where)->select();
		if(!empty($list)){
			foreach ($list as $k => $v) {
				$status = \Libs\Service\Rebate::rebate($v);
				echo $v['order_sn'];
			}
		}else{
			echo "未找到订单";
		}
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
			dump($status);
		}else{
			echo "未找到订单";
		}
	}
	//
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
			
			'phone'		=>	'电话',
			'number'	=>	'数量',
			'channel_id'=>	'渠道ID',
			'name'		=>	'渠道商',
   		);
   		$filename = "订单记录";
   		return \Libs\Service\Exports::getExcel($filename,$headArr,$list);
   		exit;
	}
	/**
	 * 批量更新全员销售头像,且下载到本地
	 */
	function batch_wx(){
		$pid = '43';

		$model = D('WxMember');
		//获取列表
		$list = $model->where(['channel'=>1])->field('openid,user_id,headimgurl')->select();
		//$user = & load_wechat('User',$pid,1);
		foreach ($list as $k => $v) {
			if(empty($v['headimgurl'])){
				//$result = $user->getUserInfo($v['openid']);
				//dump($result);
				/* 读取微信粉丝列表
				if($result===FALSE){
				    // 接口失败的处理
				    echo $user->errMsg;
				}else{
				    // 接口成功的处理
				    $model->where(['openid'=>$v['openid']])->setField('headimgurl',$result['headimgurl']);
				    echo $v['openid'].'<br/>';
				    /*下载图片到指定文件夹
				    $logo_path = SITE_PATH."d/upload/viplogo/";
	        		$logo_path = \Libs\Util\Upload::getImage($result['headimgurl'],$logo_path,'u-logo-'.$v['user_id'].'.png');
				    //生成新的二维码
				    $image_file = SITE_PATH."d/upload/".'u-'.$v['user_id'];
				    $url = U('Wechat/Index/show',array('u'=>$v['user_id'],'pid'=>$pid));
				    qr_base64($url,'u-'.$v['user_id'],$logo);
				    
				}*/
				//$openid[]=$v['openid'];
				$this->get_up_fxqr($v['openid']);
			}
		}
		//$result = $user->getUserBatchInfo($openid);
		//dump($result);
		//批量下载logo
		//批量生成二维码
	}
	function get_up_fxqr($openid){
		$pid = get_product('id');
		$model = D('WxMember');
		$info = $model->where(['channel'=>1,'openid'=>$openid])->field('openid,user_id,headimgurl')->find();
		$logo_path = SITE_PATH."d/upload/viplogo/";
		if(empty($info['headimgurl'])){
			$user = & load_wechat('User',$pid,1);
			$result = $user->getUserInfo($openid);
			if(!empty($result['headimgurl'])){
				$model->where(['openid'=>$v['openid']])->setField('headimgurl',$result['headimgurl']);
			    $logo_path = \Libs\Util\Upload::getImage($result['headimgurl'],$logo_path,'u-logo-'.$info['user_id'].'.png');
			    $logo = $logo_path.'u-logo-'.$info['user_id'].'.png';
			}else{
				$logo = $logo_path."delogo.jpg";
			}
		}else{
			$logo_path = \Libs\Util\Upload::getImage($info['headimgurl'],$logo_path,'u-logo-'.$info['user_id'].'.png');
			$logo = $logo_path.'u-logo-'.$info['user_id'].'.png';
		}
		//生成新的二维码
	    $param = $pid."&".$info['user_id']."&qrcode";
	    $param = \Libs\Util\Encrypt::authcode($param,'ENCODE');
	    $url = U('Wechat/Index/show',array('u'=>$info['user_id'],'pid'=>$pid,'param'=>$param));
	    qr_base64($url,'u-'.$info['user_id'],$logo);
	}
	//批量更新代收款支付方式不匹配问题
	function up_pay_coll(){
		//获取所有代收款订单
		$list = D('Collection')->field('order_sn,pay')->select();
		foreach ($list as $k => $v) {
			//更新对应订单支付方式
			$o_sta = D('Order')->where(['order_sn'=>$v['order_sn']])->setField('pay',$v['pay']);
			$r_sta = D('ReportData')->where(['order_sn'=>$v['order_sn']])->setField('pay',$v['pay']);
			echo $v['order_sn'].'+++'.$o_sta.'++++'.$r_sta.'<br>';
		}
		//更新报表中对应订单支付方式
	}
	//检查未返利的订单
	//http://pw.yjwts.com/api.php?m=Figure&a=get_up_no_rebate&starttime=20170701&endtime=20170812
	function get_up_no_rebate($starttime,$endtime){
        if(!empty($starttime)){
        	$start_time = strtotime($starttime);
        	$end_time = strtotime($endtime) + 86399;
        	$map = [
				'createtime' => [['GT',$start_time],['LT',$end_time],'AND'],
				'status' 	  => ['in','1,9,7,8'],
				'type'	  	  => ['in','2,4,8,9']
			];
			\Libs\Service\Check::check_rebate($map,2);
			return true;
        }else{
        	echo "日期必须...";
        }
		
	}
	function get_ac(){
		set_time_limit(0);
        $i = 0;
        $out_time = '20';
		while(true) {
            usleep(2000000);
            $i++;
            load_redis('lpush','orderquery',$i.'[='.date('Y-m-d H:i:s').'=]');
            //超过次数  关闭订单 TODO     
            if($i >= $out_time){

                break;     
            }
        }
	}
}