<Managetemplate file="Wechat/Public/header"/>
<div class="page">
  	<div class="content">
		<img src="d/wap/banner.jpg" height="120px" width="100%">
  		<div class="card" style="text-align: center;">
  		    <img src="/d/wap/water.jpg">
  		</div>
  		<div class="content-block">
	      <p><a href="#" id="led" class="button button-big button-fill button-success external">立即领取</a></p>
	    </div>
  	</div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript"> 
    $(document).on('click','#led', function () {
        $.toast('亲,请找我们业务员领取...');
    });
</script>
</body>
</html>