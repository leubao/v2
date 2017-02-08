<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageContent"> 
  <!-- Tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li> <a href="{:U('Item/Activity/seats',array('area'=>$area));}" role="tab" data-toggle="ajaxtab" data-target="#seatarea_{$area}" data-reload="true" id="seat_area_act">{$area|areaName}</a></li>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content" id="seat_seat">
    <div class="tab-pane fade" id="seatarea_{$area}"><!-- Ajax加载 --></div>
  </div>
</div>

<script>
/*自动加载当前选中tab*/
$(document).ready(function(){
    $("#seat_area_act").click();
});
</script>
