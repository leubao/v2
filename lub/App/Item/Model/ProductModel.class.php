<?php
// +----------------------------------------------------------------------
// | LubTMP 产品信息模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\RelationModel;
class ProductModel extends RelationModel{
	protected $_link = array(
		'Plan' => array(
			'mapping_type'  => self::HAS_MANY,
			'class_name'	=> 'plan',
			'foreign_key'	=> 'product_id',
			'condition'		=> array('status' => '2' , 'is_sales' => '1'),
		),
	);

}