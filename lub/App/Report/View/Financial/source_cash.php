<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/source_cash',array('menuid'=>$menuid));}" method="post">
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
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <!--
        <a type="button" class="btn btn-primary" href="{:U('Report/Exprot/export_execl',$export_map);}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出数据吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
        -->
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent" id="w_source_print">
<?php //dump($data);?>
<div class="visible-print-block w900">
    <h3 align="center">{$product_id|productName}资金来源表</h3>
    <span class="pull-left mb10">统计日期：{$starttime}-{$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
  </div>
	<table class="table table-bordered w900">
	<tbody>
    <tr>
      <th align="center" width="20px">编号</th>
      <th align="center" width="160px">销售计划</th>
      <th align="center" width="60px">支付类型</th>
      <th align="center" width="70px">金额</th>
      <th align="center" width="70px">小计</th>
      <th align="center" width="170px">备注</th>
    </tr>
  <volist name="data" id="vo">
    <tr class="subtotal" data-money="{$vo.money}">
      <td align="center" rowspan="7">{$i}</td>
      <td rowspan="7">{$vo.plan|planShow}</td>
      <td>现金</td>
      <td align="right">{$vo.cash|format_money}</td>
      <td align="right" rowspan="7">{$vo.money|format_money}</td>
      <td align="center" rowspan="7"></td>
    </tr>
    <tr>
      <td>授信额</td>
      <td align="right">{$vo.difference|format_money}</td>
    </tr>
    <tr>
      <td>签单</td>
      <td align="right">{$vo.sign|format_money}</td>
    </tr>
    <tr>
      <td>划卡</td>
      <td align="right">{$vo.stamp|format_money}</td>
    </tr>
    <tr>
      <td>支付宝</td>
      <td align="right">{$vo.alipay|format_money}</td>
    </tr>
    <tr>
      <td>微信支付</td>
      <td align="right">{$vo.wxpay|format_money}</td>
    </tr>
    <tr>
      <td>其它</td>
      <td align="right">{$vo.unknown|format_money}</td>
    </tr>
  </volist>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td align="right"><strong>合计:</strong></td>
      <td id="sub-source-money" align="right">0.00</td>
      <td ></td>
    </tr>
	  </tbody>
	</table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_source_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_money = 0;
  $("#w_source_print .subtotal").each(function(i) {
  	if($(this).data('money') != null){
      sub_money += parseFloat($(this).data('money'));
    }
  });

  sub_money = sub_money.toFixed(2);
  $("#sub-source-money").html(sub_money);
});
</script>