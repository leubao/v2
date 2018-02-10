<?php
// +----------------------------------------------------------------------
// | LubTMP  会员管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;

class MemberModel extends Model{
	protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
        array('status','1'),
        array('verify', 'genRandomString', 1, 'function'),
    );
}