<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageContent"> 
  <!-- Tabs -->
  <ul class="nav nav-tabs" role="tablist">
  <volist name="param['seat']" id="vo">
    <li> <a href="{:U('Item/Work/seat',array('area'=>$vo,'plan'=>$plan,'type'=>$type));}" role="tab" data-toggle="ajaxtab" data-target="#workarea_{$vo}" data-reload="true" id="work_area_{$vo}">{$vo|areaName}</a></li>
  </volist>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
  <volist name="param['seat']" id="vt">
    <div class="tab-pane fade" id="workarea_{$vt}"><!-- Ajax加载 --></div>
  </volist>
  </div>
</div>

<script>
/*自动加载当前选中tab*/
$(document).ready(function(){
    $("#work_area_{$area}").click();
});
//选中价格政策->选择座椅->放到购物车->构建提交数组-->生成订单
</script>