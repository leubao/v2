<Managetemplate file="Wechat/Public/header"/>
<div class="page">
  <header class="bar bar-nav">
    <h1 class="title">我的门票</h1>
  </header>
  <div class="content pull-to-refresh-content" data-ptr-distance="55">
    <!-- 默认的下拉刷新层 -->
    <div class="pull-to-refresh-layer">
        <div class="preloader"></div>
        <div class="pull-to-refresh-arrow"></div>
    </div>
    <!-- 下面是正文 -->
    <div class="card-container">
      <volist name="data" id="vo">
        <div class="card">
          <div class="card-header">单号:{$vo.order_sn}
            <if condition="$vo['status'] == 1">
            <a href="{:U('api/index/ticket',array('tid'=>$vo['sns']));}" class="external">
            <span class="pull-right">{$vo['status']|order_status}</span>
            </a>
            <else />
            <span class="pull-right">{$vo['status']|order_status}</span>
            </if>
          </div>
          <div class="card-content">
            <div class="card-content-inner">
              <b>产品名称:{$vo.product_id|productName}</b>
              <p>金额:{$vo.money} 数量: {$vo.number}</p>
              <p>创建时间:{$vo.createtime|datetime}</p>
            </div>
          </div>
        </div>
      </volist>
    </div>
  </div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
$(function() {
  wx.ready(function(){wx.hideOptionMenu();});
  // 添加'refresh'监听器
  $(document).on('refresh', '.pull-to-refresh-content',function(e) {
    // 模拟2s的加载过程
    setTimeout(function() {
        var cardNumber = $(e.target).find('.card').length + 1;
        var cardHTML = '<div class="card">' +
                          '<div class="card-header">card'+cardNumber+'</div>' +
                          '<div class="card-content">' +
                            '<div class="card-content-inner">' +
                                '这里是第' + cardNumber + '个card，下拉刷新会出现第' + (cardNumber + 1) + '个card。' +
                            '</div>' +
                          '</div>' +
                      '</div>';

        $(e.target).find('.card-container').prepend(cardHTML);
        // 加载完毕需要重置
        $.pullToRefreshDone('.pull-to-refresh-content');
    }, 2000);
  });
});
</script>
</body>
</html>