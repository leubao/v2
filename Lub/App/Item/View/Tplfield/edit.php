
<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Tplfield/edit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">场次编号:</label>
    <input type="text" name="number" class="form-control required" data-rule="required;" size="40" value="{$data.number}" placeholder="场次编号">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">开始时间:</label>
    <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="start" value="{$data.start}">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">结束时间:</label>
    <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="end" value="{$data.end}">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">排序:</label>
    <input type="text" name="sorting" class="form-control required" data-rule="required;" size="40" placeholder="排序" value="{$data.sorting}">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
	    <option value="">状态</option>
	    <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
      	<option value="0" <if condition="$data['status'] eq 0">selected</if>>禁用</option>
	</select>
  </div>
</div>
<input type="hidden" name="id" value="{$data.pid}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>