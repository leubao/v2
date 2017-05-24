<?php
// +----------------------------------------------------------------------
// | LubTMP 客户管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiRan 
// +----------------------------------------------------------------------
namespace Crm\Controller;
use Common\Controller\ManageBase;
use Libs\Service\Operate;
use Common\Model\Model;
class IndexController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
	}
	/*管理客户*/
	function index(){
		/*获得客户分组信息*/
		//$map["itemid"] = array(array('EQ','0'),array('EQ',\Libs\Util\Encrypt::authcode($_SESSION['lub_imid'], 'DECODE')),'or');  //选择系统默认以及自己添加的分组
		$map['status'] = '1';
		$list = Operate::do_read('CrmGroup',1,$map,'status DESC,id DESC');
		$this->assign('data',$list)->display();
	}
	/*客户列表*/
	function grouplist(){
		$groupid = I('id');    //客户分组id
		$type = I('type');
		$name = I('name');
		$level = I('level');//级别
		$status = I('status');
		$product_id = get_product('id');
		//$map["id"] = $groupid;
		//$map =  array('id'=>$groupid,'type'=>$type,'product_id'=>$product_id);
		$map =  array('groupid'=>$groupid);
		/*搜索查询*/
		if(!empty($name)){
			if($type <> '2'){
				$map['name'] = array("like","%".$name."%");
			}else{
				$map['nickname'] = array("like","%".$name."%");
			}
			$this->assign("name",$name);
		}
		$map['level'] = $level ? $level : '16';
		if(!empty($status)){
			$map['status'] = $status;
		}
		/*搜索END查询级别END*/
		if($type == '1'  || $type == '3'){
			//企业
			$db = "Crm";
		}elseif ($type == '4') {
			//个人
			$db = "User";
		}
		$this->basePage($db,$map,array('status'=>"DESC","id"=>"DESC"));
		$this->assign ('groupid',$groupid)
			 ->assign('type',$type)
			 ->assign('map',$map)
			 ->display();	
	}
	/**
	 * 新增渠道商
	 */
	function add(){
		if(IS_POST){
			$data["itemid"]      = \Libs\Util\Encrypt::authcode($_SESSION['lub_imid'], 'DECODE');
			$data["product_id"]  = get_product('id');
			$data["create_time"] = time();
			$data["salesman"]    = $_POST["user_id"];
			$data['param']	=	serialize(array(
				'print' => I('post.prints'),
				'rebate' => I('post.rebate'),
				));
			$add = Operate::do_add("Crm",$data);
			if($add){
				D('Crm/Crm')->crm_cache();
				$this->srun('新增成功!',array('tabid'=>$this->menuid,'closeCurrent'=>true,'divid'=>$this->menuid));
			}else{
				$this->erun('新增失败!');
			}			
		}else{
			$groupid = I('groupid');//客户分组id
			$type = I('type');
			if($type == '1'){
				//企业
				$level = Operate::do_read('Role',1,array('parentid'=>$this->config['channel_role_id'],'is_scene'=>3,'status'=>1),array('id'=>DESC));
				$this->assign("level",$level);
			}elseif ($type == '2' || $type == '3') {
				//个人
				$this->assign('role_id',self::$Cache['Config']['guide']);
			}
			$this->assign('type',$type)
				->assign("groupid",$groupid)
				->display();
		}	
	}	
	/**
	 * 删除客户
	 */
	function delete(){
		$id  = I("get.id");
		$condition = array("id"=>$id);
		$type = I('get.type');
		//企业和政府都要删除员工
		if($type <> '2'){
			if(Operate::do_read('User',0,array('cid'=>$id))){
				$this->erun('删除失败，存在员工!');
			}else{
				$del = Operate::do_del("Crm",$condition);
			}
		}else{
			//个人
			$del = Operate::do_del("User",$condition);
		}
		if($del){
			$this->srun('删除成功!',array('tabid'=>$this->menuid,'closeCurrent'=>true,'divid'=>$this->menuid));
		}else{
			$this->erun('删除失败!');
		}					
	}
	/**
	 * 修改客户
	 */
	function edit(){
		if(IS_POST){
			$info = I('post.');
			$param	=	serialize(array(
				'prints' => $info['prints'],
				'rebate' => $info['rebate'],
				));
			$data = array_merge($info,array('salesman'=>$_POST["orgLookup_ids"],'param'=>$param));
			$model = D('Crm');
			$up = $model->save($data);
			
			//更新所有二级及二级员工的分组属性
			//读取关系链接
			$channel_agents_id = agent_channel($info['id'],2);
			$model->where(array('id'=>array('in',$channel_agents_id)))->setField('groupid',$info['groupid']);
			//更新商户下所有员工所属的分组
			$user_up = M('User')->where(array('cid'=>array('in',$channel_agents_id)))->setField('groupid',$info['groupid']);
			if($up != false){
				$model->crm_cache();
				$this->srun('修改成功!',array('tabid'=>$this->menuid,'closeCurrent'=>true,'divid'=>$this->menuid));
			}else{
				$this->erun('修改失败!');
			}
		}else{
			$id = I('id');
			$type = I('type');
			$groupid = I("groupid");//客户分组id
			//企业
			$level = Operate::do_read('Role',1,array('parentid'=>$this->config['channel_role_id'],'is_scene'=>3,'status'=>1),array('id'=>DESC));//dump();
			$group = F('CrmGroup');
			$list = Operate::do_read('Crm',0,array('id'=>$id));
			$list['param'] = unserialize($list['param']);
			$this->assign("level",$level)
				->assign('group',$group)
				->assign('type',$type)
				->assign("groupid",$groupid)
				->assign('data',$list)
				->display();
		}	
	}
	/**
	 * 客户详情
	 */
	function detail(){
		$id = I("get.id");  //客户id
		$condition = array("id"=>$id);
		$list = Operate::do_read('Crm',0,$condition);
		$this->assign("data",$list);
		$this->display();
	}
	/**
	 * 导游等详情
	 */
	function gdetail(){
		$id = I("get.id");
		$type = I("get.type");
		if($type==1 ){ //政企
			$condition = array("cid"=>$id);
		}else{
			$condition = array("id"=>$id);		
		}
		$list = M("User")->join('LEFT JOIN '.C('DB_PREFIX').'user_data data ON data.user_id = '.C('DB_PREFIX').'user.id' )->where($condition)->find();
		$sale = $this->lookup($list["salesman"]);
		$list["salesman"] = $sale["nickname"];
		
		$this->assign("data",$list);

		$this->display();
	}	
	/**
	 * 客户分组
	 */
	function group(){
		//$data["itemid"] = array(array('EQ','0'),array('EQ',\Libs\Util\Encrypt::authcode($_SESSION['lub_imid'], 'DECODE')),'or');  //选择系统默认以及自己添加的分组
		$map['product_id'] = get_product('id');
		$this->basePage('CrmGroup',$data,array("status"=>"DESC","create_time"=>"DESC"));
		$this->display();		
	}
	/**
	 *客户分组新增
	 */
	function groupadd(){
		if(IS_POST){
			$data["itemid"] = \Libs\Util\Encrypt::authcode($_SESSION['lub_imid'], 'DECODE');
			$data["create_time"] = time();
			$data['price_group'] = implode(',',$_POST['price_group']);
			$add = Operate::do_add("CrmGroup",$data);
			if($add){
				$this->srun('新增成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('新增失败!');
			}
		}else{
			$price = Operate::do_read('TicketGroup',1,array('status'=>1));
			$this->assign('price',$price)
				->assign('product_id',get_product('id'))
				->display();
		}
	}
	/**
	 * 客户分组删除
	 */
	function groupdelete(){
		$id = I("get.id");
		$condition = array("id"=>$id);
		$list = Operate::do_read('Crm',0,array('groupid'=>$id));		
		if(!empty($list)){ //系统默认设置，不可删除
			$this->erun('当前分组下存在数据，不能直接!');
		}else{
			$del = Operate::do_del("CrmGroup",$condition);
			if($del){
				$this->srun('删除成功!',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				$this->erun('删除失败!');
			}			
		}
	}
	/**
	 * 客户分组修改
	 */
	function groupedit(){
		if(IS_POST){
			$pinfo = I('post.');
			$data = array(
				'id' => $pinfo['id'],
				'name' => $pinfo['name'],
				'type' => $pinfo['type'],
				'privilege' => $pinfo['privilege'],
				'settlement'=> $pinfo['settlement'],
				'status' => $pinfo['status'],
				'price_group' => implode(',',$pinfo['price_group']),
				);

			$up = D("CrmGroup")->save($data);
			if($up != false){
				$this->srun('更新成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('修改失败!');
			}
		}else{
			$id = I("id");
			$list = Operate::do_read('CrmGroup',0,array("id"=>$id));
			$price = Operate::do_read('TicketGroup',1,array('status'=>1));
			$this->assign('price',$price)
				->assign("id",$id)
				->assign('data',$list)
				->assign('product_id',get_product('id'))
				->display();			
		}
	}
	/*窗口新增导游*/
	function add_guide(){
		if(IS_POST){
			$info = I('post.');
			$password = '123456';
            $verify = genRandomString();
            $data = array(
                'username' => $info['phone'],
                'nickname' => $info['username'],
                "item_id"  => '0',
                'product'  => '0',
                'defaultpro'=>'0',
                "create_time" => time(),
                "update_time" => time(),
                "is_scene" => 4,   //应用场景为4，全员销售
                "cid"    => '0',
                "verify" => $verify,
                'phone' => $info['phone'],
                'email'  => '0',
                'role_id' => '0',
                'legally' => $info['legally'],
                'groupid' => $info['groupid'],
                "password" => md5($password.md5($verify)),
                'status' => '1',
                'remark' => '窗口增加...'
            );
            $user_id = D('User')->add($data);
            if($user_id){
                D('UserData')->add(array('user_id'=>$user_id));
                $this->srun("新增成功...",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
            }else{
                $this->erun("新增失败..."); 
            }
		}else{
			$groupid = I('groupid');//客户分组id
			$type = I('type');
			$this->assign('type',$type)->assign('groupid',$groupid)->display();
		}
	}
	/**
	 * 代售点列表
	 */
	function userslist(){
		$data["groupid"] = I("get.groupid") != "" ? I("get.groupid"):$_POST["groupid"];   //客户分组id
		$data["item_id"] = \Libs\Util\Encrypt::authcode($_SESSION['lub_imid'], 'DECODE');
		$data["cid"]     = I("get.cid") != "" ?I("get.cid"):$_POST["cid"];//客户id
		$this->assign("groupid",$data["groupid"]);
		$this->assign("cid",$data["cid"]);
		$this->basePage('User',$data);
		$this->display();
	}
	//新增代售点
	function adduser(){
		if(IS_POST){
			$info = I('post.');
			$verify = genRandomString();
			$data = array(
				'username' => $info['username'],
				'nickname' => $info['nickname'],
				"item_id"  => \Libs\Util\Encrypt::authcode($_SESSION['lub_imid'], 'DECODE'),
				'product'  =>get_product('id'),
				'defaultpro'=>get_product('id'),
				"create_time" => time(),
				"update_time" => time(),
				"is_scene" => 3,   //应用场景为3，渠道
				"cid"    =>	$info['crmid'],
				"verify" => $verify,
				'phone'	=> $info['phone'],
				'email'	 => $info['email'],
				'role_id' => $info['role_id'],
				'groupid' => $info['groupid'],
				"password" => md5('123456'.md5($verify)),
				'status' => $info['status'],
			);
			$user_id = D('User')->add($data);
			if($user_id){
				D('UserData')->add(array('user_id'=>$user_id));
				$this->srun('新增成功!',array('dialogid'=>'crm_user_list','closeCurrent'=>true));
			}else{
				$this->erun('新增失败!');
			}
		}else{
			$data["cid"]     = I("get.cid") != "" ?I("get.cid"):$_POST["cid"];//客户id
			$ginfo = I('get.');
			/*显示相关的角色id TODO*/
			$map['parentid'] = $this->config['channel_role_id'];
			$map['is_scene'] =3;$map['status'] = 1;
			$role = M("Role")->where($map)->field('id,name')->select();	
			$this->assign("ginfo",$ginfo)->assign('role',$role)->display();
		}
	}
	//编辑代售点
	function edituser(){
		if(IS_POST){
			$pinfo = I('post.');
			if(D('User')->save($pinfo)){
				$this->srun('更新成功!',array('dialogid'=>'crm_user_list','closeCurrent'=>true));
			}else{
				$this->erun('更新失败!');
			}
		}else{
			$id = I("get.id");
			if(empty($id)){
				$this->erun('参数错误!');
			}
			/*显示相关的角色id TODO*/
			$map['parentid'] = $this->config['channel_role_id'];
			$map['is_scene'] =3;$map['status'] = 1;
			$role = M("Role")->where($map)->field('id,name')->select();	
			$data = M('User')->where(array('id'=>$id))->find();
			$this->assign("data",$data)->assign('role',$role)->display();
		}
	}
	/**
	 * 代售点删除
	 */
	function deleteusers(){
		$id = I("get.id");
		$condition = array("id"=>$id);
		
		$del = Operate::do_del("User",$condition);
		$con = array("user_id"=>$id);
		$data_del = Operate::do_del("UserData",$con); //同时删除user_data表的数据

		if($del){
			$this->srun('删除成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		}else{
			$this->erun('删除失败!');
		}						
	}
	
	/** 
	 *  查询充值金额
	 */
	function checkcash(){
		$crm_id = I('id');//商户id
		$type = I('type') ? I('type') : '0';
		$channel = I('get.channel');
		if(empty($crm_id) || empty($channel)){$this->erun("参数错误!");}
		/*查询条件START*/
		$start_time = I("starttime");
		$end_time   = I("endtime");
		$groupid = I('get.groupid');
		$this->assign("starttime",$start_time);
		$this->assign("endtime",$end_time)->assign('groupid',$groupid)->assign('channel',$channel);
		/*查询条件END*/
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }elseif(empty($type)){
        	//默认只查询3个月内的数据
        	$start_time = strtotime("-1 month");
            $end_time = time();
            $where['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }
        if(!empty($type)){
        	$where['type'] = $type;
        }
        if($channel == '4'){
        	$where["crm_id"] = $crm_id;
        }else{
        	$where["crm_id"] = array('in',agent_channel($crm_id));
        }
		$this->basePage('CrmRecharge',$where,'id DESC');
		$this->assign("cid",$crm_id)
			->assign("type",$type)
			->display();
	}

	/**
	 *  充值
	 */
	function recharge(){
		if(IS_POST){
			$cash   = I("post.cash");   //当前充值金额
			$id     = I("post.crmid");  //充值的客户id
			$channel= I('post.channel');
			$groupid= I('post.groupid');
			$remark = I('post.remark'); //重置备注
			$model = new \Think\Model();
			$model->startTrans();
			//判断是企业还是个人1企业4个人
			$crmData = array('cash' => array('exp','cash+'.$cash),'uptime' => time());
			if($channel == '1'){
				//渠道商客户
				$c_pay = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$id))->setField($crmData);
			}
			if($channel == '4'){
				//个人客户
				$c_pay = $model->table(C('DB_PREFIX')."user")->where(array('id'=>$id))->setField($crmData);
			}
			//充值成功后，添加一条充值记录
			$data = array(
				'cash'		=>	$cash,
				'user_id'	=>	get_user_id(),
				'crm_id'	=>	$id,
				'createtime'=>	time(),
				'type'		=>	'1',
				'balance'	=>  balance($id,$channel),
				'tyint'		=>	$channel,//客户类型1企业4个人
				'remark'	=>	$remark,
			);		
			$recharge = $model->table(C('DB_PREFIX')."crm_recharge")->add($data);
			if($c_pay && $recharge){
				$model->commit();//成功则提交
				$this->srun('充值成功!',array('dialogid'=>'checkcash','closeCurrent'=>true));
			}else{
				$model->rollback();//不成功，则回滚
				$this->erun("充值失败!");
			}
		}else{
			$crmid = I("id");  //客户的id
			$groupid = I('get.groupid');
			$channel = I('channel');
			if(empty($crmid) || empty($channel)){$this->erun("参数错误，请重新选择商户!");}
			//查询当前客户分组
			$this->assign("crmid",$crmid)->assign('groupid',$groupid)->assign('channel',$channel);
			$this->display();
		}
	}
	/*渠道退款*/
	function refund(){
		if(IS_POST){
			$cash   = I("post.cash");   //当前充值金额
			$id     = I("post.crmid");  //充值的客户id
			$channel= I('post.channel');
			$groupid= I('post.groupid');
			$remark = I('post.remark'); //重置备注
			$model = new \Think\Model();
			$model->startTrans();
			//判断是企业还是个人1企业4个人
			$crmData = array('cash' => array('exp','cash-'.$cash),'uptime' => time());
			if($channel == '1'){
				//渠道商客户
				$c_pay = $model->table(C('DB_PREFIX')."crm")->where(array('id'=>$id))->setField($crmData);
			}
			if($channel == '4'){
				//个人客户
				$c_pay = $model->table(C('DB_PREFIX')."user")->where(array('id'=>$id))->setField($crmData);
			}
			$data = array(
				'cash'		=>	$cash,
				'user_id'	=>	get_user_id(),
				'crm_id'	=>	$id,
				'createtime'=>	time(),
				'type'		=>	'5',
				'balance'	=>  balance($id,$channel),
				'tyint'		=>	$channel,//客户类型1企业4个人
				'remark'	=>	$remark,
			);	
			$recharge = $model->table(C('DB_PREFIX')."crm_recharge")->add($data);
			if($c_pay && $recharge){
				$model->commit();//成功则提交
				$this->srun('退款成功!',array('dialogid'=>'checkcash','closeCurrent'=>true));
			}else{
				$model->rollback();//不成功，则回滚
				$this->erun("退款失败!");
			}
		}else{
			$crmid = I("id");  //客户的id
			$groupid = I('get.groupid');
			$channel = I('channel');
			if(empty($crmid) || empty($channel)){$this->erun("参数错误，请重新选择商户!");}
			//查询当前客户分组
			$this->assign("crmid",$crmid)->assign('groupid',$groupid)->assign('channel',$channel);
			$this->display();
		}
	}
	/**
	 * 产品授权
	 */
	function auth_product(){
		if(IS_POST){
			$pinfo = I('post.');
			//更新渠道商产品集
			$status = M('Crm')->where(array('id'=>$pinfo['crm_id']))->setField('product_id',implode(',',$pinfo['product']));
			foreach ($pinfo['product'] as $key => $value) {
				$data = array(
					'crm_id' => $pinfo['crm_id'],
					'product_id' => $value,
				);
				if(!M('CrmQuota')->where($data)->find()){
					D('CrmQuota')->add($data);
				}
			}
			if($status){
				$this->srun('授权成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("授权失败!");
			}
		}else{
			$id = I('get.id',0,intval);
			if(empty($id)){
				$this->erun('参数错误!');
			}
			$product = M('Product')->where(array('status'=>1))->field('id,name')->select();
			$info = Operate::do_read('Crm',0,array('id'=>$id));
			$this->assign('data',$info)
				->assign('product',$product)
				->display();
		}
	}
	/**
	 * 配额管理
	 */
	function quota(){
		if(IS_POST){
			$pinfo = I('post.');
			foreach ($pinfo['quota'] as $key => $value) {
				$status = M('CrmQuota')->where(array('product_id'=>$key,'crm_id'=>$pinfo['crm_id']))->setField('quota',$value);
			}
			$this->srun('更新成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		}else{
			$id = I('get.id',0,intval);
			if(empty($id)){
				$this->erun('参数错误!');
			}
			$info = Operate::do_read('Crm',0,array('id'=>$id));
			$quota = M('CrmQuota')->where(array('crm_id'=>$id))->field('crm_id,product_id,quota')->select();
			$this->assign('data',$info)
				->assign('quota',$quota)
				->display();
		}
	}
	/*重置密码*/
	function reset_pwd(){
		$id = I('get.id');
		$verify = genRandomString(6);
		$data =  array(
			'password' => $this->hashPassword('123456',$verify),
			'verify' => $verify,
			'status' => '1',
		);
		if(M('User')->where(array('id'=>$id))->save($data)){
			$this->srun('重置成功!',array('dialogid'=>'crm_user'));
		}else{
			$this->erun('重置失败!');
		}
	}
	/*启用停用商户
	* status 1 启用 0 禁用
	*/
	function start_us(){
		$id = I('get.id');
		$info = M('Crm')->where(array('id'=>$id))->find();
		$model = new Model();
		$model->startTrans();
		if($info['status'] == '1'){
			//停用
			$crm_start = $model->table(C('DB_PREFIX').'crm')->where(array('id'=>$id))->setField('status',0);
			$user_start = $model->table(C('DB_PREFIX').'user')->where(array('cid'=>$id))->setField('status',0);
		}else{
			//启用
			$crm_start = $model->table(C('DB_PREFIX').'crm')->where(array('id'=>$id))->setField('status',1);
			$user_start = $model->table(C('DB_PREFIX').'user')->where(array('cid'=>$id))->setField('status',1);
		}
		if($crm_start && $user_start){
			$model->commit();//提交事务
			$this->srun('停用成功!',array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$model->rollback();//事务回滚
			$this->erun('停用失败!');
		}
	}
	/**
     * 对明文密码，进行加密，返回加密后的密文密码
     * @param string $password 明文密码
     * @param string $verify 认证码
     * @return string 密文密码
     */
    public function hashPassword($password, $verify = "") {
        return md5($password . md5($verify));
    }
    /*
    *授信额导出
	*
    */
   function export_credit(){
   		$ginfo = I('get.');
   		$where['crm_id'] = array('in',agent_channel($ginfo['id']));
   		if(!empty($ginfo['type'])){
   			$where['type'] = $ginfo['type'];
   		}
   		if (!empty($ginfo['starttime']) && !empty($ginfo['endtime'])) {
            $start_time = strtotime($ginfo['starttime']);
            $end_time = strtotime($ginfo['endtime']) + 86399;
            $where['createtime'] = array(array('EGT', $start_time), array('ELT', $end_time), 'AND');
        }

   		$list = M('CrmRecharge')->where($where)->order('createtime DESC')->select();
   		foreach ($list as $k => $v) {
   			$data[] = array(
   				'title'		=>	crmName($v['crm_id'],1),
	   			'user'		=>	userName($v['user_id'],1,1),
	   			'sn'		=>	$v['order_sn'],
	   			'type'		=>	operation($v['type'],1),
	   			'money'		=>	$v['cash'],
	   			'balance'	=>	$v['balance'],
	   			'datetime'	=>	date('Y-m-d H:i:s',$v['createtime']),
	   			'remark'	=>	$v['remark'],
   			);
   		}
   	
   		$headArr = array(
   			'title'		=>	'渠道商名称',
   			'user'		=>	'操作员',
   			'sn'		=>	'单号',
   			'type'		=>	'类型',
   			'money'		=>	'金额',
   			'balance'	=>	'余额',
   			'datetime'	=>	'操作时间',
   			'remark'	=>	'备注',
   		);
   		$filename = crmName($ginfo['id'],1)."授信记录";
   		return \Libs\Service\Exports::getExcel($filename,$headArr,$data);
   		exit;
    }

}