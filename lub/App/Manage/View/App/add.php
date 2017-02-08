<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/App/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">应用名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="应用名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">应用地址:</label>
    <input type="text" name="url" class="form-control required" data-rule="required;" size="40" placeholder="例:http://www.leubao.com/">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">支付方式:</label>
    <select name="is_pay" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="">支付方式</option>
      <option value="1">信任支付</option>
      <option value="2">授信支付</option>
    </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">所属商户:</label>
    <input type="hidden" name="channel.id" value="">
    <input type="text" name="channel.name" disabled value="" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel');}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">

  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">应用描述:</label>
    <textarea name="remark"></textarea>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
	    <option value="">状态</option>
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