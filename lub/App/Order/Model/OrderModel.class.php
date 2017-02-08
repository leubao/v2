<?php
// +----------------------------------------------------------------------
// | LubTMP 订单模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Order\Model;
use Common\Model\RelationModel;
class OrderModel extends RelationModel{

	protected $_link = array(
		'Order_data' =>array(
			'mapping_type' => self::HAS_ONE,
			'class_name'   => 'OrderData',
			'mapping_name' => 'info',
			'foreign_key'  => 'order_sn',
			//'parent_key'   => 'Order_sn',
			//'condition'	=> ''
		),
	);
}