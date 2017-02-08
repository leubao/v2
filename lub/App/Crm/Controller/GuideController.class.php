<?php
// +----------------------------------------------------------------------
// | LubTMP 导游管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiRan 
// +----------------------------------------------------------------------
namespace Crm\Controller;
use Common\Controller\ItemBase;
use Libs\Service\Operate;
class GuideController extends ItemBase{
	/*导游页面*/
	function index(){
		$this->display();
	}
    /*导游新增*/
    function add(){
    	if(IS_POST){
			$data["itemid"] = \Libs\Util\Encrypt::authcode($_SESSION['lub_imid'], 'DECODE');
			$data["create_time"] = time();
			$add = Operate::do_add("CrmGuide",$data);
			if($add){
				$this->srun('新增成功!',$this->navTabId);
			}else{
				$this->erun('新增失败!');
			}    		
    	}else{
    		$this->display();
    	}
    	
    }
	/*导游修改*/
	function edit(){
		$this->display();
	}
	/*导游删除*/
	function delete(){

	}
}