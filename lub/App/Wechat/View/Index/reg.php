<Managetemplate file="Wechat/Public/header"/>
<div class="page">
  <header class="bar bar-nav">
    <button class="button button-link button-nav pull-left"  ontouchend="window.history.back()">
      <span class="icon icon-left"></span>
    </button>
    <button class="button button-link button-nav pull-right"  ontouchend="window.location.href='{:U('Wechat/Index/uinfo');}'">
      <span class="icon icon-me"></span>
    </button>
    <h1 class="title">注册账号</h1>
  </header>
  <div class="content">
    <if condition="$data['user']['channel'] neq '1'">
    <form action="{:U('Wechat/Index/reg');}" method="post">
    <div class="list-block">
      <ul>
        <li>
          <div class="item-content">
            <div class="item-inner">
              <div class="item-title label">姓名</div>
              <div class="item-input">
                <input type="text" id="name" placeholder="姓名">
              </div>
            </div>
          </div>
        </li>
        <li>
          <div class="item-content">
            <div class="item-inner">
              <div class="item-title label">手机号</div>
              <div class="item-input">
                <input type="text" id="phone" placeholder="手机号">
              </div>
            </div>
          </div>
        </li>
        <li>
          <div class="item-content">
            <div class="item-inner">
              <div class="item-title label">身份证号</div>
              <div class="item-input">
                <input type="text" id="legally" placeholder="身份证号">
              </div>
            </div>
          </div>
        </li>
        
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
        <div class="col-100"><a href="#" type="submit" class="button button-big button-fill button-success sub">立即注册</a></div>
      </div>
    </div>
    </form>
    <else />
    <div class="col-50 no"></div>
    <div class="content-padded">
    <p><strong>亲，你已经是注册过了!</strong></p>
    </div>
    <div class="content-block">
      <div class="row">
        <div class="col-100"><a href="{$url}" class="button button-big button-fill button-success external">立即购买</a></div>
      </div>
    </div>
    </if>
  </div>
</div>

<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
$(function() {
  wx.ready(function(){wx.hideOptionMenu();});
  var openid = '{$data.user.openid}',
      groupid = '{$wechat.full_group}',
      msg = '';
  $(".sub").click(function(){
    //验证输入
    var name = $("#name").val(),
        phone = $('#phone').val(),
        legally = $("#legally").val(),
        pwd = $("#pwd").val();
    if(name == '' || pwd == '' || legally == ''){
      msg = "姓名、密码、身份证号不能为空";
    }
    if(!/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[0-9])[0-9]{8}$/.test(phone)){
      msg = "手机号码格式不正确!";
    }
    if(/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/.test(legally) == false){
      msg = "身份证号码输入有误!";
    }
    if(msg != ''){
      msgs(msg);msg = '';return false;
    }else{
      //验证手机号是否可用
      $.ajax({
        type:'GET',
        url:'<?php echo U('Wechat/Index/phone');?>'+'&phone='+phone+'&legally='+legally,
        dataType:'json',
        timeout: 1500,
        error: function(){
          $.toast('服务器请求超时，请检查网络...');
        },
        success:function(data){
          if(data.statusCode == '200'){
            post_server(name,pwd, phone, legally,groupid, openid);
          }else{
            $.toast(data.msg);
          }
        }
      });

    }
  });
  function post_server(name,pwd,phone,legally,groupid,openid){
    var postData = 'info={"username":"'+name+'","password":"'+pwd+'","phone":"'+phone+'","legally":"'+legally+'","group":"'+groupid+'","openid":"'+openid+'"}';
    /*提交到服务器**/
    $.ajax({
        type:'POST',
        url:'<?php echo U('Wechat/Index/reg');?>',
        data:postData,
        dataType:'json',
        timeout: 1500,
        error: function(){
          layer.msg('服务器请求超时，请检查网络...');
        },
        success:function(data){
            if(data.statusCode == "200"){
              $.alert('注册成功,等待审核!', function () {
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