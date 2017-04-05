<?php
// +----------------------------------------------------------------------
// | LubTMP 用户视图模型
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing
// +----------------------------------------------------------------------
namespace Api\Model;
use Think\Model\ViewModel;
class OrderViewModel extends ViewModel {
    public $viewFields = array(
    	//'Order'=>array('id'=>'oid','number','channel_id','phone','type','status','create_time','_type'=>'LEFT'),
    	'Order'=>array('number','channel_id','phone','type','status','createtime','_as'=>'Myorder','_type'=>'LEFT'),
    	'Crm'=>array('name','_on'=>'Crm.id=Myorder.channel_id')   
    );
 }
?>