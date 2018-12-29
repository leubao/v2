<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Crm/Member/types',array('menuid'=>$menuid));}" method="post">
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
        <th width="100" align="center">类型名称</th>
        <th width="100">起售金额</th>
        <th width="100">类型描述</th>
        <th width="100">备注</th>
        <th width="60" align="center">状态</th>
        <th width="130" align="center">添加时间</th>
        <th width="130" align="center">操作</th>
      </tr>
    </thead>
    <tbody>
      <volist id="vo" name="data">
      <tr data-id="{$vo['id']}">
        <td>{$i}</td>
        <td><a data-toggle="dialog" href="{:U('Crm/Member/public_type_info',array('id'=>$vo['id']))}" data-id="memberconfig" data-width="900" data-height="600" data-title="{$vo['title']}详情">{$vo['title']}</a></td>
        <td>{$vo['money']}</td>
        <td>{$vo['type']|memberType}</td>
        <td>{$vo['remark']}</td>
        <td align="center">{$vo['status']|status}</td>
        <td align="center">{$vo['create_time']|datetime}</td>
        <td align="center"><a data-toggle="dialog" href="{:U('Crm/Member/config',array('id'=>$vo['id']))}" data-id="memberconfig" data-width="900" data-height="600" data-title="{$vo['title']}设置">设置</a></td>
      </tr>
    </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages"> <span>共 {$totalCount} 条</span> </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>