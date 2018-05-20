<?php
namespace Crm\Model;
use Think\Model;
class CrmModel extends Model{
	 protected $_auto = array (
         array('create_time','time',1,'function'),
         array('status','1',2),
         array('uptime','time',3,'function'), // 对update_time字段在更新的时候写入当前时间戳
     );
	//缓存渠道商
	function crm_cache(){
        $Config = cache("Config");
        $list = $this->where(array('status'=>1))->field('id,name,groupid,itemid,f_agents,level,phone,param')->select();
        $cache = array();
        foreach ($list as $rs) {
            $rs['param'] = json_decode($rs['param'],true);
        	//一级渠道商单独缓存
        	if($rs['level'] == $Config['level_1']){
        		$caches[$rs['id']] = $rs;
        	}

        	$cache[$rs['id']] = $rs;
        }
        F('Crm_level', $caches);
        F('Crm', $cache);
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
     */
     protected function _after_update(){
     	$this->crm_cache();
     }  
}
?>