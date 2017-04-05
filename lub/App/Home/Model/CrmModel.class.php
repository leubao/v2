<?php
namespace Home\Model;
use Think\Model;
class CrmModel extends Model{
	//缓存渠道商
	function crm_cache(){
		$product = M('Product')->where(array('status'=>'1'))->field('id,name')->select();
		$Config = cache("Config");
		foreach ($product as $key=>$val){
			$list = $this->where(array('status'=>1,'product_id'=>$val['id']))->field('id,name,groupid,f_agents,level,phone')->select();
	        $cache = array();
	        foreach ($list as $rs) {
	        	//一级渠道商单独缓存
	        	if($rs['level'] == $Config['level_1']){
	        		$caches[$rs['id']] = $rs;
	        	}
	        	$cache[$rs['id']] = $rs;
	        }
	        F('Crm_level'.$val['id'], $caches);
	        F('Crm'.$val['id'], $cache);
		}
        return true;
	}
	/**
     * 插入成功后的回调方法
     */
    protected function _after_insert() {
        $this->crm_cache();
    }
    /**
     *更新成功后的回调方法
     *
     */
     protected function _after_update(){
     	$this->crm_cache();
     }
}
?>