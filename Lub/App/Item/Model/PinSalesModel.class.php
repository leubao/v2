<?php
// +----------------------------------------------------------------------
// | LubTMP 销控系统
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;
class PinSalseModel extends Model{
	protected $_auto = array(
        array('createtime', 'time', 1, 'function'),
        array('updatetime', 'time', 3, 'function'),
    );
    /**
	 * 写入redis销控设置
	 * 写入redis数据库，每半个小时同步更新一次，采用计划任务来执行同步，判断更新时间，更新时间不在半小时内的不执行同步
	 */
	function insert_pin(){
		//销售计划
		
		//销售类型
		//读取默认配置
		

		//读取默认配置
	}
    /**
     * 插入成功后的回调方法
     */
    protected function _after_insert(&$data) {
    	$this->insert_pin($data);
        $this->plan_cache();
    }
}