<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Push/push',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <div class="form-group">
    <label class="col-sm-2 control-label">推送平台:</label>
    <select name="push" data-toggle="selectpicker">
      <option value="0">请选择推送平台</option>
      <volist name="push" id="vo">
      <option value="{$vo.id}">{$vo.name}</option>
      </volist>
    </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">推送日期:</label>
    <input type="text" data-toggle="datepicker" name="datetime" class="required" value="" data-rule="required;" placeholder="2019-12-12" autocomplete="off">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">人数:</label>
    <input type="text" name="count" class="form-control required" placeholder="入园人数" data-rule="required;" size="20" value="">
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>