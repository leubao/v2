<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Set/seale_area',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
</form>
<!--Page end-->
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th width="50">编号</th>
        <th align="center">区域名称</th>
        <th align="center">身份证号段</th>
        <th align="center">状态</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$i}</td>
        <td align="center">{$vo.title}</td>
        <td align="center">{$vo.idcard}</td>
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