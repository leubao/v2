<?php
// +----------------------------------------------------------------------
// | LubTMP 根据补贴报表反查订单数量
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Report\Model;
use Common\Model\RelationModel;
class TeamOrderModel extends RelationModel{

	protected $_link = array(
		'Order' =>array(
			'mapping_type' => self::HAS_ONE,
			'class_name'   => 'Order',
			'mapping_name' => 'info',
			'foreign_key'  => 'order_sn',
			//'condition'	   => array('order_sn'=>'order_sn'),
			//'parent_key'   => 'Order_sn',
			//'condition'	=> ''
			'as_fields'	   => 'number',
		),
	);
}