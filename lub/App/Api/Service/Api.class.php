<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Service;
use Api\Service\Checkin;
class Api extends \Libs\System\Service {
	/**
     * 检票方法
     * Enter description here ...
     */
    private function checkTicket(){
    	
    }
    
    /**
     * 读取产品及价格信息
     * @param $proId array 产品ID集合
     */
    private function product($proId = false){
    	foreach ($proId as $key=>$val){
    		$info[$val] = D('Item/Product')->where(array('id'=>$val))->relation(true)->find();
    	}
    	return $info;
    }
    
    /**
     * 根据计划读取票型价格
     * @param $planid int 计划ID 
     */
    function planType($planid = false){
    	$info = D('Item/Plan')->where(array('id'=>$planid,'status'=>'2'))->find();
    	return $info;
    }
     
    /**
     *获取票型信息
     *@param $typeId array 票型ID集合 
     */
    function ticketType($typeId = false){
    	
    }
    
    /*
     * 第三方应用接入  写入订单
     * @param $appid int 第三方应用ID 
     * @param $info array 订单信息
     */
    function insert_order($appid,$info){
    	
    }

/*
 * 检票程序
 * zj
 * 2013-10-15

	function _initialize(){
		parent::_initialize();
		$this->_mod=D('TicketOrder');
		//$this->_mod=D('Order');
		//将当天日期缓存起来
		//缓存客户识别码
		
	} */
	/*检票客户端注册
	 * post 传递过来的参数
	 * itemsn 6位
	 * 422: 提交方式错误
	 * 433:商户识别号错误
	 * Ok：成功
	 * */
	function itre(){
		if (IS_POST) {
			$item=I('post.');
			if(!empty($item['itemsn'])){
				$where['Identifier']=$item['itemsn'];
				$status=D('Item')->where($where)->field('id,name,Identifier')->find();
				if($status){
					/*判断是否是新终端*/
					if (empty($item['tid'])) {
						$ttid=M("Terminal")->add(array(
							'item_id' => $status['id'],
							'name'	=> $status['name'].$status['id'],
							'create_time' => time(),
							'up_time' => time(),));
						$cookie=md5($item['itemsn'].$ttid.genRandomString(4));
						cookie('item',$cookie,array('expire'=>36000,));
						session('item_'.$item['tid'],$cookie);
						$this->recordLogindetect($status['id'], $item['itemsn'], 1, "检票客户端注册成功");
						$data=array(
							'state'=>"OK",
							'data' =>array('tid'=>$ttid),
						);
					  	echo json_encode($data);
					} else {
						$cookie=md5($item['itemsn'].$item['tid'].genRandomString(4));
						$d=array('up_time'=>time(),'key'=>$cookie,);
						M("Terminal")->where('id='.$item['tid'])->setField($d);
						cookie('item_'.$item['tid'],$cookie,array('expire'=>36000,));
						session('item_'.$item['tid'],$cookie);
						$this->recordLogindetect($status['id'], $item['itemsn'], 2, "检票客户端注册成功");
						$data=array(
							'state'=>"OK",
							'data' =>array(),
						);
					  	echo json_encode($data);
					}
					
				} else {
					$data=array(
						'state'=>"433",
						'data' =>array(),
					);
					$this->recordLogindetect(0, $item, 1, "识别码错误");
				  	echo json_encode($data);
				}
			} else {
				$data=array(
					'state'=>"433",
					'data' =>array(),
				);
				$this->recordLogindetect(0, 0, 2, "识别码为空");
				echo json_encode($data);
			}
		} else {
			$data=array(
				'state'=>"422",
				'data' =>array(),
			);
			$this->recordLogindetect($status['id'], $item['itemsn'], 1, "提交方式错误");
		  	echo json_encode($data);
		}
		
		
		
	}
	/*
	 * 返回状态码
	 * 0：订单号未获取
	 * 111：未授权客户端
	 * 101：未完成订单（未完成付款）
	 * 201：未找到订单（该该门票）
	 * 301：已使用门票 
	 * 401：非当日门票/非当日门票、过期门票
	 * 411：门票已过期
	 * 99：通过 OK：通过
	 * */
	function check(){
		if (IS_POST) {
			$kfc=I('post.');
		} else {
			$kfc=I('get.');
		}
		$key=session('item_'.$kfc['tid']);
		$name="item_".$kfc['tid'];
		$mid = $_COOKIE['chengde360_item_'.$kfc['tid']];
		if(!empty($kfc['tid']) && !empty($mid) && $key == $mid) {
			if (!empty($kfc['sn'])) {
				$sn = authcode($kfc['sn'],DECODE);
				//判断产品类型5需要验证时间3不需要验证时间
				$is_time=substr($sn,0,1);
				if ($is_time == 5) {
					//需要验证时间
					//这里可以先对日期进行判断 若不属于当天门票直接返回  不去数据库检索
					$datetime=substr(date('Ymd',time()),4) - substr($sn,3,4);
					if ($datetime == 0) { 
						$data=$this->jp($sn,$kfc['tid'],$key,'','',$kfc);
						echo json_encode($data);
					} else {
						if($datetime > 0){
							$data=array(//非当日门票
							    'state'=>"401",
								'data' =>array(),
							);
							$this->recordLogindetect($kfc['tid'], $key, 1, "非当日门票" ,$kfc);
							echo json_encode($data);
						} else {
							$data=array(//过期门票
							    'state'=>"411",
								'data' =>array(),
							);
							$this->recordLogindetect($kfc['tid'], $key, 1, "过期门票" ,$kfc);
							echo json_encode($data);
						}
					}	
				} else {
					//不需要验证时间
					$data=$this->jp($sn,$kfc['tid'],$key,'','',$kfc);
					echo json_encode($data);
				}
			} else {
				$data=array(//订单号未获取
					'state'=>"0",
					'data' =>array(),
				);
	  			echo json_encode($data);
			}
		} else {
			$data=array(
				'state'=>"111",
				'data' =>array(),
			);
			$this->recordLogindetect($kfc['tid'], $key, 1, "未授权客户端" ,$kfc);
	  		echo json_encode($data);
		}
	}
	/*
	 * 检票
	 * $sn 订单号
	 * */
	function jp($sn,$tid,$key,$type,$msg,$kfc){
		$where['order_sn']='wd'.$sn;
		$info=$this->_mod->where($where)->field('status')->find();
		if ($info) {
			switch($info['status']){
					case 101:
			  			$data=array(
							'state'=>"101",
							'data' =>array(),
						);
						$this->recordLogindetect($tid, $key, 1, "未完成订单" ,$kfc);
			  			return $data;
		  			break;
		  			case 201:
			  			$data=array(//这里可以增加判断是否可以提前入园，后台添加
			  				'state'=>"201",
							'data' =>array(),
						);
						$this->recordLogindetect($tid, $key, 1, "未找到订单" ,$kfc);
		  				return $data;
		  			break;
		  			case 301:
			  			$data=array(
							'state'=>"301",
							'data' =>array(),
						);
						$this->recordLogindetect($tid, $key, 1, "已使用门票" ,$kfc);
			  			return $data;
		  			break;
		  			case 99:
			  			$data=array(
							'state'=>"Ok",
							'data' =>array(
								'info'=>$info,
							),
						);
						//更新订单状态
						//$b=array('status' => '301','jd_time' => time());
						//减少门票可用次数
						$num=$info['available']-1;
						if($num == 0) {
							$status = '301';
						} else {
							$status = '99';
						}
						$b=array('status' => $status,'order_end' => time(),'available' => $num);
						$this->_mod->where($where)->setField($b);
						$this->recordLogindetect($tid, $key, 2, "检票成功" ,$kfc);
			  			return $data;
			  		break;
			}
		} else {
			//未找到定单
			$data=array(
				'state'=>"201",
				'data' =>array(),
				);
			$this->recordLogindetect($tid, $key, 1, "未找到定单" ,$kfc);
		  	return $data;
		}
	}
	/**
     * 记录检票终端信息
     * @param type $uid 商户ID
     */
    public function recordLogindetect($uid, $identifier, $status, $info = "" , $data = "") {
        M("Checklog")->add(array(
            "uid" => $uid,
        	'identifier' => $identifier,
            "time" => date("Y-m-d H:i:s"),
            "ip" => get_client_ip(),
            "status" => $status,
            "info" => $info,
        	"data" => implode('^',$data),
        ));
    }
}