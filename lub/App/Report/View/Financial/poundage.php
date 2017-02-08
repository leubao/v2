<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/poundage',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>统计日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
   
    <input type="hidden" name="channel.id" value="{$map['crm_id']}">
    <input type="text" name="channel.name" readonly value="{$channel_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel',array('ifadd'=>1));}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">
    &nbsp;
    <input type="text" value="{$sn}" name="sn" class="form-control" size="10" placeholder="单号">&nbsp;
  	<input type="radio" name="type" data-toggle="icheck" value="1" <if condition="$type eq '1'"> checked="checked"</if> data-label="明细&nbsp;">
    <input type="radio" name="type" data-toggle="icheck" value="2" <if condition="$type eq '2'"> checked="checked"</if> data-label="汇总">
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <!--
        <a type="button" class="btn btn-primary" href="{:U('Report/Exprot/export_execl',$export_map);}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出信息吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
        -->
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent" id="w_poundage_print">
<if condition="$type eq '1'">
<table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th align="center" width="150px">场次</th>
        <th align="center" width="120px">操作时间</th>
        <th align="center" width="80px">类型</th>
        <th align="center" width="115px">订单号</th>
        <th align="center">金额</th>
        <th align="center">操作员</th>
        <th align="center">渠道商</th>
        <th align="center">备注</th>
      </tr>
    </thead>
    <tbody id="poundage-list">
    <volist name="data" id="vo">
      <tr data-id="{$vo.order_sn}" data-money="{$vo.money}">
        <td>{$vo.plan_id|planShow}</td>
        <td>{$vo.createtime|datetime}</td>
        <td align="center">{$vo.type|poundage}</td>
        <td align="center"><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn']))}" data-width="900" data-height="600" data-title="订单详情">{$vo.order_sn}</a></td>
        <td align="right">{$vo.money}</td>
        <td align="center">{$vo.user_id|userName}</td>
        <td align="center">{$vo['channel_id']|crmName}</td>
        <td align="left">{$vo.remark}</td>
       </tr>
    </volist>
     <tr>
      <td></td>
      <td></td>
      <td></td>
     
      <td align="right">当前页合计:</td>
      <td id="sub-poundage-money" align="right">0.00</td>
      
      <td></td>
      <td></td>
      <td></td>
     </tr>
    </tbody>
  </table>
<else />
<div class="visible-print-block w900">
    <h3 align="center">{$map['product_id']|productName}手续费汇总报表</h3>
    <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
<table class="table table-bordered w900">
<thead>
  <tr>
    <th rowspan="2" align="center" width="170px">渠道商</th>
    <th colspan="1" align="center">类型(金额)</th>
    <th rowspan="2" align="center" width="100px">备注</th>
  </tr>
  <tr>
    <th align="center" width="100px">退票手续费</th>
  </tr>
  </thead>
  <tbody id="poundage-list">
  <volist name="data" id="vo">
      <tr class="subtotal" data-money="{$vo.money}">
        <td align="center">{$vo.channel_id|crmName}</td>
        <td align="right">{$vo.money|format_money}</td>
        <td>&nbsp;</td>
      </tr>
  </volist>
    <tr>
        <td align="right"><strong>合计:</strong></td>
        <td align="right" id="sub-poundage">0.00</td>
        <td>&nbsp;</td>
      </tr>
  </tbody>
</table>
</if>
</div>
<div class="bjui-pageFooter">
    <if condition="$type eq '1'">
    <div class="pages">
      <span>共 {$totalCount} 条</span>
    </div>
    <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
<else />
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_poundage_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>
</if>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_money = 0;
  <if condition="$type eq '1'">
  $('#poundage-list tr').each(function(i){
    if (!isNaN(parseFloat($(this).data('money')))) {
      sub_money += parseFloat($(this).data('money'));
    };
  });
  sub_money = sub_money.toFixed(2);
  $("#sub-poundage-money").html(sub_money);
  <else />
  $("#poundage-list .subtotal").each(function(i) {
      if(!isNaN(parseFloat($(this).data('money')))){
        sub_money += parseFloat($(this).data('money'));
      }
  });
  sub_money = sub_money.toFixed(2);
  $("#sub-poundage").html(sub_money);
  </if>
  
});
</script>