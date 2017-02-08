<?php
// +----------------------------------------------------------------------
// | LubTMP
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Manage\Model;

use Common\Model\Model;
class ProductModel extends Model{
	protected $_validate = array(
		array('name','require','产品名称不能为空!'),
		array('type','require','产品类型不能为空!'),
		array('place_id','require','场所不能为空!'),
	);
	
	protected $_auto = array(
		array('createtime',time,1,'function'),
		array('idCode','genRandomString', 1, 'function', 6), //新增时自动生成识别码
	);
	
	/**
	 * 缓存产品基本信息
	 */
	function product_cache(){ 	
	 	$data = $this->where(array('status'=>1))->field(array('idCode','createtime'),true)->select();
        if (empty($data)) {
            return false;
        }
        $cache = array();
        foreach ($data as $rs) {
        	$cache[$rs['id']] = $rs;
        }
        //F('Product', $cache, DATA_PATH.'/Item/');
        cache('Product',$cache);
        return true;
	 }
	/**
     * 插入成功后的回调方法
     */
    protected function _after_insert() {
        $this->product_cache();
    }
    /**
     *更新成功后的回调方法
     *
     */
     protected function _after_update(){
     	$this->product_cache();
     }
}