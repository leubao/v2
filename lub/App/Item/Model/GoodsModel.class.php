<?php
// +----------------------------------------------------------------------
// | LubTMP 收银台商品模型
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;
class GoodsModel extends Model{
	//自动完成
	protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
        array('user_id', 'get_user_id', 1, 'function'), //新增时自动生成验证码
    );
    /**
	 * 更新缓存
	 */
	function goods_cache($proid = ''){
	 	$productId = $proid ? $proid : get_product('id');
        $where['status'] = 1;
        $where['_string'] = 'FIND_IN_SET('."'$productId'".',product)'; 	
	 	$data = $this->where($where)->field('id,title,price,discount,rebate,scene')->select();
        if (empty($data)) {
            return false;
        }
        $cache = array();
        foreach ($data as $rs) {
        	//缓存当前产品的小商品
        	$cache[$rs['id']] = $rs;
        }
        F('Goods_'.$productId, $cache);
        return true;
	}
    /**
     * 插入成功后的回调方法
     */
    protected function _after_insert(&$data) {
        $this->goods_cache();
    }
    /**
     *更新成功后的回调方法
     *
     */
    protected function _after_update(&$data){
     	$this->goods_cache();
    }
}
