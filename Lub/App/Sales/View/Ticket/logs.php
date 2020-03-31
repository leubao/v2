<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageHeader">

<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Sales/Ticket/logs',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    
    <label>&nbsp;状态:</label>
    <select name="status" data-toggle="selectpicker">
        <option value="">全部</option>
        <option value="2" <if condition="$status eq '2'">selected</if>>预定成功</option>
        <option value="99" <if condition="$status eq '99'">selected</if>>完结</option>
    </select>

    <input type="hidden" name="plan.id" value="{$plan_id}">
    <input type="text" name="plan.name" readonly value="{$plan_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_date_plan',array('ifadd'=>1));}" data-group="plan" data-width="600" data-height="445" data-title="销售计划(场次)" placeholder="销售计划(场次)">
    
    <input type="text" value="" name="sn" class="form-control" data-rule="length[5~]" size="10" placeholder="单号">&nbsp;
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
    </div>
  </div>
  <!--检索条件 e-->
</form>

<!--Page end-->
</div>
<div class="bjui-pageContent tableContent">
  <table class="table table-bordered table-hover">
  <thead>
    <tr>
      <th align="center">编号</th>
      <th align="center">订单号</th>
      <th align="center">销售计划</th>
      <th align="center">凭证数据</th>
      <th align="center">核销时间</th>
    </tr>
  </thead>
  <tbody>
    <volist name="data" id="vo">
      <tr>
        <td align="center">{$i}</td>
        <td align="center">{$vo.order_sn} </td>
        <td align="center">{$vo.plan_id|planShow}</td>
        <td align="center">{$vo.ciphertext} </td>
        <td align="center">{$vo.checktime|date="Y-m-d H:i:s",###}</td>
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