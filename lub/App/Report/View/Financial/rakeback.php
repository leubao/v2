<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/rakeback',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>统计日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    <input type="hidden" name="user.id" value="{$guide_id}">
    <input type="text" name="user.name" readonly value="{$guide_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_user',array('type'=>1,'ifadd'=>1));}" data-group="user" data-width="600" data-height="445" data-title="导游" placeholder="导游">

    <input type="hidden" name="channel.id" value="{$channel}">
    <input type="text" name="channel.name" readonly value="{$channel_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel',array('ifadd'=>2));}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">

    &nbsp;
    <input type="hidden" name="plan.id" value="{$plan_id}">
    <input type="text" name="plan.name" readonly value="{$plan_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_date_plan',array('ifadd'=>1));}" data-group="plan" data-width="600" data-height="445" data-title="销售计划(场次)" placeholder="销售计划(场次)">
    <input type="text" value="" name="sn" class="form-control" size="10" placeholder="单号">&nbsp;
  	<input type="radio" name="type" data-toggle="icheck" value="1" <if condition="$type eq '1'"> checked="checked"</if> data-label="明细&nbsp;">
    <input type="radio" name="type" data-toggle="icheck" value="2" <if condition="$type eq '2'"> checked="checked"</if> data-label="汇总">
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-primary" href="{:U('Order/Index/export_execl');}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出订单信息吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent" id="w_rakeback_print">
<if condition="$type eq '1'">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th align="center" width="115px">订单号</th>
        <th align="center">所属计划</th>
        <th align="center">渠道商</th>
        <th align="center">导游</th>
        <th align="center">下单人</th>
        <th align="center">数量</th>
        <th align="center">金额</th>
        <th align="center">创建时间</th>
        <th align="center">状态</th>
        <th align="center">操作员</th>
        <th align="center">备注</th>
      </tr>
    </thead>
    <tbody id="rakeback-list">
    <volist name="data" id="vo">
      <tr data-id="{$vo.order_sn}" data-num="{$vo.number}" data-money="{$vo.money}">
        <td><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn']))}" data-width="900" data-height="600" data-title="订单详情">{$vo.order_sn}</a></td>
        <td>{$vo.plan_id|planShow}</td>
        <td>{$vo.qd_id|crmName}</td>
        <td align="center">{$vo.guide_id|userName}</td>
        <td align="center">{$vo.user_id|userName}</td>
        <td align="center">{$vo.number}</td>
        <td align="right">{$vo.money}</td>
        <td>{$vo.createtime|datetime}</td>
        <td align="center">{$vo['status']|rebate}</td>
        <td align="center"><eq name="vo.status" value="4"> {$vo.userid|userName}</eq></td>
        <td></td>
       </tr>
    </volist>
     <tr>
	    <td></td>
	    <td></td>
	    <td></td>
	    <td></td>
	    <td align="right">当前页合计:</td>
	    <td id="sub-rakeback-num" align="center">0</td>
	    <td id="sub-rakeback-money" align="right">0.00</td>
      <td></td>
      <td></td>
	    <td></td>
	    <td></td>
     </tr>
    </tbody>
  </table>
<else />
<div class="visible-print-block w900">
    <h3 align="center">{$product_id|productName}渠道商补贴汇总报表</h3>
    <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
  </div>
<table class="table table-bordered w900">
<thead>
  <tr>
    <th rowspan="2" align="center" width="170px">渠道商</th>
    <th colspan="3" align="center">(业务员)导游</th>
    <th colspan="2" align="center">小计</th>
    <th rowspan="2" align="center" width="90px">备注</th>
  </tr>
  <tr>
    <th align="center" width="90px">名称</th>
    <th align="center" width="40px">数量</th>
    <th align="center" width="90px">补贴金额</th>
    <th align="center" width="45px">数量</th>
    <th align="center" width="100px">补贴金额</th>
  </tr>
  </thead>
  <tbody>
  <volist name="data" id="channel">
  <?php $price_count = count($channel['guide']);?>
    <volist name="channel['guide']" id="item" key="k">
      <if condition="$price_count neq '1'">
        <if condition="$k eq '1'">
          <tr class="subtotal" data-num="{$channel.number}" data-money="{$channel.money}" >
            <td rowspan="{$price_count}" align="center">{$channel.channel_id|crmName}/{$channel.qd_id|crmName}</td>
            <td align="center">{$item.guide|userName}</td>
            <td align="center">{$item.number}</td>
            <td align="right">{$item.money|format_money}</td>
            <td rowspan="{$price_count}" align="center">{$channel.number}</td>
            <td align="right" rowspan="{$price_count}">{$channel.money|format_money}</td>
            <td rowspan="{$price_count}">&nbsp;</td>
          </tr>
          <else />
            <tr>
              <td align="center">{$item.guide|userName}</td>
              <td align="center">{$item.number}</td>
              <td align="right">{$item.money|format_money}</td>
            </tr>
          </if>
      <else />
        <tr class="subtotal" data-num="{$channel.number}" data-money="{$channel.money}">
          <td align="center">{$channel.channel_id|crmName}/{$channel.qd_id|crmName}</td>
          <td align="center">{$item.guide|userName}</td>
          <td align="center">{$item.number}</td>
          <td align="right">{$item.money|format_money}</td>
          <td align="center">{$channel.number}</td>
          <td align="right">{$channel.money|format_money}</td>
          <td>&nbsp;</td>
        </tr>
      </if>
    </volist>
  </volist>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td align="right" width="70px"><strong>合计:</strong></td>
        <td align="center" width="50px" id="sub-rakeback-num">0</td>
        <td width="100px" id="sub-rakeback-money" align="right">0.00</td>
        <td rowspan="{$price_count}">&nbsp;</td>
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
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_rakeback_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>
</if>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_num = 0,
      sub_money = 0,
      sub_moneys = 0,
      sub_subsidy = 0;
  <if condition="$type eq '1'">  
  $('#rakeback-list tr').each(function(i){
    if($(this).data('num') != null){
      sub_num += parseInt($(this).data('num'));
      sub_money += parseFloat($(this).data('money'));
      sub_moneys += parseFloat($(this).data('moneys'));
      sub_subsidy += parseFloat($(this).data('subsidy'));
    }
  });
  <else />
  $("#w_rakeback_print .subtotal").each(function(i) {
  	if($(this).data('num') != null){
      sub_num += parseInt($(this).data('num'));
      sub_money += parseFloat($(this).data('money'));
      sub_moneys += parseFloat($(this).data('moneys'));
      sub_subsidy += parseFloat($(this).data('subsidy'));
    }
  });
  </if>
  sub_money = sub_money.toFixed(2);
  sub_moneys = sub_moneys.toFixed(2);
  sub_subsidy = sub_subsidy.toFixed(2);

  $("#sub-rakeback-num").html(sub_num);
  $("#sub-rakeback-money").html(sub_money);
  $("#sub-rakeback-moneys").html(sub_moneys);
  $("#sub-rakeback-subsidy").html(sub_subsidy);
});
</script>