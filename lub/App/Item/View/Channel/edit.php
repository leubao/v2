<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Channel/edit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
   <div class="form-group">
    <label class="col-sm-2 control-label">考核对象:</label>
    {$data.crm_id|crmName}
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">全年任务量:</label>
    <input type="text" name="task" class="form-control required" data-rule="required;" size="15" value="{$data.task}">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">单场配额:</label>
    <input type="text" name="quota" class="form-control required" data-rule="required;" size="15" value="{$data.quota}">
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