<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Report/channel',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>统计日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    <input type="hidden" name="channel.id" value="{$channel_id}">
    <input type="text" name="channel.name" readonly value="{$channel_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel',array('ifadd'=>1));}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">

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

<div class="bjui-pageContent tableContent" id="w_channel_report_print">
<div class="visible-print-block">
    <h3 align="center">渠道商(区域)销售统计</h3>
    <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
  <table class="table table-bordered">
  <thead>
<tr>
    <th rowspan="3" align="center" width="50px">编号</th>
    <th rowspan="3" align="center" width="120px">渠道商名称</th>
    <th colspan="6" align="center">区域(数量/金额)</th>
    <th colspan="3" align="center">小计(数量/金额)</th>
  </tr>
  
 
  <tr>
    <volist name="area" id="ar">
    <th colspan="2" align="center">{$ar.name}</th>
    </volist>
    <th rowspan="2" align="center" width="70px">数量</th>
    <th rowspan="2" align="center" width="90px">票面金额</th>
    <th rowspan="2" align="center" width="90px">结算金额</th>
  </tr>
  <tr>
 <volist name="area" id="ar">
    <th align="center" width="70px">数量</th>
    <th align="center" width="70px">金额</th>
    </volist>
  </tr>
  </thead>
   <tbody>
  <volist name="data['channel']" id="vo" key="k">
  <tr>
    <td>{$k}</td>
    <td>{$vo.channel_id|crmName}</td>
    <for start="1" end="$area_num" comparison="elt">
    <td align="center">{$vo['area'][$area[$i-1]['id']]['number']}</td>
    <td align="right">{$vo['area'][$area[$i-1]['id']]['money']|format_money}</td>
    </for>

    <td align="center">{$vo.num}</td>
    <td align="right">{$vo.money|format_money}</td>
    <td align="right">{$vo.moneys|format_money}</td>
  </tr>
  </volist>
  <tr>
  <td></td>
  <td style="text-align: right;">合计:</td>
  <for start="1" end="$area_num" comparison="elt">
    <td>{$data['area'][$area[$i-1]['id']]['num']}</td>
    <td>{$data['area'][$area[$i-1]['id']]['money']}</td>
  </for>
  <td align="center">{$data['num']}</td><td align="right">{$data['money']|format_money}</td><td align="right">{$data['moneys']|format_money}</td>
  </tr>
  </tbody>
</table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_channel_report_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>