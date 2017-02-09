<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/add_pwd',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">所有人名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="名称">
  </div>
  <div class="form-group" style="margin: 20px 0 20px; ">
      <label for="j_pwschange_newpassword" class="col-sm-2 control-label">密码：</label>
      <input type="password" data-rule="密码:required" name="password" value="" placeholder="密码" size="20">
  </div>
  <div class="form-group">
      <label for="j_pwschange_secpassword" class="col-sm-2 control-label">确认密码：</label>
      <input type="password" data-rule="required;match(password)" name="new_pwdconfirm" value="" placeholder="确认密码" size="20">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">用途:</label>
    <select name="type" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1">门票二次打印</option>
  </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
	    <option value="1">启用</option>
	    <option value="0">禁用</option>
	</select>
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>