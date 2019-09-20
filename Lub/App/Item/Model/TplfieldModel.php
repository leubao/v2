<?php
// +----------------------------------------------------------------------
// | LubTMP  销售计划模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;

class TheatrePlanModel extends Model{
	
	protected $_auto = array(
        array('createtime', 'time', 1, 'function'),
    );
}