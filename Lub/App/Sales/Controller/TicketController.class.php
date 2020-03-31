<?php
// +----------------------------------------------------------------------
// | LubTMP 全员/三级销售  佣金管理
// | 商户支付方式1打卡2支付宝转账3财务取现4微信企业转账5微信红包
// |状态1提现成功3待审核4驳回5微信红包待领取
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Sales\Controller;
use Common\Controller\ManageBase;
class TicketController extends ManageBase{
	protected function _initialize() {
        parent::_initialize();
    }
	public function cancel(){
	    if(IS_POST){
	      	$pinfo = I('post.');
		    switch ($pinfo['type']) {
	            case 'sn':
	                $map = ['order_sn' => $pinfo['code']];
	                break;
	            case 'qr':
	                $qrInfo = \Libs\Service\Encry::getQrData($pinfo['code']);
	                if(!$qrInfo){
	                    $return = ['status'=> false, 'code'  => 1000, 'data' => ['count'=>0], 'msg'   => '数据校验失败~'];
	                    die(json_encode($return)); 
	                } 
	                $map = ['id' => $qrInfo[1]];
	                break;
	            case 'mobile':
	                $map = ['phone' => $data['code']];
	                break;
	        }
	      
	        $order = D('Order')->where($map)->field('take,phone,order_sn,plan_id,number,status')->find();
		    if(empty($order)){
			  $return = [
			    'status'=> false,
			    'code'  => 1000,
			    'data'  => [],
			    'msg'   => '未找到有效订单信息'
			  ];
			  die(json_encode($return)); 
		    }
		    $count = D('Scenic')->where(['order_sn'=>$order['order_sn'],'status'=>2])->count();
		    if($count > 0){
			  $ticket = D('Scenic')->where(['order_sn'=>$order['order_sn'],'status'=>2])->field('order_sn,price_id,ciphertext,plan_id,id')->select();
			  if(!empty($ticket)){
			    foreach ($ticket as $k => $v) {
			      $tickets[] = [
			        'sn'    =>  $v['order_sn'],
			        'price' =>  ticketName($v['price_id'],1),
			        'plan'  =>  planShow($v['plan_id'],2,1),
			        'ticket'=>  $v['ciphertext'], 
			        'id'    =>  $v['id'],
			      ];
			    }
			  }
			}
			$return = [
			  'status'=> true,
			  'code'  => 0,
			  'data'  => [
			    'sn'    => $order['order_sn'],
			    'plan'  => planShow($order['plan_id'],2,1),
			    'number'=> $order['number'],
			    'count' => $count,
			    'contact' => [
			    	'user'	=> $order['take'],	
			    	'mobile'=> $order['phone']
			    ],
			    'ticket'=> $tickets ? $tickets : []
			  ]
			];
			die(json_encode($return));
	    }else{
	      // 今日核销数 TODO后期移到核销面板
	      
	      $this->display();
	    }
  	}
  	public function checkin()
  	{
	    if(IS_POST){
	      $pinfo = I('post.');
	      $upTicket = [
	        'status'    =>  99,
	        'checktime' =>  time()
	      ];
	      if($pinfo['type'] == 'all'){
	        //全部核销
	        $ticket = D('Scenic')->where(['order_sn'=>$pinfo['sn'],'status'=>2])->setField($upTicket);
	        $order = D('Order')->where(['order_sn'=>$pinfo['sn']])->setField('status',9);
	        if($ticket && $order){
	          $count = D('Scenic')->where(['order_sn'=>$pinfo['sn'],'status'=>99])->count();
	          $return = [
	            'status'=> true,
	            'code'  => 0,
	            'data'  => [
	              'count' => $count,
	            ],
	            'msg'   =>  '成功核销'.$ticket.'张'
	          ];
	          die(json_encode($return));
	        }else{
	          $return = [
	            'status'=> false,
	            'code'  => 1000,
	            'data'  => [
	              'count' => $count,
	            ],
	            'msg'   =>  '核销失败,请重试'
	          ];
	          die(json_encode($return));
	        }
	      }
	      if($pinfo['type'] == 'small'){
	        
	        foreach ($pinfo['info'] as $k => $v) {
	          $ticId[] = $v['id'];
	        }
	        $id = implode(',',$ticId);
	        $where = [
	          'order_sn'  =>  $pinfo['sn'],
	          'id'        =>  ['in',$id]
	        ];
	        $count = D('Scenic')->where(['order_sn'=>$pinfo['sn'],'status'=>2])->count();
	        if($count == 0){
	          $return = [
	            'status'=> false,
	            'code'  => 1000,
	            'data'  => [
	              'count' => $count,
	            ],
	            'msg'   =>  '核销失败,未找到可核销的门票'
	          ];
	          die(json_encode($return));
	        }
	        $number = count($pinfo['info']);
	        $ticket = D('Scenic')->where($where)->setField($upTicket);
	        if($count == $number){
	          $order = D('Order')->where(['order_sn'=>$pinfo['sn']])->setField('status',9);
	        }
	        $more = $count - $number;
	        if($ticket){
	          $return = [
	            'status'=> true,
	            'code'  => 0,
	            'data'  => [
	              'count' => $number,
	            ],
	            'msg'   =>  '成功核销'.$number.'张,剩余可核销'.$more."张"
	          ];
	          die(json_encode($return));
	        }else{
	          $return = [
	            'status'=> false,
	            'code'  => 0,
	            'data'  => [],
	            'msg'   =>  '核销失败,请重试'
	          ];
	          die(json_encode($return));
	        }
	      }
	    }
  	}
  	//核销记录
  	public function logs()
  	{	
  		$starttime = I('starttime') ? I('starttime') : date('Y-m-d',time());
	    $endtime = I('endtime') ? I('endtime') : date('Y-m-d', strtotime('+1 day'));
	    $sn = I('sn');
	    $status = I('status') ? I('status') : '99';
	    $plan_id = I('plan_id');
	    $plan_name = I('plan_name');
	    $this->assign('starttime',$starttime)
	        ->assign('endtime',$endtime)
	        ->assign('plan_id',$plan_id)
	        ->assign('plan_name',$plan_name)
	        ->assign('status',$status);
        if(!empty($sn)){
        	$map['order_sn'] = array('like','%'.$sn.'%');
        }else{
        	if(!empty($plan_id)){
				$map['plan_id'] = $plan_id;
				$export_map['plan_id'] = $plan_id;
        	}else{
        		if (!empty($starttime) && !empty($endtime)) {
		            $starttime = strtotime($starttime);
		            $endtime = strtotime($endtime) + 86399;
		            $map['checktime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
		        }else{
		        	//默认显示当天的订单
		        	$starttime = strtotime(date("Ymd"));
		            $endtime = $starttime + 86399;
		        	$map['checktime'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
		        }
        	}
	        if(!empty($status)){
	        	$map['status'] = $status;
	        }
	        
	        
        }
        $map['product_id'] = get_product('id');
        //dump($map);
  		$this->basePage('Scenic', $map, array('id'=>'DESC'));
		$this->display();
  	}
}