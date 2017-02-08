<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Crm/Index/userslist',array('menuid'=>$menuid));}" method="post">
    <input type="hidden" name="pageCurrent" value="{$currentPage}" />
    <input type="hidden" name="pageSize" value="{$numPerPage}" />
    <input type="hidden" name="groupid" value="{$groupid}" />
    <input type="hidden" name="cid" value="{$cid}" />
</form>
<!--工具条 s-->
<div class="toolBar">
  <div class="btn-group" role="group"> 
    <a type="button" class="btn btn-success" href="{:U('Crm/Index/adduser',array('cid'=>$cid,'groupid'=>$groupid));}" data-toggle="dialog" data-id="crm_user_list" data-mask="true"><i class="fa fa-plus"></i> 新增员工</a>
    <a type="button" class="btn btn-success" href="{:U('Crm/Index/edituser');}&id={#bjui-selected}" data-toggle="dialog" data-id="crm_user_list" data-mask="true"><i class="fa fa-plus"></i> 编辑员工</a>
    <a type="button" class="btn btn-warning" href="{:U('Crm/Index/reset_pwd');}&id={#bjui-selected}" data-toggle="doajax" data-confirm-msg="你确定要重置密码吗？"><span><i class="fa fa-youtube-play"></i> 密码重置</a>
  </div>
  <!--帮助 说明-->
  <div class="btn-group f-right" role="group"> <a type="button" class="btn btn-default" data-placement="bottom" data-toggle="tooltip" onclick="$(this).dialog('refresh');" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
   <button type="button" class="btn btn-default"><i class="fa fa-question-circle"></i></button>
  </div>
</div>
<!--工具条 e--> 
</div>
<div class="bjui-pageContent tableContent">
<table class="table table-bordered table-hover table-striped table-top">
    <thead>
      <tr>
        <th align="center">姓名</th>
        <th align="center">用户名</th>
        <th width="55" align="center">权限组</th>
        <th align="center">电话</th>
        <th align="center">邮箱</th>
        <th width="90" align="center">状态</th>
        <th align="center">添加时间</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td align="center">{$vo.nickname}</td>
        <td align="center">{$vo.username}</td>
        <td align="center">{$vo.role_id|roleName}</td>
        <td align="center">{$vo.phone}</td>
        <td align="center">{$vo.email}</td>
        <td align="center">{$vo.status|status}</td>
        <td align="center">{$vo.create_time|date="Y-m-d H:i:s",###}</td>
      </tr>
    </volist>
    </tbody>
</table>
</div>
<div class="bjui-pageFooter">
  <div class="pages"> <span>共 {$totalCount} 条</span> </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div> 
</div>