<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/edit_terminal',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <div class="form-group">
    <label class="col-sm-2 control-label">通道名称:</label>
    <input type="text" name="name" class="form-control required" value="{$data.name}" data-rule="required;" size="40" placeholder="名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">通道编号:</label>
    <input type="text" name="idcode" class="form-control required" value="{$data.idcode}" data-rule="required;" size="40" placeholder="通道编号">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">通道票型:</label>
    <div class="col-sm-10" style="padding: 0px">
    <volist name="ticket" id="vo">
      <div class="ticket">
        <input type="checkbox" name="ticket[]" value="{$vo.id}" <if condition="$vo['checked']">checked</if> /> {$vo.name}
      </div>
    </volist>
    </div>
  </div>
  <input name="id" type="hidden" value="{$data.id}">
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1" <eq name="data.status" value='1'>selected</eq>>启用</option>
      <option value="0" <eq name="data.status" value='0'>selected</eq>>禁用</option>
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