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
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> 销售日报表 &nbsp;&nbsp;<a href="{:U('Home/Report/sales');}">
        <span class="glyphicon glyphicon-share-alt"></span>历史销售统计</a><div class="btn-group btn-group-xs" style="float:right;"> <a href="javascript:$.printBox('w_channel_print')"class="btn btn-success"><span class="glyphicon glyphicon-print"></span>报表打印</a></div></div>
        <div class="panel-body">
          <form class="form-inline" role="form" action="{:U('Home/Report/today_sales');}" method="post">
            <label class="radio-inline">
              <input type="radio" name="sum_det" value="1" <if condition="$sum_det eq 1">checked</if>>
              明细 </label>
            <label class="radio-inline">
              <input type="radio" name="sum_det" value="2" <if condition="$sum_det eq 2">checked</if>>
              汇总 </label>
            
            <div class="form-group">
              <input size="16" type="text" value="{$start_time}" readonly class="form-control form_date" name="start_time">
            </div>
            <div class="form-group">
              <select class="form-control" name="type">
                <option value="">票型</option>
                <volist name="type" id="wo">
                <option value="{$wo.id}" <if condition="$wo['id'] eq $where['priceid']">selected </if>>{$wo.name}</option>
                </volist>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" name="channel">
                <option value="">全部渠道商</option>
                <volist name="channel" id="chan">
                <option value="{$chan.id}" <if condition="$chan['id'] eq $channel_id">selected </if>>{$chan.name}</option>
                </volist>
              </select>
            </div>
            <button type="submit" class="btn btn-default">查询</button>
          </form>
        </div>
        <div id="w_channel_print">
          <div class="visible-print-block">
              <h3 align="center">渠道商销售(票型)统计(场次)报表</h3>
              <span class="pull-left mb10">统计日期：{$start_time}</span>
              <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
          </div>
        <!-- Table -->
        <if condition="$sum_det eq '1'">
          <table class="table table-condensed table-bordered">
            <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            </colgroup>
            <volist name="data" id="vo">
            <volist name="vo['plan']" id="v">
            <thead>
            <tr><td colspan="7">{$v.plan|planshow}  渠道商：{$v.channel_id|crmName}</td></tr>
              <tr>
                <td align="center">票型名称</td>
                <td align="center">票面价</td>
                <td align="center">结算价</td>
                <td align="center">数量</td>
                <td align="center">票面金额</td>
                <td align="center">结算金额</td>
              </tr>
            </thead>
            <tbody>
              <volist name="v['price']" id="vi">  
                <tr>
                  <td align="center">{$vi.priceid|ticketName}</td>
                  <td align="center">{$vi.price}</td>
                  <td align="center">{$vi.discount}</td>
                  <td align="center">{$vi.number}</td>
                  <td align="center">{$vi.money}</td>
                  <td align="center">{$vi.moneys}</td>
                </tr>                
              </volist>
              <tr><td align="right"></td><td align="center"></td><td align="center"></td><td align="center">总数：{$v.number}</td><td align="center">票面金额：{$v.money}</td><td align="center">结算金额：{$v.moneys}</td></tr>
            </tbody>
            </volist>
            </volist>
          </table>
        
        </if>
        <if condition="$sum_det eq '2'">
          <table class="table table-condensed table-bordered">
            <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            </colgroup>
            <volist name="data" id="vo">
            <volist name="vo['plan']" id="v">
            <thead>
            <tr><td colspan="7">{$v.plan|planshow}  渠道商：{$v.channel_id|crmName}</td></tr>
              <tr>
                <td align="center">票型名称</td>
                <td align="center">票面价</td>
                <td align="center">结算价</td>
                <td align="center">数量</td>
                <td align="center">票面金额</td>
                <td align="center">结算金额</td>
              </tr>
            </thead>
            <tbody>
              <volist name="v['price']" id="vi">  
                <tr>
                  <td align="center">{$vi.priceid|ticketName}</td>
                  <td align="center">{$vi.price}</td>
                  <td align="center">{$vi.discount}</td>
                  <td align="center">{$vi.number}</td>
                  <td align="center">{$vi.money}</td>
                  <td align="center">{$vi.moneys}</td>
                </tr>                
              </volist>
              <tr><td align="right"></td><td align="center"></td><td align="center"></td><td align="center">总数：{$v.number}</td><td align="center">票面金额：{$v.money}</td><td align="center">结算金额：{$v.moneys}</td></tr>
            </tbody>
            </volist>
            </volist>
          </table>
        </if>
        </div>
      </div>
    </div>
  </div>
  
  <!--内容主体区域 end-->
  <!--页脚-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>