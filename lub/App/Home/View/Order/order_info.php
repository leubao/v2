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
  <div class="row">
    <!--导游信息START-->
    <div class="col-sm-6">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">渠道信息</h3>
        </div>
        <div class="panel-body">
            <table class="table">
              <thead>
                <tr>
                  <th>渠道商名称</th>
                  <th>区域详情</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{$data['info']["crm"][0]['qditem']|crmName}</td>
                  <td>
                <volist name="area" id="ar">{$ar.areaname}({$ar.num}) </volist></td>
                </tr>
              </tbody>
            </table>
        </div>
      </div>
    </div>
    <!--导游信息END-->
    <!--联系人信息START-->
    <div class="col-sm-6">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">联系人信息</h3>
        </div>
        <div class="panel-body">
            <table class="table">
              <thead>
                <tr>
                  <th>联系人姓名</th>
                  <th>联系人电话</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{$data['info']["crm"][0]['contact']}</td>
                  <td>{$data['info']["crm"][0]['phone']}</td>
                </tr>
              </tbody>
            </table>
        </div>
      </div>
    </div>
    <!--联系人END-->   
    <div class="col-sm-12">
      <div class="panel panel-primary">
        <div class="panel-heading">
          <h3 class="panel-title">订单信息</h3>
        </div>
        <div class="panel-body">
    		<p>产品名称：{$data.product_id|product_name}  {$data.plan_id|planShow}</p>
            <p>下单人：{$data.user_id|userName}   下单时间:{$data.createtime|date="Y-m-d h:i:s",###}</p>
            <p>备注：{$data.remark}</p>
  		</div>
      <table class="table">
    <thead>
    <if condition="$type eq '1'">
      <tr>
        <th>编号</th>
        <th>票型</th>
        <th>票价</th>
        <th>区域</th>
        <th>座位</th>
      </tr>
    <else />
      <tr>
        <th>编号</th>
        <th>票型</th>
        <th>票号</th>
      </tr>
    </if>
    </thead>
    <tbody>
      <if condition="$type eq '1'">
      <volist name="data['info']['data']" id="vo">
        <tr>
          <td>{$i}</td>
          <td>{$vo.priceid|ticketName}</td>
          <td>{$vo.price}</td>
          <td>{$vo.areaId|areaName}</td>
          <td>{$vo.seatid|seatShow}</td>
        </tr>
      </volist>
      <else />
        <volist name="data['info']['data']" id="vo">
        <tr>
          <td>{$i}</td>
          <td>{$vo.priceid|ticketName}</td>
          <td>{$vo.ciphertext}</td>
        </tr>
      </volist>
      </if>
      <if condition="$data['status'] eq '1' AND is_order_plan($data['plan_id'])">
      <if condition="$data['cancel'] eq '1'">
        <tr>
          <td colspan="4"></td>
          <td><button id="cancel_order" type="button" class="btn btn-warning">取消订单</button></td>
        </tr>        
      </if>
      </if>
      <tr id="cancel_form" style="display:none">
          <td><label>取消订单理由：</label></td>
          <td colspan="2"><textarea name="reason" cols="40"></textarea></td>
          <td><label>退款方式：</label>            
            <input type="radio" name="re_type" value="1" checked/>退还到授信额
           <!--   <input type="radio" name="re_type" value="2" />现金 </td>-->
          <td>
            <input type="hidden" name="sn" value="{$data['order_sn']}"/>
            <input type="hidden" name="order_status" value="{$data.status}" />
            <input type="hidden" name="money" value="{$data.money}"/>
            <button type="submit" class="btn btn-success">提交申请</button>
          </td>
      </tr>       
    </tbody>
  </table>
      </div>
    </div>    
  </div>
</div>
<div> 
  <!--内容主体区域 end--> 
  <!--页脚-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>