<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/Place/area',array('menuid'=>$menuid));}" method="post">
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
        <th align="center">区域名称</th>
        <th align="center">座椅数</th>
        <th align="center">单双号</th>
        <th align="center">朝向</th>
        <th align="center">起始排列</th>
        <th align="center">状态</th>
        <th align="center">操作</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$vo.name}</td>
        <td align="center">{$vo.num}</td>
        <td align="center"><if condition=" $vo['is_mono'] eq '1' ">单号<elseif  condition=" $vo['is_mono'] eq '2' "/>双号<else />单双号</if></td>
        <td align="center"><if condition=" $vo['face'] eq '1' ">朝上<else />朝下</if></td>
        <td align="center">{$vo.start_row}/{$vo.start_list}</td>
        <td align="center">{$vo.status|status}</td>
        <td align="center">
        <a type="button" class="btn btn-default" data-toggle="dialog" data-max="true" href="{:U('Manage/Place/seat',array("areaid"=>$vo[id],"tempid"=>$tempid,"placeid"=>$placeid,'menuid'=>$menuid))}"><i class="fa fa-th"></i> 座位管理</a>
        <a type="button" class="btn btn-danger" data-toggle="doajax" href="{:U('Manage/Place/del_area',array("id"=>$vo[id],"tempid"=>$tempid,"placeid"=>$placeid,'menuid'=>$menuid))}" data-confirm-msg="确定要执行此操作吗？"><i class="fa fa-trash"></i>删除</a>
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