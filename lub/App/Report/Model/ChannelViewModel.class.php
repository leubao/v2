<?php
// +----------------------------------------------------------------------
// | LubTMP 渠道销售绩效查询
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------

namespace Report\Model;
use Common\Model\ViewModel;
class ChannelViewModel extends ViewModel {
	public $viewFields = array(
		'Team_order'=>	array('id','guide_id','qd_id','createtime','order_sn','_type'=>'LEFT'),
		'Order'		=>	array('number','_as'=>'Orders','_on'=>'Team_order.order_sn = Orders.order_sn'),
		);
}
