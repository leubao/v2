<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/App/edit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">应用名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="应用名称" value="{$data.name}">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">应用地址:</label>
    <input type="text" name="url" class="form-control required" data-rule="required;" size="40" placeholder="例:http://www.leubao.com/" value="{$data.url}">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">支付方式:</label>
    <select name="is_pay" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1" <eq name="data.auth" value='1'>selected</eq>>信任支付</option>
      <option value="2" <eq name="data.auth" value='2'>selected</eq>>授信支付</option>
    </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">所属商户:</label>
    <input type="hidden" name="channel.id" value="{$data.crm_id}">
    <input type="text" name="channel.name" disabled value="{$data.crm_id|crmName}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel');}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">应用描述:</label>
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