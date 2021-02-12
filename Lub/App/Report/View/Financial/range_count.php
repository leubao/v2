<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/range_count',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>统计日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    
  	<button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent" id="w_range_print">
<div class="visible-print-block  w900">
    <h3 align="center">{$product_id|productName}区域统计报表</h3>
    <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
<table class="table table-bordered w900">
<thead>
  <tr>
    <th align="center" width="40px">编号</th>
    <th align="center" width="150px">区域</th>
    <th align="center" width="70px">数量</th>
    <th align="center" width="70px">票面金额</th>
    <th align="center" width="40px">结算金额</th>
    <th width="90px" align="center">备注</th>
  </thead>
  <tbody>
  
  <volist name="data" id="vo" key='k'>
    <tr class="subtotal" data-num="{$vo.number}" data-money="{$vo.money}" data-moneys="{$vo.moneys}">
      <td>{$k}</td>
      <td>{$vo.area|areaName}</td>
      <td align="center">{$vo.number}</td>
      <td align="right">{$vo.money}</td>
      <td align="right">{$vo.moneys}</td>
      <td align="center"></td>
    </tr>
  </volist>
    <tr>
        <td></td>
        <td align="right"><strong>合计:</strong></td>
        <td align="center" id="sub-range-num">0</td>
        <td align="right" id="sub-range-money">0.00</td>
        <td align="right" id="sub-range-moneys">0.00</td>
        <td></td>
      </tr>
  </tbody>
</table>
</div>

<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li></li>
    </ul>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_num = 0,
      sub_money = 0,
      sub_moneys = 0;
  $("#w_range_print .subtotal").each(function(i) {
  	if($(this).data('num') != null){
      sub_num += parseInt($(this).data('num'));
      sub_money += parseFloat($(this).data('money'));
      sub_moneys += parseFloat($(this).data('moneys'));
    }
  });
  sub_money = sub_money.toFixed(2);
  sub_moneys = sub_moneys.toFixed(2);

  $("#sub-range-num").html(sub_num);
  $("#sub-range-money").html(sub_money);
  $("#sub-range-moneys").html(sub_moneys);
});
</script>