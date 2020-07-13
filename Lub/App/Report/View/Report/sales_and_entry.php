<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Report/sales_and_entry',array('menuid'=>$menuid));}" method="post">
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
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent w900" id="w_sales_and_entry_print">
<div class="visible-print-block">
    <h3 align="center">{$product_id|productName}销售入园统计报表</h3>
    <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
<table class="table table-bordered w900">
<thead>
  <tr>
    <th align="center" width="40px">编号</th>
    <th align="center" width="70px">销售计划</th>
    <th align="center" width="70px">可售</th>
    <th align="center" width="70px">已售出</th>
    <th align="center" width="40px">已入园</th>
    <th width="90px" align="center">备注</th>
  </thead>
  <tbody id="sales_and_entry_list">
  <volist name="data" id="vo">
  <tr data-count="{$vo.count}" data-sold="{$vo.sold}" data-into="{$vo.into}">
    <td align="center">{$i}</td>
    <td align="center">{$vo.title}</td>
    <td align="center">{$vo.count}</td>
    <td align="center">{$vo.sold}</td>
    <td align="center">{$vo.into}</td>
    <td></td>
  </tr>
  </volist>
  <tr>
    <td align="center"></td>
    <td align="right">合计:</td>
    <td id="sub-count-count" align="center">0</td>
    <td id="sub-count-sold" align="center">0</td>
	<td id="sub-count-into" align="center">0</td>
    <td></td>
  </tr>
  <tr>
    <td align="center"></td>
    <td align="right">累计场次:</td>
    <td align="center">{$plan_count}</td>
    <td align="right"></td>
    <td align="right"></td>
    <td></td>
  </tr>
  </tbody>
</table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_sales_and_entry_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_count_count = 0,
  	  sub_count_into = 0,
      sub_count_sold = 0;
  $('#sales_and_entry_list tr').each(function(i){
    if($(this).data('count') != null){
      sub_count_count += parseInt($(this).data('count'));
      sub_count_sold += parseInt($(this).data('sold'));
      sub_count_into += parseInt($(this).data('into'));
    }
  });
  $("#sub-count-count").html(sub_count_count);
  $("#sub-count-sold").html(sub_count_sold);
  $("#sub-count-into").html(sub_count_into);
});
</script>