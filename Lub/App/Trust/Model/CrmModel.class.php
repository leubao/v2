<?php
namespace Trust\Model;
use Think\Model\RelationModel;
class CrmModel extends RelationModel{
	protected $_link = array(
        'Group'	=>	array(
            'mapping_type'      => self::HAS_ONE,
            'class_name'        => 'CrmGroup',
            'foreign_key'		=> 'groupid'
            'as_fields' 		=> 'price_group',
            // 定义更多的关联属性
        )
    );
}