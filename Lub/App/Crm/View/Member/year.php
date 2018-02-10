<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Crm/Member/year',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}"> 
</form>
<!--Page end-->
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
</div>

<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%">
    <thead>
      <tr>
        <th width="100" align="center">编号</th>
        <th width="100" align="center">姓名</th>
        <th width="100" align="center">办理方式</th>
        <th width="100" align="center">年龄</th>
        <th width="60" align="center">入园数</th>
        <th width="60" align="center">状态</th>
        <th width="130" align="center">入园时间</th>
      </tr>
    </thead>
    <tbody>
      <volist id="vo" name="data">
      <tr data-id="{$vo['id']}">
        <td align="center"><a data-toggle="dialog" href="{:U('Crm/Member/public_member',array('id'=>$vo['id'],'menuid'=>$menuid))}"  data-id="orderinfo" data-width="900" data-height="600" data-title="年卡详情">{$vo.no-number}</a></td>
        <td align="center">{$vo['nickname']}</td>
        <td align="center"><if condition="$vo['source'] eq 5"> 自助办理 <else /> 窗口办理 </if></td>
        <td align="center">{$vo['idcard']|getAgeByID}</td>
        <td align="center">{$vo['number']}</td>
        <td align="center">{$vo['status']|status}</td>
        <td align="center">{$vo['update_time']|datetime}</td>
      </tr>
    </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages"> <span>共 {$totalCount} 条</span> </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>