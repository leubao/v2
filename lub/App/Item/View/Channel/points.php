<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/add_control',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
   <div class="form-group">
    <label class="col-sm-2 control-label">处罚对象:</label>
    
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">类型:</label>
    <input type="radio" name="type" data-toggle="icheck" value="1" checked data-label="标准处罚&nbsp;">
    <input type="radio" name="type" data-toggle="icheck" value="2" data-label="自定义处罚">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">分值:</label>
    <input type="text" name="one_quota" class="form-control required" data-rule="required;" size="15" placeholder="单场配额">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">说明:</label>
    <textarea name="remark" class="form-control" rows="3" size="40"></textarea>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
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