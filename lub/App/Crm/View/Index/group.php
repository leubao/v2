<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Crm/Index/index',array('menuid'=>$menuid));}" method="post">
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
        <th width="30">编号</th>
        <th width="100">分组名</th>
        <th width="100">分组描述</th>
        <th width="100">价格政策</th>
        <th width="100">分组属性</th>
        <th width="30" align="center">配额</th>
        <th width="100" align="center">结算方式</th>
        <th width="60" align="center">状态</th>
        <th width="130">添加时间</th>
      </tr>
    </thead>
    <tbody>
      <volist id="vo" name="data">
      <tr data-id="{$vo['id']}">
        <td>{$vo['id']}</td>
        <td>{$vo['name']}</td>
        <td>{$vo['remark']}</td>
        <td>{$vo.price_group|price_group}</td>
        <td>{$vo.type|crm_group_type}</td>
        <td align="center">{$vo.group_quota}</td>
        <td align="center"><if condition="$vo['settlement'] eq '1'">票面价结算<elseif condition="$vo['settlement'] eq '3'" />结算价结算<else />底价结算</if></td>
        <td align="center">{$vo['status']|status}</td>
        <td>{$vo['create_time']|datetime}</td>
      </tr>
    </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages"> <span>共 {$totalCount} 条</span> </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>