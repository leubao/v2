<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Work/year_ticket',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <input type="text" value="" name="phone" size="11" placeholder="手机号">
    <input type="text" value="" name="card" placeholder="身份证号" class="form-control" data-rule="length[5~]" size="18" >&nbsp;
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
	<table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
      	<th align="center" width="65px">编号</th>
        <th align="center" width="65px">姓名</th>
        <th align="center" width="90px">手机号</th>
        <th align="center" width="90px">身份证号</th>
        <th align="center" width="80px">入园次数</th>
        <th align="center">上次入园时间</th>
        <th align="center" width="60px">状态</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr>
      	<td align="center"><a data-toggle="dialog" href="{:U('Crm/Member/public_member',array('id'=>$vo['id'],'menuid'=>$menuid))}"  data-id="orderinfo" data-width="900" data-height="600" data-title="年卡详情">{$vo.no-number}</a></td>
        <td align="center">{$vo.nickname}</td>
        <td align="center">{$vo['phone']}</td>
        <td align="center">{$vo['idcard']}</td>
        <td align="center">{$vo.number}</td>
        <td align="center">{$vo.update_time|date="Y-m-d H:i:s",###}</td>
        <td align="center">{$vo['status']|status}</td>
       </tr>
    </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
    </ul>
</div>