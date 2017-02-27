<?php
// +----------------------------------------------------------------------
// | LubTMP 票型分组模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\RelationModel;
class TicketGroupModel extends RelationModel{
	
	protected $_link = array(
		'TicketType' => array(
			'mapping_type'  => self::HAS_MANY,
			'class_name'	=> 'TicketType',
            'condition'     => 'status="1"',
			'foreign_key'	=> 'group_id',
		),
	);
	//数据验证
	 protected $_validate = array(
	 	//array('name','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
	 );
	 
	/**
	 * 更新缓存
	 */
	function group_cache($proid = ''){
	 	$productId = $proid ? $proid : get_product('id');	 	
	 	$data = $this->where(array('product_id'=>$productId,'status'=>1))->select();
        if (empty($data)) {
            return false;
        }
        $cache = array();
        foreach ($data as $rs) {
        	//缓存当前产品的票型缓存
        	$cache[$rs['id']] = $rs;
        }
        //cache('TicketType'.$productId, $cache);
        F('TicketGroup'.$productId, $cache);
        return true;
	}
	/**
     * 插入成功后的回调方法
     */
    protected function _after_insert() {
        $this->group_cache();
    }
    /**
     *更新成功后的回调方法
     *
     */
    protected function _after_update(){
     	$this->group_cache();
    }
	 
}