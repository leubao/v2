<?php if (!defined('LUB_VERSION')) exit(); ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Managetemplate file="Home/Public/cssjs"/>
<title>销售统计-销售日报表 - by LubTMP</title>
</head>

<body>
<div class="container">
  <Managetemplate file="Home/Public/menu"/>
  <!--内容主体区域 start-->
  <div class="main row">
    <div class="col-lg-12">
      <div class="panel panel-default"> 
        <!-- Default panel contents -->
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> 销售日报表 &nbsp;&nbsp;<!--<a href="{:U('Home/Report/sales');}">
        <span class="glyphicon glyphicon-share-alt"></span>历史销售统计</a>--><div class="btn-group btn-group-xs" style="float:right;"> <a href="{:U('Home/Set/add_channel');}" class="btn btn-success" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-open-file"></span>导出Execl</a></div></div>
        <div class="panel-body">
          <form class="form-inline" role="form" action="{:U('Home/Report/today_sales');}" method="post">
            <label class="radio-inline">
              <input type="radio" name="sum_det" value="1" <if condition="$sum_det eq 1">checked</if>>
              汇总 </label>
            <label class="radio-inline">
              <input type="radio" name="sum_det" value="2" <if condition="$sum_det eq 2">checked</if>>
              明细 </label>
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
              <select class="form-control" name="ur">
                <option value="">操作员</option>
                <volist name="user" id="ur">
                <option value="{$ur.id}" <if condition="$ur['id'] eq $where['user_id']">selected </if>>{$ur.nickname}</option>
                </volist>
              </select>
            </div>
            <button type="submit" class="btn btn-default">查询</button>
          </form>
        </div>
        <!-- Table -->
        <if condition="$sum_det eq '1'">
        <div class="table-responsive">
          <table class="table table-condensed table-bordered" id="table1" style="display:none">
            <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            </colgroup>
            <thead>
              <tr>
                <td align="center">产品</td>
                <td align="center">场次</td>
                <td align="center">渠道商</td>
                <td align="center">票型</td>
                <td align="center">数量</td>
                <td align="center">结算价</td>
                <td align="center">补贴</td>
                <td align="center">补贴汇总</td>
              </tr>
            </thead>
            <tbody>
              <volist name="data" id="vo" key="i">  
                <tr>
                  <td align="center" class="pro">{$vo.product_name}</td>
                  <td align="center" class="plan">{$vo.plan_id|planShow}</td>
                  <td align="center" class="channel_{$vo.plan_id}">{$vo.channel_name}</td>
                  <td align="center" class="price">{$vo.price_type}</td>
                  <td align="center">{$vo.number}</td>
                  <td align="center">{$vo.moneys}</td>
                  <td align="center" class="subsidy_{$vo.plan_id}_{$vo.channel_id}" >{$vo.subsidy}</td>
                  <td align="center" class="total_{$vo.plan_id}_{$vo.channel_id}"></td>
                </tr>                
              </volist>
            </tbody>
          </table>
        </div>
        <div class="panel-footer">{$page}</div>
        </if>
        <if condition="$sum_det eq '2'">
        <div class="table-responsive">
          <table class="table table-condensed table-bordered">
            <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
            </colgroup>
            <thead>
              <tr>
                <td align="center">日期场次</td>
                <td align="center">区域</td>
                <td align="center">票型</td>
                <td align="center">数量</td>
                <td align="center">单价</td>
                <td align="center">结算价</td>
                <td align="center">票面金额</td>
                <td align="center">补贴</td>
                <td align="center">支付方式</td>
                <td align="center">渠道商</td>
                <td align="center">操作员</td>
                <td align="center">单号</td>
                <td align="center">创建时间</td>
              </tr>
            </thead>
            <tbody>
            <volist name="data" id="vo">
              <tr>
                <td>{$vo.plan_id|planShow}</td>
                <td>{$vo.area|areaName}</td>
                <td>{$vo.priceid|ticketName}</td>
                <td>{$vo.number}</td>
                <td>{$vo.price}</td>
                <td>{$vo.discount}</td>
                <td>{$vo.money}</td>
                <td><?php $m = $vo['subsidy']; echo $m;?></td>
                <td>{$vo.pay|pay}</td>
                <td>{$vo.channel_id|crmName}</td>
                <td>{$vo.user_id|userName}</td>
                <td>{$vo.order_sn}</td>
                <td>{$vo.createtime|date="Y-m-d H:i",###}</td>
              </tr>
              </volist>
            </tbody>
          </table>
        </div>
        <div class="panel-footer">{$page}</div>
        </if>
      </div>
    </div>
  </div>
  
  <!--内容主体区域 end--> 
  <script>$('.form_date').datetimepicker({ format: 'yyyy-mm-dd',weekStart: 1,todayBtn:  1,autoclose: 1,todayHighlight: 1,startView: 2,minView: 2,forceParse: 0});</script> 
  <!--页脚-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>
<script type="text/javascript">
$(function(){
  //合并单元格
  //var totalCols = $("#table1").find("tr:eq(0)").find("td").length;
  var totalCols = 2;
  var totalRows = $("#table1").find("tr").length;
  for ( var i = totalCols; i >= 0; i--) {
    for ( var j = totalRows-1; j >= 0; j--) {
      startCell = $("#table1").find("tr").eq(j).find("td").eq(i);
      targetCell = $("#table1").find("tr").eq(j - 1).find("td").eq(i);
      if (startCell.text() == targetCell.text() && targetCell.text() != "" &&startCell.attr("class")==targetCell.attr("class")) {
        targetCell.attr("rowSpan", (startCell.attr("rowSpan")==undefined)?2:(eval(startCell.attr("rowSpan"))+1));
        startCell.remove();
      }
    }
  }

  //总补贴汇总
  $("[class^='subsidy_']").each(function(){
    var classname = $(this).attr("class");
    var arr = $(this).attr("class").split("_");
    var plan_id    = arr[1];
    var channel_id = arr[2];
    var total = 0;
    $("."+classname).each(function(){
      total += parseFloat($(this).text());      
    })
    $(".total_"+plan_id+"_"+channel_id).text(total.toFixed(2));
  })
  for(var h=totalRows;h>=1;h--){
    start = $("#table1").find("tr").eq(h).find("td:last");
    target = $("#table1").find("tr").eq(h - 1).find("td:last");
    if (start.text() == target.text() && target.text() != "" &&start.attr("class")==target.attr("class")) {
      target.attr("rowSpan", (start.attr("rowSpan")==undefined)?2:(eval(start.attr("rowSpan"))+1));
      start.remove();
    }    
  }



  $("#table1").show();

})
</script>