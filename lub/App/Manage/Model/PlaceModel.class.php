<?php
// +----------------------------------------------------------------------
// | LubTMP 
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Manage\Model;

use Common\Model\Model;

class PlaceModel extends  Model {
	/*array(填充字段,填充内容,[填充条件,附加规则])*/
    protected $_auto = array(
        array('idcode', 'genRandomString', 1, 'function'),
        //array('loginip', 'get_client_ip', 3, 'function'),
    );
}