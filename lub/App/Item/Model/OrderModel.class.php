<?php
// +----------------------------------------------------------------------
// | LubTMP 订单处理模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\RelationModel;
class OrderModel extends RelationModel{

	protected $_link = array(
		'OrderData' =>array(
			'mapping_type' => self::HAS_ONE,
			'class_name'   => 'OrderData',
			'mapping_name' => 'info',
			'foreign_key'  => 'oid',
			//'condition'	=> array('order_sn'=>'order_sn'),
			//'parent_key'   => 'Order_sn',
			//'condition'	=> ''
			'as_fields'	   => 'info,remark,win_rem',
		),
	);
}