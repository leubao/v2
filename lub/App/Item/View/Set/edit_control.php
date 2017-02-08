<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/edit_control',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
   <div class="form-group">
    <label class="col-sm-2 control-label">名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" value="{$data.name}" size="40" placeholder="名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">类型:</label>
    <input type="radio" name="type" data-toggle="icheck" value="1" <eq name="data.type" value='1'>checked</eq> data-label="一般控座&nbsp;">
    <input type="radio" name="type" data-toggle="icheck" value="2" <eq name="data.type" value='2'>checked</eq> data-label="特殊控座">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态码:</label>
    <input type="text" name="state" class="form-control required" data-rule="required;" size="15" value="{$data.state}" placeholder="排序">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">排序:</label>
    <input type="text" name="sort" class="form-control required" data-rule="required;" size="25" value="{$data.sort}" placeholder="排序">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">说明:</label>
    <textarea name="remark" class="form-control" rows="3" size="40">{$data.remark}</textarea>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="">状态</option>
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