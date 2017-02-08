<Managetemplate file="Wechat/Public/header"/>
<div class="page">
<header class="bar bar-nav">
    <h1 class="title">订单详情</h1>
  </header>
  <div class="content">
   <div class="list-block">
    <ul>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">产品名称 : {$data.product_id|product_name}</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">订单号 : {$data.order_sn}</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">场次 : {$data.plan_id|planShow}</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">区域详情 : <volist name="area" id="ar">{$ar.area|areaName}({$ar.num}) </volist></div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">金额 : {$data['info']['subtotal']}元</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">座位 : <volist name="data['info']['data']" id="vo">{$vo.seatid|seatShow},</volist></div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">联系人 : {$data['info']['crm'][0]['contact']}</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">电话 : {$data['info']['crm'][0]['phone']}</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">备注 : {$data.remark}</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">状态 : {$data['status']|order_status}</div>
        </div>
      </li>
    </ul>
  </div> 
  <!--支付方式-->
  <div class="content-block">
      <p><a href="#" id="closeWindow" class="button button-big button-fill button-success">确定</a></p>
    </div>
  </div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
$(function() {
wx.ready(function(){wx.hideOptionMenu();});
});
//关闭当前窗口
document.querySelector('#closeWindow').onclick = function () {
    wx.closeWindow();
  };
</script>
</body>
</html>