<?php
// +----------------------------------------------------------------------
// | LubTMP 检票类
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Service;

class Checkin extends \Libs\System\Service{
	
	/*检票
	@param $header string 信息头部
	@param $content string 待拆分的订单号*/
	function checkin($header,$content){
		//订单号拆解
		$sn = Checkin::splits($content);
		$plan = F('Plan_'.$sn['0']);
		//判断检票时间
		$checktime = Checkin::timeCheck($plan);
		if(!$checktime){
			return false;
		}
		if(!empty($plan)){
			$info = $this->ticket_info($plan['seat_table'],$sn['1']);//获取订单信息
			if($info != false){
				$state = \Libs\Service\Encry::decryption($sn['0'],$info['order_sn'],$plan['encry'],$info['area'],$info['seat'],$info['print'],$sn['1'],$sn['2']);
				if($state != false){
					//更新检票
					if(Checkin::up_seat($plan['seat_table'],$info) != false){
						$data=array(
							'state'=>"99",
							'data' =>array('msg'=>"检票成功"),
						);
						Checkin::recordLogindetect($header, 2,1,$sn,$data);
						return true;
					}else{
						$data=array(
							'state'=>"201",
							'data' =>array('msg'=>"门票状态更新失败"),
						);
						Checkin::recordLogindetect($header, 2,0,$sn,$data);
			  			return false;
					}
				}else{
					$data=array(
						'state'=>"202",
						'data' =>array('msg'=>"订单号解析失败"),
					);
					Checkin::recordLogindetect($header, 2,0,$pinfo,$data);
				  	return false;
				}
			}else{
				$data=array(
					'state'=>"201",
					'data' =>array('msg'=>"未找到定单"),
				);
				Checkin::recordLogindetect($header, 2,0,$sn,$data);
			  	return false;
			}
		}else{
			$data=array(
				'state'=>"201",
				'data' =>array('msg'=>"未找到定单"),
			);
			Checkin::recordLogindetect($header, 2,0,$sn,$data);
			return false;
		}
	}
	/*
	* 订单号拆解
	* @param $sn string 待拆分的订单号
	*/
	function splits($sn){
		if(!empty($sn)){
			$sns = explode('^', $sn);
			return $sns;
		}
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
		$plantime = $plan['plantime'];
		//检票基准时间
		$starttime = date('H:i',$plan['starttime']);
		//检票时间
		$start = date('H:i',strtotime("$starttime -60 minute"));
		$end = date('H:i',strtotime("$starttime +50 minute"));
		if($datetime == date('Ymd',$plantime)){
			//判断日期
			$totime = date('H:i');
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
	/**
	 * 检票类型   一团 ||  一人
	 */
	private function typeCheck(){
		
	}
	
	/**
	 * 剧院类检票
	 */
	private function theatreCheck($table = false, $sn = false, $type = '1'){
		if($type == '11'){
			//一团一票
		}
		//id	order_sn 订单号	area 区域ID	row 排号	print 打印次数	seat 座椅ID	sale 售出信息如票型ID票型价格	status 状态	soldtime 座位售出时间	checktime 检票时间
		$map = array(
			'order_sn' 	=> 	'',
			'area'		=>	'',
			'print'		=>	'',
			'seat'		=>	'',
		);
		D(ucwords($table))->where()->setField(array('status'=>'4','checktime'=>time()));
	}
	/*
	*获取门票信息
	*@param $table 表名称
	*@param $order 订单号
	*@param $type 请求方法 1　检票　　2：为监票
	*/
	function ticket_info($table,$order,$type = '1'){
		$map = array(	
			'id' =>	$order,
		);
		if($type == '1'){
			$map['status'] = '2';
		}
		$info = M(ucwords($table))->where($map)->find();
		return $info;
	}
	/*
	* 更新座椅状态  用于检票
	* @param $table string 表名称
	* @param $order  array 更新条件
	*/
	function up_seat($table,$info){
		$map = array(
			'order_sn'	=>	$info['order_sn'],
			'area'		=>	$info['area'],
			'seat'		=>	$info['seat'],
			'print'		=>	$info['print'],	
			'id'		=>	$info['id'],
			'status'	=>	'2',
		);
		$status = M(ucwords($table))->where($map)->save(array('status'=>'99','checktime'=>time()));
		return $status;
	}
	/**
     * 记录检票终端信息
     * @param $code 识别号
     * @param $type 监票终端类型
     * @param $status 状态
     * @param $info 请求数据
     * @param $data 返回数据
     */
    public function recordLogindetect($code, $type, $status, $info = "" , $data = "") {
        M("Checklog")->add(array(
            "code" => $code,
        	'type' => $type,
            "datetime" => time(),
            "ip" => get_client_ip(),
            "status" => $status,
            "info" => serialize($info),
        	"data" => serialize($data),
        ));
    }

}