<?php

/**
 * @Author: IT Work
 * @Date:   2019-09-20 03:12:14
 * @Last Modified by:   IT Work
 * @Last Modified time: 2019-09-20 03:13:12
 */
namespace Common\Model;

use Common\Model\Model;

class PlanModel extends Model {

	public function get_alizhiyou_plan($id)
   	{
   		$plan = M('Plan')->where(['id'=>$id])->find();
   		$postData = [];
   		if(!empty($plan)){
			$param = unserialize($plan['param']);
			//1未授权2售票中3暂停销售4已过期
			if(in_array($plan['status'], ['1','3','4'])){
				$status = false;
			}else{
				$status = true;
			}
	        $postData = [
	          'id'        =>  $plan['id'],
	          'plantime'  =>  $plan['plantime'],
	          'starttime' =>  $plan['starttime'],
	          'endtime'   =>  $plan['endtime'],
	          'ticket'    =>  $param['ticket'],
	          'status'	  =>  $status
	        ];
   		}
   		
        return $postData;
   	}
}