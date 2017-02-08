<?php
// +----------------------------------------------------------------------
// | LubTMP 商户模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\RelationModel;
class ItemModel extends RelationModel{
	
	protected $_link = array(
		'Product' => array(
			'mapping_type'  => self::HAS_MANY,
			'class_name'	=> 'Product',
		),
	);
}