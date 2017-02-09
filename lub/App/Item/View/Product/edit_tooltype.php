<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Product/add_tooltype',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">名称:</label>
    <input type="text" name="title" class="form-control required" data-rule="required;" value="{$data.title}" size="40" placeholder="类型名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">人数:</label>
    <input type="text" name="number" class="form-control required" data-rule="required;" value="{$data.number}" size="20" placeholder="人数">
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
<input type="hidden" name="id" value="{$data.id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>