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
		
		parent::_initialize();
		/*获取请求的url 
		$get_url = get_url();
		//不成功返回错误
		dump($get_url);
		//获取已经配置的url
		if($get_url){
			$return = array(
				'status'=>	'0',
				'msg'	=>	'不合法请求',
			);
			$this->ajaxReturn($return);
		}*/
	}
	function index(){
		echo "string";
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
		$where['type'] = array('in','2,4,8,9');$where['status']=array('in','1,9,7,8');
		//\Libs\Service\Check::check_rebate($where,2);
		//return \Libs\Service\Check::check_rebate();
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
}