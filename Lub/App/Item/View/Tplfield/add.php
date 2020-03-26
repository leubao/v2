<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Tplfield/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">场次编号:</label>
    <input type="text" name="number" class="form-control required" data-rule="required;" size="40" placeholder="场次编号">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">开始时间:</label>
    <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="start" value="">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">结束时间:</label>
    <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="end" value="">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">排序:</label>
    <input type="text" name="sorting" class="form-control required" data-rule="required;" size="40" placeholder="排序">
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
<input type="hidden" name="product_id" value="{$pid}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>