<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title">取消订单申请</h4>
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
      {$data.user_id|userName} </div>
    <div class="col-md-6">
      <label>订单金额：</label>
      {$data['info']['subtotal']} </div>
    <div class="col-md-12">
      <label>备注：</label>
      {$data.remark}</div>
    <div class="col-md-6">
      <label>订单状态：</label>
      <if condition="$data['status'] eq 1"><span class="label label-success">成功</span>
        <else />
        <span class="label label-danger">失败</span></if>
    </div>
  </div>
  <!--订单列表-->
  <table class="table">
    <thead>
      <tr>
        <th>编号</th>
        <th>票型</th>
        <th>票价</th>
        <th>区域</th>
        <th>座位</th>
      </tr>
    </thead>
    <tbody>
      <volist name="data['info']['data']" id="vo">
        <tr>
          <td>{$i}</td>
          <td>{$vo.priceid|ticketName}</td>
          <td>{$vo.price}</td>
          <td>{$vo.areaId|areaName}</td>
          <td>{$vo.seatid|seatShow}</td>
        </tr>
      </volist>
      <tr>
        <td colspan="4"></td>
        <td><a href="{:U('Home/Order/cancel_order',array('sn'=>$data['order_sn']))}" data-toggle="modal" data-target="#myModal"><button id="cancel_order" type="button" class="btn btn-success">取消订单</button></a></td>
      </tr>
    </tbody>

    <!--
              <tr>
                <td></td>
                <td colspan="2"><button type="button" class="btn btn-lg btn-primary">打印纸质票</button></td>
                <td colspan="2"><button type="button" class="btn btn-lg btn-info">发送电子票</button></td>
              </tr> -->
  </table>
</div>
