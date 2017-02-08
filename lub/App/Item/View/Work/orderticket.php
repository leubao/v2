<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Work/orderticket',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageCurrent" value="{$currentPage}" />
  <input type="hidden" name="pageSize" value="{$numPerPage}" />
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>订单号:</label>
    <input type="text" value="{$map['sn']}" name="sn" class="form-control" size="10" placeholder="单号">&nbsp;
    <label>手机号:</label>
    <input type="text" value="{$map['phone']}" name="phone" class="form-control" size="10" placeholder="手机号">&nbsp;
    <input type="hidden" name="plan.id" value="{$plan}">
    <input type="text" name="plan.name" readonly value="{$planname}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_date_plan');}" data-group="plan" data-width="600" data-height="445" data-title="销售计划(场次)" placeholder="销售计划(场次)">
    
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
        
        <th align="center">数量</th>
        
        <th align="center">渠道商</th>
        <th align="center">订单(场景)类型</th>
        <th align="center">创建时间</th>
        <th align="center">状态</th>
        <th align="center">操作</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.order_sn}">
        <td align="center"><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn']))}"  data-id="orderinfo" data-width="900" data-height="600" data-title="订单详情">{$vo.order_sn}</a></td>
        <td align="center">{$vo.plan_id|planShow}</td>
        <td align="center">{$vo.number}</td>
        
        <td align="center">{$vo.channel_id|crmName}</td>
        <td align="center">{$vo.addsid|addsid}（{$vo.type|channel_type}）</td>
        <td align="center">{$vo.createtime|date="Y-m-d H:i:s",###}</td>
        <td align="center">{$vo['status']|status}</td>
        <td align="center">
                    <a data-toggle="dialog" data-id="print" data-width="213" data-height="208" data-title="门票打印" href="{:U('Item/Order/drawer',array('sn'=>$vo['order_sn'],'plan_id'=>$vo['plan_id']))}" data-resizable="false" data-maxable="false" data-mask="true">出票</a>
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