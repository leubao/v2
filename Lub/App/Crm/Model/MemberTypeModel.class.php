<?php
namespace Crm\Model;
use Think\Model;
class MemberTypeModel extends Model{
	protected $_auto = array (
		array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
        array('user_id', 'get_user_id', 1, 'function'), 
        array('status', '1'),
	);
	function insert($pinfo = [])
	{
		$data = $this->create();
		$rule = [
			'year' => [],
			'datetime' => [
				'starttime' => $pinfo['starttime'],
				'endtime'	=> $pinfo['endtime']
			],
			'number'	=> $pinfo['number'],
		];
		$data->title = $pinfo['title'];
		$data->type  = $pinfo['type'];
		$data->rule  = json_encode($rule);
		$data->money = $pinfo['money'];
		return $this->add($data);
	}
}