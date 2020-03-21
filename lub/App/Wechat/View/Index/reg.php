<Managetemplate file="Wechat/Public/header"/>
<style type="text/css" media="screen">
.codecall{
  display: flex;
  justify-content:center;
  align-items:center;
}
 .btn {
  border: 1px solid #b5272d;
  background: #b5272d;
  color: #fff;
}
.send_code {
  text-align: center;
  vertical-align: middle;

} 
.ct{
  border: 1px solid #d0d0d0;background: #9d9d9d;
}
</style>
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
              <div class="item-title label">验证码</div>
              <div class="item-input codecall">
                <input type="text" id="code" placeholder="验证码">
                <button id="second" type="button" class="button btn send_code"/>获取验证码</button>
              </div>
            </div>
          </div>
        </li>
        
        <li>
          <div class="item-content">
            <div class="item-inner">
              <div class="item-title label">行业选择</div>
              <div class="item-input">
                <input type="text" id="industry" value="" placeholder="行业选择">
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
        <div class="col-100"><a href="#" class="button button-big button-fill button-success sub">立即注册</a></div>
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
      groupid = '{$wechat.full_group}',
      msg = '',
      code = '',
      name = '',
      phone = '';
  $("#second").click(function(event){
    event.preventDefault(); 
    code = $("#code").val();
    name = $("#name").val();
    phone = $('#phone').val();
    if(name == '' || phone == ''){
      msg = "姓名、密码、行业不能为空";
    }
    if(!/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[0-9])[0-9]{8}$/.test(phone)){
      msg = "手机号码格式不正确!";
    }
    if(msg != ''){
      msgs(msg);msg = '';return false;
    }else{
      $(this).attr('disabled',true).text('发送中..');
      $.ajax({
        type:'GET',
        url:'<?php echo U('Wechat/Index/send_code');?>'+'&phone='+phone,
        dataType:'json',
        timeout: 1500,
        error: function(){
          $.toast('服务器请求超时，请检查网络...');
        },
        success:function(data){
          if(data.statusCode == '200'){
            countDown();
          }else{
            $('#second').removeAttr('disabled').text('获取验证码');
            $.toast(data.msg);
          }
        }
      });
    }
  });
  $(".sub").click(function(event){
    event.preventDefault(); 
    //验证输入
    var pwd = $("#pwd").val(),
        industry = $("#industry").val();
      code = $("#code").val();
      name = $("#name").val();
      phone = $('#phone').val();
    if(name == '' || phone == ''){
      msg = "姓名、密码、行业不能为空";
    }
    if(code == ''){
      msg = '验证码不能为空';
    }
    if(!/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[0-9])[0-9]{8}$/.test(phone)){
      msg = "手机号码格式不正确!";
    }
    if(msg != ''){
      msgs(msg);msg = '';return false;
    }else{
      //验证手机号是否可用
      $(this).addClass('disabled').text('提交中..');
      post_server(name,pwd, phone, code, groupid, openid,industry);
    }
  });
  

  function post_server(name,pwd,phone,code,groupid,openid,industry){
    var postData = 'info={"username":"'+name+'","password":"'+pwd+'","phone":"'+phone+'","code":"'+code+'","group":"'+groupid+'","industry":"'+industry+'","openid":"'+openid+'"}';
    /*提交到服务器**/
    $.ajax({
        type:'POST',
        url:"<?php echo U('Wechat/Index/reg',array('type'=>$type));?>",
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
              $('.sub').removeClass('disabled').text('立即注册');
              $.toast(data.msg);
            }
        }
    });
  }

  $("#industry").picker({
    toolbarTemplate: '<header class="bar bar-nav">\
    <button class="button button-link pull-right close-picker">确定</button>\
    <h1 class="title">行业选择</h1>\
    </header>',
    cols: [
      {
        textAlign: 'center',
        values: ['导游', '运输', '餐饮', '商户', '住宿', '其它']
      }
    ]
  });
});
function countDown() {
  var n = 60;
  var inta = setInterval(function(){
      $('#second').addClass('ct').text(n+"s重新发送");
      n--;
      if (n < -1) {
          // 清除定时器
          clearInterval(inta);
          $('#second').removeClass('ct').removeAttr('disabled').text('获取验证码');
          n = 60;
      }
  },1000)
}
</script>
</body>
</html>