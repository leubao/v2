<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageContent"> 
  <!-- Tabs -->
  <ul class="nav nav-tabs" role="tablist">
  <volist name="area" id="vo">
    <li> <a href="{:U('Item/Set/seat',array('aid'=>$vo['id'],'fid'=>$fid,'type'=>$type));}" role="tab" data-toggle="ajaxtab" data-target="#grouparea_{$vo.id}" data-reload="true" id="group_area_{$vo.id}">{$vo.name}</a></li>
  </volist>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content" id="work_seat">
  <volist name="area" id="vt">
    <div class="tab-pane fade" id="grouparea_{$vt.id}"><!-- Ajax加载 --></div>
  </volist>
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