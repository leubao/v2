<Managetemplate file="Wechat/Public/header"/>
<div class="page">
  <header class="bar bar-nav">
    <button class="button button-link button-nav pull-left"  ontouchend="window.history.back()">
      <span class="icon icon-left"></span>
    </button>
    <button class="button button-link button-nav pull-right"  ontouchend="window.location.href='{:U('Wechat/Index/uinfo',array('param'=>$param));}'">
      <span class="icon icon-me"></span>
    </button>
    <h1 class="title">说明</h1>
  </header>
  <div class="content">
    <div class="card">
    <div class="card-content">
      <div class="card-content-inner">注意:您的账号目前处于待审核状态，现
      在分享链接产生的销售行为不会记录收入。待收到审核通过的信息之后，通过您分享的
      页面产生的购买系统会为您自动记录收入。</div>
    </div>
    </div>
    <div class="content-block">
      <div class="row">
        <div class="col-100"><a href="{$url}" class="button button-big button-fill button-success external">知道了,立即购买</a></div>
      </div>
    </div>
    </form>
  </div>
</div>

<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
$(function() {
wx.hideOptionMenu();
});
</script>
</body>
</html>