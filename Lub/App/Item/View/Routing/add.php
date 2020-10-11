<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Routing/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <div class="form-group">
    <label class="col-sm-2 control-label">规则名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="规则名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">商户号:</label>
    <input type="text" name="mch_id" class="form-control required" data-rule="required;" size="40" placeholder="商户号">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">商户名称:</label>
    <input type="text" name="mch_name" class="form-control required" data-rule="required;" size="40" placeholder="商户名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">关联票型:</label>
    <input type="hidden" name="ticket.id" value="{$ticket_id}">
    <input type="text" name="ticket.name" readonly value="{$ticket_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">分账类型:</label>
    <select name="type" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1">金额分账</option>
      <option value="2">比例分账</option>
    </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">分账规则:</label>
    <input type="text" name="rule" class="form-control required" data-rule="required;" size="20" placeholder="分账规则">
    <span class="remark">所有分账比例累加不能超过 1（100%）0.23(含义：23%)</span>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">说明:</label>
    <input type="text" name="remark" class="form-control" size="40" placeholder="说明">
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