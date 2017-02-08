<?php
// +----------------------------------------------------------------------
// | LubTMP  渠道商报表查询统计
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\Base;
use Home\Service\Partner;
use Libs\Service\Operate;
class ReportController extends Base{
	function _initialize(){
	 	parent::_initialize();
	}
	
	/**
	 * 财务管理
	 */
	function index(){
		if (IS_POST) {
            $this->redirect('home.php?g=home&m=report&a=index', $_POST);
        }
		$db = D('CrmRecharge');
		$where = array();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $status = I('status');
        $sn = I('sn');
        //读取渠道商下所有员工
        $tlevel = Partner::getInstance()->crm;
		$where = array(
			'crm_id'	=>	array(in,$this->channnel($tlevel['id'], $tlevel['level'])),	
		);
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
        }else{
        	//查询时间段为空时默认查询一个月内的数据
	        $start_time = time() - (86400 * 30);
	        $where['createtime'] = array(array('GT', $start_time), array('LT', time()), 'AND');
        }
        if ($status != '') {
            $where['status'] = $status;
        }
		if($sn != ''){
			$where['order_sn'] = $sn;
		}
		$count = $db->where($where)->count();
		$Page  = new \Home\Service\Page($count,15);
		$show  = $Page->show();
		$list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('data',$list)
			->assign('page',$show)
			->display();
	}
	/**
	 * 今日销售明细
	 */
	function sales(){
		if (IS_POST) {
            $this->redirect('home.php?g=home&m=report&a=index', $_POST);
        }
		$db = D('CrmRecharge');
		$where = array();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $status = I('status');
        $sn = I('sn');
        //读取渠道商下所有员工
        $tlevel = Partner::getInstance()->crm;
		$where = array(
			'crm_id'	=>	array(in,$this->channnel($tlevel['id'], $tlevel['level'])),	
		);
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
        }else{
        	//查询时间段为空时默认查询一个月内的数据
	        $start_time = time() - (86400 * 30);
	        $where['createtime'] = array(array('GT', $start_time), array('LT', time()), 'AND');
        }
        if ($status != '') {
            $where['status'] = $status;
        }
		if($sn != ''){
			$where['order_sn'] = $sn;
		}
		$count = $db->where($where)->count();
		$Page  = new \Home\Service\Page($count,15);
		$show  = $Page->show();
		$list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('data',$list)
			->assign('page',$show)
			->display();
		
	}
	/**
	 * 返利报表
	 */
	function rakeback(){
		if (IS_POST) {
            $this->redirect('home.php?g=home&m=report&a=rakeback', $_POST);
        }
		$db = D('TeamOrder');
		$where = array();
        $start_time = I('start_time');
        $end_time = I('end_time');
        $status = I('status');
        $sn = I('sn');
        //读取渠道商下所有员工
        $tlevel = Partner::getInstance()->crm;
		$where = array(
			'crm_id'	=>	array(in,$this->channnel($tlevel['id'], $tlevel['level'])),	
		);
		if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time) + 86399;
            $where['createtime'] = array(array('GT', $start_time), array('LT', $end_time), 'AND');
        }else{
        	//查询时间段为空时默认查询一个月内的数据
	        $start_time = time() - (86400 * 30);
	        $where['createtime'] = array(array('GT', $start_time), array('LT', time()), 'AND');
        }
        if ($status != '') {
            $where['status'] = $status;
        }
		if($sn != ''){
			$where['order_sn'] = $sn;
		}
		$count = $db->where($where)->count();
		$Page  = new \Home\Service\Page($count,15);
		$show  = $Page->show();
		$list = $db->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('data',$list)
			->assign('page',$show)
			->display();
	}
	/**
	 * 产品列表
	 */
	function product(){
		$this->display();
	}
}