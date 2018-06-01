<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/add_print_tpl',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">名称:</label>
    <input type="text" name="title" class="form-control required" data-rule="required;" size="40" placeholder="名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">纸张宽:</label>
    <input type="text" name="width" class="form-control required" data-rule="required;" size="30" placeholder="宽">mm
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">纸张高:</label>
    <input type="text" name="height" class="form-control required" data-rule="required;" size="30" placeholder="高">mm
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">模板数据:</label>
    <textarea name="info" class="form-control" rows="20" size="40"></textarea>
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>