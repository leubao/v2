<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/Place/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">场所名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;name;remote[get:{:U('Item/Check/public_check_name',array('ta'=>18))}]" size="40" placeholder="场所名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">地址:</label>
    <input type="text" name="address" class="form-control required" data-rule="required;" size="50" placeholder="地址">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">交通:</label>
    <input type="text" name="traffic" class="form-control required" data-rule="required;" size="50" placeholder="交通">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">类型:</label>
    <select name="type" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1">剧院</option>
      <option value="2">景区</option>
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
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>