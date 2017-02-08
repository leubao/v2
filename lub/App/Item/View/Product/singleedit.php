<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Product/singleedit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">单票名称:</label>
    <input type="text" name="name" class="form-control required" value="{$data.name}" data-rule="required;" size="40" placeholder="单票名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">单票价格:</label>
    <input type="text" name="price" class="form-control required" value="{$data.price}" data-rule="required;" size="40" placeholder="单票价格">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">季节类型:</label>
    <select name="season" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="">季节类型</option>
      <option value="1" <if condition="$data['season'] eq 1">selected</if>>淡季</option>
      <option value="2" <if condition="$data['season'] eq 2">selected</if>>旺季</option>
      <option value="3" <if condition="$data['season'] eq 3">selected</if>>淡旺季</option>
  </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
	    <option value="">状态</option>
	    <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
      <option value="0" <if condition="$data['status'] eq 0">selected</if>>禁用</option>
	</select>
  </div>
</div>
<input type="hidden" name="id" value="{$data.id}">
<input type="hidden" name="product_id" value="{$data.product_id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>