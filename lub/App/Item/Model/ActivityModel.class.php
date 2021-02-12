<?php
// +----------------------------------------------------------------------
// | LubTMP 客户端用户权限模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;

use Common\Model\Model;

class ActivityModel extends Model {
	//array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('createtime', 'time', 1, 'function'),
        array('uptime', 'time', 3, 'function'),
    );
    //获取指定活动信息
    public function getActInfo($id)
    {
    	$actInfo = json_decode(load_redis('get','actInfo_'.$id), true);
    	if(empty($actInfo) || empty($actInfo['param'])){
    		$actInfo = D('Activity')->where(['status'=>1,'id'=>$id])->field('createtime,remark,update,sort',true)->find();
    		$actInfo['param'] = json_decode($actInfo['param'], true);
    		load_redis('setex','actInfo_'.$id, json_encode($actInfo), 86400);
    	}
    	return $actInfo;
    }
}