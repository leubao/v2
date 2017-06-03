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
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> 订单取票 </div>
        <div class="panel-body">
          <!--<form class="form-inline" role="form" action="{:U('Home/Order/up_tickets');}" method="post">-->
          <form class="form-inline" role="form" action="{:U('Home/Order/up_tickets');}" method="get">
            <div class="form-group">
              <input type="text" class="form-control" name="sn" id="sn" value="{$pinfo.sn}" placeholder="订单号">
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="phone" id="phone" value="{$pinfo.phone}" placeholder="手机号">
            </div>
            <button type="submit" class="btn btn-default">查询</button>
            <button type="reset" class="btn btn-default">重置</button>
          </form>
        </div>

        <!-- Table -->
      <if condition="$data neq '404'">
        <table class="table table-striped table-bordered">
          <tbody>
            <tr>
              <td width="120px">销售计划</td>
              <td width="360px">{$data.plan_id|planShow}</td>
              <td width="100px">单号</td>
              <td>{$data.order_sn}</td>
            </tr>
            <tr>
              <td>创建时间</td>
              <td>{$data.createtime|date="Y-m-d H:i:s",###} </td>
              <td>下单人</td>
              <td>{$data.user_id|userName}</td>
            </tr>
            <tr>
              <td>联系人</td>
              <td>{$data['info']['crm']['0']['contact']} </td>
              <td>手机</td>
              <td>{$data.phone}</td>
            </tr>
            <tr>
              <td>订单金额</td>
              <td>{$data['info']['subtotal']|format_money} </td>
              <td>支付方式</td>
              <td>{$data.pay|pay}</td>
            </tr>
            <tr>
              <td>订单类型(场景)</td>
              <td>{$data.type|channel_type}（{$data.addsid|addsid}）</td>
              <td>身份证号</td>
              <td>{$data['id_card']} </td>
            </tr>
            <if condition="$data['type'] neq '1'">
            <tr>
              <td>渠道商(业务员)</td>
              <td colspan="3">{$data.channel_id|hierarchy}({$data.guide_id|userName})</td>
            </tr>
            </if>
            <if condition="$data['status'] eq '9'">
            <tr>
              <td>出票员</td>
              <td>{$data.order_sn|print_ticket_user}</td>
              <td></td>
              <td></td>
            </tr>
            </if>
            <tr>
              <td>区域详情</td>
              <td colspan="3"><volist name="area" id="ar">{$ar.area|areaName}({$ar.num}) </volist></td>
            </tr>
            <tr>
              <td>备注1</td>
              <td colspan="3">{$data.remark}</td>
            </tr>
            <tr>
              <td>备注2</td>
              <td colspan="3"><textarea name="win_rem" cols="55" rows="1" <eq name="data.win_rem" value="0">disabled</eq> >{$data.win_rem}</textarea></td>
            </tr>
          </tbody>
        </table>
        <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>编号</th>
            <th>票型</th>
            <th>票面价</th>
            <th>结算价</th>
            <th>区域</th>
            <th>座位</th>
            <th>订单号/状态/打印次数/更新时间</th>
          </tr>
        </thead>
        <tbody>
          <volist name="data['info']['data']" id="vo">
                <tr>
                  <td>{$i}</td>
                  <td>{$vo.priceid|ticketName}</td>
                  <td>{$vo.price}</td>
                  <td>{$vo.discount}</td>
                  <td>{$vo.areaId|areaName}</td>
                  <td>{$vo.seatid|seatShow}</td>
                  <td>{$vo.seatid|seatOrder=$data['plan_id'],$vo['areaId']}</td>
                </tr>
              </volist>
        </tbody>
        </table>
        <a class="btn btn-default print <if condition="$data['status'] neq '1'">disabled</if> pull-right" href="#" data-url="{:U('Home/Order/drawer',array('sn'=>$data['order_sn'],'plan_id'=>$data['plan_id']))}"><i class="glyphicon glyphicon-print"></i>打印</a>
      <else />
        <table class="table table-bordered">
        <tbody>
        <tr><td style='padding:15px;' align='center'><strong style='color:red;font-size:48px;'>未找到相关信息</strong></td></tr>
        </tbody>
        </table>
      </if> 
      </div>
    </div>
  </div>
  <!--内容主体区域 end-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
  <script type="text/javascript">
    
  </script>
</div>
</body>
</html>