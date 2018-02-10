<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/add_print_tpl',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">名称:</label>
    <input type="text" name="title" class="form-control required" data-rule="required;" size="40" placeholder="名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">产品:</label>
    <volist name="product" id="vo">
      <input type="checkbox" name="product[]" value="{$vo.id}"> {$vo.name}<br>
    </volist>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">模板数据:</label>
    <textarea name="info" class="form-control" rows="20" size="40"></textarea>
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>