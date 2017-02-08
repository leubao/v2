<?php

// +----------------------------------------------------------------------
// | LubTMP  计划任务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------

return array(
    'AUTOLOAD_NAMESPACE' => array_merge(C('AUTOLOAD_NAMESPACE'), array(
        'CronScript' => PROJECT_PATH . 'Cron/',
    )),
);
