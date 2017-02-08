<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/Place/index',array('menuid'=>$menuid));}" method="post">
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
        <th align="center">场所名称</th>
        <th align="center">识别码</th>
        <th align="center">类型</th>
        <th align="center">状态</th>
        <th align="center">操作</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$vo.name}</td>
        <td>{$vo.idcode}</td>
        <td align="center"><if condition="$vo['type'] eq 1">剧院<elseif condition="$vo['type'] eq 2"/>景区</if></td>
        <td align="center">{$vo.status|status}</td>
        <td align="center"><a type="button" class="btn btn-default" data-toggle="navtab" data-options="{"id":meny}" href="{:U('Manage/Place/template',array('menuid'=>$menuid,'placeid'=>$vo['id']));}"><i class="fa fa-puzzle-piece"></i> 座椅模板</a></td>
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