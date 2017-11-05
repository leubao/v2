<?php
// +----------------------------------------------------------------------
// | LubTMP 计划任务模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Manage\Model;

use Common\Model\Model;
class CronlogModel extends Model{
	protected $_auto = array (
        array('create_time', 'time', 1, 'function')
    );
}