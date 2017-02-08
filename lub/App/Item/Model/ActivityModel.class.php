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

}