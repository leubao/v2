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
    	$ginfo = I('get.');
        if(!empty($ginfo['id'])){
            $map['user_id'] = $ginfo['id'];
        }
		$this->basePage('RedTpl',$map,array('id'=>'DESC'));
		$this->assign('ginfo',$ginfo)->display();
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
			if(D('Sales/RedTpl')->field('quota,task,id')->save($pdata)){
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
			//作废记录
			$model = new \Common\Model\Model();
			$model->startTrans();
			//读取记录
			$info = M('RedTpl')->where(array('id'=>$id))->find();
			if(empty($info) || $info['status'] == '0'){
				$this->erun("考核对象当前状态不允许此项操作");
			}
			$up_channel = array(
				'update_time'	=>	time(),
				'status'		=>	'0'
			);
			$kpi = $model->table(C('DB_PREFIX').'kpi_channel')->where(array('id'=>$id))->save($up_channel);
			$updata = array('status'=>'0','update_time' => time());
			$water = $model->table(C('DB_PREFIX')."kpi_Water")->where(array('crm_id'=>$info['crm_id']))->setField($updata);
			if($water && $kpi){
				$model->commit();//提交事务
				$this->srun('作废成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				error_insert('410008');
				$model->rollback();//事务回滚
				$this->erun('作废失败!');
			}
		}else{
			$this->erun('参数错误!');
		}
   	}
}