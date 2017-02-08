<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
<script type='text/javascript' src='//g.alicdn.com/msui/sm/0.5.2/js/sm.min.js' charset='utf-8'></script>
<script type='text/javascript' src='//g.alicdn.com/msui/sm/0.5.2/js/sm-extend.min.js' charset='utf-8'></script>
<script type="text/javascript" src="/static/js/wap/laytpl.js" charset="utf-8"></script>
<script type="text/javascript" src="/static/js/wap/wap.js" charset="utf-8"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js" charset="utf-8"></script>
<script type="text/javascript">
	$(function() {
		wx.config({
	        debug: false,
	        appId: '{$jsapi.appId}',
	        timestamp: '{$jsapi.timestamp}',
	        nonceStr: '{$jsapi.nonceStr}',
	        signature: '{$jsapi.signature}',
	        jsApiList: [
	          'checkJsApi',
	          'showMenuItems',
	          'onMenuShareTimeline',
	          'onMenuShareAppMessage'
	        ]
    	});
	});
	 
</script>
