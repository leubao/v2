<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/edit_pwd',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">所有人名称:</label>{$data.name}
  </div>
  <div class="form-group" style="margin: 20px 0 20px; ">
      <label for="j_pwschange_newpassword" class="col-sm-2 control-label">密码：</label>
      <input type="password" name="password" value="" placeholder="新密码" size="20">
  </div>
  <div class="form-group">
      <label for="j_pwschange_secpassword" class="col-sm-2 control-label">确认密码：</label>
      <input type="password" name="new_pwdconfirm" value="" placeholder="确认新密码" size="20">
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
	    <option value="1" <eq name="data.status" value='1'>selected</eq>>启用</option>
      <option value="0" <eq name="data.status" value='0'>selected</eq>>禁用</option>
	</select>
  </div>
</div>
<input name="id" type="hidden" value="{$data.id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>