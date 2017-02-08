<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Set/blacklist',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">       
  <!--条件检索 s-->
  <div class="bjui-searchBar">
    <label>手机号码:</label>
    <input type="text" value="{$map['phone']}" name="phone" class="form-control" size="20" placeholder="手机号码">&nbsp;
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
        <th width="50">编号</th>
        <th align="center">名称</th>
        <th align="center">电话</th>
        <th align="center">状态</th>
        <th align="center">添加时间</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$i}</td>
        <td align="center">{$vo.name}</td>
        <td align="center">{$vo.phone}</td>
        <td align="center">{$vo.status|status}</td>
        <td align="center">{$vo.createtime|date="Y-m-d H:i:s",###}</td>
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