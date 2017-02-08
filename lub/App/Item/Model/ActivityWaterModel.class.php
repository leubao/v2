<?php
// +----------------------------------------------------------------------
// | LubTMP  活动送水
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;

class ActivityWaterModel extends Model{
	
	protected $_auto = array(
        array('createtime', 'time', 1, 'function'),
        array('user_id','get_user_id',1,'function'), 
    );
}