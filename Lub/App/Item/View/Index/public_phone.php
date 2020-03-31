<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
  <!--Page -->
  <form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Index/public_phone',array('menuid'=>$menuid));}" method="post">
    <!--条件检索 s-->
    <div class="bjui-searchBar">
      <label>日期:</label>
      <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
      <label>至</label>
      <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
      &nbsp;
      <label>手机号:</label>
      <input type="text" value="{$phone}" data-rule="length[11~]" name="phone" class="form-control" size="20" placeholder="手机号号">
      &nbsp;
      <button type="submit" class="btn-default" data-icon="search">查询</button>
      &nbsp; <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a></div>
    </div>
    <!--检索条件 e-->
  </form>
  <!--Page end--> 
</div>
<div class="bjui-pageContent tableContent">

<table class="table table-bordered table-hover td50 w900">
  <tbody>
    <tr>
      <td width="90px">手机号</td>
      <td width="320px">{$phone}</td>
    </tr>
    <tr>
      <td>日期</td>
      <td>{$starttime} - {$endtime} </td>
    </tr>
    <tr>
      <td>订单数</td>
      <td>{$order}</td>
    </tr>
    <tr>
      <td>门票数</td>
      <td>{$ticket} </td>
    </tr>
    <tr>
      <td>订单金额</td>
      <td>{$money} </td>
    </tr>
  </tbody>
</table>
</div>