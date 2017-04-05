<?php if (!defined('LUB_VERSION')) exit(); ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Managetemplate file="Home/Public/cssjs"/>
</head>
<body>
<div class="container">
  <Managetemplate file="Home/Public/menu"/>
  <!--内容主体区域 start-->
  <div class="main row">
    <div class="col-lg-12">
      <div class="panel panel-default"> 
        <!-- Default panel contents -->
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> 销售统计 &nbsp;&nbsp;<a href="{:U('Home/Report/today_sales');}"><span class="glyphicon glyphicon-share-alt"></span>销售日报</a>
        <div class="btn-group btn-group-xs" style="float:right;"> 
            
                <a href="{:U('Home/Exprot/export_execl',$export_map);}" class="btn btn-success"><span class="glyphicon glyphicon-open-file"></span>导出Execl</a>
        <a href="javascript:$.printBox('w_channel_print')"class="btn btn-info"><span class="glyphicon glyphicon-print"></span>报表打印</a>
        </div>
        </div>

        <div class="panel-body">
          <form class="form-inline" role="form" action="{:U('Home/Report/sales');}" method="post">
            <label class="radio-inline">
              <input type="radio" name="sum_det" value="2" <if condition="$sum_det eq 2">checked</if>>
              汇总 </label>
            <label class="radio-inline">
              <input type="radio" name="sum_det" value="1" <if condition="$sum_det eq 1">checked</if>>
              明细 </label>
            <div class="form-group">
              <input size="16" type="text" value="{$starttime}" readonly class="form-control form_date" name="start_time">
            </div>
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-addon">至</div>
                <input size="16" type="text" value="{$endtime}" readonly class="form-control form_date" name="end_time">
              </div>
            </div>
            <button type="submit" class="btn btn-default">查询</button>
          </form>
        </div>

        <div id="w_channel_print">
          <div class="visible-print-block">
            <h3 align="center">渠道商销售(票型)统计(场次)报表</h3>
            <span class="pull-left mb10">统计日期：{$starttime} 至 {$endtime}</span>
            <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
          </div>
        <if condition="$sum_det eq '1'">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th rowspan="2" align="center" width="140px">销售计划</th>
                <th colspan="6" align="center">票型</th>
                <th colspan="3" align="center">小计</th>
                <th rowspan="2" align="center">备注</th>
              </tr>
              <tr>
                <th align="center" width="120px">名称</th>
                <th align="center" width="70px">票面单价</th>
                <th align="center" width="70px">结算单价</th>
                <th align="center" width="50px">数量</th>
                <th align="center" width="90px">票面金额</th>
                <th align="center" width="90px">结算金额</th>
                <th align="center" width="45px">数量</th>
                <th align="center" width="100px">票面金额</th>
                <th align="center" width="100px">结算金额</th>
              </tr>
            </thead>
            <tbody>
            <volist name="data" id="channel">
              <volist name="channel['plan']" id="price" key="l">
              <if condition="$l eq '1'">
              <tr>
                <td align="left" colspan="11">渠道商:{$price.channel_id|crmName}</td>
              </tr>
              </if>
                <volist name="price['price']" id="item" key="k">
                  <if condition="$price['tic_num'] neq '1'">
                    <if condition="$k eq '1'">
                      <tr>
                        <td rowspan="{$price['tic_num']}" align="center">{$price['plan']|planShow}</td>
                        <td align="center">{$item.priceid|ticketName}</td>
                        <td align="right">{$item.price}</td>
                        <td align="right">{$item.discount}</td>
                        <td align="center">{$item.number}</td>
                        <td align="right">{$item.money|format_money}</td>
                        <td align="right">{$item.moneys|format_money}</td>
                        <td rowspan="{$price['tic_num']}" align="center">{$price.number}</td>
                        <td align="right" rowspan="{$price['tic_num']}">{$price.money|format_money}</td>
                        <td align="right" rowspan="{$price['tic_num']}">{$price.moneys|format_money}</td>
                        <td rowspan="{$price['tic_num']}">&nbsp;</td>
                      </tr>
                      <else />
                        <tr>
                          <td align="center">{$item.priceid|ticketName}</td>
                          <td align="right">{$item.price}</td>
                          <td align="right">{$item.discount}</td>
                          <td align="center">{$item.number}</td>
                          <td align="right">{$item.money|format_money}</td>
                          <td align="right">{$item.moneys|format_money}</td>
                        </tr>
                      </if>
                  <else />
                    <tr>
                      <td align="center">{$price['plan']|planShow}</td>
                      <td align="center">{$item.priceid|ticketName}</td>
                      <td align="right">{$item.price}</td>
                      <td align="right">{$item.discount}</td>
                      <td align="center">{$item.number}</td>
                      <td align="right">{$item.money|format_money}</td>
                      <td align="right">{$item.moneys|format_money}</td>
                      <td align="center">{$price.number}</td>
                      <td align="right">{$price.money|format_money}</td>
                      <td align="right">{$price.moneys|format_money}</td>
                      <td>&nbsp;</td>
                    </tr>
                  </if>
                </volist>
                <!--合计金额计算-->
                <?php $number += $price['number']; $money += $price['money']; $moneys += $price['moneys'];?>
              </volist>
              <tr class="subtotal" data-num="{$number}" data-money="{$money}" data-moneys="{$moneys}" >
                  <td></td>
                  <td align="center"></td>
                  <td></td>
                  <td></td>
                  
                  <td></td>
                  <td></td>
                  <td align="right" width="70px"><strong>合计:</strong></td>
                  <td align="center" width="50px"><?php echo $number;?></td>
                  <td width="100px" align="right"><?php echo format_money($money);?></td>
                  <td width="100px" align="right"><?php echo format_money($moneys);?></td>
                  <td rowspan="{$price_count}">&nbsp;</td>
                </tr>
                <?php $number = 0; $money = 0; $moneys = 0;?>
            </volist>
              <tr>
                  <td></td>
                  <td align="center"></td>
                  <td></td>
                  <td></td>
                 
                  <td></td>
                  <td></td>
                  <td align="right" width="70px"><strong>总计:</strong></td>
                  <td align="center" width="50px" id="sub-channel-num">0</td>
                  <td width="100px" id="sub-channel-money" align="right">0.00</td>
                  <td width="100px" id="sub-channel-moneys" align="right">0.00</td>
                  <td rowspan="{$price_count}">&nbsp;</td>
                </tr>
            </tbody>
          </table>
        <else />
            <table class="table table-bordered">
            <thead>
              <tr>
                <th rowspan="2" align="center" width="140px">渠道商</th>
                <th colspan="6" align="center">票型</th>
                <th colspan="3" align="center">小计</th>
                <th rowspan="2" align="center">备注</th>
              </tr>
              <tr>
                <th align="center" width="120px">名称</th>
                <th align="center" width="70px">票面价</th>
                <th align="center" width="70px">结算价</th>
                <th align="center" width="50px">数量</th>
                <th align="center" width="90px">票面金额</th>
                <th align="center" width="90px">结算金额</th>
                <th align="center" width="45px">数量</th>
                <th align="center" width="100px">票面金额</th>
                <th align="center" width="100px">结算金额</th>
              </tr>
              </thead>
              <tbody>
              
              <volist name="data" id="channel">
              <?php $price_count = count($channel['price']);?>
                <volist name="channel['price']" id="item" key="k">
                  <if condition="$price_count neq '1'">
                    <if condition="$k eq '1'">
                      <tr class="subtotal" data-num="{$channel.number}" data-money="{$channel.money}" data-moneys="{$channel.moneys}" data-subsidy="{$channel.rebate}">
                        <td rowspan="{$price_count}" align="center">{$item.channel_id|crmName}</td>
                        <td align="center">{$item.priceid|ticketName}</td>
                        <td align="right">{$item.price}</td>
                        <td align="right">{$item.discount}</td>
                        <td align="center">{$item.number}</td>
                        <td align="right">{$item.money|format_money}</td>
                        <td align="right">{$item.moneys|format_money}</td>
                        <td rowspan="{$price_count}" align="center">{$channel.number}</td>
                        <td align="right" rowspan="{$price_count}">{$channel.money|format_money}</td>
                        <td align="right" rowspan="{$price_count}">{$channel.moneys|format_money}</td>
                        <td rowspan="{$price_count}">&nbsp;</td>
                      </tr>
                      <else />
                        <tr>
                          <td align="center">{$item.priceid|ticketName}</td>
                          <td align="right">{$item.price}</td>
                          <td align="right">{$item.discount}</td>
                          <td align="center">{$item.number}</td>
                          <td align="right">{$item.money|format_money}</td>
                          <td align="right">{$item.moneys|format_money}</td>
                        </tr>
                      </if>
                  <else />
                    <tr  class="subtotal" data-num="{$channel.number}" data-money="{$channel.money}" data-moneys="{$channel.moneys}" data-subsidy="{$channel.rebate}">
                      <td align="center">{$item.channel_id|crmName}</td>
                      <td align="center">{$item.priceid|ticketName}</td>
                      <td align="right">{$item.price}</td>
                      <td align="right">{$item.discount}</td>
                      <td align="center">{$item.number}</td>
                      <td align="right">{$item.money|format_money}</td>
                      <td align="right">{$item.moneys|format_money}</td>
                      <td align="center">{$channel.number}</td>
                      <td align="right">{$channel.money|format_money}</td>
                      <td align="right">{$channel.moneys|format_money}</td>
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
                    <td align="right" width="70px"><strong>合计:</strong></td>
                    <td align="center" width="50px" id="sub-channel-num">0</td>
                    <td width="100px" id="sub-channel-money" align="right">0.00</td>
                    <td width="100px" id="sub-channel-moneys" align="right">0.00</td>
                    <td rowspan="{$price_count}">&nbsp;</td>
                  </tr>
              </tbody>
            </table>
        </if>
      </div>
      </div>
    </div>
  </div>
  <!--页脚-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>
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
    }
  });
  sub_money = sub_money.toFixed(2);
  sub_moneys = sub_moneys.toFixed(2);

  $("#sub-channel-num").html(sub_num);
  $("#sub-channel-money").html(sub_money);
  $("#sub-channel-moneys").html(sub_moneys);
});
</script>