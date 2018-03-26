<?php
// +----------------------------------------------------------------------
// | LubTMP 分级扣款票型模型
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Model;
use Common\Model\Model;
class TicketLevelModel extends Model{
	/**
	 * 更新缓存
	 */
	function ticke_level_cache(){
	 	$where = [
	 		'status' => 1
	 	]; 	
	 	$data = $this->where($where)->field('ticket_id,crm_id,discount,rebate')->select();
        if (empty($data)) {
            return false;
        }
        $cache = array();
        foreach ($data as $rs) {
        	//缓存当前产品的小商品
        	$cache[$rs['crm_id']][$rs['ticket_id']] = $rs;
        }
        F('TicketLevel', $cache);
        return true;
	}
    /**
     * 插入成功后的回调方法
     */
    protected function _after_insert(&$data) {
        $this->ticke_level_cache();
    }
    /**
     *更新成功后的回调方法
     *
     */
    protected function _after_update(&$data){
     	$this->ticke_level_cache();
    }
}