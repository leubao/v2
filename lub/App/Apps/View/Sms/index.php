<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Apps/Sms/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">       
  <input type="hidden" name="orderField" value="${param.orderField}">         
  <input type="hidden" name="orderDirection" value="${param.orderDirection}">
  <!--条件检索 s-->
  <div class="bjui-searchBar">
    <label>日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    
    <label>&nbsp;状态:</label>
    <select name="status" data-toggle="selectpicker">
        <option value="">全部</option>
        <option value="0" <if condition="$where['status'] eq '0'">selected</if>>发送中</option>
        <option value="DELIVRD" <if condition="$where['status'] eq 'DELIVRD'">selected</if>>成功</option>
        <option value="1" <if condition="$where['status'] eq '1'">selected</if>>发送失败</option>
    </select>
    
    <input type="text" value="{$where['order_sn']}" name="order_sn" class="form-control" size="10" placeholder="订单号">&nbsp;
    <input type="text" value="{$where['phone']}" name="phone" class="form-control" size="10" placeholder="目的号码">&nbsp;
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->


</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%">
    <thead>
      <tr>
        <th align="center" width="100">目的号码</th>
        <th align="center">发送内容</th>
        <th align="center" width="70">数量</th>
        <th align="center">状态</th>
        <th align="center" width="100">创建时间</th>
        <th align="center" width="100">更新时间</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td align="center">{$vo.phone}</td>
        <td>{$vo.content|urldecode}</td>
        <td align="center">{$vo.num}</td>
        <th align="center"><strong data-toggle="tooltip" data-placement="bottom" title="{$vo.remark}">{$vo.status}</strong></th>
        <td align="center">{$vo.createtime|datetime}</td>
        <td align="center">{$vo.updatetime|datetime}</td>
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