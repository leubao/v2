<?php
// +----------------------------------------------------------------------
// | LubTMP 年卡服务
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Api\Service;
class Card extends \Libs\System\Service {

	/**
	 * 新人办卡
	 * @Author   zhoujing   <zhoujing@leubao.com>
	 * @DateTime 2017-10-31
	 * @return   [type]     [description]
	 */
	public function interject()
	{
		$data = [
			'nickname'	=>	,
			'phone'		=>	,
			'cardid'	=>	,
			'openid'	=>	,
		];
		$model = D('Crm/Member');
		
	}
}