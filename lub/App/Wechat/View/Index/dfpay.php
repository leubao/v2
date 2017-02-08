<Managetemplate file="Wechat/Public/header"/>
<div class="page">
	<header class="bar bar-nav">
    	<h1 class="title">付款码</h1>
  	</header>
  	<div class="content">
  		<div class="card" style="text-align: center;">
  			<img src="{$qr}">
  		</div>
  		<div class="content-block">
      <p><a href="{$url}" class="external button button-big button-fill button-success">立即付款</a></p>
    </div>
  	</div>
  	<div class="shareit">
      <img class="arrow" src="http://new.leubao.com/static/images/guide.png">
    </div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
  $(function() {
    $(".shareit").show();
    setTimeout(function(){$(".shareit").hide();},6000);
    $(".shareit").on('click',function(){$(".shareit").hide();});
    wx.ready(function(){
        wx.showMenuItems({
            menuList: ['menuItem:share:appMessage']
        });
        wx.onMenuShareAppMessage({
            title: '{$wechat.share_title}',
            desc: '{$wechat.share_desc}',
            link: '{$url}',
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
    });
  });  
</script>
</body>
</html>