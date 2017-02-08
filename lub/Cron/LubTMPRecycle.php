<?php
// +----------------------------------------------------------------------
// | LubTMP  报表计划生成
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\Recycle;
use Libs\Service\Report;
class LubTMPRecycle{
	/*计划任务错误代码
	*120001 订单拆解失败，原因：此天已拆解，抹去旧数据失败
	*任务主体
    */
    public function run($cronId) {
    	//默认计算当天的订单
        $datetime= date('Ymd',strtotime("-1 day"));
       // $datetime = "20150407";
        //D('Cron/Cronlog')->add(array('cron_id'=>98,'performtime'=>time(),'status'=>13));
    	//Report::report($datetime);
        //生成景区日报表
        Report::today_scenic($datetime,41);
        //生成售票员日报表 TODO 角色已写死
        $user_list = M('User')->where(array('status'=>1,'role_id'=>7))->field('id,nickname')->select();
        foreach ($user_list as $key => $value) {
            Report::today_user($datetime,'41',$value['id'],9);
        }
        //根据场景生成报表 1散客订单2团队订单4渠道版定单6政府订单
        /*//散客订单
        Report::today_user($datetime,'41','',1,2);
        //窗口团队订单
        Report::today_user($datetime,'41','',2,2);
        //渠道订单
        Report::today_user($datetime,'41','',4,2);
        //政企订单
        Report::today_user($datetime,'41','',6,2);*/

        Report::daily($datetime,'41');

    }

}