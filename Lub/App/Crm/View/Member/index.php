<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Crm/Member/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <Managetemplate file="Common/Nav"/>
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    <select name="group" data-toggle="selectpicker">
        <option value="">全部</option>
        <volist name="group" id="vo">
        <option value="{$vo.id}" <if condition="$vo.id eq $group_id">selected</if>>{$vo.title}</option>
        </volist>
    </select>
    &nbsp;
    <input type="text" value="{$phone}" name="phone" data-rule="length[11~]" placeholder="手机号" size="11">
    &nbsp;
    <input type="text" value="{$card}" name="card" data-rule="length[15~]" placeholder="身份证号" size="15">
    
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-primary" href="{:U('Order/Index/public_export_order',$export_map);}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出订单信息吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%">
    <thead>
      <tr>
        <th width="120" align="center">编号</th>
        <th width="80" align="center">姓名</th>
        <th width="100" align="center">办理方式</th>
        <th width="60" align="center">年龄</th>
        <th width="100" align="center">类型</th>
        <th width="120" align="center">入园数</th>
        <th width="60" align="center">状态</th>
        <th width="130" align="center">添加时间</th>
        <th width="130" align="center">过期时间</th>
        <th width="130" align="center">操作</th>
      </tr>
    </thead>
    <tbody>
      <volist id="vo" name="data">
      <tr data-id="{$vo['id']}">
        <td align="center"><a data-toggle="dialog" href="{:U('Crm/Member/public_member',array('id'=>$vo['id'],'menuid'=>$menuid))}"  data-id="memberinfo" data-width="900" data-height="600" data-title="年卡详情">{$vo.no_number}</a></td>
        <td align="center">{$vo['nickname']}</td>
        <td align="center"><if condition="$vo['source'] eq 5"> 自助办理 <else /> 窗口办理 </if></td>
        <td align="center">{$vo['idcard']|getAgeByID}</td>
        <td align="center">{$vo['group_id']|memGroup}</td>
        <td align="center">{$vo['number']}</td>
        <td align="center">{$vo['status']|status}</td>
        <td align="center">{$vo['create_time']|datetime} {$vo['update_time']|datetime}</td>
        <td align="center">{$vo['endtime']|datetime}</td>
        <td align="center"><a href="{:U('Crm/Member/del_member',array('id'=>$vo['id'],'menuid'=>$menuid))}" data-toggle="doajax" data-confirm-msg="确定要删除这条记录吗?">删除</a></td>
      </tr>
    </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages"> <span>共 {$totalCount} 条</span> </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>