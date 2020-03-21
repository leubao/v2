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
          <div class="item-title">使用日期 : {$data.plan_id|planShow='6'}</div>
        </div>
      </li>
      
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">金额 : {$data['info']['subtotal']}元</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">人数 : {$data[number]}</div>
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
          <div class="item-title">创建时间 : {$data.createtime|datetime}</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">状态 : {$data['status']|order_status}</div>
        </div>
      </li>
    </ul>
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