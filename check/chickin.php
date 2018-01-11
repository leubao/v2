<?php
namespace check;
/**
* 
*/
class chinkin
{
	/*检票
	@param $header string 信息头部
	@param $content string 待拆分的订单号*/
	function checkin($header,$content){
		//订单号拆解
		$sn = Checkin::splits($content);
		$plan = F('Plan_'.$sn['0']);
		if(empty($plan)){
			$plan = M('Plan')->where(array('id'=>$sn['0']))->find();
		}
		if(empty($plan)){
			$data=array(
				'state'=>"412",
				'data' =>array('msg'=>$content."未找到销售计划"),
			);
			Checkin::recordLogindetect($header, 2,0,$sn,$data);
			return false;
		}
		//判断检票时间
		$checktime = Checkin::timeCheck($plan);
		if(!$checktime){
			$data=array(
				'state'=>"411",
				'data' =>array('msg'=>$sn['0']."不在检票时间范围"),
			);
			Checkin::recordLogindetect($header, 2,0,$sn,$data);
			return false;
		}
		if(!empty($plan)){
	        $state = Checkin::order_check($plan,$sn,'1');
			return $state;
		}else{
			$data=array(
				'state'=>"201",
				'data' =>array('msg'=>$sn['0']."未找到定单"),
			);
			Checkin::recordLogindetect($header, 2,0,$sn,$data);
			return false;
		}
	}
	/*
	* 更新座椅状态  用于检票
	* @param $table string 表名称
	* @param $order  array 更新条件
	*/
	function up_seat($table,$info,$product_type){
		switch ($product_type) {
			case '1':
				$map['area']	=	$info['area'];
				$map['seat']	=	$info['seat'];
				break;
			case '2':
				# code...
				break;
			case '3':
				# code...
				break;
		}
		$map = array(
			'order_sn'	=>	$info['order_sn'],
			'print'		=>	$info['print'],	
			'id'		=>	$info['id'],
			'status'	=>	'2',
		);
		$status = M(ucwords($table))->where($map)->save(array('status'=>'99','checktime'=>time()));
		return $status;
	}
	/**
	 * 时间场次验证
	 * $plan 检票场次
	 */
	private function timeCheck($plan){
		if(empty($plan)){
			return false;
		}
		//获取系统日期
		$datetime = date('Ymd');
		//日期
		$plantime = date('Ymd',$plan['plantime']);
		//检票基准时间
		$starttime = date('H:i',$plan['starttime']);
		//检票时间
		$start = date('H:i',strtotime("$starttime -55 minute"));
		$ends = date('H',strtotime("$starttime +50 minute"));
		if($ends == '00' || $ends == '01'){
			$end = '24:'.date('i',strtotime("$starttime +50 minute"));
		}else{
			$end = date('H:i',strtotime("$starttime +50 minute"));
		}
		if($datetime == $plantime){
			//判断日期
			if(date('H',$v['endtime']) == '00'){
	            $totime = '24'.':'.date('i',$v['endtime']);
	        }else{
	            $totime = date('H:i');
	        }
			if($start <= $totime && $totime <= $end){
				//判断时间
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}
