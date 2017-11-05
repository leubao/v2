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
		$rule = [
			'year' => [],
			'datetime' => [
				'starttime' => $pinfo['starttime'],
				'endtime'	=> $pinfo['endtime']
			],
			'number'	=> $pinfo['number'],
		];
		$data = [
			'title'	=>	$pinfo['title'],
			'type'	=>	$pinfo['type'],
			'rule'	=>	json_encode($rule),
			'money'	=>	$pinfo['money'],
			'status'=>	'1',
			'create_time'=>time(),
			'update_time'=>time(),
			'user_id'	=>	get_user_id(),
		];
		return $this->add($data);
	}
}