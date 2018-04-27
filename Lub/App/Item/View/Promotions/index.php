<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Promotions/index',array('menuid'=>$menuid));}" method="post">
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
        <th align="center">活动名称</th>
        <th align="center">活动类型</th>
        <th align="center">有效期</th>
        <th align="center">描述</th>
        <th align="center">操作</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td align="center">{$i}</td>
        <td>{$vo.title}</td>
        <td>{$vo.type|activity_type}</td>
        <td align="center">{$vo.starttime|date="Y-m-d",###} - {$vo.endtime|date="Y-m-d",###} </td>
        <td align="center">{$vo.remark}</td>
        <td align="center"><a href="{:U('Item/Promotions/work',array('id'=>$vo['id'],'menuid'=>$menuid))}" data-toggle="navtab" data-id="625Item" data-title="活动促销 -{$vo.title} ">售票</a></td>
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