<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Product/group',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
</form>
<!--Page end-->
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-nowrap="true">
    <thead>
      <tr>
        <th width="50">编号</th>
        <th align="center">规则名称</th>
        <th align="center">票型名称</th>
        <th align="center">商户号</th>
        <th align="center">商户名称</th>
        <th align="center">分账类型</th>
        <th align="center">金额/比例</th>
        <th align="center">说明</th>
        <th align="center">状态</th>
        <th align="center">创建时间</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$i}</td>
        <td align="center">{$vo.name}</td>
        <td align="center">{$vo.ticket_id|ticketName}</td>
        <td align="center">{$vo.mch_id}</td>
        <td align="center">{$vo.mch_name}</td>
        <td align="center">{$vo.type|billType}</td>
        <td align="center">{$vo.rule}</td>
        <td align="center">{$vo.remark}</td>
        <td align="center">{$vo.status|status}</td>
        <td align="center">{$vo.create_time|datetime}</td>
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