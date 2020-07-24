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
use Libs\Service\Refund;
use Libs\Service\Operate;
class TrustController extends LubTMP {
	//信任地址验证
	function _initialize(){
		
		//parent::_initialize();

	}
	function index(){
		// $sn = 'GEAoY4vbAUwJSzINc84LwQVv';
		// // $wechat = & load_wechat('Extends',169,1);dump($wechat);
 	// // 	$snTosecret = putIdToCode($sn, 8);
		// $lurl = U('Api/Index/ticket',['tid'=>$snTosecret]);
  // //       $surl = $wechat->getShortUrl($lurl);
  // 		$code = 'iFZmv5ERfdffsJG1Fy9ZdL&n6KQR7OlKGvLXVOR1To&Y#yWw3db3H1vVP#&iV';
  //       dump(getCodeToId($sn, 24, $code));

        vendor('jsonRPC.jsonRPCClient');
        $client = new \jsonRPCClient('https://api.msg.alizhiyou.cn');
        $param = [
        	[
        		'type'      =>  'remind',//briefing
        		'openid'	=>	'ok-_50YZeFs8MA64CM2i7zhL1xj8',
        		'content'   => [
	                'first'  => [
	                    'value' => '您有新的待处理订单,请您尽快前往云鹿票券综合管理平台处理',
	                    'color' => '#4285f4'
	                ],
	                'keyword1'  => [
	                    'value' => '1221211',
	                    'color' => '#4285f4'
	                ],
	                'keyword2'  => [
	                    'value' => '讯洲科技 周靖',
	                    'color' => '#4285f4'
	                ],
	                'keyword3'  => [
	                    'value' => '超量申请 2019年12月31日第二场普通票10张',
	                    'color' => '#4285f4'
	                ],
	                'keyword4'  => [
	                    'value' => date('Y-m-d H:i'),
	                    'color' => '#4285f4'
	                ],
	                'remark' =>  [
	                    'value' => '',
	                    'color' => '#4285f4'
	                ]
	            ]
        	]
        ];
        $result = $client->index('/msg/sendMsg', $param);
        var_dump($result,'11'); // 结果：Hello, JsonRPC!
        // $result = $client->test('ThinkPHP');
        // var_dump($result); // 结果：Hello, ThinkPHP!
    }
	//信任计划获取
	function get_plan(){
		$ginfo = I('get.');
		
		switch ($ginfo['type']) {
			case '1':
				//获取当天的场次
				$map = array(
					'status' => '2',
					'plantime'=>strtotime(date('Ymd')),
				);
				break;
			case '2':
				//获取指定日期的场次
				$map = array(
					'status' => '2',
					'plantime'=>strtotime($ginfo['datetime']),
				);
				break;
			case '3':
				//获取所有可售场次
				$map = array(
					'status' => '2',
				);
				break;
		}
		if(empty($ginfo['price'])){
			$plan = M('Plan')->where($map)->field('id,plantime,starttime,endtime,product_id,games')->select();
		}else{
			//获取带销售价格的场次
			//构造查询条件
			$proconf = cache('ProConfig');
			$proconf = $proconf[$ginfo['product']]['1'];
			$info = array(
				'scene' => $ginfo['scene'], 
				'product'=> $ginfo['product'],
				'group'=>array('price_group'=>$proconf['web_price']));
			$plans = \Libs\Service\Api::plans($info);

			foreach ($plans['plan'] as $key => $value) {
	            $plan['plan'][] = array(
	                'title' =>  $value['title'],
	                'id'    =>  $value['id'],
	                'num'   =>  $value['num'],
	                'pid'	=>	$value['product_id'],
	            );
	            $plan['area'][$value['id']] = $value['param'];
	        }
		}
		$return = array(
			'status' => '200',
			'info'	 => $plan,
			'msg'	 => 'ok',
			);
		$this->ajaxReturn($return);
	}

	//信任获取产品
	function get_product(){
		$ginfo = I('get.');
		//获取当天的场次
		$map = array(
			'status' => '1',
			'type'=>'1',
		);
		$product = M('Product')->where($map)->select();
		$return = array(
			'status' => '200',
			'info'	 => $product,
			'msg'	 => 'ok',
		);
		$this->ajaxReturn($return);
	}
	//阿里智游的订单
	function alizhiyou_order()
	{
		$info = I('post.info');
		load_redis('setex','indd', json_encode($info), 3700);
		$uInfo = ['id'=>'-1'];
		$order = new Order();
        $sn = $order->orderApi($info,'52',$uInfo);
        if($sn){
          $return = array(
          	'status'=> true,
            'code'  => 200,
            'data'  => [
            	'sn' => $sn,
            	'seat'  => sn_seat($sn['order_sn']),
            ],
            'msg'   => 'OK',
          );
        }else{
          $return = array('status'=>false,'code' => 403,'data'=>'' ,'msg' => $order->error);
        }
        die(json_encode($return));
	}
	//景区订单
	function alizhiyou_scenic_order(){
		$info = I('post.info');
		$uInfo = ['id'=>'-1'];
		$order = new Order();
        $sn = $order->orderApi($info,'52',$uInfo);
        if($sn){
          $return = array(
          	'status'=> true,
            'code'  => 200,
            'data'  => [
            	'sn' => $sn,
            ],
            'msg'   => 'OK',
          );
        }else{
          $return = array('status'=>false,'code' => 403,'data'=>'' ,'msg' => $order->error);
        }
        die(json_encode($return));
	}
	//订单查询
	function alizhiyou_detail(){
		$return = array(
	      	'status'=> true,
	        'code'  => 200,
	        'data'  => [
	        	'sn' => $sn,
	        	'seat'  => sn_seat($sn),
	        ],
	        'msg'   => 'OK',
	     );
		die(json_encode($return));
	}
	//退单
	function alizhiyou_refund()
	{
		$pinfo = I('post.info');
		$uInfo = ['id'=>'-1'];
		$info = D('Item/Order')->where(['order_sn'=>$pinfo['sn']])->relation(true)->find();
		//增加场次时间判断 开演后就不让提交退单申请
		if(if_plan($info['plan_id']) == false){
			$return = array(
		      	'status'=> false,
		        'code'  => 300,
		        'data'  => [
		        ],
		        'msg'   => '抱歉，该场次或该订单状态不允许此项操作!',
		    );
			die(json_encode($return));
		}else{
			$model = new \Think\Model();
			$model->startTrans();
			if($info["status"] == '1' || $info["status"] == '5'){
				//在lub_ticket_refund表中添加一条数据,记录申请
				$data = array(
					"createtime" => time(),
					"order_sn"   => $ginfo["sn"],
					"applicant"  => '-1',
					"crm_id"     => $info["channel_id"],
					"plan_id"    => $info["plan_id"],
					"reason"     => $pinfo['reason'],//退单原因
					"re_type"    => 5,//api接口
					"status"     => 1,
					"money"      => $info["money"],
					"launch"     => 2,
					"order_status" => '1'
				);
				$add = $model->table(C('DB_PREFIX')."ticket_refund")->add($data);
				//修改lub_order表的status字段
				$order_info = array(
					"id" => $info["id"],
					"status"   => $info['status'] == '1' ? '7' : '8',
				);
				$up = $model->table(C('DB_PREFIX')."order")->save($order_info);
				
				if($add && $up){
					$model->commit();//成功则提交
					$return = array(
				      	'status'=> true,
				        'code'  => 200,
				        'data'  => [],
				        'msg'   => '抱歉，退单申请成功,等待确认!',
				    );
					die(json_encode($return));
				}else{
					$model->rollback();//不成功，则回滚
					$return = array(
				      	'status'=> false,
				        'code'  => 300,
				        'data'  => [
				        ],
				        'msg'   => '抱歉，退单申请失败!',
				    );
					die(json_encode($return));		
				}				
			}else{
				$return = array(
			      	'status'=> false,
			        'code'  => 300,
			        'data'  => [],
			        'msg'   => '抱歉，该场次或该订单状态不允许此项操作!',
			    );
				die(json_encode($return));				
			}
		}

	}
	/*下单*/
	function insert_order(){
		if(IS_POST){
			//官网
			$scena = '31';
			$scena = Order::is_scena($scena);
			//构造用户
			$uInfo = array(
                'id' => 3,
                'guide'  => '0',
                'qditem' => '0',
                'scene'  => '31'
            );
            $sn = Order::quick_order(I('post.'),$scena,$uInfo,2);
            $return = array(
				'status' => '200',
				'info'	 => $sn,
				'msg'	 => 'ok',
			);
			$this->ajaxReturn($return);
		}
	}
	/*支付完成排座*/
	function pay_suess_seat(){
		$ginfo = I('get.');
		$oinfo = D('Item/Order')->where(array('order_sn'=>$ginfo['sn']))->relation(true)->find();
        if(empty($oinfo)){return false;}
        if($oinfo['status'] == '2'){
            //构造配置数组
            $info = array(
                'seat_type' =>  $ginfo['seat_type'],//立即排座
                'pay_type'  =>  $ginfo['pay_type'],//支付宝支付
            );
            $status = Order::mobile_seat($info,$oinfo); 
        }
        if($status){$info = '1';}else{$info = '0';}
        $return = array(
			'status' => '200',
			'info'	 => $info,
			'msg'	 => 'ok',
		);
		$this->ajaxReturn($return);
	}
	//校验传递过来的数据
    function format_seat($pinfo,$appInfo){
        $plan = F('Plan_'.$pinfo['plan']);
        //重组座位
        $seat = Order::area_group($pinfo['oinfo'],$plan['product_id'],$appInfo['group']['settlement']);
        
        $ticketType = F("TicketType".$plan['product_id']);
        foreach ($seat['area'] as $k => $v) {
          foreach ($v['seat'] as $ke => $va) {
            $price = $ticketType[$va['priceid']];
            $money += $va['num']*$price['price'];
            $moneys += $va['num']*$price['discount'];
          }
        }
        if(bccomp((float)$pinfo['money'],(float)$moneys,2) == 0){
          return $seat;
        }else{
          return false;
        }
    }
    //年卡办理
    

	/*自助机*/
	/*处理对接数据*/
	function dealpre(){
		\Libs\Service\Rebate::ajax_rebate_order();
		//轮询待发送的模板消息 TODO
		$tplMsg = new \Wechat\Controller\NotifyController;
		$tplMsg->fillToMsg();
		return true;
	}
	/**
	 * 校验返利
	 */
	function check_rebate(){
		$ln = load_redis('lsize','PreOrder');
		for ($i=0; $i < $ln; $i++) { 
			dump(\Libs\Service\Rebate::ajax_rebate_order());
		}
		dump($ln);
		// $where['type'] = array('in','2,4,8,9');$where['status']=array('in','1,9,7,8');
		// //\Libs\Service\Check::check_rebate($where,2);
		// //return \Libs\Service\Check::check_rebate();
	}
	/*校验程序*/
	function check()
	{
		if(IS_POST){
			//远端手动执行
			$pinfo = I('post.');
			if (!empty($pinfo['start_time']) && !empty($pinfo['end_time'])) {
	            $start_time = strtotime($pinfo['start_time']);
	            $end_time = strtotime($pinfo['end_time']) + 86399;
	            $where['createtime'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
	        }
	        $where['type'] = array('in','2,4,8,9');$where['status']=array('in','1,9,7,8');
			\Libs\Service\Check::check_rebate($where,2);
			return '200';
		}else{
			//校验返利
			\Libs\Service\Check::check_rebate();
			//校验微信支付排座情况
			\Libs\Service\Check::check_pay_order_seat();
			//校验微信返利的情况
			\Libs\Service\Check::check_red();
			return true;	
		}
	}
	/**
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2019-10-07T16:13:10+0800
	 * @return  微信支付校验
	 */
	public function wx()
	{
		$list = D('Order')->where(['pay'=>5,'status'=>11])->field('order_sn,product_id')->limit(700)->order('id DESC')->select();

		foreach ($list as $k => $v) {
			$pay = & load_wechat('Pay', $v['product_id']);
			//dump($pay->errMsg);
			$reslut = $pay->queryOrder($v['order_sn']);
			dump($reslut);
			if($reslut['return_code'] === 'SUCCESS' && $reslut['result_code'] === 'SUCCESS'){
				$this->uporder($v['order_sn'], $reslut['transaction_id']);
			}
		}

	}
	public function uporder($sn, $transaction)
	{
		$uppaylog = array('status'=>1, 'out_trade_no' => $transaction);
        $paylog = D('Manage/Pay')->where(array('order_sn'=>$sn,'type'=>2))->save($uppaylog);
        $orderMap = [
            'order_sn'=> $sn,
            'status'  => ['in',['11','2']],
        ];
        $oinfo = D('Item/Order')->where($orderMap)->relation(true)->find();
        //dump($oinfo);
        if(!empty($oinfo)){
            $info = array(
                'seat_type' => '1',
                'pay_type'  => '5'
            );
            $order = new \Libs\Service\Order;
            $status = $order->mobile_seat($info,$oinfo);
            return $status;
        }else{
            $status = true;
        }
	}

	/**
   * 实名认证时展示详情
   * @Author   zhoujing                 <zhoujing@leubao.com>
   * @DateTime 2020-06-04T14:40:31+0800
   * @return   [type]                   [description]
   */
  public function getTicketInfo()
  {
  	// header('Access-Control-Allow-Origin: *');
  	// header('Access-Control-Allow-Credentials: true');
  	// header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); //允许的请求类型
   //  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
   	$ginfo = I('get.');
    if(empty($ginfo['qr'])){
      $return = [
        'status'=> false,
        'code'  => 401,
        'data'  => [],
        'msg'   => '参数有误~'
      ];
      die(json_encode($return));
    }
    $content = $ginfo['qr'];//substr($gifno['qr'],-24,24);
    $qr = \Libs\Service\Encry::getQrData($content);
    if(!$qr){
      $return = [
        'status'=> false,
        'code'  => 401,
        'data'  => [],
        'msg'   => '未找到有效门票~'
      ];
      die(json_encode($return));
    }
    //读取销售计划
    $plan = F('Plan_'.$qr[2]);
    if(empty($plan)){
      $return = [
        'status'=> false,
        'code'  => 401,
        'data'  => $qr,
        'msg'   => '该场次已停用~'
      ];
      die(json_encode($return));
    }
    //读取门票详情
    $ticket = D($plan['seat_table'])->where(['id'=>$qr[0]])->field('order_sn,seat,idcard,price_id,print,area,sale,status,soldtime,checktime,number')->find();
    $sale = unserialize($ticket['sale']);  
    if(empty($ticket)){
      $return = [
        'status'=> false,
        'code'  => 401,
        'data'  => [],
        'msg'   => '未找到有效门票~'
      ];
      die(json_encode($return));
    }
    if($ticket['status'] <> 99){
      if(empty($ticket['idcard'])){
        $is_real = false;
      }else{
        $is_real = true;
      }
      $data = [
          'is_real'=> $is_real,
          'serial' => $content,//二维码信息
          'plan'   => planShow($qr[2], 4, 1),
          'sn'     => $ticket['order_sn'],
          'price'  => $sale['price'],
          'idcard' => $is_real ? substr_replace($ticket['idcard'], '**********',6,10) : '',
          'status' => seat_status($ticket['status'],1),
          'area'   => isset($ticket['area']) ? areaName($ticket['area'],1) : '',
          'seat'   => isset($ticket['area']) ? seatShow($ticket['seat'],1) : '',
          'number' => $ticket['number'] ? $ticket['number'] : '1',
          'act'    => '',//活动票
          'team'   => '',//团队
          'object' => ''//票型
        ];
      $return = [
        'status'=> true,
        'code'  => 0,
        'data'  => $data,
        'msg'   => 'ok'
      ];
      die(json_encode($return));
    }else{
      $return = [
        'status'=> false,
        'code'  => 401,
        'data'  => [],
        'msg'   => '门票已核销~'
      ];
      die(json_encode($return));
    }
  }
  //实名制绑定
  public function realBinding()
  {
	$pinfo = I('post.');
	$qr = \Libs\Service\Encry::getQrData($pinfo['qr']);
	//读取销售计划
    $plan = F('Plan_'.$qr[2]);
    if(empty($plan)){
      $return = [
        'status'=> false,
        'code'  => 401,
        'data'  => [],
        'msg'   => '该场次已停用~'
      ];
      die(json_encode($return));
    }
	//判断是否重复
	if(checkIdCard($pinfo['idcard'])){
		$map = ['idcard'=>$pinfo['idcard'],'plan'=>$qr[2]];
		if(verifyIdCard($map,2)){
			$order = D('Order')->where(['id'=>$qr[1]])->field('id,order_sn,activity')->find();
			if(empty($order)){
				$return = [
					'status'=> false,
					'code'  => 404,
					'data'  => $qr,
					'msg'   => '订单获取失败~'
				];
				die(json_encode($return));
			}
			$ticket = D($plan['seat_table'])->where(['id'=>$qr[0]])->setField('idcard', $pinfo['idcard']);
			if(!$ticket){
				$return = [
					'status'=> false,
					'code'  => 401,
					'data'  => [],
					'msg'   => '身份证绑定失败~'
				];
				die(json_encode($return));
			}
			$insert = [
				'plan_id'       =>  $qr[2],
				'order_sn'      =>  $order['order_sn'],
				'idcard'        =>  $pinfo['idcard'],
				'number'        =>  1,
				'activity_id'   =>  $order['activity']
			];
			$state = D('IdcardLog')->add($insert);
			if($state){
				$return = [
			    	'status'=> true,
			    	'code'  => 0,
			    	'data'  => $state,
			    	'msg'   => '完成绑定~'
				];
				die(json_encode($return));
			}else{
				$return = [
					'status'=> false,
					'code'  => 401,
					'data'  => [],
					'msg'   => '身份证绑定失败~'
				];
				die(json_encode($return));
			}
		}else{
			$return = [
				'status'=> false,
				'code'  => 401,
				'data'  => [],
				'msg'   => '该身份证已完成绑定~'
			];
			die(json_encode($return));
		}
	}else{
		$return = [
			'status'=> false,
			'code'  => 401,
			'data'  => [],
			'msg'   => '身份证号码有误~'
		];
		die(json_encode($return));
	}
  }
}