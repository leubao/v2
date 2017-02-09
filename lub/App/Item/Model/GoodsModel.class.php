<?php
// +----------------------------------------------------------------------
// | LubTMP 收银台商品模型
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;
class GoodsModel extends Model{
	//自动完成
	protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
        array('user_id', 'get_user_id', 1, 'function'), //新增时自动生成验证码
    );
}
