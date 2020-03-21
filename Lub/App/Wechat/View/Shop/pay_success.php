<Managetemplate file="Wechat/Public/header"/>
<div class="page">
	<div class="content">
	    <div class="content-block">
	    	<div class="weui_msg">
		    <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
		    <div class="weui_text_area">
		        <h2 class="weui_msg_title">操作成功</h2>
		        <p class="weui_msg_desc">订单{$sn}支付成功</p>
		    </div>
			<a href="{:U('api/index/ticket',array('tid'=>$sns));}" class="button button-big button-fill button-success external">立即使用</a>
		    <div class="weui_extra_area">
		        <a href="{:U('Wechat/shop/order_info',array('sn'=>$sn));}" class="external">查看详情</a>
		    </div>
		</div>
	</div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
//关闭当前窗口
document.querySelector('#closeWindow').onclick = function () {
    wx.closeWindow();
  };
</script>
</body>
</html>