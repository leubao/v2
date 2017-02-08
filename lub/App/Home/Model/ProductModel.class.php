<?php

// +----------------------------------------------------------------------
// | LubCMF  渠道商产品列表
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com> 2014-11-28
// +----------------------------------------------------------------------
namespace Home\Model;
use Common\Model\RelationModel;
class ProductModel extends RelationModel{
	protected $_link = array(
			'Plan' => array(
					'mapping_type'  => self::HAS_MANY,
					'class_name'    => 'Plan',
					'foreign_key'   => 'product_id',
					'mapping_name'  => 'plan',
					//'mapping_order' => 'create_time desc',
					// 定义更多的关联属性
					'condition'	=>	'status=2',
				
			),
			
			
	);
		
}