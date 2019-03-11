<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title">订单详情</h4>
</div>
<div class="modal-body">
  <div class="col-md-12 row">
    <div class="col-md-12">
      <label>产品名称：</label>
      {$data.product_id|product_name}  {$data.plan_id|planShow}</div>
    <div class="col-md-6">
      <label>订单号：</label>
      {$data.order_sn} </div>
    <div class="col-md-6">
      <label>下单时间：</label>
      {$data.createtime|date="Y-m-d h:i:s",###} </div>
    <div class="col-md-6">
      <label>下单人:</label>
      {$data['user_id']|userName=$data['addsid']} </div>
    <div class="col-md-6">
      <label>订单金额：</label>
      {$data['info']['subtotal']} </div>
    
      <div class="col-md-6">
        <if condition="$type eq '1'"><label>区域详情：</label><else /><label>票型详情:</label></if>
        <volist name="area" id="ar">{$ar.areaname}({$ar.num}) </volist>
      </div>

    
    <div class="col-md-6">
      <label>联系人姓名:</label>
      {$data['info']['crm']['0']['contact']} </div>
    <div class="col-md-6">
      <label>联系人电话：</label>
      {$data['info']['crm']['0']['phone']} </div>  
    <div class="col-md-6">
      <label>支付方式：</label>
      {$data['pay']|pay} </div>  
    <div class="col-md-12">
      <label>备注：</label>
      {$data.remark}</div>
    <div class="col-md-6">
      <label>订单状态：</label>
      {$data['status']|order_status}
    </div>
  </div>
  <!--订单列表-->
  <form action="{:U('Home/Order/cancel_order')}" method="post"> 
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
</form>
</div>
<script type="text/javascript">
$(function(){
  $("#cancel_order").click(function(){
    $("#cancel_form").toggle();
  });
})  
</script>