<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageContent"> 
  <!-- Tabs -->
  <ul class="nav nav-tabs" role="tablist">
  <volist name="area" id="vo">
    <li> <a href="{:U('Item/Order/public_seats',array('area'=>$vo['area'],'plan'=>$plan,'num'=>$vo['num'],'sn'=>$data['order_sn']));}" role="tab" data-toggle="ajaxtab" data-target="#seatarea_{$vo['area']}" data-reload="true" id="seat_area_{$i}">{$vo['area']|areaName}</a></li>
  </volist>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content" id="seat_seat">
  <volist name="area" id="vt">
    <div class="tab-pane fade" id="seatarea_{$vt['area']}"><!-- Ajax加载 --></div>
  </volist>
  </div>
</div>

<script>
/*自动加载当前选中tab*/
$(document).ready(function(){
    $("#seat_area_1").click();
});
</script>