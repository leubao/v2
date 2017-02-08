<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
  <form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Report/region',array('menuid'=>$menuid));}" method="post">
    <input type="hidden" name="pageCurrent" value="{$currentPage}" />
    <input type="hidden" name="pageSize" value="{$numPerPage}" />
    <!--条件检索 s--> 
    <div class="bjui-searchBar">
      <label>统计日期:</label>
      <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
      <label>至</label>
      <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
      &nbsp;
      

      <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
      <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
      <div class="pull-right">
          <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
      </div>
    </div>
    <!--检索条件 e-->
  </form>
</div>


<div class="bjui-pageContent tableContent" id="w_region_print">
  <table  class="table table-bordered w900" >
    <thead>
      <tr>
        <th width="30">编号</th>
        <th width="180">地区</th>
        <th width="60">人数</th>
      </tr>
    </thead>
    <tbody>
      <volist name="data" id="vo">
        <tr>
          <td>{$i}</td>
          <td>{$vo['region']|region}</td>
          <td>{$vo['number']}</td> 
        </tr>
      </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_region_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>