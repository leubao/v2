<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Crm/Index/edituser',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-3 control-label">姓名:</label>
    <input type="text" name="nickname" class="form-control required" data-rule="required;" value="{$data.nickname}" size="20" placeholder="姓名">
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">手机号码:</label>
    <input type="text" name="phone" class="form-control" size="20" value="{$data.phone}" placeholder="手机号码">
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">E-mail:</label>
    <input type="text" name="email" class="form-control" size="20" value="{$data.email}" placeholder="E-mail">
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">所属角色：</label>
      <select name="role_id" class="required combox">
        <option selected value="0">===请选择===</option>
        <volist name="role" id="vo">
          <option value="{$vo.id}" <if condition="$data['role_id'] eq $vo['id']">selected</if>>{$vo.name}</option>
        </volist>
      </select>
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">状态：</label>
      <select name="status" class="required combox">
        <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
        <option value="0" <if condition="$data['status'] eq 0">selected</if>>不启用</option>
      </select>
  </div>
</div>
<input name="id" value="{$data.id}" type="hidden"> 
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>