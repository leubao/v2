<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title">订单核减</h4>
</div>
<div class="modal-body">
  <form action="{:U('Home/Order/subtract')}" method="post">
    <div class="col-md-12 row">
      <div class="col-md-6">
        <label>订单号：</label>
        {$sn} </div>
      <div class="col-md-6">
        <label>可核减数：</label>
        {$num} </div>
      <div class="col-md-12">
        <label>核减区域:</label>
        <volist name="area" id="vo">
          <label>
            <input type="checkbox" name="area[]" id="a_{$vo['area']}" value="{$vo['area']}" onclick="subtract({$vo['area']});">
            {$vo['areaname']}({$vo['num']})</label>
          <input type="text" name="seat_num[]" id="num_{$vo['area']}" size="2" disabled>
        </volist>
      </div>
      <div class="col-md-12">注：单一订单最多核减一次</div>
      <div class="col-md-6">
      <input type="hidden" name="sn" value="{$data['order_sn']}"/>
            <input type="hidden" name="order_status" value="{$data.status}" />
            <input type="hidden" name="money" value="{$data.money}"/>
            <button type="submit" class="btn btn-success">立即核减</button>
            </div>
    </div>
  </form>
</div>
<script>
function subtract(id){
	if($('#a_'+id).is(':checked')){
		$('#num_'+id).removeAttr('disabled');
	}else{
		$('#num_'+id).attr("disabled","disabled");
	}		
}
</script>