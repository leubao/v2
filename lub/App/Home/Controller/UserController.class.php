<?php
// +----------------------------------------------------------------------
// | LubTMP  渠道商账户管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use Libs\Service\Operate;
use Home\Service\Partner;

class UserController extends Base{
	
	function _initialize(){
		 parent::_initialize();
	}
	/**
	 * 公司/个人信息管理
	 * 员工列表
	 */
	function index(){
		$cid = I('cid');
		if (IS_POST) {
            $this->redirect('home.php?g=home&m=user&a=index', $_POST);
        }
		$db = D('User');
		$where = array();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $status = I('status');
		$where = array(
			'cid'	=> $cid ? $cid:\Home\Service\Partner::getInstance()->cid,
		);
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['create_time'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
        }
        if ($status != '') {
            $where['status'] = array(array('NEQ',2),array('EQ',$status), 'AND');
        }else{
        	$where['status'] = array('NEQ',2);//删除之后的不予显示
        }
		$count = $db->where($where)->count();
		$Page  = new \Home\Service\Page($count,25);
		$show  = $Page->show();
		$list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('data',$list)
			->assign('page',$show)
			->assign('cid',$where['cid'])
			->display();
	}
	function user_info(){
		$id=I('get.id',0,intval);
		if(empty($id)){
			$this->error('参数错误');
		}
		$data = Operate::do_read('User',0,array('id'=>$id));
		$this->assign('data',$data)->display();
	}
	//个人信息维护
	function uinfo(){
		$id = get_user_id();
		$info = D('Home/User')->where(array('id'=>$id))->find();
		//生成支付连接
		//dump($info);
		$url = U('Wechat/Wechat/show',array('u'=>$id));
		$this->assign('data',$info)->assign('url',$url)->display();
	}
	/**
	 * 添加员工
	 */
	function add(){
		if(IS_POST){

			$User = D("User"); // 实例化User对象
			if (!$User->create()){
			     // 如果创建失败 表示验证没有通过 输出错误提示信息
			     
			     $this->error('新增失败:'.$User->getError());
			}else{
			     // 验证通过 可以进行其他数据操作
			    $result = $User->add(); // 写入数据到数据库 
			    if($result){
			        D('UserData')->add(array('user_id'=>$result));
					$this->success("新增成功!");
			    }else{
			    	$this->error('新增失败!');
			    }
			}
		}else{
			
			$map['parentid'] = $this->config['channel_role_id'];
			$map['is_scene'] = 3;
			$map['status'] = 1;
			$level = Operate::do_read('Role',1,$map,array('id'=>DESC));
			$crm = Operate::do_read('Crm',0,array('id'=>I('get.cid',0,intval)));
 			$this->assign("level",$level)
				->assign("crm",$crm)
				->display();
		}
	}
	/**
	 * 用户编辑
	 */
	function edit(){
		if(IS_POST){
	        //判断是否修改本人，在此方法，不能修改本人相关信息
	        if (\Home\Service\Partner::getInstance()->id == $id) {
	            $this->error("修改当前登录用户信息请进入[菜单栏]中进行修改！");
	        }
	        if (1 == $id) {
	            $this->error("该帐号不允许修改！");
	        }
	        if (IS_POST) {
	            if (false !== D('Home/User')->amendManager($_POST)) {
	                $this->success("更新成功！", U("User/index",array('cid'=>$_POST['cid'])));
	            } else {
	                $error = D('Home/User')->getError();
	                $this->error($error ? $error : '修改失败！');
	            }
	        }
		}else{
			$id=I('get.id',0,intval);
			if(empty($id)){
				$this->error('参数错误');
			}
			$Config = cache("Config");
			$info = M('User')->where(array('id'=>$id))->find();
			$map['parentid'] = $Config['channel_role_id'];
			$map['is_scene'] =3;$map['status'] = 1;
			$level = Operate::do_read('Role',1,$map,array('id'=>DESC));
			$this->assign('data', $info)
				->assign("level",$level)
				->display();
		}
	}
	/*
	 * 删除
	 */
	function delete(){
		$id=I('get.id',0,intval);
		if(empty($id)){
			$this->error('参数错误');
		}
		//不实际删除 而是停用
		if(Operate::do_up('User',array('id'=>$id),'',array('status'=>'2'))){
			 $this->success("更新成功！", U("User/index"));
		}else{
			 $this->error($error ? $error : '修改失败！');
		}
	}
	/*微信解除绑定*/
	function up_wechat(){
		$id=I('get.id',0,intval);
		if(empty($id)){
			$this->error('参数错误');
		}
		//不实际删除 而是停用
		if(M('UserData')->where(array('user_id'=>$id))->setField('wechat','0')){
			if(M('WxMember')->where(array('user_id'=>$id))->save(array('channel'=>0,'user_id'=>0))){
				$this->success("解除绑定成功！", U("User/index"));
			}else{
				$this->error($error ? $error : '解除绑定失败！');
			}
		}else{
		   $this->error($error ? $error : '解除绑定失败！');
		}
	}
	/**
	 * 修改密码
	 */
	function passwords(){
		if (IS_POST) {
            $oldPass = I('post.password', '', 'trim');
            if (empty($oldPass)) {
                $this->error("请输入旧密码！");
            }
            $newPass = I('post.new_password', '', 'trim');
            $new_pwdconfirm = I('post.new_pwdconfirm', '', 'trim');
            if ($newPass != $new_pwdconfirm) {
                $this->error("两次密码不相同！");
            }
            if (D("Home/User")->changePassword(Partner::getInstance()->id, $newPass, $oldPass)) {
                //退出登陆
                Partner::getInstance()->logout();
                $this->success("密码已经更新，请从新登陆！", U("Home/Public/login"));
            } else {
                $error = D("Home/User")->getError();
                $this->error($error ? $error : "密码更新失败！");
            }
        } else {
            $this->assign('userInfo', Partner::getInstance()->getInfo());
            $this->display();
        }
	}
	/**
	 * 下级代理商列表
	 */
	function agents(){
		if (IS_POST) {
            $this->redirect(U('Home/User/agents'), $_POST);
        }
        $db = D('Crm');
		$where = array();
		$where = array(
			'user_id'=>\Home\Service\Partner::getInstance()->id,
			'type'=>4,	
		);
		$count = $db->where($where)->count();
		$Page  = new \Home\Service\Page($count,25);
		$show  = $Page->show();
		$list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('data',$list)
			->assign('page',$show)
			->display();
	}
	/**
	 * 添加下级代理商
	 */
	function add_agents(){
		if(IS_POST){
			
		}else{
			
		}
	}
	/**
	 * 获取当前商户余额
	 */
	function money(){
		$uinfo = Partner::getInstance()->getInfo();
		/*TODO 渠道商为个人
		if($uinfo['group']['type'] <> '1'){
			//导游 政企
			$money = Operate::do_read('Crm',0,array('id'=>$uinfo['cid']),'',array('cash'));
			//$money = Operate::do_read('User',0,array('id'=>$uinfo['id']),'',array('cash'));
		}else{
			
		}*/
		//是否开启多级扣款
		$item_id = $uinfo['crm']['itemid'];
		$itemConf = cache('ItemConfig');
        if($itemConf[$item_id]['1']['level_pay']){
        	//渠道商
			$cid = $uinfo['cid'];
        }else{
        	//渠道商
			$cid = money_map($uinfo['cid']);
        }
		
		$money = Operate::do_read('Crm',0,array('id'=>$cid),'',array('cash'));
		$return = array(
			'status' => 'ok',
			'money'	=>	$money['cash']? $money['cash'] : "0.00",
		);
		die(json_encode($return));
	}
}