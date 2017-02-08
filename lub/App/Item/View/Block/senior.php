<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageContent"> 
  <!-- Tabs -->
  <ul class="nav nav-tabs" role="tablist">
  <volist name="param['seat']" id="vo">
    <li> <a href="{:U('Item/Block/seat',array('area'=>$vo,'plan'=>$plan,'type'=>$type));}" role="tab" data-toggle="ajaxtab" data-target="#blockarea_{$vo}" data-reload="true" id="block_area_{$vo}">{$vo|areaName}</a></li>
  </volist>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
  <volist name="param['seat']" id="vt">
    <div class="tab-pane fade" id="blockarea_{$vt}"><!-- Ajax加载 --></div>
  </volist>
  </div>
</div>

<script>
/*自动加载当前选中tab*/
$(document).ready(function(){
    $("#block_area_{$area}").click();
});
</script>