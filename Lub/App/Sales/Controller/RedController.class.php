<?php
// +----------------------------------------------------------------------
// | LubTMP 红包设置
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>2014-12-19 
// +----------------------------------------------------------------------
namespace Sales\Controller;
use Common\Controller\ManageBase;
class RedController extends ManageBase{

	protected function _initialize() {
        parent::_initialize();
    }
    /**
     * 红包模板列表
     * @Company  承德乐游宝软件开发有限公司
     * @Author   zhoujing      <zhoujing@leubao.com>
     * @DateTime 2018-03-05
     */
    public function index()
    {
		$this->basePage('RedTpl','',array('id'=>'DESC'));
		$this->display();
    }
    /**
     * 新增模板
     * @Company  承德乐游宝软件开发有限公司
     * @Author   zhoujing      <zhoujing@leubao.com>
     * @DateTime 2018-03-05
     */
   	public function add()
   	{
   		if(IS_POST){
   			$pinfo = I('post.');
   			if(mb_strlen($pinfo['act_name'], 'utf8') > 10){$this->erun('活动名称超过10个汉字');}
   			if(mb_strlen($pinfo['send_name'], 'utf8') > 10){$this->erun('商家名称超过10个汉字');}
   			if(mb_strlen($pinfo['wishing'], 'utf8') > 20){$this->erun('商家名称超过20个汉字');}
   			if(mb_strlen($pinfo['remark'], 'utf8') > 20){$this->erun('商家名称超过20个汉字');}
   			$model = D('Sales/RedTpl');
   			if($model->create($pinfo)){
   				if($model->add()){
   					$this->srun("新增成功".mb_substr($pinfo['act_name'], 'utf8'),array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
   				}else{
   					$this->erun("新增失败");
   				}
   			}else{
   				$this->erun("新增失败");
   			}
   		}else{
   			$this->display();
   		}
   	}
   	/**
   	 * 编辑
   	 * @Company  承德乐游宝软件开发有限公司
   	 * @Author   zhoujing      <zhoujing@leubao.com>
   	 * @DateTime 2018-03-05
   	 */
   	public function edit()
   	{
   		if(IS_POST){
			$pdata = I('post.');
			if(D('Sales/RedTpl')->save($pdata)){
				$this->srun("更新成功",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("更新失败");
			}
		}else{
			//读取当前绩效配置  绩效配置的type 5
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$info = D('Sales/RedTpl')->where(array('id'=>$id))->find();
				$this->assign('data',$info)->display();
			}else{
				$this->erun('参数错误!');
			}
		}
   	}
   	/**
   	 * 删除
   	 * @Company  承德乐游宝软件开发有限公司
   	 * @Author   zhoujing      <zhoujing@leubao.com>
   	 * @DateTime 2018-03-05
   	 */
   	public function delete()
   	{
   		$id = I('get.id',0,intval);
  		if(!empty($id)){
  			$updata = array('status'=>'0','update_time' => time());
  			$status = D('Sales/RedTpl')->where(array('id'=>$id))->setField($updata);
  			if($status){
  				$this->srun('作废成功',array('tabid'=>$this->menuid.MODULE_NAME));
  			}else{
  				$this->erun('作废失败!');
  			}
  		}else{
  			$this->erun('参数错误!');
  		}
   	}
}