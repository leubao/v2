<?php
// +----------------------------------------------------------------------
// | LubTMP 商户模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Manage\Model;

use Common\Model\Model;
class ItemModel extends Model{
	protected $_validate = array(
		array('name','require','商户名称不能为空!'),
	);
	protected $_auto = array(
		array('createtime',time,1,'function'),
		array('idcode', 'genRandomString', 1, 'function'),
	);
	/**
	 * 更新商户缓存
	 * 商户缓存中包含商户信息、和商户相关的产品信息、员工信息
	 */
	function item_cache(){ 	
	 	$data = $this->where(array('status'=>1))->select();
        if (empty($data)) {
            return false;
        }
        $cache = array();
        foreach ($data as $rs) {
        	$cache[$rs['id']] = $rs;
        }
       //F('Item', $cache, DATA_PATH.'/Item/');
       cache('Item',$cache);
        return true;
	 }
	/**
     * 插入成功后的回调方法
     */
    protected function _after_insert() {
        $this->item_cache();
    }
    /**
     *更新成功后的回调方法
     *
     */
     protected function _after_update(){
     	$this->item_cache();
     }
}