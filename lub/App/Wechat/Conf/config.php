<?php
return array(
	//'配置项'=>'配置值'
   'TMPL_ACTION_ERROR' => APP_PATH . 'Wechat/View/error.php', // 默认错误跳转对应的模板文件
   'TMPL_ACTION_SUCCESS' => APP_PATH . 'Wechat/View/success.php', // 默认成功跳转对应的模板文件
   'URL_MODEL'             =>  3,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
	// 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
   );