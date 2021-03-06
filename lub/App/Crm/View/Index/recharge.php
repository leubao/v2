<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Crm/Index/recharge',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <div class="form-group">
    <label class="col-sm-3 control-label">当前客户:</label>
    <if condition="$channel neq 4">{$crmid|crmName}<else />{$crmid|userName}</if>
  </div>
	 <div class="form-group">
    <label class="col-sm-3 control-label">充值金额:</label>
    <input type="text" name="cash" class="form-control required" data-rule="required;" size="20" placeholder="0.00">
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">备注:</label>
    <textarea name="remark"></textarea>
  </div>
</div>
<input name="crmid" value="{$crmid}" type="hidden">
<input name="channel" value="{$channel}" type="hidden"> 
<input name="groupid" value="{$groupid}" type="hidden"> 
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>