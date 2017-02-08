<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/App/index',array('menuid'=>$menuid));}" method="post">
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
        <th align="center">应用名称</th>
        <th align="center">appid</th>
        <th align="center">URL地址</th>
        <th align="center">所属商户</th>
        <th align="center">操作员</th>
        <th align="center">状态</th>
        <th align="center">创建时间</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$vo.name}</td>
        <td align="center"><a href="{:U('Manage/App/appinfo',array('id'=>$vo['id'],'menuid'=>$menuid));}" data-toggle="dialog" data-options="{title:'应用详情:{$vo.name}',width:'600',height:'400'}">{$vo.appid}</a></td>
        <td>{$vo.url}</td>
        <td align="center">{$vo.crm_id|crmName}</td>
        <td align="center">{$vo.userid|userName}</td>
        <td align="center">{$vo.status|status}</td>
        <td align="center">{$vo.createtime|datetime}</td>
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