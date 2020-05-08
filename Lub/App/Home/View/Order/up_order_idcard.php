<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title">订单详情</h4>
</div>
<div class="modal-body">
  <div class="col-md-12 row">
    <div class="col-md-12">
      <label>订单号：</label>
      {$order.order_sn} </div>
  </div>
  <!--订单列表-->
  <form action="{:U('Home/Order/up_order_idcard')}" method="post"> 
  <table class="table">
    <thead>
      <tr>
      	<th></th>
        <th>编号</th>
        <th>证件号</th>
      </tr>
    </thead>
    <tbody>
      <volist name="ticket" id="vo">
        <tr>
          <td>{$i}</td>
          <td>{$vo.seat}</td>
          <td>
          	<input type="text" name="idcard[{$vo['id']}]" value="{$vo.idcard}">
          	<input type="hidden" name="seat[{$vo['id']}]" value="{$vo.seat}">
          </td>
        </tr>
      </volist>
      <input type="hidden" name="sn" value="{$order.order_sn}">
        <tr>
          <td></td><td></td>
          <td><button type="submit" class="btn btn-warning">立即更新</button></td>
        </tr>   
    </tbody>
  </table>
</form>
</div>