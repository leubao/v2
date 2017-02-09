<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>

<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Activity/water',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <div class="bjui-searchBar">
    <label>日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    
    <input type="hidden" name="user.id" value="{$user_id}">
    <input type="text" name="user.name" readonly value="{$user_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_user',array('type'=>1,'ifadd'=>1));}" data-group="user" data-width="600" data-height="445" data-title="领取人" placeholder="领取人">
    <label>&nbsp;手机:</label>
    <input type="text" value="{$map.phone}" name="phone" size="10">
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
  </div>  
</form>
<!--Page end-->
</div>
<div class="bjui-pageContent tableContent">
  <table class="" data-toggle="tablefixed" data-width="100%"  data-nowrap="true">
    <thead>
      <tr>
        <th align="center" width="40">编号</th>
        <th align="center">推广员</th>
        <th align="center">有效关注/已兑换</th>
        <th align="center">时间</th>
        <th align="center">备注</th>
        <th align="center">状态</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td align="center">{$i}</td>
        <td>{$vo.nickname}</td>
        <td align="center">{$vo.id|get_effective_focus} / {$vo.id|exchange_focus}</td>
        <td align="center">{$vo.create_time|date="Y-m-d H:i:s",###}</td>
        <td align="left">{$vo.remark}</td>
        <td align="center">{$vo.status|status}</td>
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