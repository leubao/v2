<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/edit_seat',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
   <div class="form-group">
    <label class="col-sm-2 control-label">分组名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" value="{$data.name}" placeholder="名称">
  </div>
  
  <div class="form-group">
    <label class="col-sm-2 control-label">排序:</label>
    <input type="text" name="sort" class="form-control required" data-rule="required;" size="25" value="{$data.sort}" placeholder="排序">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">类型:</label>
    <select name="stype" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="seat01" class="seat01" <eq name="data.stype" value='seat01'>selected</eq>>样式一</option>
      <option value="seat02" class="seat02" <eq name="data.stype" value='seat02'>selected</eq>>样式二</option>
      <option value="seat03" class="seat03" <eq name="data.stype" value='seat03'>selected</eq>>样式三</option>
      <option value="seat04" class="seat04" <eq name="data.stype" value='seat04'>selected</eq>>样式四</option>
      <option value="seat05" class="seat05" <eq name="data.stype" value='seat05'>selected</eq>>样式五</option>
      <option value="seat06" class="seat06" <eq name="data.stype" value='seat06'>selected</eq>>样式六</option>
      <option value="seat07" class="seat07" <eq name="data.stype" value='seat07'>selected</eq>>样式七</option>
      <option value="seat08" class="seat08" <eq name="data.stype" value='seat08'>selected</eq>>样式八</option>
      <option value="seat09" class="seat09" <eq name="data.stype" value='seat09'>selected</eq>>样式九</option>
      <option value="seat10" class="seat10" <eq name="data.stype" value='seat10'>selected</eq>>样式十</option>
      <option value="seat11" class="seat11" <eq name="data.stype" value='seat11'>selected</eq>>样式十一</option>
      <option value="seat12" class="seat12" <eq name="data.stype" value='seat12'>selected</eq>>样式十二</option>
  </select>

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
<input name="id" type="hidden" value="{$data.id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>