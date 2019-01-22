<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/top_up',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>统计日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
   <label>范围:</label>
      <select name="scope" data-toggle="selectpicker">
        <option value="1" <eq name="scope" value="1"> selected</eq>>全部</option>
        <option value="2" <eq name="scope" value="2"> selected</eq>>一级商户</option>
        <option value="3" <eq name="scope" value="3"> selected</eq>>二级商户</option>
        <option value="4" <eq name="scope" value="4"> selected</eq>>三级商户</option>
      </select>
    &nbsp;
    <input type="hidden" name="channel.id" value="{$channel_id}">
    <input type="text" name="channel.name" readonly value="{$channel_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel',array('ifadd'=>2));}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">
    &nbsp;
    <input type="text" value="{$map['order_sn']}" name="sn" class="form-control" size="10" placeholder="单号">&nbsp;
    <select class="required" name="types" data-toggle="selectpicker" id="top_up_types">
        <option value="">类型</option>
         <option value="1"  <if condition="$map['type'] eq '1'">selected</if>>充值</option>
         <option value="2"  <if condition="$map['type'] eq '2'">selected</if>>花费</option>
         <option value="3"  <if condition="$map['type'] eq '3'">selected</if>>补贴</option>
         <option value="4"  <if condition="$map['type'] eq '4'">selected</if>>退票</option>
         <option value="5"  <if condition="$map['type'] eq '5'">selected</if>>提现</option>
      </select>
  	&nbsp;
    
    <select class="required" name="type" data-toggle="selectpicker">
      <option value="1" <if condition="$type eq '1'">selected</if>>明细</option>
      <option value="2" <if condition="$type eq '2'">selected</if>>汇总</option>
    </select>
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-info" href="javascript:$.printBox('w_topup_print')"><i class="fa fa-print"> 打印报表</i></a>
        <a type="button" class="btn btn-primary" href="{:U('Report/Exprot/export_execl',$export_map);}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出信息吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent" id="w_topup_print">
<if condition="$type eq '1'">
<table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th align="center" width="120px">操作时间</th>
        <th align="center" width="80px">类型</th>
        <th align="center" width="115px">订单号</th>
        <th align="center">操作金额</th>
        <th align="center">余额</th>
        <th align="center">操作员</th>
        <th align="center">渠道商</th>
        <th align="center">备注</th>
      </tr>
    </thead>
    <tbody id="top-up-list">
    <volist name="data" id="vo">
      <tr data-id="{$vo.order_sn}" data-money="{$vo.cash}">
        
        <td>{$vo.createtime|datetime}</td>
        <td align="center">{$vo.type|operation}</td>
        <td align="center"><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn']))}" data-width="900" data-height="600" data-title="订单详情">{$vo.order_sn}</a></td>
        <td align="right">{$vo.cash}</td>
        <td align="right">{$vo.balance}</td>
        <td align="center">{$vo.user_id|userName=$vo['addsid']}</td>
        <td align="center">{$vo['crm_id']|crmName}</td>
        <td align="left">{$vo.remark}</td>
       </tr>
    </volist>
     <tr>
      
      <td></td>
      <td></td>
     
      <td align="right">当前页合计:</td>
      <td id="sub-top-up-money" align="right">0.00</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
     </tr>
    </tbody>
  </table>
<else />
<div class="visible-print-block w900">
    <h3 align="center">授信汇总报表</h3>
    <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
<table class="table table-bordered w900">
<thead>
  <tr>
    <th rowspan="2" align="center" width="170px">渠道商</th>
    <th colspan="5" align="center">类型(金额)</th>
    <th rowspan="2" align="center" width="100px">备注</th>
  </tr>
  <tr>
    <th align="center" width="100px">充值</th>
    <th align="center" width="100px">花费</th>
    <th align="center" width="100px">补贴</th>
    <th align="center" width="100px">退票</th>
    <th align="center" width="100px">提现</th>
  </tr>
  </thead>
  <tbody id="top-up-list">
  <volist name="data" id="vo">
      <tr class="subtotal" data-topup="{$vo.topup}" data-cost="{$vo.cost}" data-subsidies="{$vo.subsidies}" data-refund="{$vo.refund}" data-now="{$vo.now}">
        <td align="center">{$i|crmName}</td>
        <td align="right">{$vo.topup|format_money}</td>
        <td align="right">{$vo.cost|format_money}</td>
        <td align="right">{$vo.subsidies|format_money}</td>
        <td align="right">{$vo.refund|format_money}</td>
        <td align="right">{$vo.now|format_money}</td>
        <td>&nbsp;</td>
      </tr>
  </volist>
    <tr>
        <td align="right"><strong>合计:</strong></td>
        <td align="right" id="sub-top-up-topup">0.00</td>
        <td id="sub-top-up-cost" align="right">0.00</td>
        <td id="sub-top-up-subsidies" align="right">0.00</td>
        <td id="sub-top-up-refund" align="right">0.00</td>
        <td id="sub-top-up-now" align="right">0.00</td>
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
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_topup_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>
</if>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_money = 0,
      sub_topup = 0,
      sub_cost = 0,
      sub_subsidies = 0,
      sub_refund = 0,
      sub_now = 0;
  <if condition="$type eq '1'">
  $('#top-up-list tr').each(function(i){
    if (!isNaN(parseFloat($(this).data('money')))) {
      sub_money += parseFloat($(this).data('money'));
    };
   
  });
  sub_money = sub_money.toFixed(2);
  $("#sub-top-up-money").html(sub_money);
  <else />
  $("#w_topup_print .subtotal").each(function(i) {
      if(!isNaN(parseFloat($(this).data('topup')))){
        sub_topup += parseFloat($(this).data('topup'));
      }
      if(!isNaN(parseFloat($(this).data('cost')))){
        sub_cost += parseFloat($(this).data('cost'));
      }
      if(!isNaN(parseFloat($(this).data('subsidies')))){
        sub_subsidies += parseFloat($(this).data('subsidies'));
      }
      if(!isNaN(parseFloat($(this).data('refund')))){
        sub_refund += parseFloat($(this).data('refund'));
      }
      if(!isNaN(parseFloat($(this).data('now')))){
        sub_now += parseFloat($(this).data('now'));
      }
  });
  </if>
  sub_topup = sub_topup.toFixed(2);
  sub_cost = sub_cost.toFixed(2);
  sub_subsidies = sub_subsidies.toFixed(2);
  sub_refund = sub_refund.toFixed(2);
  sub_now = sub_now.toFixed(2);
  $("#sub-top-up-topup").html(sub_topup);
  $("#sub-top-up-cost").html(sub_cost);
  $("#sub-top-up-subsidies").html(sub_subsidies);
  $("#sub-top-up-refund").html(sub_refund);
  $("#sub-top-up-now").html(sub_now);
});
</script>