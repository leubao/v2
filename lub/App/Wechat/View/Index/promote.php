<Managetemplate file="Wechat/Public/header"/>
<div class="page">
  	<div class="content">
		<img src="d/wap/banner.jpg" height="120px" width="100%">
  		<div class="card" style="text-align: center;">
  			<img src="{$qr}">
  		</div>
  		<div class="content-block">
	      <p><a href="{$urls}" class="button button-big button-fill button-success external">立即购买</a></p>
	    </div>
	    <p class="remark">点击右上角按钮分享给朋友</p>
  	</div>
    <div class="shareit">
      <img class="arrow" src="http://new.leubao.com/static/images/guide.png">
    </div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
  $(function() {

    wx.ready(function(){
        wx.checkJsApi({
            jsApiList: [
                'onMenuShareAppMessage',
            ]
        });
        wx.showMenuItems({
            menuList: ['menuItem:share:appMessage','menuItem:share:timeline']
        });
        wx.onMenuShareAppMessage({
            title: '{$wechat.share_title}',
            desc: '{$wechat.share_desc}',
            link: '{$urls}',
            imgUrl: '{$config_siteurl}static/images/wshare_{$pid}.jpg',
            trigger: function (res) {
            },
            success: function (res) {
                alert('分享给好友成功');
            },
            cancel: function (res) {
            },
            fail: function (res) {
                alert(JSON.stringify(res));
            }
        });
        wx.onMenuShareTimeline({
            title: '{$wechat.share_title}',
            desc: '{$wechat.share_desc}',
            link: '{$urls}',
            imgUrl: '{$config_siteurl}static/images/wshare_{$pid}.jpg',
            trigger: function (res) {
            },
            success: function (res) {/*
                $.post('../api/v0.0.1/marketing.php', {
                        action:'ShareLog',
                        openid:'',
                        page_id: 'http://wx.12301.cc/html/p.html?lid=2270'
                    }, function (json) { alert('分享成功');}, 'json');*/
            },
            cancel: function (res) {
            },
            fail: function (res) {
                alert(JSON.stringify(res));
            }
        });
    });
    $(".shareit").show();
    setTimeout(function(){$(".shareit").hide();},6000);
    $(".shareit").on('click',function(){$(".shareit").hide();});
  });  
</script>
</body>
</html>