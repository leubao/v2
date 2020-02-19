<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/terminal',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <div class="form-group">
    <label class="col-sm-2 control-label">通道名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">通道编号:</label>
    <input type="text" name="idcode" class="form-control required" data-rule="required;" size="40" placeholder="通道编号">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">通道票型:</label>
    <div class="col-sm-10" style="padding: 0px">
    <volist name="ticket" id="vo">
      <div class="ticket">
        <input type="checkbox" name="ticket[]" value="{$vo.id}"> {$vo.name}
      </div>
    </volist>
    </div>
  </div>
  <input name="product_id" type="hidden" value="{$pid}">
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="">状态</option>
      <option value="1" selected>启用</option>
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