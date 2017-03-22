<?php
// +----------------------------------------------------------------------
// | LubTMP 用户视图模型
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing
// +----------------------------------------------------------------------
namespace Crm\Model;
use Think\Model\ViewModel;
class UserViewModel extends ViewModel {
   public $viewFields = array(
     'User'=>array('id','nickname','phone','status','groupid','type','cash','legally','is_scene','remark','create_time','_type'=>'LEFT'),
     'UserData'=>array('industry','sex', '_on'=>'UserData.user_id=User.id'),
     'WxMember'=>array('openid','channel','nickname'=>'wxname', '_on'=>'WxMember.user_id=User.id'),
   );
 }
?>