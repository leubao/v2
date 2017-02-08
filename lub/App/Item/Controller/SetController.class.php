<?php
// +----------------------------------------------------------------------
// | LubTMP  商户端系统设置
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
use Libs\Service\Operate;
class SetController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
		//取得所有产品信息
		$this->products = cache('Product');
		//取得当前产品信息
		$this->product = $this->products[$this->pid];
	 }
	/**
	 * 操作日志查询
	 */
	function logs(){
		$this->display();
	}
	/**
	 * 检票终端管理
	 */
	function check_in(){
		$this->basePage('Terminal',array('product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE')));
		$this->display();
	}
	/**
	 * 添加终端
	 */
	function terminal(){
		if(IS_POST){
			$product_type = M('Product')->where(array('id'=>$_POST['product_id']))->getField('type');
			if(Operate::do_add('Terminal',array('createtime'=>time(),'product_type'=>$product_type,'user_id'=>\Manage\Service\User::getInstance()->id? : 0))){
				$this->srun('新增成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('新增失败!');
			}
		}else{
			$pid = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
			$this->assign('pid',$pid)
				->assign('idcode',rand(0, 9999))
				->display();
		}
	}
	/**
	 * 删除终端
	 */
	function terminalDel(){
		$id = I('get.id',0,intval);
		if(empty($id)){
			$this->erun('参数有误!');
		}
		if(Operate::do_del('Terminal',array('id'=>$id))){
			$this->srun('删除成功!', array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$this->erun('删除失败!');
		}
	}
	/**
	 * 中间层管理
	 * 用于景区票务闸机管理
	 */
	function middle(){
		if(IS_PSOT){
			
		}else{
			$this->display();
		}
	}
	/**
	 * 打印设置
	 */
	function printer(){
		$this->display();
	}
	/**
	 * 产品设置
	 */
	function proset(){
		$db = M("ConfigProduct");   //产品设置表
		$type = '1';
		$list = $db->where(array('product_id'=>$this->pid,'type'=>$type))->select();
		foreach ($list as $k => $v) {
			$config[$v["varname"]] = $v["value"];
		}
		if(IS_POST){
			$product_id = $_POST["product_id"];
			if($product_id <> $this->pid){
				$this->erun('配置失败,请刷新页面重试...');
	            return false;
			}
			$data = $_POST;
			if (empty($data) || !is_array($data)) {
	            $this->erun('配置数据不能为空！');
	            return false;
	        }
	        $diff_key = array_diff_key($config,$data);
	        foreach ($data as $key => $value) {
	            if (empty($key)) {
	                continue;
	            }
	            $saveData = array($config,);
	            $saveData["value"] = trim($value);
	            //$saveData["product_id"] = $product_id;
	            $count = $db->where(array("varname"=>$key,'type'=>$type,'product_id'=>$product_id))->count();
	            $ginfo = array();	
	            if ($count == 0) {//此前无此配置项
	            	if($key!="__hash__"&&$key!="product_id"&&$key!='type'){
		            	$ginfo["varname"] = $key;
		            	$ginfo["value"]   = trim($value);
		            	$ginfo["product_id"] = $product_id;
		            	$ginfo["type"]	=	$type;
		            	$add = $db->add($ginfo);
	            	}
	            }else{
		            if ($db->where(array("varname" => $key,'product_id'=>$product_id,'type'=>$type))->save($saveData) === false) {
		                $this->erun("更新到{$key}项时，更新失败！");
		                return false;
		            }	            	
	            }
	        }
	        //更新未选择的复选框
	        foreach ($diff_key as $key => $value) {
	        	$saveData = array();
	            $saveData["value"] = '0';
	            $saveData["product_id"] = $product_id;
	            if ($db->where(array("varname" => $key,'type'=>$type))->save($saveData) === false) {
		            $this->erun("更新到{$key}项时，更新失败！");
		            return false;
		        }
	        }
	        D('Common/Config')->config_cache();
	        $this->srun("配置成功!", array('tabid'=>$this->menuid.MODULE_NAME));	
		}else{
			$this->assign("vo",$config);
			$this->display();
		}
	}
	
	/**
	 * 渠道座椅
	 */
	function auto_seat(){
		$this->basePage('AutoSeat',array('product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE')),'sort ASC');
		$this->display();
	}
	
	/**
	 * 新增座椅区域
	 */
	function add_seat(){
		if(IS_POST){
			$pinfo = I('post.');
			$area = M('Area')->where(array('template_id'=>$pinfo['template_id'],'status'=>'1'))->field('id,name')->select();
			if(!empty($area)){
				foreach($area as $v){
					$are[$v['id']] = array('id'=>$v['id'],'name'=>$v['name'],'seat'=>'');
				}
				$data = array(
					'name'	=>	$pinfo['name'],
					'template_id'	=>	$pinfo['template_id'],
					'createtime'=>time(),
					'user_id'=>get_user_id(),
					'seat'=>serialize($are),
					'product_id'	=>	$pinfo['product_id'],
					'stype'		=>	$pinfo['stype'],
					'status'	=>	$pinfo['status'],
					'sort'		=>	$pinfo['sort'],
				);
				$status = M('AutoSeat')->add($data);
				if($status){
					$this->srun('新增成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun('新增失败!');
				}
			}else{
				$this->erun('新增失败!');
			}
		}else{
			$pid = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
			//模板列表
			$template = M('templateList')->where(array('status'=>'1'))->field('id,name')->select();
			$this->assign('pid',$pid)
				->assign('template',$template)
				->display();
		}
	}
	/**
	 * 编辑座椅区域
	 */
	function edit_seat(){
		if(IS_POST){
			$pinfp = I('post.');
			if(Operate::do_up('AutoSeat')){
				$this->srun('更新成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('更新失败!');
			}
		}else{
			$id = I('get.id',0,intval);
			if(empty($id)){
				$this->erun("参数有误!");
			}
			//模板列表
			$template = M('templateList')->where(array('status'=>'1'))->field('id,name')->select();
			$ginfo = Operate::do_read('AutoSeat',0,array('id'=>$id));
			$this->assign('data',$ginfo)
				->assign('template',$template)
				->display();
		}
	}
	/**
	 * 删除座椅区域
	 */
	function del_seat(){
		$id = I('get.id',0,intval);
		if(Operate::do_del('AutoSeat',array('id'=>$id))){
			$this->srun('删除成功!',array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$this->erun('删除失败!');
		}
	}
	/**
	 * 设置座椅
	 */
	function set_seat(){
		if(IS_POST){
			$pinfo = json_decode($_POST['info'],true);//dump($pinfo);
			$info = M('AutoSeat')->where(array('id'=>$pinfo['group']))->getField('seat');
			$seat = unserialize($info);
			$num = count(explode(',',$pinfo['data']));
			foreach ($seat as $key=>$val){
				if($pinfo['aid'] == $key){
					$sea[$key]=array(
						'id'=>$val['id'],
						'name'=>$val['name'],
						'seat'=>$pinfo['data'],
						'num' => $num,
					);
				}else{
					$sea[$key]=$val;
					if(!empty($val['seat'])){
						$nums += count(explode(',',$val['seat']));
					}
					
				}	
			}
			$num += $nums; 
			$data = array('seat'=>serialize($sea),'num'=>$num);
			$status = M('AutoSeat')->where(array('id'=>$pinfo['group']))->save($data);
			if($status != false){
				$return = array(
					'statusCode' => '200',
					'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn)),
				);
			}else{
				$return = array(
					'statusCode' => '300',
					'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn)),
				);
			}	
			echo json_encode($return);
		}else{
			$id = I('get.id',0,intval);
			$type = I('get.type',0,intval);
			if(empty($id) || empty($type)){
				$this->erun('参数错误!');
			}
			//根据当前分组所属模板加载模板
			$info = M('AutoSeat')->where(array('id'=>$id))->field('id,template_id')->find();
			$area = M('Area')->where(array('template_id'=>$info['template_id'],'status'=>'1'))->field('id,name,status')->select();
			$this->assign('area',$area)
				->assign('fid',$id)
				->assign('type',$type)
				->display();
		}
	}
	/**
	 * 加载座位
	 * 根据区域加载座位信息   区域页面打开时  先加载座椅模板   然后加载售出情况   页面打开时  每个10分无刷新更新页面
	 * $aid   区域iD
	 * $fid   分组id
	 */
	function seat(){
		$aid = I('get.aid',0,intval);
		$fid = I('get.fid',0,intval);
		$type = I('get.type',0,intval);
		if(empty($aid) || empty($fid)){
			$this->erun('参数错误!');
		}
		//加载座椅
		$info = Operate::do_read('Area',0,array('id'=>$aid,'status'=>1),'','id,name,face,is_mono,seats,num,template_id');
		$info['seats'] = unserialize($info['seats']);
		//$row = array_keys($seat);
		$this->assign('data',$info)
			->assign('fid',$fid)
			->assign('type',$type)
			->display();
	}
	/**
	 * 加载座椅状态
	 *$fid 分组ID
	 */
	function seats(){
		$aid = I('get.aid',0,intval);
		$fid = I('get.fid',0,intval);
		$type = I('get.type',0,intval);
		if(empty($aid) || empty($fid) || empty($type)){
			$this->erun('参数错误!');
		}
		if($type == '1'){
			//座椅分组的状态
			$info = Operate::do_read('AutoSeat',1,array('product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),'status'=>1));	
		}else{
			//基础控座
			$info = Operate::do_read('ControlSeat',1,array('product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),'status'=>1));	
		}
		
		foreach ($info as $k=>$v){
			$infos[$k] = unserialize($v['seat']);
			foreach ($infos[$k] as $key=>$val){
				if($aid == $key){
					if($v['id'] == $fid){
						$group_seat = $val['seat'];
						$num = $val['num'];
					}else{
						if(empty($ngroup_seat)){
							$ngroup_seat = $val['seat'];
						}else{
							if(!empty($val['seat'])){
								$ngroup_seat = $ngroup_seat.','.$val['seat'];
							}
						}
						
					}
				}
			}
		}
		
		//分组内存储数据的格式
		$return = array(
			'statusCode' => '200',
			'message' => '区域加载成功!',
			'group_seat_str' => $group_seat,//当前分组的座位
			'group_seat' => explode(',',$group_seat),//当前分组的座位
			'ngroup_seat' => explode(',', $ngroup_seat),//不是当前分组的座位
			'num'	=> $num,
		);//dump($return);
		echo json_encode($return);
		return true;	
	}
	// 密码管理
	function pwd(){
		$this->basePage('Pwd','',"status DESC");
		$this->display();
	}
	//添加密码
	function add_pwd(){
		if(IS_POST){
			$data = array(
				'createtime'	=>	time(),
				'userid'		=>	\Libs\Util\Encrypt::authcode($_SESSION['lub_userid'], 'DECODE'),
				'password'		=>	md5($_POST['password']),
			);
			if(Operate::do_add('Pwd',$data)){
				$this->srun('新增成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('新增失败!');
			}
		}else{
			$this->assign('pid',\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'))
				->display();
		}
	}
	//编辑密码
	function edit_pwd(){
		if(IS_POST){
			$pinfo = I('post.');
			if(empty($pinfo['password'])){
				if(Operate::do_up('Pwd','','',array('status'=>$pinfo['status']))){
					$this->srun('更新成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun('更新失败!');
				}
			}else{
				$data = array(
					'id' => $pinfo['id'],
					'password' => md5($pinfo['password']),
					'status' => $pinfo['status'],
					'createtime'=>time()
					);
				$status = M('Pwd')->save($data);
				if($status){
					$this->srun('更新成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun('更新失败!');
				}
			}
		}else{
			$id = I('get.id',0,intval);
			$info = Operate::do_read('Pwd',0,array('id'=>$id));
			$this->assign('data',$info)
				->display();
		}
	}
	//删除密码
	function del_pwd(){
		$id = I('get.id',0,intval);
		if(Operate::do_up('Pwd',array('id'=>$id),'',array('status'=>0))){
			$this->srun('删除成功!', array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$this->erun('删除失败!');
		}
	}
	//领导短信、邮件
	function leader(){
		$this->basePage('LeaderSms');
		$this->display();
	}
	//添加
	function add_leader(){
		if(IS_POST){
			if(Operate::do_add('LeaderSms',$data)){
				$this->srun('新增成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('新增失败!');
			}
		}else{
			$this->assign('pid',\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'))
				->display();
		}
	}
	//编辑
	function edit_leader(){
		if(IS_POST){
			if(Operate::do_up('LeaderSms')){
				$this->srun('更新成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('更新失败!');
			}
		}else{
			$id = I('get.id',0,intval);
			$info = Operate::do_read('LeaderSms',0,array('id'=>$id));
			$this->assign('data',$info)
				->display();
		}
	}
	//删除
	function del_leader(){
		$id = I('get.id',0,intval);
		if(Operate::do_del('LeaderSms',array('id'=>$id))){
			$this->srun('删除成功!', array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$this->erun('删除失败!');
		}
	}
	//入园统计
	function statistical(){
		//选择场次
		$today = strtotime(date('Y-m-d'))."-1";
		$plans = M('Plan')->where(array('plantime'=>$today))->select();
		//区域统计
		$this->assign('plans',$plans)
			->assign('today',$today)
			->display();
	}
	//根据场次查询已检票和为检票座位
	function have_tickets(){
		//选择场次
		$plan_id = I('plan_id');
		$status = I('seat') ? I('seat') : '99';
		$plan = F('Plan_'.$plan_id);
		$today = strtotime(date('Y-m-d'));
		$plans = M('Plan')->where(array('plantime'=>$today))->select();
		$map =  array('status'=>$status);
		if(!empty($plan)){
			$db = M(ucwords($plan['seat_table']));
	        $count = $db->where($map)->count();// 查询满足要求的总记录数
	        $p = new \Item\Service\Page($count,20);
	        $currentPage = !empty($_REQUEST["pageNum"])?$_REQUEST["pageNum"]:1;
	        $firstRow = ($currentPage - 1) * 20;
	        $list = $db->where($map)->order("area DESC")->field('id,order_sn,area,seat,status,checktime')->limit($firstRow . ',' . $p->listRows)->select();
		}
		//区域统计
		$this->assign('plans',$plans)
			->assign('plan',$plan)
			->assign ( 'totalCount', $count )
            ->assign ( 'numPerPage', $p->listRows)
            ->assign ( 'currentPage', $currentPage)
			->assign('planid',$plan_id)
			->assign('status',$status)
			->assign('today',$todaya)
			->assign('data',$list)
			->display();
	}
	/**
	 * 默认控坐
	 * 建立多个默认控制组，方便操作
	 * @return [type] [description]
	 */
	function control_block(){
		$this->basePage('ControlSeat','','sort DESC');
		$this->display();
	}
	/**
	 * 新增控制组
	 */
	function add_control(){
		if(IS_POST){
			$pinfo = I('post.');
			$area = M('Area')->where(array('template_id'=>$pinfo['template_id'],'status'=>'1'))->field('id,name')->select();
			if(!empty($area)){
				foreach($area as $v){
					$are[$v['id']] = array('id'=>$v['id'],'name'=>$v['name'],'seat'=>'');
				}
				$data = array(
					'name'	=>	$pinfo['name'],
					'template_id'	=>	$pinfo['template_id'],
					'createtime'=>time(),
					'user_id'=>get_user_id(),
					'type'	=>	$pinfo['type'],
					'seat'=>serialize($are),
					'product_id'	=>	$pinfo['product_id'],
					'state'		=>	$pinfo['state'],
					'status'	=>	$pinfo['status'],
					'remark'	=>	$pinfo['remark'],
					'sort'		=>	$pinfo['sort'],

				);
				$status = M('ControlSeat')->add($data);
				if($status){
					$this->srun('新增成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun('新增失败!');
				}
			}else{
				$this->erun('新增失败!');
			}
		}else{
			$pid = \Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE');
			//模板列表
			$template = M('templateList')->where(array('status'=>'1'))->field('id,name')->select();
			$this->assign('pid',$pid)
				->assign('template',$template)
				->display();
		}
	}
	/**
	 * 编辑控制组
	 */
	function edit_control(){
		if(IS_POST){
			$pinfo = I('post.');
			if(M('ControlSeat')->save($pinfo)){
				$this->srun('更新成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('更新失败!');
			}
		}else{
			$id = I('get.id',0,intval);
			if(empty($id)){
				$this->erun("参数有误!");
			}
			//模板列表
			$ginfo = Operate::do_read('ControlSeat',0,array('id'=>$id));
			$this->assign('data',$ginfo)
				->display();
		}
	}
	/**
	 * 删除控制组
	 */
	function del_control(){
		$id = I('get.id',0,intval);
		if(Operate::do_del('ControlSeat',array('id'=>$id))){
			$this->srun('删除成功!',array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$this->erun('删除失败!');
		}
	}
	/**
	 * 设置控制组
	 */
	function set_block(){
		if(IS_POST){
			$pinfo = json_decode($_POST['info'],true);//dump($pinfo);
			$info = M('ControlSeat')->where(array('id'=>$pinfo['group']))->getField('seat');
			$seat = unserialize($info);
			$num = count(explode(',',$pinfo['data']));
			foreach ($seat as $key=>$val){
				if($pinfo['aid'] == $key){
					$sea[$key]=array(
						'id'=>$val['id'],
						'name'=>$val['name'],
						'seat'=>$pinfo['data'],
						'num' => $num,
					);
				}else{
					$sea[$key]=$val;
					if(!empty($val['seat'])){
						$nums += count(explode(',',$val['seat']));
					}
					
				}	
			}
			$num += $nums; 
			$data = array('seat'=>serialize($sea),'num'=>$num);
			$status = M('ControlSeat')->where(array('id'=>$pinfo['group']))->save($data);
			if($status != false){
				$return = array(
					'statusCode' => '200',
					'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn)),
				);
			}else{
				$return = array(
					'statusCode' => '300',
					'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn)),
				);
			}	
			echo json_encode($return);
		}else{
			$id = I('get.id',0,intval);
			$type = I('get.type',0,intval);
			if(empty($id) || empty($type)){
				$this->erun('参数错误!');
			}
			//根据当前分组所属模板加载模板
			$info = M('ControlSeat')->where(array('id'=>$id))->field('id,template_id')->find();
			$area = M('Area')->where(array('template_id'=>$info['template_id'],'status'=>'1'))->field('id,name,status')->select();
			$this->assign('area',$area)
				->assign('fid',$id)
				->assign('type',$type)
				->display('set_seat');
		}
	}
	//报表重置 
	function reset_report(){
		if(IS_POST){
			$type = I('type');
		    $datetime = I('datetime');
		    $this->assign('type',$type)->assign('datetime',$datetime);
			if(!empty($type) || !empty($date)){
				//根据报表数据生成类型选择删除条件
				$datetime = date('Ymd',strtotime($datetime));
				if($datetime == date('Ymd')){
					$this->erun('亲，你太热爱工作，还未到报表生成时间！', array('tabid'=>$this->menuid.MODULE_NAME));
				}else{
					if($this->proconf['report'] == '1'){
						//按日期
						$map['datetime'] = $datetime;
					}else{
						//按场次
						$map['plantime'] = $datetime;
					}
					//$map['product_id'] = $this->pid;
					if($type == '1'){
					  //删除已生成的数据
					  $status = M('ReportData')->where($map)->delete();
					  if($status){
					  	$this->srun('删除成功', array('tabid'=>$this->menuid.MODULE_NAME));
					  }else{
					  	$this->erun('删除错误！', array('tabid'=>$this->menuid.MODULE_NAME));
					  }
					}else{
					  //生成数据
					  $count = M('ReportData')->where($map)->count();
					  if($count <> '0'){
						$this->erun('请先删除作废数据，再执行此项操作', array('tabid'=>$this->menuid.MODULE_NAME));
					  }else{
					  	$stat = \Libs\Service\Report::report($datetime);
					  	//dump($stat);
					  	if($stat == '200'){
							$this->srun('生成成功', array('tabid'=>$this->menuid.MODULE_NAME));
						  }else{
						  	$this->erun('重置失败', array('tabid'=>$this->menuid.MODULE_NAME));
						  }
						}
					}
				}
			}else{
				$this->erun('参数错误', array('tabid'=>$this->menuid.MODULE_NAME));
			}
		}else{
			$this->display();
		}
	}
	//导游黑名单
	function blacklist(){
		$phone = I('phone');
		if(!empty($phone)){
			$map['phone'] = I('phone');
		}
		$this->basePage('Blacklist',$map,"status DESC");
		$this->assign('map',$map)->display();
	}
	/*
	 * 添加单票
	 * @param $pid int 产品ID
	*/
	function add_blacklist(){
		if(IS_POST){
			if(Operate::do_add('Blacklist',array('createtime'=>time(),'user_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_userid'], 'DECODE')))){
				$this->srun('新增成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("添加失败！");
			}
		}else{
			$product_id = (int) $this->pid;
			if(!empty($product_id)){
				$this->assign('pid',$product_id);
				$this->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}

	/**
	 * 编辑单票
	 * @param $pid int 产品ID
	 * @param $sid int 单票ID
	 */

	function edit_blacklist(){
		if(IS_POST){
			$up = Operate::do_up("Blacklist");
			if($up){
				$this->srun('更新成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('修改失败!');
			}
		}else{
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$list = Operate::do_read("Blacklist",0,array("id"=>$id));
				$this->assign("data",$list);
				$this->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}
	/**
	 * 删除
	 */
	function del_blacklist(){
		$id = I('get.id',0,intval);
		if(!empty($id)){
			$del = Operate::do_del("Blacklist",array('id'=>$id));
			if($del){
				$this->srun('删除成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				$this->erun('删除失败!');
			}
		}else{
			$this->erun('参数错误!');
		}
	}
}