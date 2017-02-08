<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/Api/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">       
  <input type="hidden" name="orderField" value="${param.orderField}">         
  <input type="hidden" name="orderDirection" value="${param.orderDirection}">
</form>
<!--Page end-->
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th align="center">接口名称</th>
        <th align="center">接口地址</th>
        <th align="center">授权方式</th>
        <th align="center">接口描述</th>
        <th align="center">状态</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$vo.name}</td>
        <td>{$vo.url}</td>
        <td align="center"><if condition="$vo['auth'] eq 1">需要授权<elseif condition="$vo['auth'] eq 0"/>无需授权</if></td>
        <td align="center">{$vo.remark}</td>
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