<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/reception',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>统计日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    <input type="hidden" name="plan.id" value="{$plan_id}">
    <input type="text" name="plan.name" readonly value="{$plan_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_date_plan',array('ifadd'=>1));}" data-group="plan" data-width="600" data-height="445" data-title="销售计划(场次)" placeholder="销售计划(场次)">
    
  	<button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-primary" href="{:U('Order/Index/export_execl');}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出订单信息吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent" id="w_reception_print">
<div class="visible-print-block  w900">
    <h3 align="center">{$product_id|productName}工作票统计报表</h3>
    <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
<table class="table table-bordered w900">
<thead>
  <tr>
    <th align="center" width="40px">编号</th>
    <th align="center" width="150px">销售计划</th>
    <th align="center" width="70px">票型名称</th>
    <th align="center" width="70px">票面单价</th>
    <th align="center" width="40px">数量</th>
    <th width="90px" align="center">小计</th>
    <th width="90px" align="center">备注</th>
  </thead>
  <tbody>
  
   <volist name="data" id="vo" key='k'>
  <volist name="vo['price']" id="price" key='i'>
  <if condition="$vo['tic_num'] gt '1'" >
    <if condition="$i eq '1'">
    <tr class="subtotal" data-num="{$price['number']}" data-money="{$vo.money}" data-moneys="{$vo.moneys}" data-subsidy="{$vo.rebate}">
      <td rowspan="{$vo['tic_num']}">{$k}</td>
      <td rowspan="{$vo['tic_num']}">{$vo.plan|planShow}</td>
      <td>{$price['price_id']|ticketName}</td>
      <td align="right">{$price['price']}</td>
      <td align="center">{$price['number']}</td>
      <td align="center" rowspan="{$vo['tic_num']}">{$vo['number']}</td>
      <td align="center" rowspan="{$vo['tic_num']}"></td>
    </tr>
    <else />
    <tr class="subtotal" data-num="{$price['number']}" data-money="{$vo.money}" data-moneys="{$vo.moneys}" data-subsidy="{$vo.rebate}">
      <td>{$price['price_id']|ticketName}</td>
      <td align="right">{$price['price']}</td>
      <td align="center">{$price['number']}</td>
    </tr>
    </if>
  <else />
    <tr class="subtotal" data-num="{$vo.number}" data-money="{$vo.money}" data-moneys="{$vo.moneys}" data-subsidy="{$vo.rebate}">
      <td>{$k}</td>
      <td>{$vo.plan|planShow}</td>
      <td>{$price['price_id']|ticketName}</td>
      <td align="right">{$price['price']}</td>
      <td align="center">{$price['number']}</td>
      <td align="center">{$vo['number']}</td>
      <td></td>
    </tr>
  </if>
  </volist>
  </volist>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right" width="70px"><strong>合计:</strong></td>
        <td align="center" id="sub-reception-num">0</td>
        <td>&nbsp;</td>
      </tr>
  </tbody>
</table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_reception_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_num = 0,
      sub_money = 0,
      sub_moneys = 0,
      sub_subsidy = 0;
  $("#w_reception_print .subtotal").each(function(i) {
  	if($(this).data('num') != null){
      sub_num += parseInt($(this).data('num'));
      sub_money += parseFloat($(this).data('money'));
      sub_moneys += parseFloat($(this).data('moneys'));
      sub_subsidy += parseFloat($(this).data('subsidy'));
    }
  });
  sub_money = sub_money.toFixed(2);
  sub_moneys = sub_moneys.toFixed(2);
  sub_subsidy = sub_subsidy.toFixed(2);

  $("#sub-reception-num").html(sub_num);
  $("#sub-reception-money").html(sub_money);
  $("#sub-reception-moneys").html(sub_moneys);
  $("#sub-reception-subsidy").html(sub_subsidy);
});
</script>