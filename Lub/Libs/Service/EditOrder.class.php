<?php
/**
 * 订单编辑
 * @Author: IT Work
 * @Date:   2020-05-05 12:12:38
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-07-14 18:17:29
 */
namespace Libs\Service; 
use Org\Util\Date;
use Libs\Service\Sms;
use Common\Model\Model;
use Libs\Service\Autoseat;
class EditOrder extends \Libs\System\Service {
    //数据
    protected $data = array();
    /** 执行错误消息及代码 */
    public $error = '';


    public function upIdcardOrder($sn, $data)
    {
    	try{
    		//读取订单
    		$order = D('Item/Order')->where(array('order_sn'=>$sn,'status'=>1))->relation(true)->field('uptime,createtime', true)->find();
    		if(empty($order)){
    			$this->error = '订单状态不允许此项操作~';
    			return false;
    		}
    		$plan = F('Plan_'.$order['plan_id']);
    		if(empty($plan)){
    			$this->error = '该计划已暂停销售或已过期~';
    			return false;
    		}
    		//读取座位
    		$ticket = D($plan['seat_table'])->where(array('order_sn'=>$sn,'status'=>2))->field('id,seat,idcard')->select();
    		$ticket = array_column($ticket, NULL, 'id');

    		$oinfo = unserialize($order['info']);
    		$seat = array_column($oinfo['data'], NULL, 'seatid');
    		//更新座位
    		$model = new Model();
			$model->startTrans();
			foreach ($data['idcard'] as $k => $v) {
				/*校验身份证号码是否正确*/
				$id_card = strtoupper($v);
				$seatid = $data['seat'][$k];
				if(!empty($id_card)){
					if(!checkIdCard($id_card)){
						$this->error = '400030 : 身份证号码'.$v.'有误...';
						return false;
					}
				}
				if(isset($ticket[$k])){
					//校验是否可用
					if($this->checkIdCard($v, $order['activity'], $sn)){
						$upState = $model->table(C('DB_PREFIX'). $plan['seat_table'])->where(array('id'=>$k,'status' => 2))->setField('idcard', $v);
						if(!$upState){
							//记录错误
							$this->error = $v.'更新失败~';
						}
						// //更新订单详情
						if(isset($seat[$seatid])){
							$seat[$seatid]['idcard'] = $v;
						}
					}else{
						$this->error = $v.'已经参加过活动~';
						return false;
					}
				}
			}
			foreach ($seat as $k => $v) {
				$seatL[] = $v;
			}
			$oinfo['data'] = $seatL;
			$state1 = $model->table(C('DB_PREFIX'). 'order')->where(array('order_sn'=>$sn))->setField('uptime', time());
			$state2 = $model->table(C('DB_PREFIX'). 'order_data')->where(array('order_sn'=>$sn))->setField('info', serialize($oinfo));
			if($state1 && $state2){
				$model->commit();
				return true;
			}else{
				$model->rollback();
				$this->error = '更新失败~';
				return false;
			}
    	} catch(Exception $e){
    		$this->error = $e->errorMessage();
    		exit;
    	} 
    }
    /**
     * 校验身份证号
    */
    public function checkIdCard($idcard, $activity, $sn){
    	$map = ['idcard' => $idcard, 'activity_id' => $activity, 'order_sn'=>[NEQ,$sn]];
		$count = (int)D('IdcardLog')->where($map)->count();
		if($count > 0){
			//读取活动
			$actInfo = D('Item/Activity')->getActInfo($activity);
			$number = (int)$actInfo['param']['info']['number'];
			if($number > 0){
				if($count >= $number){
					return false;
				}else{
					return true;
				}
			}else{
				return true;
			}
		}else{
			return true;
		}
    }
}