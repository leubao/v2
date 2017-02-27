<?php
// +----------------------------------------------------------------------
// | LubTMP 票型分组模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;
class TicketTypeModel extends Model{
	
	
	//数据验证
	 protected $_validate = array(
	 );
	 //自动完成字段
	 protected $_auto = array(
        array('createtime', 'time', 1, 'function'),
        array('updatetime', 'time', 3, 'function'),
    );
    
	/**
	 * 更新缓存
	 */
	 function type_cache($proid = ''){
	 	$productId = $proid ? $proid : get_product('id');	 	
	 	$data = $this->where(array('product_id'=>$productId))->select();
        if (empty($data)) {
            return false;
        }
        $cache = array();
        foreach ($data as $rs) {
            $rs['param'] = unserialize($rs['param']);
        	//缓存当前产品的票型缓存
        	$cache[$rs['id']] = $rs;
        }
        F('TicketType'.$productId, $cache);
        return true;
	 }
	/**
     * 插入成功后的回调方法
     */
    protected function _after_insert() {
        $this->type_cache();
    }
    /**
     *更新成功后的回调方法
     *
     */
     protected function _after_update(){
     	$this->type_cache();
     }
}