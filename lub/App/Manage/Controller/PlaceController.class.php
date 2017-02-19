<?php
// +----------------------------------------------------------------------
// | LubTMP 场所管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Manage\Controller;

use Common\Controller\ManageBase;
use Libs\Service\Operate;
class PlaceController extends ManageBase{
	protected function _initialize() {
        parent::_initialize();
    }
	function index(){
		$this->basePage('Place','',array('id'=>'DESC'));
		$this->display();
	}
	/**
	 * 添加场所
	 */
	function add(){
		if(IS_POST) {
			if(Operate::getInstance()->do_add('Place')){
				$this->srun("添加成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("添加失败！");
			}
		} else {
			$this->display();
		}	
	}
	/**
	 * 编辑场所
	 */
	function edit(){
		if(IS_POST) {
			if(Operate::do_up('Place')){
				$this->srun("更新成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("更新失败！");
			}
		} else {
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$place = Operate::do_read('Place',1,array('id'=>$id));//dump($info);
				$this->assign('place',$place)
					->display();
			}else{
				$this->erun('参数错误!');
			}
		}	
	}
	/**
	 * 删除场所
	 */
	function del(){
		$id = I('get.id',0,intval);
		$status = Operate::do_read('Product','0',array('place_id'=>$id));
		if($status){
			$this->erun("该场所下存在产品，不能直接删除!");
		}
		if(Operate::do_del('Place',array('id'=>$id))){
			$this->srun("删除成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		}else{
			$this->erun("删除失败！");
		}	
	}



	/**
	 * 剧院的座椅模板
	 */
	function template(){
		if(IS_POST){
			$place_id = I('placeid');
			$info = Operate::getInstance()->do_read('TemplateList',1,array('place_id'=>$place_id,'status'=>1),array('id'=>DESC));
			$str = '<option value=0>===请选择====</option>';
			foreach ($info as $val){
				$str .= '<option value='.$val['id'].'>'.$val['name'].'</option>';
			}
			echo $str;
	
		}else{
			$place_id = I('placeid');//dump($place_id);
			$this->tempnav($place_id);
			$this->basePage('TemplateList',array('place_id'=>$place_id),array('id'=>DESC));
			$this->display();
		}
		
	}
	/**
	 * 添加模板
	 */
	function addtemplate(){
		C('TOKEN_ON',true);
		if(IS_POST) {
				//$status = Operate::do_add('TemplateList',array('createtime'=>time()));dump($status);
			if(Operate::do_add('TemplateList',array('createtime'=>time())) !== false){
				$this->srun("添加成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("添加失败！");
			}
		} else {
			$place_id = I('get.placeid',0,'intval');
			$this->assign('place_id',$place_id);
			$this->tempnav($place_id);
			
			$this->display();
		}	
	}
	/**
	 * 编辑模板
	 */
	function edittemplate(){
		if(IS_POST) {
			
		} else {
			$place_id = I('get.placeid',0,'intval');
			$this->tempnav($placeid);
			$this->display();
		}	
	}
	/**
	 * 删除模板
	 */
	function delTemplate(){
		$id = I('get.id',0,intval);
		$status = Operate::do_read('Area','0',array('template_id'=>$id));
		if($status){
			$this->erun("该模板下存在区域，不能直接删除!");
		}
		if(Operate::do_del('TemplateList',array('id'=>$id))){
			$this->srun("删除成功！", array('tabid'=>$this->menuid.MODULE_NAME));
		}else{
			$this->erun("删除失败！");
		}	
	}
	//座椅模板列表头部菜单
	function tempnav($placeid){
		//菜单导航
        $Custom['tool'] = array(
           array('name' => '添加模板', 'app' => MODULE_NAME, 'controller' => CONTROLLER_NAME, 'action' => 'addtemplate', 'parameter' => "placeid={$placeid}",'icon'=>'plus','target'=>'dialog'),
        );
        $menuReturn = array('name' => '返回场所列表', 'title'=>"场所列表",'tabid'=> $this->menuid.MODULE_NAME,'url' => U('place/index',array('menuid'=>$this->menuid)));
        $this->assign('Custom', $Custom)
            ->assign('menuReturn', $menuReturn);
	}
	//区域管理头部菜单
	function areanav($tempid,$placeid){
		//菜单导航
        $Custom['tool'] = array(
            array('name' => '添加区域', 'app' => MODULE_NAME, 'controller' => CONTROLLER_NAME, 'action' => 'areaAdd', 'parameter' => "tempid={$tempid}&placeid={$placeid}&menuid=".$this->menuid,'icon'=>'plus','target'=>'dialog'),
        );
        $menuReturn = array('name' => '返回模板列表','title'=>"模板列表",'tabid'=> $this->menuid.MODULE_NAME, 'url' => U('place/template',array('placeid'=>$placeid,'menuid'=>$this->menuid)));
        $this->assign('Custom', $Custom)
             ->assign('menuReturn', $menuReturn);
	}
	/*区域管理
	 * */
	function area(){
		$tempid=I('get.tempid',0,intval);//模板ID
		$placeid = I('get.placeid',0,intval);//场所ID
		$this->basePage('Area',array('template_id'=>$tempid));
		$this->areanav($tempid,$placeid);
		$this->assign('tempid',$tempid)
			 ->assign('placeid',$placeid)
			 ->display();
	}
	
	/*添加区域
	 * <！-n 座位ID r 行		l 列 -》  s v显示h隐藏 		a 行列集
	 * */  
	function areaAdd(){
		if(IS_POST){
			$pinfo = I('post.');		
			//座椅布局 起始行列
			$i = $pinfo['start_row'];//起始行
			$start_list = $pinfo['start_list'];//起始列
			//座椅模板
			$row = $pinfo['row']+$i;//总行数
			$list = $pinfo['list']-1;//列循环次数
			//定义初始座椅数为0
			$num = 0;
			if ($pinfo['is_mono'] == '1') {
				//单号
				for($i;$i<$row;$i++){
					for($k=0;$k<=$list;$k++){
						if($k == 0){
							if($start_list%2 == 0){
								$start_list = 1+$start_list;
								$slist = $start_list;
							}else{
								$slist = $start_list;
							}
						}else{
							$slist = $start_list+2*$k;
						}
						
						$r['seat'][$i] .= 'a';
						//座位ID
						$seat = $i.'-'.$slist;
						$r['seatid'][$i][] = $seat;
						//座椅计数器
						$num = $num + 1;
					}
					$r['rows'][] = $i;
				}
				//生成列号
				for ($k=0; $k <= $list; $k++) {
					if($k == 0){
						if($start_list%2 == 0){
							$start_list = 1+$start_list;
							$slist = $start_list;
						}else{
							$slist = $start_list;
						}
					}else{
						$slist = $start_list+2*$k;
					}
					$r['columns'][] = $slist;
				}
				
			} elseif ($pinfo['is_mono'] == '2'){
				//双号
				for($i;$i<$row;$i++){
					for($k=0;$k<=$list;$k++){
						if($k == 0){
							if($start_list%2 == 0){
								$slist = $start_list;
							}else{
								$start_list = 1+$start_list;
								$slist = $start_list;
							}
						}else{
							$slist = $start_list+2*$k;
						}
						//座位ID
						$seat = $i.'-'.$slist;
						$r['seatid'][$i][] = $seat;
						$r['seat'][$i] .= 'a';
						//座椅计数器
						$num = $num + 1;
					}
					$r['rows'][] = $i;
				}
				//生成列号
				for ($k=0; $k <= $list; $k++) {
					if($k == 0){
						if($start_list%2 == 0){
							$slist = $start_list;
						}else{
							$start_list = 1+$start_list;
							$slist = $start_list;
						}
					}else{
						$slist = $start_list+2*$k;
					}
					$r['columns'][] = $slist;
				}
				
			} else {
				//单双号
				for($i;$i<$row;$i++){
					for($k=0;$k<=$list;$k++){
						$slist = $start_list+$k;
						//座位ID
						$seat = $i.'-'.$slist;
						$r['seatid'][$i][] = $seat;
						$r['seat'][$i] .= 'a';
						//座椅计数器
						$num = $num + 1;
					}
					$r['rows'][] = $i;
				}
				//生成列号
				for ($k=0; $k <= $list; $k++) {
					$slist = $start_list+$k;
					$r['columns'][] = $slist;
				}
			}
			//dump($r);
			$fg = "','";
			$data = array(
				'seat' => implode($fg,$r['seat']),
				'rows' => implode($fg,$r['rows']),
				'seatid' => serialize($r['seatid']), 
				'columns' => implode($fg,$r['columns']),
				'h_seat' => '0-0',
				'n_seat' => '0-0',
			);
			$arr['seat']=serialize($data);
			$arr['num']=$num;
			$arr['start_list'] = $pinfo['start_list'];
			$arr['start_row'] = $pinfo['start_row'];
			if(Operate::getInstance()->do_add('Area',$arr,'')){
				$this->srun('添加成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('添加失败!');
			}
		}else {
			$tempid=I('get.tempid',0,intval);
			$placeid = I('get.placeid',0,intval);//场所ID
			$this->areanav($tempid,$placeid);
			$this->assign('template_id',$tempid)
				 ->assign('placeid',$placeid)
			     ->display();
		}
	}
	
	 /*座位管理
	  * $theaterid int 剧场ID
	  * $areaid  int 区域ID
	  * 
	  * */
	function seat(){
		$ginfo=I('get.');
		$info=Operate::do_read('Area',0,array('id'=>(int)$ginfo['areaid']));
		$data=unserialize($info['seat']);
		session('seat',$data);
		$row=array_keys($seta);
		$this->areanav($ginfo['tempid'],$ginfo['placeid']);
		$this->assign('template_id',$ginfo['tempid'])
			->assign('placeid',$ginfo['placeid'])
			->assign('areaid',$ginfo['areaid'])
			->assign('data',$data)
			->display();
	}
	//添加座椅
	function seatadd(){
		if(IS_POST){
			$info = $_POST;
			$info = json_decode($info['data'],true);
			$h_seat = session('seat');
			$status = D('Manage/Area')->up_seat($h_seat,$info);
			if($status){
				session('seat',null);
				$this->srun('更新成功!',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true,'tempid'=>$info['tempid'],'placeid'=>$info['placeid'],'areaid'=>$info['areaid']));
			}else{
				$this->erun('更新失败!');
			}
		}
	}


	//区域删除
	function del_area(){
		$info = I('get.');
		if(!empty($info['id'])){
			if(Operate::do_del('Area',array('id'=>$info['id']))){
				$this->srun('删除成功!',array('tabid'=>$this->menuid.MODULE_NAME,'tempid'=>$info['tempid'],'placeid'=>$info['placeid'],'areaid'=>$info['areaid']));
			}else{
				$this->erun("删除失败!");
			}
			
		}else{
			$this->erun("参数错误!");
		}
	}
/*	
INSERT INTO `lub_menu` (`id`, `name`, `parentid`, `app`, `controller`, `action`, `parameter`, `type`, `status`, `is_scene`, `remark`, `listorder`) VALUES
('', '添加产品', 151, 'Manage', 'Product', 'add', '', 1, 1, 1, '', 0),
('', '编辑产品', 151, 'Manage', 'Product', 'edit', '', 1, 0, 1, '', 0),
('', '删除', 151, 'Manage', 'Product', 'del', '', 1, 0, 1, '', 0),
('', '添加属性', 144, 'Manage', 'Productmodel', 'add', '', 1, 1, 1, '', 0),
('', '编辑属性', 144, 'Manage', 'Productmodel', 'edit', '', 1, 0, 1, '', 0),
('', '删除', 144, 'Manage', 'Productmodel', 'del', '', 1, 0, 1, '', 0),
('', '添加场所', 146, 'Manage', 'Place', 'Addtype', '', 1, 1, 1, '', 0),
('', '编辑场所', 146, 'Manage', 'Place', 'Edittype', '', 1, 0, 1, '', 0),
('', '删除', 146, 'Manage', 'Place', 'DelType', '', 1, 0, 1, '', 0);
	*/
	/**
	 * 场所分类
	 */
	function type(){
		$info = Operate::getInstance()->do_read('PlaceType',1);
		$this->assign('data',$info);
		$this->display();
	}
	/**
	 * 添加分类
	 */
	function Addtype(){
		if(IS_POST) {
			if(Operate::getInstance()->do_add('PlaceType')){
				$this->srun("添加成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("添加失败！");
			}
			
		} else {
			$this->display();
		}	
	}
	/**
	 * 编辑分类
	 */
	function Edittype(){
		if(IS_POST) {
			
		} else {
			$this->display();
		}	
	}
	/**
	 * 删除
	 */
	function DelType(){
		if(IS_POST) {
			//批量删除
		} else {
			
		}	
	}
}