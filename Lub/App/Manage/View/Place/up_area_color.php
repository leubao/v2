<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/Place/up_area_color',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <div class="form-group">
    <label class="col-sm-3 control-label">区域名称:</label>
    <input type="text" name="name" value="{$data.name}" class="form-control required" data-rule="required;" size="20" placeholder="区域名称">
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">区域背景色:</label>
    <input type="text" name="bgcolor" value="{$data.bgcolor}" data-toggle="colorpicker" data-bgcolor="true" size="15" readonly>
  </div>
</div>
<input type="hidden" name="id" value="{$data.id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>