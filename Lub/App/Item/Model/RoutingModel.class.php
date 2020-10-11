<?php
// +----------------------------------------------------------------------
// | LubTMP 销售计划模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;
class RoutingModel extends Model{
	protected $_auto = array(
        array('create_time', 'time', 1, 'function')
    );
}