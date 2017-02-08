<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/Place/template',array('menuid'=>$menuid));}" method="post">
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
        <th align="center">模板名称</th>
        <th align="center">状态</th>
        <th align="center">操作</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$vo.name}</td>
        <td align="center">{$vo.status|status}</td>
        <td align="center"><a type="button" class="btn btn-default" data-toggle="navtab" href="{:U('Manage/Place/area',array('menuid'=>$menuid,"tempid"=>$vo[id],"placeid"=>$vo['place_id']));}"><i class="fa fa-th-large"></i> 区域管理</a>
        <a type="button" class="btn btn-danger" data-toggle="doajax" href="{:U('Manage/Place/delTemplate',array("id"=>$vo[id],"tempid"=>$tempid,"placeid"=>$placeid,'menuid'=>$menuid))}" data-confirm-msg="确定要执行此操作吗？"><i class="fa fa-trash"></i>删除</a>
        </td>
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