<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/channel',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>统计日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker" value="{$endtime}">
    &nbsp;
    <input type="hidden" name="channel.id" value="{$channel_id}">
    <input type="text" name="channel.name" readonly value="{$channel_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel',array('ifadd'=>1));}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">
    <select class="required" name="work" data-toggle="selectpicker">
      <option value="1" <if condition="$work eq '1'">selected</if>>含工作票</option>
      <option value="2" <if condition="$work eq '2'">selected</if>>不含工作票</option>
      <option value="3" <if condition="$work eq '3'">selected</if>>仅含工作票</option>
    </select>
  	&nbsp;
    <select class="required" name="type" data-toggle="selectpicker">
      <option value="1" <if condition="$type eq '1'">selected</if>>明细</option>
      <option value="2" <if condition="$type eq '2'">selected</if>>汇总</option>
    </select>
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
        <a type="button" class="btn btn-primary" href="{:U('Report/Exprot/export_execl',$export_map);}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出报表信息吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent" id="w_channel_print">
<div class="visible-print-block">
    <h3 align="center">{$product_id|productName}渠道商销售(票型)统计(场次)报表</h3>
    <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
</div>
<?php //dump($data);?>
<if condition="$type eq '1'">
<table class="table table-bordered">
  <thead>
    <tr>
      <th rowspan="2" align="center" width="130px">销售计划</th>
      <th colspan="7" align="center">票型</th>
      <th colspan="4" align="center">小计</th>
      <th rowspan="2" align="center">备注</th>
    </tr>
    <tr>
      <th align="center" width="120px">名称</th>
      <th align="center" width="70px">票面单价</th>
      <th align="center" width="70px">结算单价</th>
      <th align="center" width="40px">数量</th>
      <th align="center" width="90px">票面金额</th>
      <th align="center" width="90px">结算金额</th>
      <th align="center" width="90px">差额</th>
      <th align="center" width="45px">数量</th>
      <th align="center" width="100px">票面金额</th>
      <th align="center" width="100px">结算金额</th>
      <th align="center" width="100px">差额</th>
    </tr>
    </thead>
    <tbody>
    
    <volist name="data" id="channel">
      <volist name="channel['plan']" id="price" key="l">
      <if condition="$l eq '1'">
      <tr>
        <td align="left" colspan="13">渠道商:{$price.channel_id|crmName}</td>
      </tr>
      </if>
        <volist name="price['price']" id="item" key="k">
          <if condition="$price['tic_num'] neq '1'">
            <if condition="$k eq '1'">
              <tr>
                <td rowspan="{$price['tic_num']}" align="center">{$price['plan']|planShow}</td>
                <td align="center">{$item.price_id|ticketName}</td>
                <td align="right">{$item.price}</td>
                <td align="right">{$item.discount}</td>
                <td align="center">{$item.number}</td>
                <td align="right">{$item.money|format_money}</td>
                <td align="right">{$item.moneys|format_money}</td>
                <td align="right">{$item.rebate|format_money}</td>
                <td rowspan="{$price['tic_num']}" align="center">{$price.number}</td>
                <td align="right" rowspan="{$price['tic_num']}">{$price.money|format_money}</td>
                <td align="right" rowspan="{$price['tic_num']}">{$price.moneys|format_money}</td>
                <td align="right" rowspan="{$price['tic_num']}">{$price.rebate|format_money}</td>
                <td rowspan="{$price['tic_num']}">&nbsp;</td>
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
            <tr>
              <td align="center">{$price['plan']|planShow}</td>
              <td align="center">{$item.price_id|ticketName}</td>
              <td align="right">{$item.price}</td>
              <td align="right">{$item.discount}</td>
              <td align="center">{$item.number}</td>
              <td align="right">{$item.money|format_money}</td>
              <td align="right">{$item.moneys|format_money}</td>
              <td align="right">{$item.rebate|format_money}</td>
              <td align="center">{$price.number}</td>
              <td align="right">{$price.money|format_money}</td>
              <td align="right">{$price.moneys|format_money}</td>
              <td align="right">{$price.rebate|format_money}</td>
              <td>&nbsp;</td>
            </tr>
          </if>
        </volist>
        <!--合计金额计算-->
        <?php $number += $price['number']; $money += $price['money']; $moneys += $price['moneys']; $rebate += $price['rebate'];?>
      </volist>
      <tr class="subtotal" data-num="{$number}" data-money="{$money}" data-moneys="{$moneys}" data-subsidy="{$rebate}">
          <td></td>
          <td align="center"></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td align="right" width="70px"><strong>合计:</strong></td>
          <td align="center" width="50px"><?php echo $number;?></td>
          <td width="100px" align="right"><?php echo format_money($money);?></td>
          <td width="100px" align="right"><?php echo format_money($moneys);?></td>
          <td width="100px" align="right"><?php echo format_money($rebate);?></td>
          <td rowspan="{$price_count}">&nbsp;</td>
        </tr>
        <?php $number = 0; $money = 0; $moneys = 0; $rebate = 0;?>
    </volist>
      <tr>
          <td></td>
          <td align="center"></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td align="right" width="70px"><strong>总计:</strong></td>
          <td align="center" width="50px" id="sub-channel-num">0</td>
          <td width="100px" id="sub-channel-money" align="right">0.00</td>
          <td width="100px" id="sub-channel-moneys" align="right">0.00</td>
          <td width="100px" id="sub-channel-subsidy" align="right">0.00</td>
          <td rowspan="{$price_count}">&nbsp;</td>
        </tr>
    </tbody>
  </table>
<else />
  <table class="table table-bordered">
  <thead>
    <tr>
      <th rowspan="2" align="center" width="130px">渠道商</th>
      <th colspan="7" align="center">票型</th>
      <th colspan="4" align="center">小计</th>
      <th rowspan="2" align="center">备注</th>
    </tr>
    <tr>
      <th align="center" width="120px">名称</th>
      <th align="center" width="70px">票面价</th>
      <th align="center" width="70px">结算价</th>
      <th align="center" width="40px">数量</th>
      <th align="center" width="90px">票面金额</th>
      <th align="center" width="90px">结算金额</th>
      <th align="center" width="90px">差额</th>
      <th align="center" width="45px">数量</th>
      <th align="center" width="100px">票面金额</th>
      <th align="center" width="100px">结算金额</th>
      <th align="center" width="100px">差额</th>
    </tr>
    </thead>
    <tbody>
    <?php //dump($data);?>
    <volist name="data" id="channel">
    <?php $price_count = count($channel['price']);?> 
      <volist name="channel['price']" id="item" key="k">
        <if condition="$price_count neq '1'">
          <if condition="$k eq '1'">
            <tr class="subtotal" data-num="{$channel.number}" data-money="{$channel.money}" data-moneys="{$channel.moneys}" data-subsidy="{$channel.rebate}">
              <td rowspan="{$price_count}" align="center">{$item.channel_id|crmName}</td>
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
            <td align="center">{$item.channel_id|crmName}</td>
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
          <td align="center" width="50px" id="sub-channel-num">0</td>
          <td width="100px" id="sub-channel-money" align="right">0.00</td>
          <td width="100px" id="sub-channel-moneys" align="right">0.00</td>
          <td width="100px" id="sub-channel-subsidy" align="right">0.00</td>
          <td rowspan="{$price_count}">&nbsp;</td>
        </tr>
    </tbody>
  </table>
</if>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_channel_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_num = 0,
      sub_money = 0,
      sub_moneys = 0,
      sub_subsidy = 0;
  $("#w_channel_print .subtotal").each(function(i) {
  	if($(this).data('num') != null){
      sub_num += parseInt($(this).data('num'));
      sub_money += parseFloat($(this).data('money'));
      sub_moneys += parseFloat($(this).data('moneys'));
      sub_subsidy += parseFloat($(this).data('subsidy'));
    }
  });
  sub_money = sub_money.toFixed(2);
  sub_moneys = sub_moneys.toFixed(2);
  sub_subsidy = sub_subsidy.toFixed(2);

  $("#sub-channel-num").html(sub_num);
  $("#sub-channel-money").html(sub_money);
  $("#sub-channel-moneys").html(sub_moneys);
  $("#sub-channel-subsidy").html(sub_subsidy);
});
</script>