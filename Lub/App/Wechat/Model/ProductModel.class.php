<?php

// +----------------------------------------------------------------------
// | LubTMP 微信模型
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------

namespace Wechat\Model;
use Common\Model\RelationModel;

class ProductModel extends RelationModel{
	protected $_link = array(
		'TicketSingle' => array(
				//'mapping_type'  => self::HAS_MANY,
				'mapping_type'  => self::HAS_ONE,
				'class_name'    => 'TicketSingle',
				'foreign_key'   => 'product_id',
				'mapping_name'  => 'ticket',
				'mapping_order' => 'price asc',
				// 定义更多的关联属性
				'condition'	=>	'status=1',
				'as_fields' => 'price',
			
		)
	);
}