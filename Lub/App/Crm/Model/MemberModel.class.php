<?php
namespace Crm\Model;
use Think\Model;
class MemberModel extends Model{
	 protected $_auto = array (
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
        array('verify', 'genRandomString', 1, 'function'), 
        array('status', '1'),
     );
}