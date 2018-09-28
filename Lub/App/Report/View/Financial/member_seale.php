<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/member_seale',array('menuid'=>$menuid));}" method="post">
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

<div class="bjui-pageContent tableContent w900" id="w_member_seale_print">
<div class="visible-print-block">
    <h3 align="center">{$product_id|productName}会员卡销售统计报表</h3>
    <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
<table class="table table-bordered w900">
<thead>
  <tr>
    <th align="center" width="40px">编号</th>
    <th align="center" width="70px">会员卡类型</th>
    <th align="center" width="70px">单价</th>
    <th align="center" width="40px">数量</th>
    <th align="center" width="90px">金额</th>
    <th width="90px" align="center">备注</th>
  </thead>
  <tbody>
  <volist name="member_seale" id="mem">
  <tr>
    <td align="center">{$i}</td>
    <td align="center">{$mem.title}</td>
    <td align="right">{$mem.price|format_money}</td>
    <td align="center">{$mem.number}</td>
    <td align="right">{$mem.money|format_money}</td>
    <td></td>
  </tr>
  </volist>
  <tr>
    <td align="center"></td>
    <td align="center"></td>
    <td align="right">合计:</td>
    <td align="center">{$member_sum.number}</td>
    <td align="right">{$member_sum.money|format_money}</td>
    <td></td>
  </tr>
  </tbody>
</table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_member_seale_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>