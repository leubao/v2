<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>账号登录</title>
  <link rel="stylesheet" href="../static/layui/css/layui.css">
</head>
<body>
<style>
body{ background: #FFF; padding-top: 100px}
/*顶部活动票样式*/

</style>
<div class="layui-container"> 
  <form class="layui-form" action="">
  <div class="layui-form-item">
    <input type="text" name="username" required  lay-verify="required" placeholder="请输入用户名" autocomplete="off" class="layui-input">
  </div>
  <div class="layui-form-item">
    <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
  </div>
  <div class="layui-form-item">
      <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="login">立即提交</button>
  </div>
</form>
</div>
<script src="../static/layui/layui.js"></script>
<script>
layui.use(['form','layer'], function(){
  var $ = layui.$
  ,form = layui.form
  ,layer= layui.layer;
  //监听提交
  form.on('submit(login)', function(data){
    $.ajax({
      url: '{:U('wechat/scenic/login');}',
      type: 'POST',
      dataType: 'json',
      data: data.field,
      success:function(res){
        if(res.status){
          window.location.href = res.data.url;
          layer.msg('登录成功~');
        }else{
          layer.msg(res.msg);
        }
      }
    })
    return false;
  });
});
</script>
</body>
</html>