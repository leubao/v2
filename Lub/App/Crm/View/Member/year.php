<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Crm/Member/year',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}"> 
</form>
<!--Page end-->
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
</div>

<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%">
    <thead>
      <tr>
        <th width="30" align="center">编号</th>
        <th width="100" align="center">姓名</th>
        <th width="100">性别</th>
        <th width="100">年龄</th>
        <th width="60" align="center">入园数</th>
        <th width="60" align="center">状态</th>
        <th width="130" align="center">注册时间</th>
      </tr>
    </thead>
    <tbody>
      <volist id="vo" name="data">
      <tr data-id="{$vo['id']}">
        <td>{$i}</td>
        <td>{$vo['name']}</td>
        <td>{$vo['sex']}</td>
        <td>{$vo['count']}</td>
        <td align="center">{$vo['status']|status}</td>
        <td align="center">{$vo['create_time']|datetime}</td>
        <td align="center"></td>
      </tr>
    </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages"> <span>共 {$totalCount} 条</span> </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>