<?php
// +----------------------------------------------------------------------
// | LubTMP 用户视图模型  微信->user
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing
// +----------------------------------------------------------------------
namespace Wechat\Model;
use Think\Model\ViewModel;
class WxMemberViewModel extends ViewModel {
   public $viewFields = array(
   	 'WxMember'=>array('openid','channel','nickname'=>'wxname', '_type'=>'LEFT'),
     'User'=>array('id','nickname','phone','status','groupid','type','cash','legally','is_scene','remark','create_time','_on'=>'WxMember.user_id=User.id')
     
   );
 }
?>