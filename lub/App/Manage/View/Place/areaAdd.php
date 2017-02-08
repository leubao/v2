<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/Place/areaAdd',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">区域名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="区域名称">
  </div>
  <div class="form-group">
      <label class="col-sm-2 control-label">座椅布局:</label>
      <label for="row" class="control-label x85">行：</label>
      <input type="text" class="form-control required" data-rule="digits;required;" name="row" size="5"/>
      <label for="list" class="control-label x85">列：</label>
      <input type="text" class="form-control required" data-rule="digits;required;" name="list" size="5"/>
  </div>
  <div class="form-group">
      <label class="col-sm-2 control-label">起始行列:</label>
      <label for="row" class="control-label x85">起始行：</label>
      <input type="text" class="form-control required" data-rule="digits;required;" name="start_row" size="5"/>
      <label for="list" class="control-label x85">起始列：</label>
      <input type="text" class="form-control required" data-rule="digits;required;" name="start_list" size="5"/>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">单双号:</label>
    
    <label class="radio-inline">
      <input type="radio" name="is_mono" value="1"> 单号
    </label>
    <label class="radio-inline">
      <input type="radio" name="is_mono" value="2"> 双号
    </label>
    <label class="radio-inline">
      <input type="radio" name="is_mono" value="3" checked> 单双号
    </label>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">座椅朝向:</label>
    <label class="radio-inline">
      <input type="radio" name="face" value="1"> 向上
    </label>
    <label class="radio-inline">
      <input type="radio" name="face" value="2" checked> 向下
    </label>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">说明:</label>
    <input type="text" name="reamrk" class="form-control" size="50" placeholder="说明">
  </div>
  
  <div class="form-group">
    <label class="col-sm-2 control-label">排序:</label>
    <input type="text" name="listorder" class="form-control" size="10" value="0">
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
<input type="hidden" name="template_id" value="{$template_id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>