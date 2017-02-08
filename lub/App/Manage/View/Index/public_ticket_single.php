<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/Index/public_ticket_single');}" method="post">
        <input type="hidden" name="pageCurrent" value="{$currentPage}" />
        <input type="hidden" name="pageSize" value="{$numPerPage}" />
        <input type="hidden" name="navTabId" value="{$navTabId}" />
        <div class="bjui-searchBar">
            <label>名称：</label><input type="text" value="" name="name" size="10" />&nbsp;
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>&nbsp;
        </div>
    </form>
</div>
<div class="bjui-pageContent">
    <table data-toggle="tablefixed" data-width="100%">
        <thead>
            <tr>
                <th width="25">No.</th>
                <th>名称</th>
                <th>价格</th>
                <th width="74" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
            <volist name="data" id="vo">
                <tr>
                    <td>{$vo.id}</td>
                    <td>{$vo.name}</td>
                    <td>{$vo.price}</td>
                    <td align="center"><a href="javascript:;" data-toggle="lookupback" data-args="{id:'{$vo.id}', name:'{$vo.name}', price:'{$vo.price}'}" class="btn btn-blue" title="选择本项" data-icon="check">选择</a></td>
                </tr>               
            </volist>
        </tbody>
    </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages"> <span>共 {$totalCount} 条</span> </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>