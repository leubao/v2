<include file="Index:header" />
<div class="sui-container pd80">
  <div class="header-1">
      <h2>订单信息</h2>
  </div>
  <div class="span8">
    <div class="span5"><h3><strong>观演时间:</strong> {$data['plan_id']|planShow=4}</h3></div>
    <div class="span5"><h3><strong>订 单  号:</strong> {$data['order_sn']}</h3></div>
    <div class="span5"><h3><strong>取 票  人:</strong> {$data['info']['crm'][0]['contact']}</h3></div>
    <div class="span5"><h3><strong>手机号码:</strong> {$data['info']['crm'][0]['phone']}</h3></div>
    <div class="span5"><h3><strong>备 注:</strong> {$data.remark}</h3></div>
    <div class="span5"><h3><strong>订单金额:</strong> <dfn id="J-price" style="color: #f60">{$data['info']['subtotal']|format_money}元</dfn></h3></div>
    <div class="span8">
      <table class="sui-table table-bordered mt10">
      <thead>
      <tr>
        <th>观演时间</th>
        <th>区域名称</th>
        <th>座位号</th>
      </tr>
      </thead>
      <tbody id="price_box">
      <volist name="data['info']['data']" id="vo">
      <tr>
        <td>{$data['plan_id']|planShow=4}</td>
        <td>{$vo.areaId|areaName}</td>
        <td>{$vo.seatid|seatShow}</td>
      </tr>
      </volist>
      </tbody>
      </table>
    </div>
  </div> 
</div>