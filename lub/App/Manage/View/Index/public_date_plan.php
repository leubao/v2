<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/Index/public_date_plan')}" method="post">
        <input type="hidden" name="pageCurrent" value="{$currentPage}" />
        <input type="hidden" name="pageSize" value="{$numPerPage}" />
        <div class="bjui-searchBar">
            <label>日期：</label><input type="text" size="11" name="datetime" data-toggle="datepicker" value="{$datetime}">&nbsp;
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>&nbsp;
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
    <table class="table table-bordered" data-toggle="tablefixed" data-width="100%">
        <thead>
            <tr>
                <th>销售计划</th>
                <th width="74" align="center">操作</th>
            </tr>
        </thead>
        <tbody>
            <volist name="plan" id="vo">
                <tr>
                    <td>{$vo.id|planShow}</td>
                    <td align="center"><a href="javascript:;" data-toggle="lookupback" data-args="{id:'{$vo.id}', name:'{$vo.id|planShow}'}" class="btn btn-blue" title="选择本项" data-icon="check">选择</a></td>
                </tr>               
            </volist>
        </tbody>
    </table>
    </div>