<?php
// +----------------------------------------------------------------------
// | LubTMP 信任接口服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Common\Controller\LubTMP;
use Libs\Service\Api;
use Libs\Service\Order;
use Libs\Service\Zyb;
class ZhiyoubaoController extends LubTMP {
	//测试账号：admin 测试企业码：TESTFX  私钥：TESTFX
	//测试用票票型编码：PST20160918013085备注：测试环境用上述参数
	//信任地址验证
	function _initialize(){
		$this->config = [
			'API_BASE_URL_PREFIX' => 'http://ds-zff.sendinfo.com.cn/boss/service/code.htm',
			'CORP_CODE'		=>	'TESTFX',
			'PRIVATE_KEY'	=>	'TESTFX',
			'ACCOUNT'		=>	'admin',
			'PRODUCT_NO'	=>	'201704180000001893'
		];
	}
	//批量获取销售计划
	function get_all_plan(){
		//获取当前日期后7天的日期
		$i = 0;
		for ($i=0; $i < 4; $i++) {
			$day = date('Y-m-d',strtotime("+".$i." day"));dump($day);
			$this->get_query_plan($day);
		}
		
	}
	//场次查询SEND_THEATRE_ORDER_REQ
	function get_query_plan($time = ''){
		$data = [
			'transactionName' =>	'QUERY_THEATRE_SEASON_REQ',//接口的方法名
			'queryParam'	=>	[
				'showNo'	=> $this->config['PRODUCT_NO'],//节目编号
				//'occDate'	=> date('Y-m-d'),//时间
				'occDate'	=> $time,//时间
			],
		];
		$obj = new Zyb();
		$return = $obj->postServer($data);
		//dump($return);
		//一天有多场的情况 TODO
		if(!empty($return['sessions'])){
			//获取数据包
			$planlist = $return["sessions"]["showSessionGetDto"];
			if(!empty($planlist['theaterCode'])){
				$planlist = $return["sessions"];
			}
			//dump($planlist);
			foreach ($planlist as $k => $v) {
				//获取时间
				$time = explode('-',$v['palyDate']);
				//判断该场次是否在系统中已经存在
				if($this->if_plan($data['queryParam']['occDate'],$time[0],$time[1])){
					//构建场次
					$games = $this->get_or_games($time[0]);
					//获取产品ID
					$product_id = $this->get_or_product($v['theaterCode'],1);
					//插入销售计划
					$basePlan = [
						'product_id'	=> $product_id,
						'plantime' 		=> $data['queryParam']['occDate'],
						'starttime'		=> $time[0],
						'endtime'  		=> $time[1],
						'games'			=> $games,
						'product_type'	=> '1',//固定为剧院 TODO
						'template_id'	=> '23',//固定座椅模板 TODO
						'seat' 			=> ['151','152','153','161'],//固定座椅区域
						'ticket'		=> ['33','37','38','43','55','56','29','30','31','35','36','45','46','47','48','49','32','34','44'],//固定可售票型
						'goods'			=> '',
					];//dump($basePlan);
					
					$model = D('Item/Plan');
					$plan_id = $model->add_plan($basePlan);
					if($plan_id){
						//授权
						$model->auth($plan_id);
						//开始销售
						$model->where(array('id'=>$plan_id))->setField('status','2');
					}
				}else{
					echo "已入库";
				}
				/*动态调整库存
				$areaPrice = $return['sessions']['showSessionGetDto']['list']['canSaleDto'];
				foreach ($areaPrice as $key => $value) {
					//循环区域
					$area[] = [
						'zyb_area' => $value['areaCode'],
						'zyb_num'  => $value['num'],
						'zyb_name' => $value['areaName'],
					];
					//调用控座代码

				}*/
			}
		}
	}
	//终端动态库存
	function get_up_sku(){
		//读取当前在售场次，逐个查询远端库存，更新本地库存
	}
	/**
	 * 准换产品ID TODO
	 * @param  int $param 参数
	 * @param  int  $type  1代表第三方平台2代表系统产品ID
	 * @return [type]        [description]
	 */
	function get_or_product($param,$type){
		if($type == '1'){
			return '41';
		}else{
			return '109';
		}
	}
	/**
	 * 写入场次
	 */
	function get_or_games($starttime){
		$h = date('H',strtotime($starttime));
		if($h == 0){
			$h = '24';
		}
		if($h < '20'){
			return '1';
		}
		if($h >= '20' && $h < '21'){
			return '1';
		}
		if($h >= '21' && $h < '22'){
			return '2';
		}
		if($h >= '22' && $h < '23'){
			return '3';
		}
		if($h >= '23' && $h < '23'){
			return '3';
		}
	}
	//判断场次是否存在
	function if_plan($plantime,$starttime,$endtime)
	{
		$map = [
			'plantime'	=> strtotime($plantime),
			'starttime'	=> strtotime($starttime),
			'endtime'	=> strtotime($endtime)
		];
		$info = D('Item/Plan')->where($map)->select();
		if(empty($info)){
			return true;
		}else{
			return flase;
		}

	}
	//api写入销售计划
	//模拟下单
	function get_post_order(){

	}

	/**
	 * 云鹿订单同步智游宝订单
	 * 参数替换
	 */
	function yunlu_to_zyb(){
		$data = [
			'transactionName' =>	'SEND_THEATRE_ORDER_REQ',//接口的方法名
			/*
			'certificateNo'	  =>	$info['id_card'],//身份证号
			'linkName'		  =>	$info['take'],//联系人必填
			'linkMobile'	  =>	$info['phone'],//必填联系人电话
			'orderCode'		  =>	$info['order_sn'],//订单号，
			'orderPrice'	  =>	$info['money'],//订单总价格
			'payMethod'		  => 	'vm',//支付方式值spot现场支付vm备用金，zyb智游宝支付
			*/
			'orderRequest'	  => 	[
				'order'			  =>	[
					'certificateNo' => $info['id_card'],
					'linkName' => $info['take'],
					'linkMobile' => $info['phone'],
					'orderCode' => $info['order_sn'],
					'orderPrice' => $info['money'],
					'payMethod' => 'vm',
					'ticketOrders'=>	[
						'ticketOrder' =>[
							'orderCode'	=>	$info['order_sn'],//必填你们的子订单编码
							'totalPrice'=>	$info['money'],//必填子订单总价
							'price'		=>	'1',//票价，必填，线下要统计的
							'quantity'	=>	'1',//必填票数量
							'occDate'	=>	'22:30-23:30',//游玩日期
							'goodsCode'	=>	'康熙大典2016',//商品名称
							'goodsName'	=>	'PTT20170418014854',//必填 商品编码，同票型编码
						]
					],
					'showNo'	=>	'201704180000001893',////节目编号
					'sessionCode'	=> '00001099',//场次编号
					'palyTime'	=>	'22:30-23:30',//开演时间
				]
			]
		];
	}

	//查询订单
	function get_query_order($sn = ''){
		$data = [
			'transactionName' =>	'QUERY_ORDER_REQ',//接口的方法名
			'orderRequest'	=>	[
				'order'	=>	[
					'orderCode'	=>	$sn,//你们的主订单编码
				]
			],
		];
		$return = $this->postServer($data);
		if($return != flase){
			//入库操作

		}
		dump($return);
	}
	//退单接口
	function refund_order(){
		$data = [
			'transactionName' =>	'SEND_CODE_CANCEL_NEW_REQ',//接口的方法名
			'orderRequest'	=>	[
				'order'	=>	[
					'orderCode'	=>	$sn,//你们的主订单编码
				]
			],
		];
		$return = $this->postServer($data);
		if($return != flase){
			//入库操作

		}
	}
	//退部分门票
	function part_refund_ticket(){
		$data = [
			'transactionName' =>	'SEND_CODE_CANCEL_NEW_REQ',//接口的方法名
			'orderRequest'	=>	[
				'returnTicket'	=>	[
					'orderCode'	=>	$sn,//你们的主订单编码
					'returnNum'	=>	'',//退票数量
					'thirdReturnCode' => '',//退单单号
				]
			],
		];
		$return = $this->postServer($data);
		if($return != flase){
			//入库操作

		}
	}

}
