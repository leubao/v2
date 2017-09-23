<Managetemplate file="Wechat/Public/header"/>
<div class="page">
  <header class="bar bar-nav">
    <button class="button button-link button-nav pull-left"  ontouchend="window.history.back()">
      <span class="icon icon-left"></span>
    </button>
    <button class="button button-link button-nav pull-right"  ontouchend="window.location.href='{:U('Wechat/Index/uinfo',array('param'=>$param));}'">
      <span class="icon icon-me"></span>
    </button>
    <h1 class="title">账号绑定</h1>
  </header>
  <div class="content">
  <if condition="$type neq '2'">
    <div class="list-block">
      <ul>
        <li id="username">
          <div class="item-content">
            <div class="item-media"><i class="icon icon-form-name"></i></div>
            <div class="item-inner">
              <div class="item-input">
                <input type="text" id="name" placeholder="用户名">
              </div>
            </div>
          </div>
        </li>
        <li id="phone" style="display: none">
          <div class="item-content">
            <div class="item-media"><i class="icon icon-form-name"></i></div>
            <div class="item-inner">
              <div class="item-input">
                <input type="text" id="phone" placeholder="手机号">
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
        <div class="col-100"><a href="#" class="button button-big button-fill button-success sub">立即绑定</a></div>
      </div>
    </div>
  </div>
  <else />
  <div class="col-50 no"></div>
  <div class="content-padded">
  <p><strong>亲，你已完成绑定,请勿重复操作!</strong></p>
  </div>
  </if>
</div>

<Managetemplate file="Wechat/Public/footer"/>
<if condition="$type neq '2'">
<script type="text/javascript">
  $(function() {
    var openid = '{$openid}',
        name = '',
        pwd = '',
        msg = '';
    wx.ready(function(){wx.hideOptionMenu();});
    $(".sub").click(function(){
      //验证输入
      var name = $("#name").val(),
          pwd = $("#pwd").val();
      if(name == '' || pwd == ''){
        msg = "用户密码不能为空";
      }
      if(msg != ''){
        msgs(msg);msg = '';return false;
      }else{
        $.confirm('确定绑定微信吗?', 
          function () {
            post_server(name,pwd,openid);
          },
          function () {
            $.alert('亲，您要取消绑定吗？');
          }
        );
      }
    });
    function post_server(name,pwd,openid){
      var postData = 'info={"username":"'+name+'","password":"'+pwd+'","openid":"'+openid+'"}';
      /*提交到服务器**/
      $.ajax({
          type:'POST',
          url:'<?php echo U('Wechat/Index/auth_channel',array('param'=>$param));?>',
          data:postData,
          dataType:'json',
          timeout: 1500,
          error: function(){
            $.toast('服务器请求超时，请检查网络...');
          },
          success:function(data){
              if(data.statusCode == "200"){
                $.alert('亲您已成功绑定微信,赶紧和我一起开始移动互联之旅吧!', function () {
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
</if>
  </body>
</html>