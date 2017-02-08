<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/add_seat',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
   <div class="form-group">
    <label class="col-sm-2 control-label">分组名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">所属模板:</label>
    <select name="template_id" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="">选择模板</option>
      <volist name="template" id="id">
      <option value="{$id.id}">{$id.name}</option>
      </volist>
  </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">排序:</label>
    <input type="text" name="sort" class="form-control required" data-rule="required;" size="25" placeholder="排序">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">类型:</label>
    <select name="stype" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="seat01" class="seat01">样式一</option>
      <option value="seat02" class="seat02">样式二</option>
      <option value="seat03" class="seat03">样式三</option>
      <option value="seat04" class="seat04">样式四</option>
      <option value="seat05" class="seat05">样式五</option>
      <option value="seat06" class="seat06">样式六</option>
      <option value="seat07" class="seat07">样式七</option>
      <option value="seat08" class="seat08">样式八</option>
      <option value="seat09" class="seat09">样式九</option>
      <option value="seat10" class="seat10">样式十</option>
      <option value="seat11" class="seat11">样式十一</option>
      <option value="seat12" class="seat12">样式十二</option>
  </select>
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
<input name="product_id" type="hidden" value="{$pid}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>