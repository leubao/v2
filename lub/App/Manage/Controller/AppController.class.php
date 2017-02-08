<?php
// +----------------------------------------------------------------------
// | LubTMP  应用管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>   2014-10-14
// +----------------------------------------------------------------------
namespace Manage\Controller;
use Common\Controller\ManageBase;
use Libs\Service\Operate;
class AppController extends ManageBase{
	function index(){
        $this->basePage('App');
		$this->display();
	}
	/**
	 * 添加接口
	 */
	function add(){
		if(IS_POST){
            $pinfo = I('post.');
            $db = D('Manage/App');
            if($db->create()){
                $db->crm_id = I('channel_id');
                if($db->add()) {
                    $this->srun("添加成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
                }else{
                    $this->erun("添加失败!");
                }
            }else{
                $this->erun("添加失败1!");
            }
		}else{
            $this->display();
		}
	}
	/**
	 * 编辑接口
	 */
	function edit(){
		if(IS_POST){
			if (Operate::do_up('App')){
                $this->srun("更新成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("更新失败!");
			}	
		}else{
			$id = I('get.id', 0, 'intval');
	        if (empty($id)) {
	            $this->erun('请指定需要删除的接口！');
	        }
			$list = Operate::do_read('App',0,array('id'=>$id));
            $crm = M('Crm')->where(array('status'=>1,'level'=>self::$Cache["Config"]['level_1']))->field('id,name')->select();
            $this->assign('crm',$crm)
			     ->assign('data',$list)
				->display();
		}
	}
	//删除行为
    public function delete() {
        $id = I('get.id', 0, 'intval');
        if (empty($id)) {
            $this->erun('请指定需要删除的应用！');
        }
        //删除
        if (Operate::do_del('App',array('id'=>$id))) {
            $this->srun("应用删除成功，需要更新缓存后生效!", array('tabid'=>$this->menuid.MODULE_NAME));
        } else {
            $this->erun('删除失败！');
        }
    }
	//状态转换
    public function status() {
        $id = I('get.id', 0, 'intval');
        if (empty($id)) {
            $this->erun('请指定需要状态转换的行为！');
        }
        $status = I('get.status',0,'intval') ? 0 : 1;
        //状态转换
        if (Operate::do_status('App',array('id'=>$id),$status)) {
            $this->success('状态转换成功！', U('App/index'));
        } else {
            $this->erun('状态转换失败！');
        }
    }
    /**
     * 更新appkey
     */
    public function appkey(){
    	$id = I('get.id',0,intval);
    	if(empty($id)){
    		$this->erun("参数错误!");
    	}
    	$status = M('App')->where(array('id'=>$id))->setField('appkey',genRandomString(8));
    	if($status){
    		$this->srun("更新成功!", array('tabid'=>$this->menuid.MODULE_NAME));
    	}else{
    		$this->erun("更新失败!");
    	}
    }
	/**
	 * 头部菜单
	 */
	function tempnav($param){
		//菜单导航
       	$Custom = array(
            array('name' => '操作记录', 'app' => MODULE_NAME, 'controller' => CONTROLLER_NAME, 'action' => 'logs', 'parameter' => "id={$param}"),
            array('name' => '登录日志', 'app' => MODULE_NAME, 'controller' => CONTROLLER_NAME, 'action' => 'llogs', 'parameter' => "id={$param}"),
            array('name' => '产品列表', 'app' => MODULE_NAME, 'controller' => CONTROLLER_NAME, 'action' => 'product', 'parameter' => "id={$param}"),
         );
        $menuReturn = array('name' => '返回应用列表', 'url' => U('App/index'));
        $this->assign('Custom', $Custom)
             ->assign('menuReturn', $menuReturn);
	}
    /**
     * 接口权限
     */
    public function port(){
    	if(IS_POST){
    		$port = implode(',', I('post.port'));
            $id = I('post.id',0,intval);
    		if(M('App')->where(array('id'=>$id))->setField('port',$port)){
    			$this->srun("更新成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
    		}else{
    			$this->erun('更新失败!');
    		}
    	}else{
    		$app_id = I('get.id',0,intval);
			if(!empty($app_id)){
				$portAll = Operate::do_read('Port',1,array('status'=>1));//所有接口
				$pro = M('App')->where(array('id' => $app_id))->getField('port');//商户下所有产品
				$proArr = explode(',',$pro);//转换为数组
				$this->tempnav($app_id);
				$this->assign('port',$portAll)
					 ->assign('proArr',$proArr)
					 ->assign('id',$app_id)
				     ->assign('empty','<center><div class='/onShow/' id='/nameTip/'><font color='/red/'>该商户无直接产品 </font></div></center>');
				$this->display();
			} else {
				$this->erun('参数错误!');
			}
    	}
    }
    /**
     * 产品权限
     */
	public function product(){
    	if(IS_POST){
            $product = implode(',', I('post.pro'));
            $id = I('post.id',0,intval);
            if(M('App')->where(array('id'=>$id))->setField('product',$product)){
                $this->srun("更新成功!", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
            }else{
                $this->erun("更新失败！".M('App')->getError());
            }
        }else{ 
            $id = I('get.id',0,intval);
            if(!empty($id)){
                $proAll = Operate::do_read('Product',1,array('status'=>1));//所有产品
                $pro = M('App')->where(array('id' => $id))->getField('product');//商户下所有产品
                $proArr = explode(',',$pro);//转换为数组
                foreach ($proAll as $val){
                    //查询直接产品
                    $dpro[] = $val;
                }
                $this->tempnav($id);
                $this->assign('dpro',$dpro)
                     ->assign('proArr',$proArr)
                     ->assign('id',$id)
                     ->assign('empty','<center><div class='/onShow/' id='/nameTip/'><font color='/red/'>系统暂无可用产品 </font></div></center>');
                $this->display();
            } else {
                $this->erun('参数错误!');
            }
        }
    }
    /*appinfo 应用详情*/
    function appinfo(){
        $id = I('get.id',0,intval);
        $info = M('App')->where(array('id'=>$id))->find();
        $this->assign('data',$info)->display();
    }
}