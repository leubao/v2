<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/Logs/pay_log',array('menuid'=>$menuid));}" method="post">
 <!--Page --> 
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">

<!--Page end-->
<!--条件检索 s-->
  <div class="bjui-searchBar">
    <label>日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    <label>&nbsp;类型:</label>
    <select name="status" data-toggle="selectpicker">
        <option value="">全部</option>
        <option value="0" <if condition="$where['status'] eq '0'">selected</if>>待支付</option>
        <option value="1" <if condition="$where['status'] eq '1'">selected</if>>支付完成</option>
    </select>
    &nbsp;
    <label>&nbsp;支付方式:</label>
    <select name="type" data-toggle="selectpicker">
        <option value="">全部</option>
        <option value="1" <if condition="$where['type'] eq '1'">selected</if>>支付宝</option>
        <option value="2" <if condition="$where['type'] eq '2'">selected</if>>微信支付</option>
    </select>
    <input type="text" value="{$where['sn']}" name="sn" class="form-control" size="10" placeholder="单号">&nbsp;
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
  </div>
  <!--检索条件 e-->
  </form>
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th>ID</th>
        <th>支付方式</th>
        <th>订单号</th>
        <th align="center">网银单号</th>
        <th align="center">创建场景</th>
        <th align="center">金额</th>
        <th align="center">类型</th>
        <th align="center">创建时间</th>
        <th align="center">更新时间</th>
        <th>状态</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$vo.id}</td>
        <td><if condition="$vo['type'] eq '1'">支付宝<else />微信支付</if></td>
        <td><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn']))}"  data-id="orderinfo" data-width="900" data-height="600" data-title="订单详情">
        {$vo.order_sn}</a></td>
        <td>{$vo['out_trade_no']}</td>
        <td><if condition="$vo['scene'] eq '1'">当面付<else />在线支付</if></td>
        <td>{$vo.money}</td>
        <td>{$vo.create_time|date="Y-m-d H:i:s",###}</td>
        <td>{$vo.update_time|date="Y-m-d H:i:s",###}</td>
        <td><if condition="$vo['pattern'] eq '1'">收款<else />付款</if></td>
        <td><if condition="$vo['status'] eq '1'">支付完成<else />待支付</if></td>
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