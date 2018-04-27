<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/ticket_type',array('menuid'=>$menuid));}" method="post">
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
    <input type="text" name="channel.name" readonly value="{$channel_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel',array('ifadd'=>2));}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">

    &nbsp;
    <input type="hidden" name="ticket.id" value="{$ticket_id}">
    <input type="text" name="ticket.name" readonly value="{$ticket_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称">
    &nbsp;
    <select class="required" name="is_check" data-toggle="selectpicker">
      <option value="1" <if condition="$is_check eq '1'">selected</if>>结算价核算</option>
      <option value="2" <if condition="$is_check eq '2'">selected</if>>净收入核算</option>
    </select>
    &nbsp;
  	<button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-primary" href="{:U('Report/Exprot/export_execl',$export_map);}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出订单信息吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent w900" id="w_ticket_type_print">
<div class="visible-print-block">
    <h3 align="center">{$product_id|productName}票型销售统计报表</h3>
    <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
<table class="table table-bordered w900">
<thead>
  <tr>
    <th align="center" width="40px">编号</th>
    <th align="center" width="70px">票型名称</th>
    <th align="center" width="70px">票面单价</th>
    <th align="center" width="70px">结算单价</th>
    <th align="center" width="40px">数量</th>
    <th align="center" width="90px">票面金额</th>
    <th align="center" width="90px">结算金额</th>
    <th align="center" width="90px">差额</th>
    <th width="90px" align="center">备注</th>
  </thead>
  <tbody>
  <volist name="data['price']" id="vo">
  <tr>
    <td align="center">{$i}</td>
    <td align="center">{$vo.name}</td>
    <td align="right">{$vo.price}</td>
    <td align="right">{$vo.discount}</td>
    <td align="center">{$vo['number']}</td>
    <td align="right">{$vo.money|format_money}</td>
    <td align="right">{$vo.moneys|format_money}</td>
    <td align="right">{$vo.rebate|format_money}</td>
    <td></td>
  </tr>
  </volist>
  <tr>
    <td align="center"></td>
    <td align="center"></td>
    <td align="right"></td>
    <td align="right">合计:</td>
    <td align="center">{$data['info']['number']}</td>
    <td align="right">{$data['info'].money|format_money}</td>
    <td align="right">{$data['info'].moneys|format_money}</td>
    <td align="right">{$data['info'].rebate|format_money}</td>
    <td></td>
  </tr>
  <tr>
    <td align="center"></td>
    <td align="center"></td>
    <td align="right"></td>
    <td align="right">累计场次:</td>
    <td align="center">{$data['info']['games']}</td>
    <td align="right"></td>
    <td align="right"></td>
    <td align="right"></td>
    <td></td>
  </tr>
  </tbody>
</table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_ticket_type_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>