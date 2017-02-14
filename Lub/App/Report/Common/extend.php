<?php
/**
 * 根据渠道商拉去产品配额
 * @param  string $param 渠道商ID
 * @param  int $type  返回类型
 * @return [type]        [description]
 */
function crmQuota($param = '', $type = null){
	$quota = M('CrmQuota')->where(array('crm_id'=>$param,'product_id'=>(int)get_product()))->getField('quota');
	if($type == '1'){
		echo $quota;
	}else{
		return $quota;
	}
}