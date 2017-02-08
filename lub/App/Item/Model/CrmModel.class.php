<?php
// +----------------------------------------------------------------------
// | LubTMP 订单处理模型
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\RelationModel;
class CrmModel extends RelationModel{

	protected $_link = array(
		'CrmQuota' =>array(
			'mapping_type' => self::HAS_ONE,
			'class_name'   => 'CrmQuota',
			'mapping_name' => 'quota',
			'foreign_key'  => 'crm_id',
			//'condition'	=> array('order_sn'=>'order_sn'),
			//'parent_key'   => 'Order_sn',
			//'condition'	=> ''
			'as_fields'	   => 'quota',
		),
	);
}