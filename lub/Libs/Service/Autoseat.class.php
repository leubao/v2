<?php
// +----------------------------------------------------------------------
// | LubTMP  智能排座
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;

use Common\Model\Model;
class Autoseat extends \Libs\System\Service {
	/**
	 * 智能排座
	 * @param $group array 排座分组的id
	 * @param $table string 表名称
	 * @param $group_id int 分组id
	 * @param $product_id int 产品id
	 * @param $area int 区域id
	 * @param $num int 数量
	 * return array $nums 是否有足够的座椅数   $groups 条件 
	 * 规定分组不超过五十 满足条件  立即结束循环
	 */
	public function auto_group($group, $area = null, $num = null, $product_id = null, $table = null){
		if(!empty($group)){
			//判断当前分组数
			$g_num = count($group);
			//$g_num = $g_num > 50 ? 50 : $g_num;
			//判断是否可存在可售区域座椅
			for($i = 0; $i < $g_num; $i++){
				$nums = Autoseat::go_num($group[$i],$table,$area);
				if((int)$nums >= (int)$num){
					//当前分组满足需要 返回分组id
					$g_id = $group[$i];
					//结束循环
					break;
				}
				//当前分组不满足需要  继续循环
			}
			//未找到单个满足条件的分组  连续查询多个分组
			if(empty($g_id)){
				for ($ii = 0; $ii < $g_num; $ii++) { 
					$groups = $group[$ii].','.$groups;
					//最后一次循环自动加上未分组的座椅
					if($ii == $g_num){
						$groups = $groups.','.'0';
					}
					$nums = Autoseat::go_num($groups,$table,$area);
					if($nums >= $num){
						//当前分组满足需要 返回分组id
						$g_id = $groups;
						//结束循环
						break;
					}
				}
			}
 			//返回符合条件的分组
 			return $g_id;
		}else{
			return false;
		}
	}
	// /*
	// * 更新分组内座椅数量
	// * @param $table string 表名称
	// * @param $area int 区域id
	// * @param $group_id int 分组id
	// * @param $product_id int 产品id
	// * @param $num int 操作数量
	// * @param $type int 更新类型  1 减少 2 增加
	// * runturn true | false
	// */
	// function up_auto_seat_num($table = null, $area = null, $group = null, $product_id = null, $num = 0, $type = '1'){
	// 	$map = array('product_id' => $product_id, 'plan_id' => $table, 'group_id' => $group_id, 'area' =>$area);
	// 	if($type == '1'){
	// 		$status = M('AutoNum')->where($map)->setDec('nums',$num);
	// 	}else{
	// 		$status = M('AutoNum')->where($map)->setInc('nums',$num);
	// 	}
	// 	return $status;
	// }
	/*
	*查询分组座椅数
	* @param $table string 表名称
	* @param $area int 区域id
	* @param $group_id array 分组id
	* @param $product_id int 产品id
	*/
	static public function go_num($group_id = null, $table = null, $area = null){
		$map = array('group' => array('in',$group_id), 'area' =>$area, 'status' => '0');
		$num = M(ucwords($table))->where($map)->count();
		return $num;
	}
	
}