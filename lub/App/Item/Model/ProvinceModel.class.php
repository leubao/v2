<?php
// +----------------------------------------------------------------------
// | LubTMP 区域缓存
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;

use Common\Model\Model;
class ProvinceModel extends Model{
	/**
	 * 缓存省份信息
	 */
	function province_cache(){
	 	$data = $this->order('listorder ASC')->select();
        if (empty($data)) {
            return false;
        }
        $cache = array();
        foreach ($data as $o => $e) {
            if($e['fid'] > 0){
               $city[$e['fid']][$e['id']] = $e; 
            }
        }
        foreach ($data as $o => $rs) {
            
            if($rs['fid'] == 0){
                if(empty($city[$rs['id']])){
                    $city[$rs['id']][$rs['id']] = [
                        'id'  => $rs['id'],
                        'name'=> $rs['name']
                    ];
                }
                $cache[$rs['id']] = [
                    'id'  => $rs['id'],
                    'name'=> $rs['name'],
                    'city'=> $city[$rs['id']]
                ];
            }
        }
        F('Province', $cache);
        return true;
	 }
	/**
     * 插入成功后的回调方法
     */
    protected function _after_insert() {
        $this->province_cache();
    }
    /**
     *更新成功后的回调方法
     *
     */
     protected function _after_update(){
     	$this->province_cache();
     }
}