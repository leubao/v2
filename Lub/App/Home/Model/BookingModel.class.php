<?php
namespace Home\Model;

use Common\Model\Model;

class BookingModel extends Model {

	protected $_auto = array(
        array('createtime', 'time', 1, 'function'),
        array('uptime', 'time', 3, 'function'),
        array('status', '5'),
        array('user_id', 'get_user_id', 1, 'function')
    );
}
