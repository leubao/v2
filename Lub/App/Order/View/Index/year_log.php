<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageHeader">



<!--Page end-->
</div>
<div class="bjui-pageContent tableContent">
  <table class="table table-bordered table-hover">
  <thead>
    <tr>
      <th align="center">编号</th>
      <th align="center">流水号</th>
      <th align="center">凭证类型</th>
      <th align="center">会员</th>
      <th align="center">凭证数据</th>
      <th align="center">入园日期</th>
      <th align="center">入园时间</th>
      <th align="center">状态</th>
    </tr>
  </thead>
  <tbody>
    <volist name="data" id="vo">
          <tr>
            <td align="center">{$i}</td>
            <td align="center">{$vo.sn} </td>
            <td align="center">{$vo.thetype|thetype}</td>
            <td align="center">{$vo.member_id|memberName} </td>
            <td align="center">{$vo.password} </td>
            <td align="center">{$vo.datetime|date="Y-m-d",###}</td>
            <td align="center">{$vo.update_time|datetime}</td>
            <td align="center">{$vo.status|minto}</td>
            
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