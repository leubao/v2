<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Crm/Index/subsidies',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-3 control-label">当前客户:</label>
    {$id.name}
  </div>
  
  <div class="form-group">
    <label class="col-sm-3 control-label">金额:</label>
    <input type="text" name="phone" class="form-control required" size="20" value="" placeholder="手机号码">
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">备注:</label>
    <textarea name="remark"></textarea>
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>