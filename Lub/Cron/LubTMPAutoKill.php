<?php
// +----------------------------------------------------------------------
// | LubTMP  秒杀程序上线
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace CronScript;
use Libs\Service\Check;
class LubTMPAutoKill {
    //任务主体
    public function run($cronId) {
        $list = M('Activity')->where(array('type'=>7,'status'=>1))->field('id,param,product_id')->select();
        foreach ($list as $k => $v) {
            $param = json_decode($v['param'], true);
            $rule = $param['info']['rule'];
            foreach ($rule as $key => $value) {
                
            }
            $rule = load_redis('set','kill_'.$pinfo['act']);
        }	
    }
}