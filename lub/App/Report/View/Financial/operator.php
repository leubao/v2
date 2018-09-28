<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/operator',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>统计日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$map['starttime']}" data-rule="required">
    &nbsp;
    <input type="hidden" name="plan.id" value="{$map['plan_id']}">
    <input type="text" name="plan.name" readonly value="{$map['plan_name']}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_date_plan',array('ifadd'=>1));}" data-group="plan" data-width="600" data-height="445" data-title="销售计划(场次)" placeholder="销售计划(场次)">
    &nbsp;
  	<select class="required" name="user" data-toggle="selectpicker" data-rule="required">
        <option value="">售票员</option>
        <volist name="user" id="vo">
          <option value="{$vo.id}"  <if condition="$vo['id'] eq $map['user']">selected</if>>{$vo.nickname}</option>
        </volist>
        <option value="2"  <if condition="$map['user'] eq '2'">selected</if>>微信售票</option>
      </select>
    &nbsp;
    <select class="required" name="work" data-toggle="selectpicker">
      <option value="1" <if condition="$work eq '1'">selected</if>>含工作票</option>
      <option value="2" <if condition="$work eq '2'">selected</if>>不含工作票</option>
      <option value="3" <if condition="$work eq '3'">selected</if>>仅含工作票</option>
    </select>
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

<div class="bjui-pageContent tableContent" id="w_operator_print" style="padding: 20px;">
<div class="visible-print-block">
    <h3 align="center">{$product_id|productName}售票员日报表</h3>
    <span class="pull-left mb10">统计日期：{$starttime} &nbsp;&nbsp;售票员：{$map['user']|userName}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
<table class="table table-bordered">
<thead>
  <tr>
    <th rowspan="2" align="center" width="160px">销售计划</th>
    <th colspan="7" align="center">票型</th>
    <th colspan="4" align="center">小计</th>
    <th rowspan="2" align="center" width="100px">备注</th>
  </tr>
  <tr>
    <th align="center" width="120px">名称</th>
    <th align="center" width="70px">票面单价</th>
    <th align="center" width="70px">结算单价</th>
    <th align="center" width="40px">数量</th>
    <th align="center" width="90px">票面金额</th>
    <th align="center" width="90px">结算金额</th>
    <th align="center" width="90px">补贴金额</th>
    <th align="center" width="45px">数量</th>
    <th align="center" width="100px">票面金额</th>
    <th align="center" width="100px">结算金额</th>
    <th align="center" width="100px">差额</th>
  </tr>
  </thead>
  <tbody>
  <volist name="data" id="channel" empty="$empty">
  <?php $price_count = count($channel['price']);?>
    <volist name="channel['price']" id="item" key="k">
      <if condition="$price_count neq '1'">
        <if condition="$k eq '1'">
          <tr class="subtotal" data-num="{$channel.number}" data-money="{$channel.money}" data-moneys="{$channel.moneys}" data-subsidy="{$channel.rebate}">
            <td rowspan="{$price_count}" align="center">{$channel.plan|planShow}</td>
            <td align="center">{$item.price_id|ticketName}</td>
            <td align="right">{$item.price}</td>
            <td align="right">{$item.discount}</td>
            <td align="center">{$item.number}</td>
            <td align="right">{$item.money|format_money}</td>
            <td align="right">{$item.moneys|format_money}</td>
            <td align="right">{$item.rebate|format_money}</td>
            <td rowspan="{$price_count}" align="center">{$channel.number}</td>
            <td align="right" rowspan="{$price_count}">{$channel.money|format_money}</td>
            <td align="right" rowspan="{$price_count}">{$channel.moneys|format_money}</td>
            <td align="right" rowspan="{$price_count}">{$channel.rebate|format_money}</td>
            <td rowspan="{$price_count}">&nbsp;</td>
          </tr>
          <else />
            <tr>
              <td align="center">{$item.price_id|ticketName}</td>
              <td align="right">{$item.price}</td>
              <td align="right">{$item.discount}</td>
              <td align="center">{$item.number}</td>
              <td align="right">{$item.money|format_money}</td>
              <td align="right">{$item.moneys|format_money}</td>
              <td align="right">{$item.rebate|format_money}</td>
            </tr>
          </if>
      <else />
        <tr  class="subtotal" data-num="{$channel.number}" data-money="{$channel.money}" data-moneys="{$channel.moneys}" data-subsidy="{$channel.rebate}">
          <td align="center">{$channel.plan|planShow}</td>
          <td align="center">{$item.price_id|ticketName}</td>
          <td align="right">{$item.price}</td>
          <td align="right">{$item.discount}</td>
          <td align="center">{$item.number}</td>
          <td align="right">{$item.money|format_money}</td>
          <td align="right">{$item.moneys|format_money}</td>
          <td align="right">{$item.rebate|format_money}</td>
          <td align="center">{$channel.number}</td>
          <td align="right">{$channel.money|format_money}</td>
          <td align="right">{$channel.moneys|format_money}</td>
          <td align="right">{$channel.rebate|format_money}</td>
          <td>&nbsp;</td>
        </tr>
      </if>
    </volist>
  </volist>
    <tr>
        <td></td>
        <td align="center"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right" width="70px"><strong>合计:</strong></td>
        <td align="center" width="50px" id="sub-operator-num">0</td>
        <td width="100px" id="sub-operator-money" align="right">0.00</td>
        <td width="100px" id="sub-operator-moneys" align="right">0.00</td>
        <td width="100px" id="sub-operator-subsidy" align="right">0.00</td>
        <td rowspan="{$price_count}">&nbsp;</td>
      </tr>
  </tbody>
</table>
<?php if(!empty($member_seale)){ ?>
  <table class="table table-bordered">
  <caption><h3 align="center">{$product_id|productName}售票员会员卡销售汇总表</h3></caption>
  <thead>
    <tr>
      <th align="center" width="80px">日期</th>
      <th align="center" width="80px">卡型</th>
      <th align="center" width="80px">单价</th>
      <th align="center" width="80px">数量</th>
      <th align="center" width="80px">金额</th>
      <th align="center" width="80px">备注</th>
    </tr>
  </thead>
  <tbody>
    <volist name="member_seale" id="mem">
    <tr>
      <td align="center">{$starttime}</td>
      <td align="center">{$mem.title}</td>
      <td align="right">{$mem.price|format_money}</td>
      <td align="center">{$mem.number}</td>
      <td align="right">{$mem.money|format_money}</td>
      <td>&nbsp;</td>
    </tr>
    </volist>
    <tr>
      <td></td><td></td>
      <td align="right"><strong>合计:</strong></td>
      <td align="center">{$member_sum.number}</td>
      <td align="right">{$member_sum.money|format_money}</td>
      <td>&nbsp;</td>
    </tr>
  </tbody>
</table>
<?php } ?>
<table class="table table-bordered">
  <caption><h3 align="center">{$product_id|productName}售票员资金一览表</h3></caption>
  <thead>
    <tr>
      <th rowspan="2" align="center" width="160px">销售计划</th>
      <th colspan="5" align="center">门票销售</th>
      <th colspan="5" align="center">政企销售</th>
      <th rowspan="2" align="center" width="100px">备注</th>
    </tr>
    <tr>
      <th align="center" width="80px">现金</th>
      <th align="center" width="80px">签单</th>
      <th align="center" width="80px">POS机划卡</th>
      <th align="center" width="80px">支付宝</th>
      <th align="center" width="80px">微信支付</th>
      <th align="center" width="80px">现金</th>
      <th align="center" width="80px">签单</th>
      <th align="center" width="80px">POS机划卡</th>
      <th align="center" width="80px">支付宝</th>
      <th align="center" width="80px">微信支付</th>
    </tr>
  </thead>
  <tbody>
    <volist name="conductor['money']" id="co">
    <tr>
      <td align="center">{$co.plan|planShow}</td>
      <td align="right">{$co.data.cash|format_money}</td>
      <td align="right">{$co.data.sign|format_money}</td>
      <td align="right">{$co.data.stamp|format_money}</td>
      <td align="right">{$co.data.alipay|format_money}</td>
      <td align="right">{$co.data.wxpay|format_money}</td>
      
      <td align="right">{$co.data.dcash|format_money}</td>
      <td align="right">{$co.data.dsign|format_money}</td>
      <td align="right">{$co.data.dstamp|format_money}</td>
      <td align="right">{$co.data.dalipay|format_money}</td>
      <td align="right">{$co.data.dwxpay|format_money}</td>
      
      <td>&nbsp;</td>
    </tr>
    </volist>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td rowspan="5" align="center"><strong>合计:</strong></td>
      <td align="right"><strong>现金:</strong></td>
      <td align="right">{$conductor.sum.cash|format_money}</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
     
      <td align="right"><strong>签单:</strong></td>
      <td align="right">{$conductor.sum.sign|format_money}</td>
      <td>&nbsp;</td>
    </tr>

    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    
      <td align="right"><strong>POS机划卡:</strong></td>
      <td align="right">{$conductor.sum.stamp|format_money}</td>
      <td>&nbsp;</td>
    </tr>

    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td align="right"><strong>支付宝:</strong></td>
      <td align="right">{$conductor.sum.alipay|format_money}</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td align="right"><strong>微信支付:</strong></td>
      <td align="right">{$conductor.sum.wxpay|format_money}</td>
      <td></td>
    </tr>
    <tr>
      <td colspan="12"><strong>说明:</strong>因微信支付、支付宝支付无凭证，收入金额以资金来源表为准，不计入售票员报表。</td>
    </tr>
  </tbody>
</table>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_operator_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_num = 0,
      sub_money = 0,
      sub_moneys = 0,
      sub_subsidy = 0;
  <if condition="$type eq '1'">  
  $('#report-list tr').each(function(i){
    if($(this).data('num') != null){
      sub_num += parseInt($(this).data('num'));
      sub_money += parseFloat($(this).data('money'));
      sub_moneys += parseFloat($(this).data('moneys'));
      sub_subsidy += parseFloat($(this).data('subsidy'));
    }
  });
  <else />
  $("#w_operator_print .subtotal").each(function(i) {
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

  $("#sub-operator-num").html(sub_num);
  $("#sub-operator-money").html('￥'+sub_money);
  $("#sub-operator-moneys").html('￥'+sub_moneys);
  $("#sub-operator-subsidy").html('￥'+sub_subsidy);
});
</script>