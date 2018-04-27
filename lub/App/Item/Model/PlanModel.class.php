<?php
// +----------------------------------------------------------------------
// | LubTMP 销售计划模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;
use Libs\System\Createsql;
class PlanModel extends Model{
	
	/**
	 * 增加计划
	 * 座椅模板表 名称规则
	 * 前缀+名称+产品ID+日期+场次
	 * lub_seat_2_140607_2
	 * @param unknown_type $data 
	 */
	function add_plan($data = null){
		if (empty($data)) {return false;}
        $plantime = strtotime($data['plantime']);
		//判断场次是否已存在
		if($data['product_type'] == '1'){
			$starttime = strtotime($data['starttime']);
			$endtime = strtotime($data['endtime']);
			if($this->is_plan($data['product_id'],$plantime,$starttime,$endtime,$data['games'])){return false;}
		}
		$info = $this->structure_data($data,$plantime,$starttime,$endtime);
		if(count($info) == 1){
			$planid = $this->add($info['0']);
		}else{
			$planid = $this->addAll($info);
		}
		return $planid;
	}
	/*构造计划写入数据*/
	function structure_data($data,$plantime,$starttime,$endtime)
	{
		if($data['product_type'] == '1'){
			//引入销控默认数据
			//读取配置
			$proconf = $this->procof;
			if($proconf['quota']){
				$quotaBase = [
					'channel_often_quota'		=>	$proconf['channel_often_quota'],
					'channel_political_quota'	=>	$proconf['channel_political_quota'],
					'channel_full_quota'		=>	$proconf['channel_full_quota'],
					'channel_directly_quota'	=>	$proconf['channel_directly_quota'],
					'channel_electricity_quota'	=>	$proconf['channel_electricity_quota'],
				];
			}else{
				$quotaBase = [];
			}
			//剧院
			$infos = array(
				'games' => (int)$data['games']  ? (int)$data['games'] : 1,
				'seat_table' => 'seat_'.$data['product_id'].'_'.substr(date('Ymd',$plantime),2).'_'.$data['games'],
				'template_id'=>$data['template_id'],
				'quota'	=> serialize($quotaBase),
			);
			
			$param = $this->plan_param($data['product_id'],$data['seat'],$data['ticket'],$data['goods'],$data['product_type']);
			$infoAll = array(
				'plantime' => $plantime,
				'product_id' => $data['product_id'],
				'starttime'	=> $starttime,
				'endtime'	=> $endtime,
				'product_type' => $data['product_type'],
				'status'=>'1',
				'is_sales' => 1,
				'user_id' => get_user_id(),
				'createtime' => time(),
				'param'	=> serialize($param),
				'encry'	=> genRandomString(6,1),
			);
			$info[] = array_merge($infos,$infoAll);
		}elseif($data['product_type'] == '2'){
			//景区
			foreach ($data['plan'] as $key => $value) {
				//判断同一天的越过
				$plantime = strtotime($value['plantime']);
				/*
				判断是否开启单位时间限量 TODO
				if(!in_array($plantime, $plan_time)){
				}*/
				$plan_time[] = strtotime($value['plantime']);
				$infos = array(
					'games' => 1,
					'seat_table' => 'scenic',
					'template_id'=> 1,
					'quota'  =>	$value['quota'],
					'quotas' =>	$value['quotas'],
				);
				$param = $this->plan_param($data['product_id'],'',$data['ticket'],$data['goods'],$data['product_type']);
				$infoAll = array(
					'plantime' 	=> $plantime,
					'product_id' => $data['product_id'],
					'starttime'	=> strtotime($value['starttime']),
					'endtime'	=> strtotime($value['endtime']),
					'product_type' => $data['product_type'],
					'status' => '3',
					'is_sales' => 1,
					'user_id' => get_user_id(),
					'createtime' => time(),
					'param'	=> serialize($param),
					'encry'	=> genRandomString(6,1),
				);
				$info[] = array_merge($infos,$infoAll);
			}
		}else{
			//漂流
			//批量新增排次
			foreach ($data['plan'] as $key => $value) {
				$infos = array(
					'product_id' => $data['product_id'],
					'starttime'	=> strtotime($value['starttime']),
					'endtime'	=> strtotime($value['endtime']),
					'seat_table' => 'drifting',
					'games'	=>	$value['no'],
					'quota' =>	$value['quota'],
					'quotas' =>	$value['quotas'],
					'product_type' => $data['product_type'],
				);
				$param = $this->plan_param($data['product_id'],$value['tooltype'],$data['ticket'],$data['goods'],$data['product_type']);
				$infoAll = array(
					'plantime' => $plantime,
					'status'=>'3',
					'is_sales' => 1,
					'user_id' => get_user_id(),
					'createtime' => time(),
					'param'	=> serialize($param),
					'encry'	=> genRandomString(6,1),
				);
				$info[] = array_merge($infos,$infoAll);
			}
		}
		return $info;
	}
	/*
	* 构造参数
	* @param $product int 产品id
	* @param $seat 座椅区域|景区产品为空
	* @param $ticket 票型集合
	* @param $goods 收银台商品
	* return $param array 返回参数
	*/
	function plan_param($product = null, $seat, $ticket, $goods, $type = '1'){
		if(empty($product)){return false;}
		//获取分组排序规则
		if($type == '1'){
			//剧院产品  加载座椅分组
			$auto_group = M('AutoSeat')->where(array('status'=>1,'product_id'=>$product))->field('id,sort,num')->order('sort ASC')->select();
			foreach ($auto_group as $k=>$v){
				$a_g[] =$v['id'];
			}
			$param = array(
				'seat' => $seat,
				'ticket' => $ticket,
				'goods'	=>	$goods,
				'auto_group' => $a_g,//自动排座分组排序
			);
		}elseif($type == '2'){
			$param = array(
				'seat' => $seat,
				'goods'	=>	$goods,
				'ticket' => $ticket,
			);
		}else{
			$param = array(
				'tooltype' => $seat,
				'goods'	=>	$goods,
				'ticket' => $ticket,
			);
		}
		return $param;
	}
	/*判断该时段是否已存在场次
	*@param $product int 产品id
	*@param $plantime string 演出日期
	*@param $strattime string 演出开始时间
	*@param $endtime string 演出结束时间
	*@param $games string 场次
	*@param $type int 产品类型
	*return true|false
	*/
	function is_plan($product = null,$plantime = null, $starttime = null, $endtime = null, $games = '1', $type = '1'){
		if(empty($product) || empty($plantime) || empty($starttime) || empty($endtime)){return false;}
		$list = $this->where(array('plantime'=>$plantime,'product_id'=>$product))->field('id,starttime,plantime,endtime,games')->select();
		if(empty($list)){
			return false;
		}else{
			foreach ($list as $key => $value) {
				if($type == '1'){
					if($value['starttime'] <= $starttime && $starttime <= $value['endtime'] || $value['games'] == $games){
						return true;
					}
				}else{//景区产品
					return true;
				}
			}
			return false;
		}
	}
	/**
	 * 编辑计划
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public function editPlan($data){
		if (empty($data)) {
            return false;
        }
		$starttime = strtotime($data['starttime'].$data['start']);
		$endtime = strtotime($data['starttime'].$data['end']);
		//获取分组排序规则
		$auto_group = M('AutoSeat')->where(array('status'=>1))->field('id,sort')->order('sort ASC')->select();
		$param=array(
			'seat' => $data['seat'],
			'ticket' => $data['ticket'],
			'auto_group'=> $auto_group,//自动排座分组排序
		);
		if($data){
			$data = array(
				'id' => $data['id'],
				'plantime' => strtotime($data['starttime']),
				'product_id' => $data['product_id'],
				'games' => (int)$data['games']  ? (int)$data['games'] : 1,
				'starttime'	=> $starttime,
				'endtime'	=> $endtime,
				'product_type' => $data['product_type'],
				'seat_table' => 'seat_'.$data['product_id'].'_'.substr(date('Ymd',$starttime),2).'_'.$data['games'],
				'status' => $data['status'],
				'is_sales' => 1,
				'user_id' => \Libs\Util\Encrypt::authcode($_SESSION['lub_userid'], 'DECODE'),
				'template_id'=>$data['template_id'],
				'param'	=> serialize($param),
				'quota'	=> $data['quota'],
			);
			$planid = $this->save($data);
		}
		return $planid;

	}
	/**
	 * 计划授权并创建相应数据表
	 * 此处须执行事务，保证整个操作的完整性   计划授权同时创建座位表和该场次订单表 同时创建成功后更新计划中授权字段
	 */
	function auth($planid){
		if (empty($planid)) {
            return false;
        }
        $info = $this->where(array('id'=>$planid))->field('product_type,seat_table,product_id,status,param,template_id,quotas')->find();
        if(!$info){
        	return false;
        }
        if($info['status'] > 1){
        	return false;
        }
        //判断产品类型
        if($info['product_type'] == '1'){
        	//剧院产品
        	$seattable  = $this->createtable($info['seat_table'],seat);
        	if($seattable !== false){
        		//根据计划写入座椅信息
        		//读取座椅信息
        		$param = unserialize($info['param']);
        		$seatadd = $this->addSeat($info['seat_table'],$param,$info['template_id']);
        		if($seatadd == false){
        			$this->roll_back($info['seat_table']);
        			return false;
        		}else{
        			$up = $this->where(array('id'=>$planid))->setField('status',3);
			        if($up){
			        	//查询所有有效渠道商
			        	/*$crm = M('Crm')->where(array('status'=>'1','product_id'=>$info['product_id']))->field('id,product_id')->select();
			        	foreach ($crm as $v){
			        		$quota_crm[] = array(
			        			'number'	=>	"0",
			        			'channel_id' => $v['id'],
			        			'plan_id'	 => $planid,
			        			'product_id' => $info['product_id'],
			        		); 
			        	}
			        	$quota_use = M('QuotaUse')->addAll($quota_crm);
			        	写入智能排座
			        	$auto = M('AutoSeat')->where(array('status'=>1,'product_id'=>$info['product_id']))->field('id,seat,num')->select();
			        	foreach ($auto as $va){
			        		//分区域、分组写入数量
			        		$va['seat'] = unserialize($va['seat']);
			        		foreach($va['seat'] as $kk=>$vv){
			        			if(!empty($vv['num'])){
			        				$auto_seat[] = array(	
					        			'product_id'=>	$info['product_id'],	
					        			'plan_id'	=>	$planid, 
					        			'group_id'	=>	$va['id'],
					        			'nums'		=>	$vv['num'],
					        			'area'		=>	$kk,
					        		); 
			        			}
			        		}
			        	}
			        	$auto_group = M('AutoNum')->addAll($auto_seat);
			        	*/
			        	$auto_group =  true;
			        	$quota_use = true;
			        	if($quota_use && $auto_group){
			        		$this->commit();
			        		//批量写入配额
			        		$proconf = cache('ProConfig');
					        if($proconf[$info['product_id']][1]['quota'] == '1'){
					        	\Libs\Service\Quota::reg_quota($planid,$info['product_id']);
					        }
					        //注册销售类型
					        $this->pin_sales_type($planid,$info['product_id']);
			        		return true;
			        	}else{
			        		$this->rollback();//事务回滚
							return false;
			        	}
			        }else{
			        	//执行回滚
			        	$this->roll_back($info['seat_table']);
			        	return false;
			        }
        		}
        	}else{
        		error_insert('400111');
        		$this->roll_back($seattable);
        		return false;
        	}    	
        }else{
        	switch ($info['product_type']) {
        		case '2':
        			$table = 'scenic';
        			break;
        		case '3':
        			$table = 'drifting';
        			break;
        	}
        	/*
        	for ($i=0; $i < $info['quotas']; $i++) {
				$ciphertext = genRandomString();
				$dataList[] = array(
					'order_sn' => '',
					'plan_id'=>	$planid,
					'ciphertext' => $ciphertext,
					'price_id'   =>	'',
					'password' => '',
					'sale' => '',
					'status' => '0',
					'createtime' => '',
				);
			}
			$status = D(ucwords($table))->addAll($dataList);
			*/
        	//景区产品
        	$up = $this->where(array('id'=>$planid))->setField('status',3);
			if($up && $status){
				$this->commit();
			    return true;
			}else{
			    //执行回滚
			    $this->roll_back();
			    return false;
			}
        }
	}

/*
	 * CREATE TABLE IF NOT EXISTS `{$dbPrefix}{$tableName}` (
				  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,

				  `order_sn` int(10) unsigned NOT NULL COMMENT '订单号',

				  `seat` varchar(15) NOT NULL COMMENT '座椅ID',

				  `area` smallint(5) unsigned NOT NULL COMMENT '区域ID',

				  `status` tinyint(1) unsigned NOT NULL COMMENT '状态',

				  `soldtime` int(10) unsigned NOT NULL COMMENT '座位售出时间',

				  `checktime` int(10) unsigned NOT NULL COMMENT '检票时间',

				  PRIMARY KEY (`id`),

				  KEY `order_sn` (`order_sn`),

				  KEY `area` (`area`)

				)

				ENGINE=InnoDB 

				DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

				CHECKSUM=0

				ROW_FORMAT=DYNAMIC

				DELAY_KEY_WRITE=0
				;
	 * 
	 * CREATE TABLE IF NOT EXISTS `{$dbPrefix}{$tableName}` (
				  `id` smallint(3) NOT NULL AUTO_INCREMENT,
				  `order_sn` int(10) unsigned NOT NULL COMMENT '订单流水号',
				  `type` tinyint(1) unsigned NOT NULL COMMENT '订单类型1散客订单2团队订单',
				  `user_id` smallint(5) unsigned NOT NULL COMMENT '售票员ID',
				  `addsid` smallint(5) unsigned NOT NULL COMMENT '创建场景',
				  `money` decimal(10,2) NOT NULL COMMENT '订单总价',
				  `info` text NOT NULL COMMENT '订单详情',
				  `createtime` int(10) unsigned NOT NULL COMMENT '创建时间',
				  `status` tinyint(1) NOT NULL COMMENT '状态',
				  PRIMARY KEY (`id`),
				  KEY `user_id` (`user_id`),
				  KEY `order_sn` (`order_sn`),
				  KEY `addsid` (`addsid`),
				  KEY `addsid_2` (`addsid`)
				)
				ENGINE=InnoDB 
				DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
				CHECKSUM=0
				ROW_FORMAT=DYNAMIC
				DELAY_KEY_WRITE=0
				;
				CREATE TABLE IF NOT EXISTS `{$dbPrefix}{$tableName}` (
				  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
				  `order_sn` bigint(18) unsigned NOT NULL,
				  `area` smallint(5) unsigned NOT NULL,
				  `row` int(5) unsigned NOT NULL,
				  `print` int(2) unsigned NOT NULL,
				  `seat` varchar(10) NOT NULL,
				  `sale` varchar(500) NOT NULL,
				  `status` tinyint(1) unsigned NOT NULL,
				  `group` smallint(3) NOT NULL,
				  `sort` int(3) DEFAULT NULL,
				  `soldtime` int(10) unsigned DEFAULT NULL,
				  `checktime` int(10) unsigned NOT NULL,
				  `price_id` int(3) unsigned NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `order_sn` (`order_sn`),
				  KEY `area` (`area`),
				  KEY `seat` (`seat`),
				  KEY `group` (`group`),
				  KEY `IDX_SEAT_AREA` (`seat`,`area`),
				  KEY `price_id` (`price_id`) USING BTREE
				) 
				ENGINE=InnoDB 
				DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
				CHECKSUM=0
				ROW_FORMAT=DYNAMIC
				DELAY_KEY_WRITE=0
				;
	 * 创建数据表
	 */
	function createtable($tableName,$type){
		if (empty($tableName)) {
            return false;
        }
        //表前缀
        $dbPrefix = C("DB_PREFIX");
        if($type == 'seat'){
        	$sql = <<<sql
				CREATE TABLE `{$dbPrefix}{$tableName}` (
				  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
				  `order_sn` bigint(18) unsigned NOT NULL,
				  `idcard` varchar(18) DEFAULT NULL,
				  `area` smallint(5) unsigned NOT NULL,
				  `print` int(2) unsigned NOT NULL,
				  `seat` varchar(10) NOT NULL,
				  `sale` varchar(500) NOT NULL,
				  `status` tinyint(1) unsigned NOT NULL,
				  `group` smallint(3) NOT NULL,
				  `sort` int(3) DEFAULT NULL,
				  `number` int(3) DEFAULT NULL,
				  `soldtime` int(10) unsigned DEFAULT NULL,
				  `checktime` int(10) unsigned NOT NULL,
				  `price_id` int(3) unsigned NOT NULL,
				  `middle` tinyint(1) unsigned NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `order_sn` (`order_sn`),
				  KEY `area` (`area`),
				  KEY `seat` (`seat`),
				  KEY `group` (`group`),
				  KEY `IDX_SEAT_AREA` (`seat`,`area`),
				  KEY `price_id` (`price_id`) USING BTREE,
				  KEY `idcard` (`idcard`) USING BTREE
				)
				ENGINE=InnoDB 
				DEFAULT 
				CHARSET=utf8 
				ROW_FORMAT=DYNAMIC
				;
sql;
        }
		$res = M()->execute($sql);
		//dump($res);
    	return $res !== false;
	}
/**
	 * 写入坐席信息
	 * @param $table string 表名
	 * @param $data array 写入数据 
	 * @param $template_id int 模板ID
	 */
	function addSeat($table,$data,$template_id){
		$db = M(ucwords($table));
		$seat = implode(',', $data['seat']);
		$map = array(
			'template_id'=>$template_id,
			'id'=>array('in',$seat),
		);
		$area = M('Area')->where($map)->field('id,seatid')->select();
		 //循环区域
		foreach ($area as $k=>$v){
			$area_seat[$k] = unserialize($v['seatid']);
			//按排遍历
			foreach ($area_seat[$k] as $ka=>$va){
				//遍历座位
				foreach ($va as $ks=>$vs){
					$dataseat[] = array(
						'seat'		=>	$vs,
						'area'		=>	$v['id'],
					);
				}				
			}	
		}
		$status = $db->addAll($dataseat);
		if(!empty($data['auto_group'])){
			//写入分组信息
			$auto_group = implode(',',$data['auto_group']);
			$group_map = array(
				'id'	=>	array('in',$auto_group),
				'status'=>	'1',
				'product_id'=>get_product('id'),
				'template_id'=>$template_id,
			);
			$group  = M('AutoSeat')->where($group_map)->field('id,sort,seat')->select();
			foreach ($group as $ke=>$va){
				$group_seat[$ke] = unserialize($va['seat']);
				//按排遍历
				foreach ($group_seat[$ke] as $ka=>$ve){
					if(!empty($ve['seat'])){
						$map = array(
							'area'	=>	$ve['id'],
							'seat'	=>	array('in',$ve['seat']),
						);
						$up_seat = $db->where($map)->setField(array('group'=>$va['id'],'sort'=>$va['sort']));
						if($up_seat == false){
							error_insert('400113');
							//删除已创建的计划表 TODO
							//$this->roll_back($table);
							return false;
						}
					}	
				}
			}
		}
		
		if($status){return true;}else{error_insert('4001012');return false;}
	}
	/**
	 * 删除表
	 * @param $param string || array 表名
	 */

	function roll_back($param = ''){
		//表前缀
        $dbPrefix = C("DB_PREFIX");
		if(is_array($param)){
			//同时删除多张表
			foreach ($param as $val){
				$sql = <<<sql
				DROP TABLE {$dbPrefix}{$val};
sql;
    			$res = M()->execute($sql);
    			return $res !== false;
			}
		}else{
			$sql = <<<sql
				DROP TABLE {$dbPrefix}{$param};
sql;
    			$res = M()->execute($sql);
    			return $res !== false;

		}
	}
	/*计划任务缓存*/
	function plan_cache($proid = null ){
		F('planlist',null);
	 	$productId = $proid ? $proid : get_product('id');	 	
	 	$data = $this->where(array('status'=>'2'))->select();
        if (empty($data)) {
            return false;
        }
        $cache = array();
        foreach ($data as $rs) {
        	$rs['product_name'] = productName($rs['product_id'],1);
        	//缓存
        	F('Plan_'.$rs['id'], $rs);
        	//缓存可售列表
        	$plan[] = $rs['id']; 
        }
        F('planlist',implode(',',$plan));
        //S('planlist',json_encode($plan));
        return $data;
	}

	/**
	 * 更新类型销控
	 * 1、检测当前配额读取缓存配额、同步配额、更新配额
	 */
	function update_pin($type, $plan_id, $product_id){
		//1、读取配额
		$quota = load_redis('get','pin_'.$product_id.'_'.$planid.'_'.$type);
		//2、同步配额
		
		//3、更新配额
		
	}
	//注册销售类型的整体销控
    //pin_sales 销售控制表
    function pin_sales_type($planid,$product){
    	$procof = cache('ProConfig');
    	$procof = $procof[$product]['1'];
	    $baseData = [
	    	'plan_id'		=>	$planid,
	    	'product_id'	=>	$product,
    		'number'		=>	'',//可售总量
    		'often'			=>	$procof['channel_often_quota'],//常规渠道
    		'political'		=>	$procof['channel_political_quota'],//政企渠道
    		'full'			=>	$procof['channel_full_quota'],//全员销售
    		'directly'		=>	$procof['channel_directly_quota'],//电商直营
    		'electricity'	=>	$procof['channel_electricity_quota'],//电商渠道
    	];
    	$satus = D('Item/PinSales')->add($baseData);
    	if(!$satus){
    		$err = [
    			'location'	=>	'Item/Model/PlanModel',
    			'action'	=>	'pin_sales_type',
    			'data'		=>	$baseData,
    			'msg'		=>	'按销售类型注册销控数据失败',
    			'datetime'	=>	time(),
    		];
    		load_redis('lpush','LUBERR',json_encode($err));
    	}else{
    		//在内存数据库存储
    		load_redis('set','pin_'.$product.'_'.$planid.'_often',$baseData['often']);
    		load_redis('set','pin_'.$product.'_'.$planid.'_political',$baseData['political']);
    		load_redis('set','pin_'.$product.'_'.$planid.'_full',$baseData['full']);
    		load_redis('set','pin_'.$product.'_'.$planid.'_directly',$baseData['directly']);
    		load_redis('set','pin_'.$product.'_'.$planid.'_electricity',$baseData['electricity']);
    	}

    	return true;
   	}
   	/**
   	 * 销毁过期内存存储
   	 * @param  int $product 产品ID
   	 * @param  int $planid  计划ID
   	 * @return int          [description]
   	 */
   	function destroyed($product,$planid){
   		load_redis('delete','pin_'.$product.'_'.$planid.'_often');
    	load_redis('delete','pin_'.$product.'_'.$planid.'_political');
    	load_redis('delete','pin_'.$product.'_'.$planid.'_full');
    	load_redis('delete','pin_'.$product.'_'.$planid.'_directly');
    	load_redis('delete','pin_'.$product.'_'.$planid.'_electricity');
   	}
	/**
     * 插入成功后的回调方法
     */
    protected function _after_insert(&$data) {
        $this->plan_cache();
    }
    /**
     *更新成功后的回调方法
     *
     */
     protected function _after_update(&$data){
     	$this->plan_cache();
     }
}