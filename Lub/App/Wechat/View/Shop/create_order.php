<Managetemplate file="Wechat/Public/header"/>
<div class="page">
<header class="bar bar-nav">
    <h1 class="title">订单确认</h1>
  </header>
  <div class="content">
    <div class="card">
        <!-- <div class="card-header">{$data.plan_id|planShow}</div> -->
        <div class="card-content">
          <div class="list-block">
            <ul>
            <volist name="data['info']['data']['area']" id="vo" key='k'>
              <li class="item-content">
                  <div class="item-inner">
                    <div class="item-title">{$vo.priceid|ticketName}</div>
                    <div class="item-after">x {$vo.num}</div>
                  </div>
              </li>
            </volist>
            </ul>
          </div>
        </div>
        <div class="card-footer price"> <span>优惠金额</span><span>{$data['info']['data']['poor']|format_money}</span></div>
        <div class="card-footer price"> <span>实付金额</span><span> ￥ {$data['money']}</span></div>
    </div>
   <div class="list-block">
    <ul>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">订单号 : {$data.order_sn}</div>
        </div>
      </li>
    </ul>
  </div> 
  {$error}
  <!--支付方式-->
  <if condition="$data.status neq '1' || $data.status neq '9' ">
  <div class="content-block">
    <p><a href="#" class="button button-big button-fill button-success" id="wxpay">微信支付</a></p>
    </div>
  </div>
  </if>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
  $(function() {
    var seat_type = '1',
        money = {$data.money},
        sn = {$data.order_sn},
        pay_type = '2';
    wx.ready(function(){wx.hideOptionMenu();});
    /*微信支付*/
    $(document).on('click', '#wxpay',function () {
      if (typeof WeixinJSBridge == "undefined"){
         if(document.addEventListener){
             document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
         }else if(document.attachEvent){
             document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
             document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
         }
      }else{
         onBridgeReady();
      }
    });
    function onBridgeReady(){
       WeixinJSBridge.invoke(
           'getBrandWCPayRequest', 
           {$wxpay|json_encode},
           function(res){
             if(res.err_msg == "get_brand_wcpay_request:ok"){
                var link = "{:U('Wechat/shop/pay_success',array('sn'=>$data['order_sn']));}";
                window.location.href=link;
             }
           }
       ); 
    }
  });
  
</script>
</body>
</html>