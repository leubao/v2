<?php
// +----------------------------------------------------------------------
// | LubTMP 阿里智游信任接口
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Trust\Controller;

use Common\Controller\TrustBase;
use Libs\Service\Order;
use Libs\Service\Refund;
use Libs\Service\ArrayUtil;
class MpcController extends TrustBase{

	protected function _initialize() {
        parent::_initialize();
    }

    /**
     * 获取销售计划
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-08T12:28:31+0800
     * @return   [type]                   [description]
     */
    public function get_plan_list()
    {
    	$pinfo = I('post.');
    	if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
    		return showReturnCode(false,1003);
    	}
    	$product = $this->getProduct($pinfo['incode']);
    	$model = D('Item/Plan');
    	$map = [
    		'product_id' => $product['id'],
    		'plantime'	 => ['egt', strtotime(date('Y-m-d'))]
    	];
    	$list = $model->where($map)->field('id,games,plantime,starttime,endtime,product_type,status')->order('plantime DESC')->select();
    	return showReturnCode(true,0, $list, 'ok');
    }
    /**
     * 获取销售详情
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-08T14:25:24+0800
     * @return   [type]                   [description]
     */
    public function get_plan_info()
    {
    	$pinfo = I('post.');
    	if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
    		return showReturnCode(false,1003);
    	}
    	if(!isset($pinfo['id']) || empty($pinfo['id'])){
    		return showReturnCode(false,1003);
    	}
    	$product = $this->getProduct($pinfo['incode']);
    	$model = D('Item/Plan');
    	$map = [
    		'product_id' => $product['id'],
    		'id'	 => $pinfo['id']
    	];
    	$info = $model->where($map)->find();
        if(empty($info)){
            return showReturnCode(false,1004, [], '用户名或密码错误~');
        }
        if($info){
            $info['param'] = unserialize($info['param']);
            //票型价格信息
            $ticket = D('Item/TicketGroup')->relation(true)->where(array('product_id'=>$info['product_id'],'status'=>'1'))->select();
            
            //区域
            $area = D('Area')->where(['template_id' => $product['template_id']])->field('id,name')->select();
            $info['ticket'] = $ticket;
            $info['area'] = $area;
        }
    	return showReturnCode(true,0, $info, 'ok');
    }
    /**
     * 新增销售计划初始化数据
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-08T14:36:42+0800
     * @return   [type]                   [description]
     */
    public function get_plan_init()
    {
    	$pinfo = I('post.');
    	if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
    		return showReturnCode(false,1003);
    	}
    	$product = $this->getProduct($pinfo['incode']);
    	$model = D('Item/Plan');
    	$init = $model->create_plan_init($product);
    	$init['product'] = $product;
    	return showReturnCode(true,0, $init, 'ok');
    }
    /**
     * 新增计划
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-08T12:29:09+0800
     * @return   [type]                   [description]
     */
    public function post_plan()
    {
    	$pinfo = I('post.');
    	if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
    		return showReturnCode(false,1003);
    	}
    	$field = ['plantime','games','starttime','endtime'];
    	
    	$product = $this->getProduct($pinfo['incode']);
    	$model = D('Item/Plan');
        //校验票型编码是否都存在
        $ifTicket = false;
        $ticket = D('TicketType')->where(['product_id'=>$product['id'],'status'=>1])->field('id')->select();
        $ticket = array_column($ticket, 'id');
        foreach ($pinfo['ticket'] as $k => $v) {
            if(!in_array($v, $ticket)){
                $ifTicket = true;
                break;
            }
        }
        if($ifTicket){
            return showReturnCode(false,1007, [], '新增失败,存在过期票型,请重新加载~');
        }
        //判断产品类型
        if((int)$product['type'] === 1){
            $ifArea = false;
            $area = D('Area')->where(['status'=>1,'template_id'=>$product['template_id']])->field('id')->select();
            $area = array_column($area, 'id');
            if(empty($area)){
                return showReturnCode(false,1007, [], '新增失败,存在未授权区域,请重新加载~');
            }
            foreach ($pinfo['seat'] as $k => $v) {
                if(!in_array($v, $area)){
                    $ifArea = true;
                    break;
                }
            }
            if($ifArea){
                return showReturnCode(false,1007, [], '新增失败,存在未授权区域,请重新加载~');
            }
        }
    	$data = [
    		'batch'			=>	'one',
    		'user_id'		=>	$pinfo['user_id'],
    		'plantime'		=>	$pinfo['plantime'],
    		'games'			=>	$pinfo['games'],
    		'starttime'		=>	$pinfo['starttime'],
    		'endtime'		=>  $pinfo['endtime'],
    		'product_id'	=>	$product['id'],
    		'product_type'	=>	$product['type'],
            'template_id'   =>  $product['template_id'],
    		'ticket'		=>	$pinfo['ticket'],
    		'seat'			=>	$pinfo['seat'],
    		'goods'			=>	[]
    	];
    	$state = $model->add_plan($data);
    	if($state){
    		return showReturnCode(true,0, [], 'ok');
    	}else{
    		return showReturnCode(false,1007, [], '新增失败~');
    	}
    }
    /**
     * 更新状态
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-08T12:30:04+0800
     * @return   [type]                   [description]
     */
    public function up_plan_state()
    {
    	$pinfo = I('post.');
    	if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
    		return showReturnCode(false,1003);
    	}
    	if(!isset($pinfo['id']) || empty($pinfo['id'])){
    		return showReturnCode(false,1003);
    	}
    	$product = $this->getProduct($pinfo['incode']);
    	$model = D('Item/Plan');
    	$map = [
    		'product_id' => $product['id'],
    		'id'	 => $pinfo['id']
    	];
    	$info = $model->where($map)->field('id,product_id,product_type,status')->find();
        if($info['status'] == '3'){
            $procof = cache('ProConfig');
            //判断是否开启配额
            if($procof['quota'] == '1' && $info['product_type'] <> '1'){
                $count = M('QuotaUse')->where(array('plan_id'=>$info['id']))->count();
                if($count == '0'){
                    \Libs\Service\Quota::reg_quota($info['id'],$info['product_id']);
                }
            }
            //暂停中开始销售
            $status = '2';
        }elseif($info['status'] == '2'){
            //售票中暂停销售
            $status = '3';
            F('Plan_'.$id,null);
        }else{
            return showReturnCode(false,1003, [], '计划状态不允许此项操作~');
        }
        if($model->where(array('id'=>$info['id']))->setField('status',$status)){
            $model->toAlizhiyouPlan($info['id'], $info['product_id']);
            $model->plan_cache();
            return showReturnCode(true,0, [], 'ok');
        }else{
            return showReturnCode(false,1001, [], '更新失败~');
        }
    }
    /**
     * 更新票型
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-08T16:30:56+0800
     * @return   [type]                   [description]
     */
    public function up_plan_ticket()
    {
    	$pinfo = I('post.');
    	if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
    		return showReturnCode(false,1003);
    	}
    	if(!isset($pinfo['id']) || empty($pinfo['id'])){
    		return showReturnCode(false,1003);
    	}
    	$product = $this->getProduct($pinfo['incode']);
    	$model = D('Item/Plan');
    	$map = [
    		'product_id' => $product['id'],
    		'id'	 	 => $pinfo['id']
    	];
    	$info = $model->where($map)->find();
        $param = unserialize($info['param']);
        $param['ticket'] = $pinfo['ticket'];
        $state = D('Plan')->where(['id'=>$pinfo['id']])->setField('param',serialize($param));
        if($state){
            return showReturnCode(true,0, [], 'ok');
        }else{
            return showReturnCode(false,1001, [], '更新失败~');
        }
    }

    /***********************************订单处理**********************************************/
    /**
     * 待审核订单
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-09T11:38:41+0800
     */
    public function get_audit_order()
    {
        $pinfo = I('post.');
        if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
            return showReturnCode(false,1003);
        }
        $product = $this->getProduct($pinfo['incode']);
        $model = D('Item/Order');
        $map = [
            'product_id' => $product['id'],
            'status'     => array('in',['5','6'])
        ];
        $list = $model->where($map)->field('id,order_sn,plan_id,number,channel_id')->select();
        return showReturnCode(true,0, $list, 'ok');
    }
    public function get_audit_oinfo()
    {
        $pinfo = I('post.');
        if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
            return showReturnCode(false,1003);
        }
        if(!isset($pinfo['sn']) || empty($pinfo['sn'])){
            return showReturnCode(false,1003);
        }
        $product = $this->getProduct($pinfo['incode']);
        $model = D('Item/Order');
        $info = $model->where(['order_sn'=>$pinfo['sn']])->relation(true)->find();
        if(empty($oinfo)){
            return showReturnCode(false,1010, '', '未找到有效订单~');
        }
        //拉取所有特殊控座模板
        $control = D('ControlSeat')->where(['status' => 1,'type'=>2,'product_id'=>$info['product_id']])->field('id,name,num')->select();
        $return = [
            'order'  => $info,
            'control'=> $control
        ];
        return showReturnCode(true,0, $return, 'ok');
    }
    /**
     * 审核订单
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-09T11:39:38+0800
     * @return   [type]                   [description]
     */
    public function post_audit_order()
    {
        $pinfo = I('post.');
        if(!isset($pinfo['action']) || empty($pinfo['action'])){
            return showReturnCode(false,1003);
        }
        if(!in_array($pinfo['action'], ['1','2','4'])){
            return showReturnCode(false,1003);
        }
        if(!isset($pinfo['sn']) || empty($pinfo['sn'])){
            return showReturnCode(false,1003);
        }
        $model = D('Item/Order');
        $info = $model->where(['order_sn'=>$pinfo['sn']])->relation(true)->find();
        if(empty($oinfo)){
            return showReturnCode(false,1010, '', '未找到有效订单~');
        }
        switch ((int)$pinfo['action']) {
            case 1:
                $order = new \Libs\Service\Order();
                $status = $order->add_seat($oinfo);
                break;
            case 2:
                //使用控座模板设置座位
                if(!isset($pinfo['control']) || empty($pinfo['control'])){
                    $status = false;
                }else{
                    $order = new \Libs\Service\Order();
                    $status = $order->up_control_seat($pinfo, $oinfo);
                }
                break;
            case 4:
                //不同意退款
                $status = \Libs\Service\Refund::arefund($oinfo);
                break;
        }
        if($status){
            return showReturnCode(true,0, [], '操作成功~');
        }else{
            return showReturnCode(false,1000, [], '操作失败~');
        }
    }
    /**
     * 退单列表
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-09T11:40:17+0800
     * @return   [type]                   [description]
     */
    public function get_refund_order()
    {
        $pinfo = I('post.');
        if(!isset($pinfo['incode']) || empty($pinfo['incode'])){
            return showReturnCode(false,1003);
        }
        $product = $this->getProduct($pinfo['incode']);
        $model = D('Item/TicketRefund');
        $field = 'param,against_reason,reason';
        $list = $model->where(['product_id'=>$product['id'],'status'=>1,'launch'=>2])->field($field,true)->select();
        return showReturnCode(true,0, $list, 'ok');
    }
    /**
     * 退单审核
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-09T11:40:26+0800
     * @return   [type]                   [description]
     */
    public function post_refund_order()
    {
        # code...
    }
    /*******************************销售简报****************************************/
    /**
     * 销售简报 日报
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-09T11:43:08+0800
     * @return   [type]                   [description]
     */
    public function today()
    {
        
    }
    /**
     * 场次简报
     * @Author   zhoujing                 <zhoujing@leubao.com>
     * @DateTime 2020-07-09T11:43:49+0800
     * @return   [type]                   [description]
     */
    public function plan_report()
    {
        # code...
    }
}