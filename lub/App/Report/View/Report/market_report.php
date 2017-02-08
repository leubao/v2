<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Report/market_report',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>统计日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    <input type="hidden" name="user.id" value="{$user_id}">
    <input type="text" name="user.name" readonly value="{$user_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_user',array('type'=>1,'ifadd'=>1));}" data-group="user" data-width="600" data-height="445" data-title="客户经理" placeholder="客户经理">

    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-primary" href="{:U('Order/Index/export_order');}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出订单信息吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent" id="w_market_report_print">
<div class="visible-print-block">
    <h3 align="center">客户经理({$user_name})销售统计</h3>
    <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
  <table class="table table-bordered">
  <thead>
  <tr>
    <th align="center">编号</th>
    <th align="center">渠道商名称</th>
    <th align="center">小计(数量)</th>
    <th align="center">备注</th>
  </tr>
  </thead>
  <tbody>
  <tr>
  
    <td colspan="4"><strong>企业客户</strong></td>
  </tr>
  <volist name="data['channel']" id="vo">
  <tr>
    <td>{$i}</td>
    <td>{$vo.qd_id|crmName}</td>
    <td>{$vo.number}</td>
    <td></td>
  </tr>
  </volist>
  <tr>
    <td colspan="4"><strong>个人客户</strong></td>
  </tr>
  <volist name="data['guide']" id="vo">
  <tr>
    <td>{$i}</td>
    <td>{$vo.guide_id|userName}</td>
    <td>{$vo.number}</td>
    <td></td>
  </tr>
  </volist>
  <tr>
  <td></td>
  <td style="text-align: right;">合计:</td>
  <td>{$data['info']['number']}</td><td></td>
  </tr>
  </tbody>
</table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_market_report_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>