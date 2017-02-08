<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Product/plan',array('menuid'=>$menuid));}" method="post">
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
        <th align="center">销售日期</th>
        <th align="center">场次</th>
        <th align="center">时间</th>
        <th align="center">操作员</th>
        <th align="center">配额</th>
        <th align="center">状态</th>
        <th align="center">添加时间</th>
        <th align="center">操作</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$i}</td>
        <td align="center"><a href="{:U('Item/Product/planinfo',array('menuid'=>$menuid,'id'=>$vo['id']));}" data-toggle="dialog" data-width="800" data-height="400" data-id="planinfo" data-mask="true">{$vo.plantime|date="Y-m-d",###}</a></td>
        <td align="center">{$vo.games}</td>
        <td align="center">{$vo.starttime|date="H:i",###} - {$vo.endtime|date="H:i",###} </td>
        <td align="center">{$vo.user_id|userName}</td>
        <td align="center">{$vo.quota}</td>
        <td align="center">{$vo.status|plan_status}</td>
        <td align="center">{$vo.createtime|date="Y-m-d H:i:s",###}</td>
        <td align="center"><if condition="$vo.status eq 4">计划已过期<else />[<a href="{:U('Item/Product/auth',array('menuid'=>$menuid,'id'=>$vo['id']));}" data-toggle="dialog" data-width="800" data-height="600" data-id="planauth" data-mask="true">销售权限</a>]</if></td>
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