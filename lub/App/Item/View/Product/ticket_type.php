<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Product/type',array('menuid'=>$menuid,'gid'=>$gid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <input type="hidden" name="gid" value="{$gid}">
</form>
<!--Page end-->
<div class="bjui-pageHeader">
<div class="toolBar">
  <div class="btn-group" role="group">
   <a type="button" class="btn btn-success" href="{:U('Item/Product/typeadd',array('menuid'=>$menuid,'gid'=>$gid));}" data-toggle="dialog" data-width="800" data-height="400" data-id="新增" data-mask="true"><i class="fa fa-plus"></i> 新增</a>
    <a type="button" class="btn btn-info" href="{:U('Item/Product/typeedit',array('menuid'=>$menuid,'gid'=>$gid));}&id={#bjui-selected}" data-toggle="dialog" data-width="800" data-height="400" data-id="编辑"><i class="fa fa-pencil"></i> 编辑</a>
    <a type="button" class="btn btn-danger" href="{:U('Item/Product/typedel',array('menuid'=>$menuid,'gid'=>$gid));}&id={#bjui-selected}" data-toggle="doajax" data-confirm-msg="确定要删除吗？" data-id="票型删除" ><i class="fa fa-trash-o"></i> 删除</a>

 </div>
  <!--帮助 说明-->
  <div class="btn-group f-right" role="group"> <a type="button" class="btn btn-default" data-placement="bottom" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
    <a type="button" class="btn btn-default" href="http://www.leubao.com/index.php?g=Manual&a=show&sid=38" target="_blank" data-placement="left" data-toggle="tooltip" title="使用帮助"><i class="fa fa-question-circle"></i></a>
  </div>
</div>
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th width="50">序号</th>
        <th align="center">编号</th>
        <th align="center">票型名称</th>
        <th align="center">所属分组</th>
        <th align="center">票型价格</th>
        <th align="center">结算价格</th>
        <th align="center">补贴金额</th>
        <th align="center">销售场景</th>
        <th align="center">状态</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td>{$i}</td>
        <td align="center">{$vo.id}</td>
        <td align="center">{$vo.name}</td>
        <td align="center">{$vo.group_id|groupName}</td>
        <td align="center">{$vo.price} </td>
        <td align="center">{$vo.discount} </td>
        <td align="center">{$vo.rebate|format_money} </td>
        <td align="center">{$vo.scene|addsid} </td>
        <td align="center">{$vo.status|status}</td>
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