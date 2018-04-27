<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/edit_print_tpl',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">名称:</label>
    <input type="text" name="title" value="{$data.title}" class="form-control required" data-rule="required;" size="40" placeholder="名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">产品:</label>
   {$data['product']|product_name}
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">模板数据:</label>
    <textarea name="info" class="form-control" rows="20" size="40">{$vo.info}</textarea>
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
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>