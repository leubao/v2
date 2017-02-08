<?php
// +----------------------------------------------------------------------
// | LubTMP  计划任务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

namespace Cron\Controller;

use Common\Controller\ManageBase;
use Libs\Util\Page;
class CronController extends ManageBase{

    private $db;

    //初始化
    protected function _initialize() {
        parent::_initialize();
        $this->db = D("Cron/Cron");
    }

    public function index() {
        $count = $this->db->where($where)->count();
        $currentPage = !empty($_REQUEST["pageCurrent"])?$_REQUEST["pageCurrent"]:1;
        $firstRow = ($currentPage - 1) * 25;
        $page = new page($count, 25);
        $data = $this->db->where($where)->order($order)->limit($firstRow . ',' . $page->listRows)->select();
        
        //created_time 上次执行时间
        //next_time 下次执行时间
        foreach ($data AS $key => &$cron) {
            $cron['type'] = $this->db->_getLoopType($cron['loop_type']);
            list($day, $hour, $minute) = explode('-', $cron['loop_daytime']);
            if ($cron['loop_type'] == 'week') {
                $cron['type'] .= '星期' . $this->db->_capitalWeek($day);
            } elseif ($day == 99) {
                $cron['type'] .= '最后一天';
            } else {
                $cron['type'] .= $day ? $day . '日' : '';
            }
            if ($cron['loop_type'] == 'week' || $cron['loop_type'] == 'month') {
                $cron['type'] .= $hour . '时';
            } else {
                $cron['type'] .= $hour ? $hour . '时' : '';
            }

            $cron['type'] .= $minute ? $minute . '分' : '00分';
        }

        $this->assign('data', $data);
        $this->assign( 'totalCount', $count )
             ->assign( 'numPerPage', $page->listRows)
             ->assign( 'currentPage', $currentPage);
        $this->display();
    }

    //添加计划任务
    public function add() {
        if (IS_POST) {
            if ($this->db->CronAdd($_POST)) {
                $this->srun("计划任务添加成功！",array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
            } else {
                $this->erun($this->db->getError());
            }
        } else {
            $this->assign("loopType", $this->db->_getLoopType());
            $this->assign("fileList", $this->db->_getCronFileList());
            $this->display();
        }
    }

    //编辑
    public function edit() {
        if (IS_POST) {
            if ($this->db->CronEdit($_POST)) {
                $this->srun("修改成功！", array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
            } else {
                $this->erun($this->db->getError());
            }
        } else {
            $cron_id = I('get.id', 0, 'intval');
            $info = $this->db->where(array("cron_id" => $cron_id))->find();
            if (!$info) {
                $this->erun("该计划任务不存在！");
            }
            list($info['day'], $info['hour'], $info['minute']) = explode('-', $info['loop_daytime']);
            $this->assign($info);
            $this->assign("loopType", $this->db->_getLoopType());
            $this->assign("fileList", $this->db->_getCronFileList());
            $this->display();
        }
    }
    //删除
    public function delete() {
        $cron_id = I('get.id', 0, 'intval');
        $info = $this->db->where(array("cron_id" => $cron_id))->delete();
        if ($info !== false) {
            $this->srun("删除成功！",array('tabid'=>$this->menuid.MODULE_NAME));
        } else {
            $this->erun("删除失败！");
        }
    }
}