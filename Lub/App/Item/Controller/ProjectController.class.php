<?php
namespace Item\Controller;
use Common\Controller\ManageBase;
/**
 * 游乐场模式
 * @Author: IT Work
 * @Date:   2020-01-18 13:11:19
 * @Last Modified by:   IT Work
 * @Last Modified time: 2020-01-19 12:47:14
 */
class ProjectController extends ManageBase{

	public function index()
	{
		$where = [];
		$this->basePage('project',$where,'status DESC,id DESC');
		$this->display();
	}

	public function add()
	{
		try{
		  if(IS_POST){
		  	$model = M('Project');
		  	if($model->create()){
			    $result = $model->add(); // 写入数据到数据库 
			    if($result){
			      $this->srun('新增成功~',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			    }else{
		  		  $this->erun('新增失败~');
		  		}
			}
		  }else{
			$this->display();
		  }
		} catch(Exception $e) {
          $this->erun('错误:'.$e->getMessage());
        }
	}

	public function edit()
	{
		try{
		  if(IS_POST){
		  	$pinfo = I('post.');
		  	$model = M('Project');
		  	$model->name = $pinfo['name'];
		  	$model->status = $pinfo['status'];
		  	$model->remark = $pinfo['remark'];
		  	$result = $model->where(['id'=>$pinfo['id']])->save(); // 写入数据到数据库 
		    if($result){
		        $this->srun('新增成功~',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		    }else{
	  			$this->erun('新增失败~');
	  		}
		  }else{
			$this->display();
		  }
		} catch(Exception $e) {
          $this->erun('错误:'.$e->getMessage());
        }
	}

	public function delete()
	{
		try{
		  $id = I('get.id', 0, 'intval');
	      if (empty($id)) {
	        $this->erun('请指定需要删除的项目！');
	      }
	      $model = M('Project');
	      $model->id = $id;
	      $model->status = 0;
	      $result = $model->save(); // 写入数据到数据库 
		  if($result){
		    $this->srun("项目删除成功!", array('tabid'=>$this->menuid.MODULE_NAME));
		  }else{
	  	    $this->erun('删除失败！');
	      }
		} catch(Exception $e) {
          $this->erun('错误:'.$e->getMessage());
        }
	}
}