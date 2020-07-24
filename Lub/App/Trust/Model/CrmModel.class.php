<?php
namespace Trust\Model;
use Think\Model\RelationModel;
class CrmModel extends RelationModel{
	protected $_link = array(
        'Group'	=>	array(
            'mapping_type'      => self::HAS_ONE,
            'class_name'        => 'CrmGroup',
            'foreign_key'		=> 'groupid',
            'as_fields' 		=> 'price_group',
            // 定义更多的关联属性
        )
    );
	/**
	 * 客户ID
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-07-21T12:37:12+0800
	 * @param    [type]                   $crm_id               [description]
	 * @return   [type]                                         [description]
	 */
    public function getCrm($crm_id)
    {
    	$crm = D('Crm')->where(['id'=>$crm_id])->field('id,level')->cache('trust_crm_'.$crm_id, 7200)->find();
    	if(empty($crm)){
    		return false;
    	}
    	return channel($crm['id'], $crm['level']);
    }

    public function get_crm_user($crm_id)
    {
    	$crm = D('Crm')->where(['id'=>$crm_id])->field('id,level')->cache('trust_crm_'.$crm_id, 7200)->find();
    	if(empty($crm)){
    		return false;
    	}
    	$channel = channel($crm['id'], $crm['level']);
    	$map = [
    		'cid'		=> ['in', $crm],
    		'status' 	=> 1,
    		'is_scene'	=> 3,
    	];
    	$user = D('User')->where($map)->field('id,nickname')->select();
    	$crmList = D('Crm')->where(['id' => ['in', $channel]])->field('id,name')->select();

    	return [
    		'crm' => $crmList,
    		'user'=> $user,
    	];
    }


}