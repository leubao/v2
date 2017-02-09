<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Channel/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}"> 
</form>
<!--Page end-->
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
</div>
<div class="bjui-pageContent tableContent">
  <table class="" data-toggle="tablefixed" data-width="100%"  data-nowrap="true">
    <thead>
      <tr>
        <th align="center" width="40">编号</th>
        <th align="center">考核对象</th>
        <th align="center">记录类型</th>
        <th align="center">分值</th>
        <th align="center">操作员</th>
        <th align="center">状态</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td align="center">{$i}</td>
        <td>{$vo.crm_id|crmName}</td>
        <td align="center">{$vo.fill|kpi_fill}</td>
        <td align="center">{$vo.score}</td>
        <td align="center">{$vo.user_id|userName}</td>
        <td align="center">{$vo.status|status}</td>
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