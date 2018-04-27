<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Activity/index',array('menuid'=>$menuid));}" method="post">
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
        <th align="center">类型</th>
        <th align="center">时间段</th>
        <th align="center">状态</th>
        <th align="center">操作</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td align="center">{$i}</td>
        <td><a href="{:U('Item/Activity/activity',array('id'=>$vo['id']));}" data-toggle="dialog" data-mask="true" data-max="true">{$vo.title}</a></td>
        <td align="center">{$vo.type|activity_type}</td>
        <td>{$vo.starttime|date="Y-m-d",###}至{$vo.endtime|date="Y-m-d",###}</td>
        <td align="center">{$vo.status|status}</td>
        <td align="center">
          <if condition="$vo['scope'] eq '1' ">
          <a type="button" class="btn btn-default" data-toggle="dialog" data-mask="true" data-id="activity" href="{:U('Item/Activity/public_up_scope_channel',array('menuid'=>$menuid,'id'=>$vo['id']));}" data-title="{$vo.title}_活动范围"><i class="fa fa-puzzle-piece"></i>活动范围</a>
          </if>

          
        </td>
        <!--
        <td align="center"><a type="button" class="btn btn-default" data-toggle="dialog" data-mask="true" data-max="true" data-id="activity" href="{:U('Item/Activity/add_activity',array('menuid'=>$menuid,'id'=>$vo['id']));}" data-title="{$vo.title}_详情"><i class="fa fa-puzzle-piece"></i>活动页面</a></td>-->
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