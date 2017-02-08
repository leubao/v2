<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Channel/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
   <div class="form-group">
    <label class="col-sm-2 control-label">考核对象:</label>
    <input type="hidden" name="channel.id" value="">
    <input type="text" name="channel.name" data-rule="required" value="" size="10" data-toggle="lookup" data-url="{:U('Manage/index/public_channel',array('ifadd'=>'2','level'=>'16'));}" data-group="channel" data-width="700" data-height="300">
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