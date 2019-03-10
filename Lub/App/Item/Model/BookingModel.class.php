<?php
namespace Item\Model;

use Common\Model\Model;

class BookingModel extends Model {

	protected $_auto = array(
        array('uptime', 'time', 3, 'function'),
        array('admin_id', 'get_user_id', 3, 'function')
    );
}
