<?php
// +----------------------------------------------------------------------
// | LubTMP  自动年卡统计
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
class LubTMPMemberSum {
	/*计划任务错误代码
	*130001 订单查询失败
	*/
    //任务主体
    public function run($cronId) {
    	$memType = F('MemGroup');
    	if(empty($memType)){
    		D('Crm/MemberType')->mem_group_cache();
    		$memType = F('MemGroup');
    	}
    	$starttime = strtotime(date('Ymd',strtotime("-1 day")));
	    $endtime = $starttime  + 86399;
	    $map =[
			'status' => 1,
    		'create_time' => array(array('EGT', $starttime), array('ELT', $endtime), 'AND'),
    	];
	    $member = D('Member')->where($map)->field('user_id')->select();
	    if(empty($member)){
	    	return false;
	    }
	    $userId = array_unique(array_column($member, 'user_id'));
		foreach ($memType as $k => $v) {
			foreach ($userId as $ka => $va) {
				$mmap = [
					'group_id' => $v['id'],
					'status' => 1,
					'user_id'=>$va,
		    		'create_time' => array(array('EGT', $starttime), array('ELT', $endtime), 'AND'),
		    	];
		    	$number = D('Member')->where($mmap)->count();
				if($number > 0){
					$money = $number*$v['money'];
					$memSum[] = [
						'datetime'  => date('Ymd',$starttime),
						'price'	    => $v['money'],
						'user_id'	=> $va,
						'group_id'	=> $v['id'],
						'number' 	=> $number,
						'money'	 	=> $money
					];
				}
			}
		}
		if(!empty($memSum)){
			D('MemberSum')->addAll($memSum);
		}
		$this->renewal();
    }
	public function renewal()
    {
    	$starttime = strtotime(date('Ymd',strtotime("-1 day")));
	    $endtime = $starttime  + 86399;
	    $map =[
			'status' => 1,
			'renewal'=>['gt',0],
    		'update_time' => array(array('EGT', $starttime), array('ELT', $endtime), 'AND'),
    	];
	    $member = D('Member')->where($map)->field('user_id')->select();
	    if(empty($member)){
	    	return false;
	    }
	    $userId = array_unique(array_column($member, 'user_id'));
		foreach ($memType as $k => $v) {
			foreach ($userId as $ka => $va) {
				$mmap = [
					'group_id' => $v['id'],
					'status' => 1,
					'user_id'=>$va,
					'renewal'=>['gt',0],
		    		'update_time' => array(array('EGT', $starttime), array('ELT', $endtime), 'AND'),
		    	];
		    	$number = D('Member')->where($mmap)->count();
				if($number > 0){
					$money = $number*$v['money'];
					$memSum[] = [
						'datetime'  => date('Ymd',$starttime),
						'price'	    => $v['money'],
						'user_id'	=> $va,
						'group_id'	=> $v['id'],
						'number' 	=> $number,
						'money'	 	=> $money
					];
				}
			}
		}
		if(!empty($memSum)){
			D('MemberSum')->addAll($memSum);
		}
    }
}