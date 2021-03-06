<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--工具条 s-->
  <Managetemplate file="Common/Nav"/>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Work/bookticket',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <input type="hidden" name="channel.id" value="{$map['channel_id']}">
    <input type="text" name="channel.name" readonly value="{$channelname}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel');}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">
    <input type="hidden" name="plan.id" value="">
    <input type="text" name="plan.name" readonly value="" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_date_plan');}" data-group="plan" data-width="600" data-height="445" data-title="销售计划(场次)" placeholder="销售计划(场次)">
    <input type="text" value="" name="sn" class="form-control" data-rule="length[5~]" size="10" placeholder="单号">&nbsp;
     <input type="text" value="" name="phone" class="form-control" data-rule="mobile" size="10" placeholder="取票手机号">&nbsp;
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th align="center">订单号</th>
        <th align="center">所属计划</th>
        <th align="center">订单(场景)类型</th>
        <th align="center">数量</th>
        <th align="center">金额</th>
        <th align="center">渠道商</th>
        <th align="center">导游</th>
        <th align="center">创建时间</th>
        <th align="center">状态</th>
      </tr>
    </thead>
    <tbody id="order-book-list">
    <volist name="data" id="vo">
      <tr data-id="{$vo.order_sn}" data-num="{$vo.number}" data-money="{$vo.money}">
        <td><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn']))}"  data-id="orderinfo" data-width="900" data-height="600" data-title="订单详情">{$vo.order_sn}</a></td>
        <td>{$vo.plan_id|planShow}</td>
        <td align="center">{$vo.addsid|addsid}（{$vo.type|channel_type}）</td>
        <td align="center">{$vo.number}</td>
        <td align="right">{$vo.money}</td>
        <td align="center">{$vo.channel_id|crmName}</td>
        <td align="center">{$vo.take}</td>
        <td align="center">{$vo.createtime|date="Y-m-d H:i:s",###}</td>
        <td align="center">{$vo['status']|order_status}</td>
       </tr>
    </volist>
    <tr>
     <td></td>
     <td></td>
     <td align="right">当前页合计:</td>
     <td id="sub-book-num" align="center">0</td>
     <td id="sub-book-money" align="right">0.00</td>
     <td></td>
     <td></td>
     <td></td>
     <td></td>
    </tr>
    </tbody>
  </table>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_num = 0,
      sub_money = 0;
  $('#order-book-list tr').each(function(i){
    if($(this).data('num') != null){
      sub_num += parseInt($(this).data('num'));
      sub_money += parseFloat($(this).data('money'));
    }
  });
  sub_money = sub_money.toFixed(2);
  $("#sub-book-num").html(sub_num);
  $("#sub-book-money").html(sub_money);
});
</script>
<div class="bjui-pageFooter">
  <div class="pages">
    <span>共 {$totalCount} 条</span>
  </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>