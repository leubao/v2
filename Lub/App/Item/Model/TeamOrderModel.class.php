<?php
// +----------------------------------------------------------------------
// | LubTMP  返利订单模型
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;

class TeamOrderModel extends Model{
	protected $_auto = array(
        array('createtime', 'time', 1, 'function'),
        array('uptime', 'time', 3, 'function'),
    );
}