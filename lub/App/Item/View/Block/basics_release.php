<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Block/release',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <volist name="param['seat']" id="area">
      <div class="form-group">
      <label class="col-sm-2 control-label">{$area|areaName}</label>
        <input type="hidden" name="area[]" value="{$area}">
        <input type="text" name="area_num[]" class="form-control" data-rule="number;integer[+1]; range[~200]" size="20" placeholder="释放数量">
      </div>
  </volist>
</div>
<input type="hidden" name="type" value="2">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>