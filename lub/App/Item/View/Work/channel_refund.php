<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Work/channel_refund',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageCurrent" value="{$currentPage}" />
  <input type="hidden" name="pageSize" value="{$numPerPage}" />
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th align="center">订单号</th>
        <th align="center">说明</th>
        <th align="center">金额</th>
        <th align="center">所属计划</th>
        <th align="center">申请人</th>
        <th align="center">渠道商</th>
        <th align="center">创建时间</th>
        <th align="center">状态</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td align="center"><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn']))}"  data-id="orderinfo" data-width="900" data-height="600" data-title="订单详情">{$vo.order_sn}</a></td>
        <td align="left">{$vo.reason}</td>
        <td align="right">{$vo.money}</td>
        <td align="center">{$vo.plan_id|planShow}</td>
        <td align="center">{$vo.applicant|userName}</td>
        <td align="center">{$vo.crm_id|crmName}</td>
        <td align="center">{$vo.createtime|date="Y-m-d H:i:s",###}</td>
        <td align="center">{$vo['status']|refund_status}</td>
       </tr>
    </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages">
    <span>共 {$totalCount} 条</span>
  </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>