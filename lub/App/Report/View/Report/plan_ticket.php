<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Report/plan_ticket',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>销售计划:</label>
    <input type="hidden" name="plan.id" value="{$plan_id}">
    <input type="text" name="plan.name" readonly value="{$plan_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_date_plan',array('ifadd'=>1));}" data-group="plan" data-width="600" data-height="445" data-title="销售计划(场次)" placeholder="销售计划(场次)">
    
    &nbsp;
    <input type="hidden" name="ticket.id" value="{$ticket_id}">
    <input type="text" name="ticket.name" readonly value="{$ticket_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称">
  	<button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-primary" href="{:U('Order/Index/export_order');}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出订单信息吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent" id="w_plan_type_print">
<div class="visible-print-block w900">
    <h3 align="center">票型销售统计报表</h3>
    <span class="pull-left mb10">销售计划：{$plan_name}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
<table class="table table-bordered w900">
<thead>
  <tr>
    <th align="center" width="40px">编号</th>
    <th align="center" width="70px">票型名称</th>
    <th align="center" width="70px">票面价</th>
    <th align="center" width="70px">结算价</th>
    <th align="center" width="40px">数量</th>
    <th align="center" width="90px">票面金额</th>
    <th align="center" width="90px">结算金额</th>
    <th align="center" width="90px">补贴金额</th>
    <th width="90px" align="center">备注</th>
  </thead>
  <tbody>
  <volist name="data" id="vo">
  <tr>
    <td align="center">{$i}</td>
    <td align="center">{$vo.name}</td>
    <td align="right">{$vo.price}</td>
    <td align="right">{$vo.discount}</td>
    <td align="center">{$vo['number']}</td>
    <td align="right">{$vo.money|format_money}</td>
    <td align="right">{$vo.moneys|format_money}</td>
    <td align="right">{$vo.rebate|format_money}</td>
    <td></td>
  </tr>
  </volist>
  <tr>
    <td align="center"></td>
    <td align="center"></td>
    <td align="right"></td>
    <td align="right">合计:</td>
    <td align="center">{$info['number']}</td>
    <td align="right">{$info.money|format_money}</td>
    <td align="right">{$info.moneys|format_money}</td>
    <td align="right">{$info.rebate|format_money}</td>
    <td></td>
  </tr>
  
  </tbody>
</table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_plan_type_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>