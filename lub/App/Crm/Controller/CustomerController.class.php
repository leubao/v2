<?php
// +----------------------------------------------------------------------
// | LubTMP 客户关系管理
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing
// +----------------------------------------------------------------------
namespace Crm\Controller;
use Common\Controller\ManageBase;
use Libs\Service\Operate;
use Libs\Util\Upload;
class CustomerController extends ManageBase{

	//通知公告
	function notice()
	{
		$start_time = I('start_time');
	    $end_time = I('end_time') ? I('end_time') : date('Y-m-d',time());
	    //传递条件
	    $this->assign('starttime',$start_time);
        $this->assign('endtime',$end_time);
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
        }
		$this->basePage('Notice',$where,'createtime DESC');
		$this->display();
	}
	//新增通知
	function add_notice()
	{
		if(IS_POST){
			$info = $_POST;
			if(empty($info['content']) || empty($info['title'])){
				$this->erun("标题 、内容不能为空");
			}
			$data = array(
				'product_id'	=>	\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
				'content'		=>	$info['content'],
				'status'		=>	$info['status'],
				'createtime'	=>	time(),
				'title'			=>	$info['title'],
				'user_id'		=>	\Libs\Util\Encrypt::authcode($_SESSION['lub_userid'], 'DECODE')
				);
			if(M('Notice')->add($data)){
				$this->srun('新增成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("新增失败");
			}
		}else{
			$this->display();
		}
		
	}
	//编辑通知
	function edit_notice()
	{
		if(IS_POST){
			$info = $_POST;
			if(empty($info['content']) || empty($info['title'])){
				$this->erun("标题 、内容不能为空");
			}
			$data = array(
				'content'	=>	$info['content'],
				'status'	=>	$info['status'],
				'title'		=>	$info['title'],);
			$status = M('Notice')->where(array('id'=>$info['id']))->save($data);
			if($status){
				$this->srun('更新成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("编辑失败");
			}
		}else{
			$id = I('get.id');
			if(empty($id)){
				$this->erun("参数错误");
			}
			$info = M('Notice')->where(array('id'=>$id))->find();
			$this->assign('info',$info)->display();
		}
		
	}
	//删除通知
	function del_notice()
	{
		$id = I('get.id');
		if(empty($id)){
			$this->erun("参数错误");
		}
		$status = M('Notice')->where(array('id'=>$id))->delete();
		if($status){
			$this->srun('删除成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		}else{
			$this->erun("删除失败");
		}
		
	}
	//上传
	public function upload(){
	    $upload = new Upload();// 实例化上传类
	    $upload->maxSize   =  3145728 ;// 设置附件上传大小
	    $upload->exts      =  array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->rootPath  =  'd/upload/'; // 设置附件上传根目录
	    // 上传单个文件 
	    $info   =   $upload->uploadOne($_FILES['imgFile']);
	    if(!$info) {// 上传错误提示错误信息
	        $this->error($upload->getError());
	    }else{// 上传成功 获取上传文件信息
	    	 $return['error'] = 0;
	    	 $return['url'] = $upload->rootPath.$info['savepath'].$info['savename'];
	    }
	    /* 返回JSON数据 */
        echo json_encode($return);
	}

}