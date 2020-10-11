<?php

/**
 * @Author: IT Work
 * @Date:   2020-07-29 19:29:19
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-08-21 01:14:31
 * 设置分账
 */
namespace Item\Controller;
use Common\Controller\ManageBase;
class RoutingController extends ManageBase{

	function _initialize(){
	 	parent::_initialize();
	}

	public function index()
	{
		$where = [];
		$this->basePage('Routing',$where,array("id" => "desc"));
        $this->assign('where', $where)
        	->display();
	}
	/**
	 * 1、分账列表
	 * 2、新增分账
	 * 3、编辑分账
	 * 4、删除分账
	 */
	public function add()
	{
		if(IS_POST){
			$pinfo = I('post.');
			if(!$this->checkThan($pinfo)){
				$this->erun("分账规则有误,请重新确认！");
			}
			$model = D('Item/Routing');
			if($model->create($pinfo)){
				$model->user_id = get_user_id('id');
				$res = $model->add();
				if($res){
					$this->srun('新增成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun("添加失败！");
				}
			}else{
				$this->erun("添加失败！");
			}
		}else{
			$this->display();
		}
	}
	public function edit()
	{
		if(IS_POST){
			$pinfo = I('post.');
			if(!$this->checkThan($pinfo)){
				$this->erun("分账规则有误,请重新确认！");
			}
			$model = D('Item/Routing');
			if($model->create($pinfo)){
				$res = $model->save();
				if($res){
					$this->srun('更新成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
				}else{
					$this->erun("更新失败！");
				}
			}else{
				$this->erun("更新失败！");
			}
		}else{
			$id = I('get.id',0,intval);
			$model = D('Item/Routing');
			$info = $model->where(['id'=>$id])->find();
			$info['ticket_name'] = ticketName($info['ticket_id'],1);var_dump($info);
			$this->assign('data',$info)->display();
		}
	}
	public function delete()
	{
		$id = I('get.id',0,intval);

		if(!empty($id)){
			$model = D('Item/Routing');
			$del = $model->where(['id'=>$id])->setField('status',0);
			if($del){
				$this->srun('删除成功',array('tabid'=>$this->menuid.MODULE_NAME));
			}else{
				$this->erun('删除失败!');
			}
		}else{
			$this->erun('参数错误!');
		}
	}
	/**
	 * 校验规则
	 * @Author   zhoujing                 <zhoujing@leubao.com>
	 * @DateTime 2020-08-21T01:11:52+0800
	 * @param    array                   $info                 
	 * @return   [type]                                         [description]
	 */
	protected function checkThan($info)
	{
		if((int)$info['type'] === 1){
			//读取票型结算金额
			$ticket = D('TicketType')->where(['id'=>$info['ticket_id']])->getField('discount');
			if($info['rule'] > $ticket){
				return false;
			}
		}
		if((int)$info['type'] === 2){
			if($info['rule'] > 1){
				return false;
			}
		}
		return true;
	}
}