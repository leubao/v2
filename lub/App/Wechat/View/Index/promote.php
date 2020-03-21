<Managetemplate file="Wechat/Public/header"/>
<div class="page">
  	<div class="content">
		<!--img src="d/wap/banner.jpg" height="120px" width="100%"-->
  		<div class="card" style="text-align: center;">
  			<img src="{$qr}">
  		</div>
  		<div class="content-block">
	      <p><a href="{$urls}" class="button button-big button-fill button-warning external">立即购票</a></p>
	    </div>
	    <p class="remark">点击右上角按钮分享给朋友</p>
  	</div> 
    <div class="shareit">
      <img class="arrow" src="http://www.yxpttk.com/static/images/guide.png">
    </div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
  $(function() {
    $(".shareit").show();
    setTimeout(function(){$(".shareit").hide();},6000);
    $(".shareit").on('click',function(){$(".shareit").hide();});
  });  
</script>
</body>
</html>