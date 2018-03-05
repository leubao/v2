<?php
// +----------------------------------------------------------------------
// | LubTMP 红包模型
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Sales\Model;

use Common\Model\Model;

class RedTplModel extends Model {
	protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('user_id','get_user_id',1,'function'),
        array('item_id','get_item_id',1,'function'), 
    );
}