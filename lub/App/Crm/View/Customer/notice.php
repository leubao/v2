<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Crm/Customer/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
   
</form>
<!--Page end-->


</div>

<div class="bjui-pageContent tableContent">
	
	<table data-toggle="tablefixed" data-width="100%">
    <thead>
      <tr>
        <th width="30" align="center">编号</th>
    		<th width="200" align="center">标题</th>
    		<th width="100" align="center">操作员</th>
    		<th width="60" align="center">状态</th>
        <th width="130" align="center">添加时间</th>
      </tr>
    </thead>
    <tbody>
      <volist id="vo" name="data">
      <tr data-id="{$vo['id']}">
        <td>{$vo['id']}</td>
    		<td>{$vo['title']}</td>
    		<td align="center">{$vo['user_id']|userName}</td>
    		<td align="center">{$vo.status|status}</td>
    		<td>{$vo['createtime']|datetime}</td>
      </tr>
    </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages"> <span>共 {$totalCount} 条</span> </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>