<?php
// +----------------------------------------------------------------------
// | LubTMP  商户端日志管理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Common\Controller\ManageBase;
class logsController extends ManageBase{
		/**
	 * 操作日志查询
	 */
	function index(){
		if (IS_POST) {
            $this->redirect('index', $_POST);
        }
        $uid = I('uid');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $ip = I('ip');
        $status = I('status');
        if (!empty($uid)) {
            $data['uid'] = array('eq', $uid);
        }
        if (!empty($start_time) && !empty($end_time)) {
            $data['_string'] = " `time` >'$start_time' AND  `time`<'$end_time' ";
        }
        if (!empty($ip)) {
            $data['ip '] = array('like', '%' . $ip . '%');
        }
        if ($status != '') {
            $data['status'] = array('eq', (int) $status);
        }
        if (is_array($data)) {
            $data['_logic'] = 'or';
            $map['_complex'] = $data;
        } else {
            $map = array();
        }
        $count = M("log")->where($map)->count();
        $page = $this->page($count, 20);
        $Logs = M("log")->where($map)->limit($page->firstRow . ',' . $page->listRows)->order(array("id" => "desc"))->select();
        $this->assign("Page", $page->show());
        $this->assign("logs", $Logs);
        $this->display();
	}
	/**
	 * 登录日志查询
	 * Enter description here ...
	 */
	public function loginlog() {
		if (IS_POST) {
            $this->redirect('loginlog', $_POST);
        }
        $where = array();
        $username = I('username');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $loginip = I('loginip');
        $status = I('status');
        if (!empty($username)) {
            $where['username'] = array('like', '%' . $username . '%');
        }
        if (!empty($start_time) && !empty($end_time)) {
            $where['_string'] = " `logintime` >'$start_time' AND  `logintime`<'$end_time' ";
        }
        if (!empty($loginip)) {
            $where['loginip '] = array('like', '%' . $loginip . '%');
        }
        if ($status != '') {
            $where['status'] = array('eq', $status);
        }
        $where['is_scene'] = array('eq', 2);
        $model = D("Item/Loginlog");
        $count = $model->where($where)->count();
        $page = $this->page($count, 20);
        $data = $model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order(array('id' => 'DESC'))->select();
        $this->assign("Page", $page->show())
                ->assign("data", $data)
                ->assign('where', $where)
                ->display();
	}
    
	/*计划任务执行日志*/
    public function cronlog(){
        M('CronLog');
    }
   
}