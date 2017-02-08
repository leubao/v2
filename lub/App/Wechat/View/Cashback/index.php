<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--工具条 s-->
<div class="toolBar">
  <div class="btn-group" role="group"> 
    <a type="button" class="btn btn-success" href="{:U('Wechat/Cashback/back',array('menuid'=>$menuid));}&id={#bjui-selected}" data-toggle="dialog" data-width="800" data-height="400" data-id="back550" data-mask="true"><i class="fa fa-legal"></i>  提现审核</a> 

    <a type="button" class="btn btn-info" href="{:U('Wechat/Cashback/subsidies',array('id'=>$ginfo['id']));}}" data-toggle="dialog" data-width="800" data-height="400" data-id="subsidies553" data-mask="true"><i class="fa fa-cny"></i>  现金支付</a>
  </div>
  <!--帮助 说明--> 
  <div class="btn-group f-right" role="group"> <a type="button" class="btn btn-default" data-placement="bottom" data-toggle="tooltip" onclick="$(this).navtab('refresh');" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
   <button type="button" class="btn btn-default"><i class="fa fa-question-circle"></i></button>
  </div>
</div>
<!--工具条 e--> 
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Wechat/Cashback/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    
    <input type="hidden" name="user.id" value="">
    <input type="text" name="user.name" readonly value="" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_user',array('type'=>5,'ifadd'=>2));}" data-group="user" data-width="600" data-height="445" data-title="申请人" placeholder="申请人">
    <input type="text" value="" name="sn" class="form-control" size="10" placeholder="单号">&nbsp;
    <button type="button" class="showMoreSearch" data-toggle="moresearch" data-name="custom"><i class="fa fa-angle-double-down"></i></button>
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
        <th align="center" width="115px">订单号</th>
        <th align="center" width="90px">申请人</th>
        <th align="center" width="80px">金额</th>
        <th align="center" width="120px">创建时间</th>
        <th align="center" width="70px">审核员</th>
        <th align="center" width="90px">备注</th>
        <th align="center" width="60px">状态</th>
      </tr>
    </thead>
    <tbody id="cash-list">
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}" data-money="{$vo.money}">
        <td align="center"><a data-toggle="dialog" href="{:U('Wechat/Cashback/public_cashinfo',array('sn'=>$vo['sn']))}"  data-id="orderinfo" data-width="900" data-height="600" data-title="订单详情">{$vo.sn}</a></td>
        <td align="center">{$vo['user_id']|userName}</td>
        <td align="right">{$vo.money}</td>
        <td align="center">{$vo.createtime|date="Y-m-d H:i:s",###}</td>
        <td align="center">{$vo['userid']|userName}</td>
        <td align="center">{$vo['remark']}</td>
        <td align="center">{$vo['status']|status}</td>
       </tr>
    </volist>
     <tr>
     <td></td>
     <td align="right">当前页合计:</td>
     <td id="sub-back-money" align="right">0.00</td>
     <td></td><td></td><td></td><td></td></tr>
    </tbody>
  </table>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_back_money = 0;
  $('#cash-list tr').each(function(i){
    if($(this).data('money') != null){
      sub_back_money += parseFloat($(this).data('money'));
    }
  });
  sub_back_money = sub_back_money.toFixed(2);
  $("#sub-back-money").html(sub_back_money);
});
</script>
<div class="bjui-pageFooter">
  <div class="pages">
    <span>共 {$totalCount} 条</span>
  </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>