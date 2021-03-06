<Managetemplate file="Wechat/Public/header"/>
<div class="page">
  <header class="bar bar-nav">
    <button class="button button-link button-nav pull-left"  ontouchend="window.history.back()">
      <span class="icon icon-left"></span>
    </button>
    <button class="button button-link button-nav pull-right"  ontouchend="window.location.href='{:U('Wechat/Index/uinfo');}'">
      <span class="icon icon-me"></span>
    </button>
    <h1 class="title">解除绑定</h1>
  </header>
  <div class="content">
    <form action="{:U('Wechat/Index/remove');}" method="post">
    <div class="list-block">
      <ul>
        <li>
          <div class="item-content">
            <div class="item-inner">
              <div class="item-title label">密码</div>
              <div class="item-input">
                <input type="password" id="pwd" placeholder="密码" class="">
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <div class="content-block">
      <div class="row">
        <div class="col-100"><a href="#" type="submit" class="button button-big button-fill button-success sub">解除绑定</a></div>
      </div>
    </div>
    </form>
  </div>
</div>

<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
$(function() {
  wx.ready(function(){wx.hideOptionMenu();});
  var openid = '{$data.user.openid}',
      msg = '';
  $(".sub").click(function(){
    //验证输入
    var pwd = $("#pwd").val();
    if(pwd == ''){
      msg = "密码不能为空";
    }
    if(msg != ''){
      msgs(msg);msg = '';return false;
    }else{
      /*提交到服务器**/
      var postData = 'info={"password":"'+pwd+'"}';
      $.ajax({
          type:'POST',
          url:'<?php echo U('Wechat/Index/remove');?>',
          data:postData,
          dataType:'json',
          timeout: 1500,
          error: function(){
            layer.msg('服务器请求超时，请检查网络...');
          },
          success:function(data){
              if(data.statusCode == "200"){
                $.alert('注销成功!', function () {
                    location.href = data.url;
                });
              }else{
                  $.toast(data.msg);
              }
          }
      });

    }
  });
});
</script>
</body>
</html>