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
			'datetime' => [
				'starttime' => strtotime($pinfo['starttime']),
				'endtime'	=> strtotime($pinfo['endtime'])
			],
			'efftime'	=> [
				'start'	=>	strtotime($pinfo['eff_starttime']),
				'end'	=>	strtotime($pinfo['eff_endtime'])
			],
			'area'		=> $pinfo['area'],
			'number'	=> $pinfo['number'],//次卡，或单日入园次数
		];
		$data = [
			'title'	=>	$pinfo['title'],
			'type'	=>	$pinfo['type'],
			'rule'	=>	json_encode($rule),
			'money'	=>	$pinfo['money'],
			'print_tpl'=>$pinfo['print_tpl'],
			'create_time' => time(),
			'update_time' => time(),
			'user_id' => get_user_id(),
			'status' => 1
		];
		return $this->add($data);
	}
	function update($pinfo = [])
	{
		$rule = [
			'datetime' => [
				'starttime' => strtotime($pinfo['starttime']),
				'endtime'	=> strtotime($pinfo['endtime'])
			],
			'efftime'	=> [
				'start'	=>	strtotime($pinfo['eff_starttime']),
				'end'	=>	strtotime($pinfo['eff_endtime'])
			],
			'area'		=> $pinfo['area'],
			'number'	=> $pinfo['number'],//次卡，或单日入园次数
		];
		$data = [
			'id'=>$pinfo['id'],
			'title'	=>	$pinfo['title'],
			'rule'	=>	json_encode($rule),
			'print_tpl'=>$pinfo['print_tpl'],
			'update_time' => time(),
			'status' => $pinfo['status']
		];
		return $this->save($data);
	}
	//缓存渠道商
	function mem_group_cache(){
		$list = $this->where(array('status'=>1))->field('id,title,type,print_tpl,money,rule')->select();
        $cache = array();
        foreach ($list as $rs) {
        	$rs['rule'] = json_decode($rs['rule'],true);
            $cache[$rs['id']] = $rs;
        }
        F('MemGroup', $cache);
        return true;
	}
		
	/**
     * 插入成功后的回调方法
     */
    protected function _after_insert() {
        $this->mem_group_cache();
    }
    /**
     *更新成功后的回调方法
     */
     protected function _after_update(){
     	$this->mem_group_cache();
     }
}