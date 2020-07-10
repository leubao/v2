<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Set/control_block',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
</form>
<!--Page end-->
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th width="50">序号</th>
        <th align="center">所属模板</th>
        <th align="center">名称</th>
        <th align="center">类型</th>
        <th align="center">数量</th>
        <th align="center">状态码</th>
        <th align="center">备注</th>
        <th align="center">创建时间</th>
        <th align="center">状态</th>
        <th align="center">操作</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$vo.sort}</td>
        <td align="center">{$vo.template_id|templateName}</td>
        <td align="center">{$vo.name}</td>
        <td align="center">{$vo.type|controlType}</td>
        <td align="center">{$vo.num}</td>
        <td align="center">{$vo.state}</td>
        <td align="center">{$vo.remark}</td>
        <td align="center">{$vo.createtime|datetime}</td>
        <td align="center">{$vo.status|status}</td>
        <td align="center">
          <a type="button" class="btn btn-default" data-toggle="dialog" data-max="true" href="{:U('Item/Set/set_block',array("id"=>$vo[id],'type'=>$vo['type'],'menuid'=>$menuid))}"><i class="fa fa-th"></i> 设置座位</a>
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