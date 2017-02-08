<?php
namespace Crm\Model;
use Common\Model\RelationModel;
class CrmGroupModel extends RelationModel{
	//关联模型
	protected $_link = array(
        'User' =>array(
            'mapping_type' => self::HAS_ONE,
            'class_name'   => 'UserData',
            'mapping_name' => 'info',
            'foreign_key'  => 'user_id',
            //'condition'   => array('order_sn'=>'order_sn'),
            //'parent_key'   => 'Order_sn',
            //'condition'   => ''
            //'as_fields'    => 'info,remark,win_rem',
        ),
    );
	//缓存渠道商
	function crm_group_cache(){
		$list = $this->where(array('status'=>1))->select();
        $cache = array();
        foreach ($list as $rs) {
            $cache[$rs['id']] = $rs;
        }
        F('CrmGroup', $cache);
        return true;
	}
		
	/**
     * 插入成功后的回调方法
     */
    protected function _after_insert() {
        $this->crm_group_cache();
    }
    /**
     *更新成功后的回调方法
     */
     protected function _after_update(){
     	$this->crm_group_cache();
     }  
}
?>