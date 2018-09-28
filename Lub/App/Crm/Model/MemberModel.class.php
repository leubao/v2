<?php
// +----------------------------------------------------------------------
// | LubTMP 会员模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Crm\Model;
use Think\Model;
class MemberModel extends Model{
	 protected $_auto = array (
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
        array('number', 0),
        array('verify', 'genRandomString', 1, 'function'), 
     );
     //计算有效期
     public function validity($data)
     {
     	//读取类型
     	$memType = F('MemGroup');
     	$type = $memType[$data['group_id']];
     	switch ($type['type']) {
     		case '1':
     			$endtime = $type['rule']['efftime']['end'];
     			break;
     		case '3':
     			$endtime = $type['rule']['efftime']['end'];
     			break;
     		case '4':
     			$endtime = $type['rule']['efftime']['end'];
     			break;
     		case '5':
     			//读取当前日期+类型有效期
     			$endtime = strtotime("+".$type['rule']['number']." day");
     			break;
     	}
     	return $endtime;
     }
     /**
     * 插入成功后的回调方法
     */
    protected function _after_insert($data, $options) {
        //添加信息后，更新密码字段
        $this->where(array('id' => $data['id']))->save(array(
            'endtime' => $this->validity($data),
        ));
    }
    /**
     *更新成功后的回调方法
     */
     protected function _after_update(){
     	
     }  
}