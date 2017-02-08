<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/sub_business',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>销售计划:</label>
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

<div class="bjui-pageContent tableContent" id="w_business_print">
  <div class="pull-left">
    <table class="table table-bordered w900">
      <thead>
        <tr>
          <th align="center" width="115px">票型名称</th>
          <th align="center">票面单价</th>
          <th align="center">结算单价</th>
          <th align="center">数量</th>
          <th align="center">票面金额</th>
          <th align="center">结算金额</th>
          <th align="center">差额</th>
        </tr>
      </thead>
      <tbody id="business-list">
      <volist name="data['price']" id="vo">
        <tr data-id="{$vo.order_sn}" data-num="{$vo.number}" data-money="{$vo.money}" data-moneys="{$vo.moneys}" data-subsidy="{$vo.subsidy}">
          <td>{$vo.priceid|ticketName}</td>
          <td align="right">{$vo.price}</td>
          <td align="right">{$vo.discount}</td>
          <td align="center">{$vo.number}</td>
          <td align="right">{$vo.money|format_money}</td>
          <td align="right">{$vo.moneys|format_money}</td>
          <td align="right">{$vo.subsidy|format_money}</td>
         </tr>
      </volist>
       <tr>
  	    <td></td>
  	    <td></td>
  	    <td align="right"><strong>合计:</strong></td>
  	    <td id="sub-business-num" align="center">0</td>
  	    <td id="sub-business-money" align="right">0.00</td>
  	    <td id="sub-business-moneys" align="right">0.00</td>
  	    <td id="sub-business-subsidy" align="right">0.00</td>
       </tr>
      </tbody>
    </table>
  </div>

</div>
<div class="bjui-pageFooter">
<if condition="$type eq '1'">
	  <div class="pages">
	    <span>共 {$totalCount} 条</span>
	  </div>
	  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
<else />
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_business_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>
</if>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_num = 0,
      sub_money = 0,
      sub_moneys = 0,
      sub_subsidy = 0;
  $('#business-list tr').each(function(i){
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

  $("#sub-business-num").html(sub_num);
  $("#sub-business-money").html(sub_money);
  $("#sub-business-moneys").html(sub_moneys);
  $("#sub-business-subsidy").html(sub_subsidy);
});
</script>