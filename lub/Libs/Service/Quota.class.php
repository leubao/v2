<?php
// +----------------------------------------------------------------------
// | LubTMP 渠道配额
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Common\Model\Model;
class Quota extends \Libs\System\Service {
	/** 执行错误消息及代码 */
	public $error = '';
	/**
	 * 查询配额
	 * @param  int $plan_id    销售计划ID
	 * @param  int $product_id 产品ID
	 * @param  int $crm_id     客户ID
	 * @param  int $number     数量
	 * @param  int $salse      销售类型
	 * @return true|false
	 */
	function quota($plan_id, $product_id, $crm_id, $number, $salse = '1')
	{
		//新增销售计划注册销售类型的库存
		//查询销售类型的库存
		//查询l
		$proconf = cache('ProConfig');
		$proconf = $proconf[$product][1];
		$config = cache("Config");
		if($proconf['quota'] <> '1'){return true;}
		//根据产品ID获取该产品所有一级渠道商
		$channel = D('Item/Crm')->where(array('status'=>1,'level'=>$config['level_1']))->field('id')->select();
		$map =  array(
			'plan_id'=>$plan_id,
			'product_id' => $product,
			'channel_id'=>array('in',implode(',',array_column($channel, 'id'))),
			'type'	=>	'1',
		);
		$today_quota = D('QuotaUse')->where($map)->sum('number');
		$today_quota = $today_quota+$number;
		$plan = F('Plan_'.$plan_id);
		if($plan['quota'] < $today_quota){
			return 'false';
		}else{
			//获取系统当前时间
			$today = date('Ymd',time());
			$plan_day = date('Ymd',$plan['plantime']);
			if($today == $plan_day && date("H") > 11){
				return true;
				//Quota::get_quota($crm['id'],$plan_id,$product);
			}else{
				$crm = D('Item/Crm')->where(array('id'=>$crm_id))->field('id,f_agents,level')->relation(true)->find();
				//判断当前渠道商级别
				switch ($crm['level']){
					case $config['level_1'] :
						//获取当前已经消耗的配额
						$quota = Quota::get_quota($crm['id'],$plan_id,$product);
						//进行比对
						$status = Quota::judge_quota($crm['quota'],$quota,$number);
						//dump($status);
						break;
					case $config['level_2'] :
						//获取当前已经消耗的配额
						$quota_level_2 = Quota::get_quota($crm['id'],$plan_id,$product);
						//进行比对
						$status_level_2 = Quota::judge_quota($crm['quota'],$quota_level_2,$number);
						if($status_level_2){
							//获取当前已经消耗的配额
							$quota_level = Quota::get_quota($crm['f_agents'],$plan_id,$product);
							$quota_level_num = Quota::get_quota_default($crm['f_agents']);
							//进行比对
							$status_level = Quota::judge_quota($quota_level_num,$quota_level,$number);
							if($status_level){
								$status = true;
							}else{
								$status = false;
							}
						}else{
							$status = false;
						}
						break;
					case $config['level_3'] :
						//三级渠道商  获取二级的上一级ID
						//$status = Quota::level_3($crm['id'],$crm['f_agents'],$plan_id,$number,$crm,$product);
						//获取当前已经消耗的配额
						$quota_level_3 = Quota::get_quota($crm['id'],$plan_id,$product);
						//进行比对
						$status_level_3 = Quota::judge_quota($crm['quota'],$quota_level_3,$number);
						if($status_level_3){
							//获取当前已经消耗的配额
							$quota_level_2 = Quota::get_quota($crm['f_agents'],$plan_id,$product);
							$quota_level_2_num = Quota::get_quota_default($crm['f_agents']);
							//进行比对
							$status_level_2 = Quota::judge_quota($quota_level_2_num,$quota_level_2,$number);
							if($status_level_2){
								//获取三级顶级渠道商id
								//获取当前已经消耗的配额
								$level_id = money_map($crm['id']);
								$quota_level = Quota::get_quota($level_id,$plan_id,$product);
								$quota_level_num = Quota::get_quota_default($level_id);
								//进行比对
								$status_level = Quota::judge_quota($quota_level_num,$quota_level,$number);
								if($status_level){
									$status = true;
								}else{
									$status = false;
								}
							}else{
								$status = false;
							}
						}else{
							$status = false;
						}
						break;
				}
				if($status != false){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	//根据销售类型查询配额
	function query_sales_quota($plan_id,$product,$type){
		//读取类型剩余数据
		$quota = load_redis('get','pin_'.$product_id.'_'.$planid.'_'.$type);
		return $quota;
	}
	//更新销售类型销控
	function up_sales_quota($plan_id,$product,$type,$num,$param = '1'){
		if($param == '2'){
			//减少
			$quota = load_redis('decrby','pin_'.$product_id.'_'.$planid.'_'.$type,$num);
		}
		if($param == '1'){
			//增加
			$quota = load_redis('incrby','pin_'.$product_id.'_'.$planid.'_'.$type,$num);
		}
		return $quota;
	}
	//删除销售类型销控
	function del_sales_quota($plan_id,$product,$type){
		//读取类型剩余数据
		$quota = load_redis('get','pin_'.$product_id.'_'.$planid.'_'.$type);
		return $quota;
	}
	//同步销售类型销控
	function FunctionName($value='')
	{
		# code...
	}
	/**
	 * 更新配额 TODO 核减、退票还回配额
	 * 先更新销售类型的配额
	 * @param $number 数量
	 * @param $crm_id 渠道商ID
	 * @param $plan_id 计划id
	 * @param $product_id 产品ID
	 * @param $sale 销售类型 2常规渠道 4政企渠道 8全员销售 9 三级分销
	 */
	public function update_quota($number = '', $crm_id = '', $plan_id = '', $product_id = '', $salse = '')
	{
		$plan = F('Plan_'.$plan_id);
		$today = date('Ymd',time());
		$plan_day = date('Ymd',$plan['plantime']);
		if($today == $plan_day && date("H") > 11){
			return '200';
		}
		//判断销售类型是否存在余量
		$salse = self::pin_sales_link($salse);
		(int)$salse_num = load_redis('decrby','pin_'.$product_id.'_'.$plan_id.'_'.$salse,$number);
		if($salse_num < 0){
			//$this->error = "销售类型余量不足...";
			return false;
		}
		//判断是否设置单体限额，目前只有常规渠道和政企渠道需要设置
		if(in_array($salse,['2','4'])){
			self::check_quota($plan_id,$plan['product_id'],$crm_id);
			$config = cache("Config");
			//判断渠道商级别,写入消耗配额
			$cinfo = M('Crm')->where(array('id'=>$crm_id))->field('id,level,f_agents')->find();
			$map = array();
			switch ($cinfo['level']){
				case $config['level_1'] :
					//一级渠道商
					$map['channel_id'] = $crm_id;
					break;
				case $config['level_2'] :
					//二级级渠道商
					$ids = $crm_id.','.$cinfo['f_agents'];
					$map['channel_id'] = array('in',$ids);
					break;
				case $config['level_3']:
					//三级级渠道商
					$ids = $crm_id.','.$cinfo['f_agents'].','.$level1;
					$map['channel_id'] = array('in',$ids);
					break;
			}
			$map['plan_id'] = $plan_id;
			$map['type']	= '1';
			$data = array('number' => array('exp','number+'.$number));
			$up_quota = D('QuotaUse')->where($map)->save($data);
		}else{
			$up_quota = true;
		}

		//Quota::check_quota($plan_id,$plan['product_id'],$crm_id);



		if($up_quota){
			return '200';
		}else{
			return '400';
		}
	}
	/**
	 * 判断销售的配额是否允许单项操作
	 * @param  int $salse 销售类型
	 * @param  int $type  1查询2更新
	 * @return true false
	 */
	function pin_salse($salse, $type){
		//查询
		if($type == '1'){
			switch ($salse) {
				case '2':

					break;
				case '4':

					break;
				case '8':

					break;
			}
		}
		//更新
		if($type == '2'){

		}

	}
	//更新
	/**
	 * 拉取当前渠道商配额
	 */
	function get_quota_default($cid){
		$quota = D('CrmQuota')->where(array('crm_id'=>$cid))->getField('quota');
		return $quota;
	}
	/**
	 * 拉取当前渠道商已经消耗的配额
	 * @param $cid int 配额
	 * return
	 * TODO 后期考虑使用缓存
	 */
	function get_quota($cid,$plan_id,$product_id){
		//查询是否标记配额
		Quota::check_quota($plan_id,$product_id,$cid);
		$quota = D('QuotaUse')->where(array('channel_id'=>$cid,'plan_id'=>$plan_id,'type'=>'1'))->getField('number');
		return $quota;
	}
	/**
	 * 比较大小
	 * @param $quota int 渠道商拥有的配额
	 * @param $quotas int 渠道商已经消耗的配额（不包含即将销售的配额）
	 */
	function judge_quota($quota,$quotas,$number){
		$nums = $quotas+$number;
		if($quota < $nums){
			return false;
		}else{
			return true;
		}
	}
    /**
     * 检测该场次是否渠道配额注册
     * @param  int $plan_id 销售计划
     * @param  int $product 产品ID
     * @param  int $crm_id 渠道商ID
     * @return return true|false
     */
    public function check_quota($plan_id,$product,$crm_id,$type = 1){
        $map = array(
            'plan_id'=> $plan_id,
            'product_id'=>$product,
            'channel_id'=>$crm_id,
            'type'=>$type,
        );
        $db = M('QuotaUse');
        if(!$db->where($map)->field('id')->find()){
        	$db->add($map);
        }
        return true;
    }
    /**
     * 批量注册渠道商
     * @param  int $planid  销售计划
     * @param  int $product 产品id
     */
    public function reg_quota($planid,$product){
    	//读取所有渠道商
    	$list = M('Crm')->where(array('status'=>'1'))->field('id')->select();
    	foreach ($list as $key => $value) {
    		$data[] = array(
    			'plan_id'=> $planid,
	            'product_id'=>$product,
	            'channel_id'=>$value['id'],
	            'type'=>'1',
    		);
    	}
    	//查询是否开启全民分销
    	$proconf = cache('ProConfig');
    	if($proconf[$product][1]['full_sales'] == '1'){
    		//查询销售计划关于分销的配额设置
    		$data[] = array(
    			'plan_id'=> $planid,
	            'product_id'=>$product,
	            'channel_id'=>1,
	            'type'=>'3',
    		);
    	}
    	if(!M('QuotaUse')->addAll($data)){
    		error_insert("400038");
    	}
    	return true;
    }
    //活动配额比较
    function activity_quota($number, $crm_id, $plan_id, $area){
    	$map =  array(
    		'channel_id' => $crm_id,
    		'plan_id'	 =>	$plan_id,
    		'area_id'	 => $area,
    		'type'		 => '2',
    		);
    	$num = M('QuotaUse')->where($map)->getField('number');
    	$param = session('param');
    	$quota = $param['info'][$area]['quota'];
    	if(Quota::judge_quota($quota,$num,$number)){
    		return true;
    	}else{
    		return false;
    	}
    }
    //全员销售比较配额
    function full_quota($number, $crm_id, $plan_id, $area){
    	$map =  array(
    		'channel_id' => 1,
    		'plan_id'	 =>	$plan_id,
    		'area_id'	 => $area,
    		'type'		 => '3',
    		);
    	$num = M('QuotaUse')->where($map)->getField('number');
    	$param = session('param');
    	$quota = $param['info'][$area]['quota'];
    	if(Quota::judge_quota($quota,$num,$number)){
    		return true;
    	}else{
    		return false;
    	}
    }
    /*销售类型
	1、窗口散客
	2、窗口团队
	3、微信散客
	4、微信团队、渠道版
	5、政企单位
	6、分销
	7、活动
    */
    /**
     * 活动配额更新
     * @param  int $number  数量
     * @param  int $crm_id  活动ID
     * @param  int $plan_id 销售计划
     * @param  int $area    区域id
     * @return true|False
     */
    function up_activity_quota($number, $crm_id, $plan_id, $area){
    	$map =  array(
    		'channel_id' => $crm_id,
    		'plan_id'	 =>	$plan_id,
    		'area_id'	 => $area,
    		);
    	$status = M('QuotaUse')->where($map)->setInc('number',$number);
    	if($status){
    		return true;
    	}else{
			return false;
    	}
    }
    /**
     * 全员销售配额更新
     * @param  int $number  数量
     * @param  int 1 全员销售指定id wei
     * @param  int $plan_id 销售计划
     * @param  int $area    区域id
     * @return true|False
     */
    function up_full_quota($number, $crm_id, $plan_id, $area){
		$map =  array(
    		'channel_id' => 1,
    		'plan_id'	 =>	$plan_id,
    		'area_id'	 => $area,
    	);
    	$status = M('QuotaUse')->where($map)->setInc('number',$number);
    	if($status){
    		return true;
    	}else{
			return false;
    	}
    }
    /**
   	 * 销售类型操作符号与操作量转换关系
   	 * @param  string  $param 参数
   	 * @return [type]        [description]
   	 */
   	function pin_sales_link($param){
   		switch ($param) {
   			case 'often':
   				//常规渠道
   				return '4';
   				break;
   			case 'political':
   				//
   				return '6';
   				break;
   			case 'full':
   				return '8';
   				break;
   			case 'directly':
   				return '';
   				break;
   			case 'electricity':
   				return '';
   				break;
   			default:
   				return '4';
   				break;
   		}
   	}
}
