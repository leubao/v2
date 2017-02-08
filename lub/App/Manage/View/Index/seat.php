<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageContent"> 
  <!-- Tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li> <a href="{:U('Manage/Index/area',array('area'=>1));}" role="tab" data-toggle="ajaxtab" data-target="#area_1" data-reload="true" id="ab">A区域</a></li>
    <li><a href="{:U('Manage/Index/area',array('area'=>2));}" role="tab" data-toggle="ajaxtab" data-target="#area_2" data-reload="true"  id="aa">B区域</a></li>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content" id="work_seat">
    <div class="tab-pane fade" id="area_1"><!-- Ajax加载 --></div>
    <div class="tab-pane fade" id="area_2"><!-- Ajax加载 --></div>
  </div>
</div>

<script>
/*自动加载当前选中tab*/
$(document).ready(function(){
    $("#ab").click();
});
/*计算总金额
function recalculateTotal(sc) {
	var total = 0;
	sc.find('selected').each(function () {
		total += this.data().price;
	});			
	return total;
}
//选中价格政策->选择座椅->放到购物车->构建提交数组-->生成订单
*/

		
</script>