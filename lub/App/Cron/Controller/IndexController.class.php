<?php

// +----------------------------------------------------------------------
// | LubTMP  计划任务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

namespace Cron\Controller;

use Common\Controller\LubTMP;

class IndexController extends LubTMP{

    //初始化
    protected function _initialize() {
        parent::_initialize();
        //单个任务最大执行时间
        $CRON_MAX_TIME = C('CRON_MAX_TIME');
        if (empty($CRON_MAX_TIME)) {
            C('CRON_MAX_TIME', 3000);
        }
    }

    //执行计划任务
    public function index() {
        // 锁定自动执行
        $lockfile = RUNTIME_PATH . 'cron.lock';
        if (is_writable($lockfile) && filemtime($lockfile) > $_SERVER['REQUEST_TIME'] - C('CRON_MAX_TIME')) {
            //return;
        } else {
            //设置指定文件的访问和修改时间
            touch($lockfile);
        }
        set_time_limit(0);
        ignore_user_abort(true);

        //执行计划任务
        $this->runCron();
        // 解除锁定
        unlink($lockfile);
    }

    /**
     * 递归执行计划任务
     * Enter description here ...
     */
    private function runCron() {
        $_time = time();
        $cron = D("Cron/Cron")->where(array("isopen" => array("EGT", 1)))->order(array("next_time" => "ASC"))->find();
        
        //检测是否还有需要执行的任务
        if (!$cron || $cron['next_time'] > $_time) {dump($cron['next_time'] > $_time);
            return false;
        }
        list($day, $hour, $minute) = explode('-', $cron['loop_daytime']);
        //获取下一次执行时间
        $nexttime = D("Cron/Cron")->getNextTime($cron['loop_type'], $day, $hour, $minute);
        //更新计划任务的下次执行时间
        D("Cron/Cron")->where(array("cron_id" => $cron['cron_id']))->save(array(
            "modified_time" => $_time,
            "next_time" => $nexttime,
        ));dump($cron['cron_file']);
        if (!$this->_runAction($cron['cron_file'], $cron['cron_id'])) {
            $this->inslog($cron['cron_id'],0);
            return false;
        }else{
            //$this->inslog(7,3);
            $this->inslog($cron['cron_id'],1);
        }
        //自身调用
        $this->runCron();
        return true;
    }

    //运行计划
    private function _runAction($filename = '', $cronId = 0) {
        $dir = PROJECT_PATH . 'Cron/';
        if (!$filename || strpos($filename, "LubTMP") != 0) {$this->inslog(7,3);
            return false;
        }
        //载入文件
        $require = require_cache("{$dir}{$filename}.php");
        \Think\Log::record("计划任务:$filename，执行出错！");
        if ($require) {
            $class = "\\CronScript\\{$filename}";
            try {
                $cron = new $class();
                $cron->run($cronId);
            } catch (Exception $exc) {
                \Think\Log::record("计划任务:$filename，执行出错！");
            }
        } else {
            \Think\Log::record("计划任务:$filename，文件载入出错！");
        }
        return true;
    }
    /*写入任务执行日志
    *@param $cronid int 计划id
    *@param $statue int 状态
    */
    function inslog($cronid,$status = 1){
        D('Cron/Cronlog')->add(array('cron_id'=>$cronid,'performtime'=>time(),'status'=>$status));
        return true;
    }
}