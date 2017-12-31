<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageHeader">

<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Order/Index/plan_sales_seat',array('menuid'=>$menuid));}" method="post">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <select class="required" name="plan" id="plan" data-toggle="selectpicker">
        <option value="">+=^^=销售计划=^^=+</option>
        <volist name="plan" id="vo">
          <?php $ptime =  $vo['plantime']."-".$vo['games'];?>
          <option value="{$vo.id}"  <if condition="$pinfo['plan'] eq $vo['id']">selected</if>>{$vo.plantime|date="Y-m-d",###} <if condition="$product['type'] eq 1"> 第{$vo.games}场 {$vo.starttime|date="H:i",###}</if>
          </option>
        </volist>
    </select>
    
    <select name="type" data-toggle="selectpicker">
        <option value="">类型</option>
        <option value="1" <eq name="pinfo['type']" value="1"> selected</eq>>全部</option>
        <option value="2" <eq name="pinfo['type']" value="2"> selected</eq>>未售出</option>
        <option value="3" <eq name="pinfo['type']" value="3"> selected</eq>>已售出</option>
        <option value="4" <eq name="pinfo['type']" value="4"> selected</eq>>身份证入园</option>
        <option value="5" <eq name="pinfo['type']" value="5"> selected</eq>>已检票</option>
        <option value="6" <eq name="pinfo['type']" value="6"> selected</eq>>未检票</option>
    </select>
    &nbsp;
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>

    <div class="btn-group f-right" role="group"> 
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-default" href="http://www.leubao.com/index.php?g=Manual&a=show&sid=33" target="_blank" data-placement="left" data-toggle="tooltip" title="使用帮助"><i class="fa fa-question-circle"></i></a>
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
      <th align="center">区域</th>
      <th align="center">座位号</th>
      <th align="center">订单号</th>
      <th align="center">身份证</th>
      <th align="center">状态</th>
      <th align="center">检票时间</th>
    </tr>
  </thead>
  <tbody>
    <volist name="data" id="vo">
          <tr>
            <td align="center">{$i}</td>
            <td align="center">{$vo.area|areaName}</td>
            <td align="center">{$vo.seat|seatShow}</td>
            <td align="center">{$vo.order_sn}</td>
            <td align="center">{$vo.idcard} <if condition="!empty($vo['idcard'])"> [ {$vo.idcard|getAgeByID} 周]</if></td>
            <td align="center">{$vo.status|seat_status}</td>
            <td align="center">{$vo.checktime|datetime}</td>
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