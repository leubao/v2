<Managetemplate file="Wechat/Public/header"/>
<div class="page">
  <header class="bar bar-nav">
    <button class="button button-link button-nav pull-left"  ontouchend="window.history.back()">
      <span class="icon icon-left"></span>
    </button>
    <button class="button button-link button-nav pull-right"  ontouchend="window.location.href='{:U('Wechat/Index/uinfo',array('param'=>$param));}'">
      <span class="icon icon-me"></span>
    </button>
    <h1 class="title">账号登录</h1>
  </header>
  <div class="content">
    <form action="{:U('Wechat/Index/login',array('param'=>$param));}" method="post">
    <div class="list-block">
      <ul>
        <li>
          <div class="item-content">
            <div class="item-media"><i class="icon icon-form-name"></i></div>
            <div class="item-inner">
              <div class="item-input">
                <input type="text" id="name" placeholder="用户名">
              </div>
            </div>
          </div>
        </li>
        <li>
          <div class="item-content">
            <div class="item-media"><i class="icon icon-form-password"></i></div>
            <div class="item-inner">
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
        <div class="col-100"><a href="#" type="submit" class="button button-big button-fill button-success sub">登录</a></div>
      </div>
    </div>
    </form>
  </div>
</div>

<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
$(function() {
  var openid = '{$user.user.openid}',
      name = '',
      pwd = '',
      msg = '';
  $(".sub").click(function(){
    //验证输入
    var name = $("#name").val(),
        pwd = $("#pwd").val();
    if(name == '' || pwd == ''){
      msg = "用户密码不能为空";
    }
    if(msg != ''){
      msgs(msg);msg = '';return false;
    }
  });
  function post_server(name,pwd,openid){
    var postData = 'info={"username":"'+name+'","password":"'+pwd+'","openid":"'+openid+'"}';
    /*提交到服务器**/
    $.ajax({
        type:'POST',
        url:'<?php echo U('Wechat/Index/login',array('param'=>$param));?>',
        data:postData,
        dataType:'json',
        timeout: 1500,
        error: function(){
          layer.msg('服务器请求超时，请检查网络...');
        },
        success:function(data){
            if(data.statusCode == "200"){
              $.alert('登录成功!', function () {
                  location.href = data.url;
              });
            }else{
                $.toast(data.msg);
            }
        }
    });
  }
});
</script>
</body>
</html>