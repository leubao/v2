<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/Api/edit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">接口名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="应用名称" value="{$data.name}">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">接口地址:</label>
    <input type="text" name="url" class="form-control required" data-rule="required;" size="40" value="{$data.url}" placeholder="例:api.php?m=api&a=checkIn">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">支付方式:</label>
    <select name="auth" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1" <eq name="data.auth" value='1'>selected</eq>>需要授权</option>
      <option value="0" <eq name="data.auth" value='0'>selected</eq>>无需授权</option>
    </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">接口描述:</label>
    <textarea name="remark">{$data.remark}</textarea>
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