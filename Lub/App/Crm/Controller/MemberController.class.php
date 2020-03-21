<?php
// +----------------------------------------------------------------------
// | LubTMP  会员管理
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Crm\Controller;
use Common\Controller\ManageBase;
use Common\Model\Model;
class MemberController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
	}
	//会员管理
	function index()
	{
		if(IS_POST){
			$starttime = I('starttime');
		    $endtime = I('endtime') ? I('endtime') : date('Y-m-d',time());
		    $group = I('group');
		    $phone = I('phone');
		    $card = I('card');
		    $this->assign('starttime',$starttime)
		        ->assign('endtime',$endtime)
		        ->assign('group_id',$group)
		        ->assign('phone',$phone)
		        ->assign('card',$card);
		    $map = [];
		    if(!empty($phone) || !empty($card)){
		    	if(!empty($card)){
		    		$map['idcard'] = $card;
		    	}
		    	if(!empty($phone)){
		    		$map['phone'] = $phone;
		    	}
		    }else{
		    	$starttime = strtotime($starttime);
			    $endtime = strtotime($endtime) + 86399;
			   	$map['create_time'] = array(array('EGT', $starttime), array('ELT', $endtime), 'AND');
		    }
		    if(!empty($group)){
		    	$map['group_id'] = $group;
		    }
		}
		$map['status']	= 1;
		$this->basePage('Member',$map,'id DESC,status DESC');
		$group = M('MemberType')->where(['status'=>1])->order('status DESC,id DESC')->field('id,title')->select();
		$this->assign ('group',$group)
			 ->assign('map',$map)
			 ->display();
	}

	//会员类型
	public function types()
	{
		$this->basePage('MemberType','','id DESC,status DESC');
		$this->display();
	}
	public function add_type(){
		if(IS_POST){
			$pinfo = I('post.');
			$model = D('Crm/MemberType');
			if($model->insert($pinfo)){
				$model->mem_group_cache();
				$this->srun('新增成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('新增失败!');
			}			
		}else{
			$printer = D('Printer')->where(['status'=>1,'product'=>$this->pid])->field('id,title')->select();
			$this->assign('printer',$printer);
			$this->display();
		}
	}
	public function edit_type(){
		if(IS_POST){
			$pinfo = I('post.');
			$model = D('Crm/MemberType');
			if($model->update($pinfo)){
				$model->mem_group_cache();
				$this->srun('编辑成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('编辑失败!');
			}			
		}else{
			$ginfo = I('get.');
			$model = D('Crm/MemberType');
			$info = $model->where(['id'=>$ginfo['id']])->field('type,create_time,update_time,user_id',true)->find();
			$rule = json_decode($info['rule'],true);
			$info['rule'] = [
				'datetime' => [
					'starttime' => date('Y-m-d',$rule['datetime']['starttime']),
					'endtime'	=> date('Y-m-d',$rule['datetime']['endtime'])
				],
				'efftime'	=> [
					'start'	=>	date('Y-m-d',$rule['efftime']['start']),
					'end'	=>	date('Y-m-d',$rule['efftime']['end'])
				],
				'area'		=> $rule['area'],
				'number'	=> $rule['number'],//次卡，或单日入园次数
			];
			$printer = D('Printer')->where(['status'=>1,'product'=>$this->pid])->field('id,title')->select();
			$this->assign('data',$info)->assign('printer',$printer);
			$this->display();
		}
	}
	public function del_type()
	{
		$id = I('get.id',0,intval);
		if($id == '1'){
			$this->erun('系统内置类型禁止删除!');
		}
		if(!empty($id)){
			$map = array("id"=>$id);
			$model = D('Item/MemberType');
			//停用状态的数据，删除会直接删除
			if($model->where($map)->getField('status') == '0'){
				$del = $model->where($map)->delete();
			}else{
				$del = $model->where($map)->setField('status','0');
			}
			if($del){
				$this->srun("删除成功!", array('tabid'=>$this->menuid.MODULE_NAME));
			}else {
				$this->erun('删除失败!');
			}
		}else{
			$this->erun('参数错误!');
		}
	}
	//会员列表
	public function member()
	{
		$model = D('Item/Member');
		$info = $model->where($map)->select();
		$this->display();
	}
	//新增会员
	public function add_member()
	{
		if(IS_POST){
			$pinfo = I('post.');
			$model = D('Member');
			//判断身份证号是否唯一
			if($model->where(['idcard'=>$pinfo['idcard'],'status'=>['gt',0]])->field('id')->find()){
				$this->erun("添加失败,该身份证已注册");
				return false;
			}
			$data = [
				'source'	=>  '1',
				'no_number' =>  date('YmdH').genRandomString(6,1),
				'idcard'	=>	strtolower(trim($pinfo['idcard'])),
				'nickname'	=>	$pinfo['content'],
				'phone'		=>	$pinfo['phone'],
				'group_id'	=>	(int)$pinfo['group'],
				'user_id'	=>	get_user_id(),//窗口时写入办理人
				'thetype'	=>	$pinfo['type'], //凭证类型
				'remark'	=>	$pinfo['remark'],//备注
				'status'	=>	'1',
			];
			if($model->token(false)->create($data)){
				$result = $model->add();
				if($result){
					$this->srun('办理成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun("添加失败:");
				}
			}else{
				$this->erun("添加失败tokern".$e);
			}
			
		}else{
			$type = F('MemGroup');
			if(empty($type)){
				D('Crm/MemberType')->mem_group_cache();
			}
			$this->assign('type',$type)->display();
		}
	}
	function edit_member(){
		if(IS_POST){
			$pinfo = I('post.');

		}else{
			$id  = I('id');
			if(empty($id)){$this->erun('参数错误');}
			$info = D('Member')->where(['id'=>$id])->find();
			$this->assign('data',$info)->display();
		}
	}
	//删除会员
	public function del_member()
	{
		$id  = I("get.id");
		$data = [
			'update_time'	=>	time(),
			'user_id'		=>	get_user_id(),
			'status'		=> '-1',
		];
		$del = D('Member')->where(['id'=>$id])->save($data);
		if($del){
			//记录删除日志
			$this->srun('删除成功!',array('tabid'=>$this->menuid));
		}else{
			$this->erun('删除失败!');
		}
	}
	//类型配置
	public function config()
	{
		if(IS_POST){
			$map = array("id"=>$id);
			$model = D('Item/MemberType');
			$info = $model->where($map)->find('rule');
			$info['rule'] = json_decode($info['rule'],true);
			$rule = [

			];
			//$info['rule']['year'] = 
			$status = $model->where($map)->setField('rule',json_encode($rule));
			//rule
		}else{
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$map = array("id"=>$id);
				$model = D('Item/MemberType');
				$info = $model->where($map)->find();
				$this->assign('data',$info)->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}
	//类型详情
	public function public_type_info()
	{
		$id = I('get.id',0,intval);
		if(!empty($id)){
			$map = array("id"=>$id);
			$model = D('Crm/MemberType');
			$info = $model->where($map)->find();
			$info['rule'] = json_decode($info['rule'],true);
			$this->assign('data',$info)->display();
		}else{
			$this->erun('参数错误!');
		}
	}
	public function year()
	{
		$groupid = I('id');    //客户分组id
		$type = I('type');
		$name = I('name');
		$level = I('level');//级别
		$status = I('status');
		$product_id = get_product('id');
		/*搜索查询*/
		if(!empty($name)){
			if($type <> '2'){
				$map['name'] = array("like","%".$name."%");
			}else{
				$map['nickname'] = array("like","%".$name."%");
			}
			$this->assign("name",$name);
		}
		//$map['level'] = $level ? $level : '16';
		if(!empty($status)){
			$map['status'] = $status;
		}
		$this->basePage('Member',$map,array('update_time'=>"DESC","id"=>"DESC"));
		$this->assign ('groupid',$groupid)
			 ->assign('type',$type)
			 ->assign('map',$map)
			 ->display();
	}
	/**
	 * 年卡设置
	 * @Company  承德乐游宝软件开发有限公司
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2017-11-06
	 * @return   [type]        [description]
	 */
	public function config_year()
	{
		$map = array("id"=>'1');
		$model = D('Crm/MemberType');
		$info = $model->where($map)->find();
		$info['rule'] = json_decode($info['rule'],true);
		if(IS_POST){
			$pinfo = I('post.');
			$rule = [
				'area' => $pinfo['area'],
				'day'  => (int)$pinfo['day'],
				'datetime' => [
					'starttime' => date('Ymd',strtotime($pinfo['starttime'])),
					'endtime'	=> date('Ymd',strtotime($pinfo['endtime']))
				],
				'overdue'	=> date('Ymd',strtotime($pinfo['overdue'])),//过期时间
			];
			$data = [
				'update_time' => time(),
				'money'		  => $pinfo['money'],
				'rule'		  => json_encode($rule),
			];
			if($model->where($map)->save($data)){
				//生成year.config.js
				$this->srun('配置成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('配置失败!');
			}
		}
		$url = $this->config['siteurl'].'card/apply.html';
		$this->assign('url',$url)
			->assign('data',$info)
			->display();
	}
	//会员详情
	public function public_member()
	{
		$id = I('get.id',0,intval);
		if(!empty($id)){
			$map = array("id"=>$id);
			$model = D('Crm/Member');
			$info = $model->where($map)->find();
			$this->assign('data',$info);
			$this->display();
		}else{
			$this->erun('参数错误!');
		}
	}
	// 续期
	public function renewal()
	{
		if(IS_POST){
			$pinfo = I('post.');
			$model = D('Member');
			$updata = [
				'id'		=>	$pinfo['id'],
				'renewal'	=>	$pinfo['renewal'],
				'group_id'	=>	(int)$pinfo['group'],
				'user_id'	=>	get_user_id(),//窗口时写入办理人
			];
			$updata['endtime'] = D('Member')->validity($updata);
			if($model->token(false)->create($updata)){
				$result = $model->save();
				if($result){
					$this->srun('续期成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun("续期失败:");
				}
			}else{
				$this->erun("续期失败tokern".$e);
			}
		}else{
			//读取会员详情，以及会员卡类型
			$id = I('id');
			$info = D('Member')->where(['id'=>$id])->field('id,nickname,group_id,endtime')->find();
			if(empty($info)){
				$this->erun('未找到该会员!');
			}
			//到期时间大于三个月的，禁止操作 TODO  次卡未考虑
			if( $info['endtime'] > time() ){
				$timediff = timediff(date('Y-m-d'), date('Y-m-d',$info['endtime']), 'day');
				if((int)$timediff['day'] > 90){
					$this->erun('当前会员余额充足,暂不支持此项操作!');
				}
			}
			// TODO 续费只能续当前类型相同
			$type = F('MemGroup');
			if(empty($type)){
				D('Crm/MemberType')->mem_group_cache();
			}
			$type = $type[$info['group_id']]['type'];
			$group = D('MemberType')->where(['type'=>$type])->field('id,title,money')->select();
			$this->assign('type',$group)->assign('data',$info);
		}
		$this->display();
	}
}