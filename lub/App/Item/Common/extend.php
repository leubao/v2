<?php
/**
 * 活动类型
 * @param  [int] $param [参数]
 * @param  [int] $type  [类型]
 * @return [string]   
 */
function activity_type($param,$type = null)
{
	if(empty($param)){
		echo "未知";
	}
	switch ($param) {
		case '1':
			$return = "买赠";
			break;
	}
	if($type){
		return $return;
	}else{
		echo $return;
	}
}
/**
 * 获取有效推广数
 */
function get_effective_focus($param,$type = null){
	$count = M('WxMember')->where(array('promote'=>$param))->count();
	if($type){
		return $count;
	}else{
		echo $count;
	}
}
/**
 * 已经兑换数
 */
function exchange_focus($param = null,$type = null){
	$count = M('ActivityWater')->where(array('member_id'=>$param))->sum('number');
	if($type){
		return $count;
	}else{
		echo $count;
	}
}
/**
 * KPI 记录类型
 */
function kpi_fill($param = null){
	switch ($param) {
        case 1:
            echo "<span class='label label-info'>系统创建</span>";
            break;
        case 2:
            echo "<span class='label label-warning'>考核员创建</span>";
            break;

    }
}
/**
 * 记录窗口售票代收款
 * @param  订单内容 $info
 * @return [type]       [description]
 */
function collection_log($info,$pay)
{
	$data = array(
		'user_id' 	=> 	get_user_id(),
		'money'	  	=> 	$info['money'],
		'pay'	  	=> 	$pay,
		'order_sn'	=> 	$info['order_sn'],
		'product_id'=>	$info['product_id'],
		'plan_id'	=>	$info['plan_id'],
		'status'    => '1',
		'createtime'=>	time()	
	);
	M('Collection')->add($data);
	return true;
}
?>