<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageHeader">

<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Order/Index/idcard_log',array('menuid'=>$menuid));}" method="post">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <select class="required" name="activity" data-toggle="selectpicker">
        <option value="">+=^^=请选择活动=^^=+</option>
        <volist name="activity" id="vo">
          <option value="{$vo.id}"  <if condition="$pinfo['activity'] eq $vo['id']">selected</if>>{$vo.title}
          </option>
        </volist>
    </select>
    <input type="text" value="{$pinfo.idcard}" name="idcard" class="form-control" data-rule="length[5~]" size="18" placeholder="身份证号">&nbsp;
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
      <th align="center">订单号</th>
      <th align="center">销售计划</th>
      <th align="center">身份证号</th>
      <th align="center">人数</th>
      <th align="center">活动名称</th>
      <th align="center">操作</th>
    </tr>
  </thead>
  <tbody>
    <volist name="data" id="vo">
          <tr>
            <td align="center">{$i}</td>
            <td align="center"><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn'],'menuid'=>$menuid))}"  data-id="orderinfo" data-width="900" data-height="600" data-title="订单详情">{$vo.order_sn}</a> </td>
            <td align="center">{$vo.plan_id|planShow}</td>
            <td align="center">{$vo.idcard} </td>
            <td align="center">{$vo.number} </td>
            <td align="center">{$vo.activity_id|getActivity}</td>
            <td align="center"><a href="{:U('Order/index/del_idcard',array('id'=>$vo['id'],'menuid'=>$menuid));}" data-toggle="doajax" data-confirm-msg="确定要删除这条记录吗?">删除</a></td>
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