<?php
// +----------------------------------------------------------------------
// | LubTMP 应用模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Model;
use Common\Model\RelationModel;
class AppModel extends RelationModel{

	protected $_link = array(
		'Crm' =>array(
			'mapping_type' => self::HAS_ONE,
			'class_name'   => 'Crm',
			'mapping_name' => 'crm',
			'foreign_key'  => 'id',
			'as_fields'	   => 'groupid',
			)
	);
}