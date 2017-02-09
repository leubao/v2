<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Cashier/goods',array('menuid'=>$menuid));}" method="post">
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
        <th align="center">商品名称</th>
        <th align="center" width="50">价格</th>
        <th align="center" width="50">结算价</th>
        <th align="center" width="50">补贴</th>
        <th align="center" width="80">销售场景</th>

        <th align="center" width="100">创建时间</th>
        <th align="center" width="50">状态</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td align="center">{$i}</td>
        <td>{$vo.title}</td>
        <td align="center">{$vo.price|format_money} </td>
        <td align="center">{$vo.discount|format_money} </td>
        <td align="center">{$vo.rebate|format_money} </td>
        <td align="center">{$vo.scene|addsid} </td>
        <td align="center">{$vo.create_time|date="Y-m-d H:i:s",###}</td>
        <td align="center">{$vo.status|status}</td>
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