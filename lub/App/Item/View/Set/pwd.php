<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Set/pwd',array('menuid'=>$menuid));}" method="post">
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
        <th align="center">所有人</th>
        <th align="center">产品范围</th>
        <th align="center">用途</th>
        <th align="center">状态</th>
        <th align="center">添加时间</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$i}</td>
        <td align="center">{$vo.name}</td>
        <td align="center">{$vo.product_id|productName}</td>
        <td align="center"><if condition="$vo['type'] eq '1'">二次打印</if></td>
        <td align="center">{$vo.status|status}</td>
        <td align="center">{$vo.createtime|date="Y-m-d H:i:s",###}</td>
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