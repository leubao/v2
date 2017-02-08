<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Product/singleadd',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">单票名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="单票名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">单票价格:</label>
    <input type="text" name="price" class="form-control required" data-rule="required;" size="40" placeholder="单票价格">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">季节类型:</label>
    <select name="season" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="">季节类型</option>
      <option value="1">淡季</option>
      <option value="2">旺季</option>
      <option value="3">淡旺季</option>
  </select>
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
<input type="hidden" name="product_id" value="{$pid}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>