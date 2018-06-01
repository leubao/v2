<?php if (!defined('LUB_VERSION')) exit(); ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Managetemplate file="Home/Public/cssjs"/>
<style type="text/css">
body {
	padding-top: 40px;
	padding-bottom: 40px;
	background-color: #eee;
}
.form-signin {
	max-width: 330px;
	padding: 15px;
	margin: 0 auto;
}
.form-signin .form-signin-heading, .form-signin .checkbox {
	margin-bottom: 10px;
}
.form-signin .checkbox {
	font-weight: normal;
}
.form-signin .form-control {
	position: relative;
	height: auto;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	padding: 10px;
	font-size: 16px;
}
.form-signin .form-control:focus {
	z-index: 2;
}
.form-signin input[type="email"] {
	margin-bottom: -1px;
	border-bottom-right-radius: 0;
	border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
	margin-bottom: 10px;
	border-top-left-radius: 0;
	border-top-right-radius: 0;
}
.title{
	text-align: center;
}
</style>
<title>账户登录  - by LubTMP</title>
</head>

<body>
<div class="container">
  <div class="col-md-4 col-xs-12 col-md-offset-4">
  	<h3 class="title">清龙瀑布渠道预订系统</h3>
    <form class="form-signin" role="form" action="{:U('Home/Public/tologin');}" method="post">
  	  <input type="hidden" name="type" value="1">
      <input type="text" class="form-control" name="username" placeholder="用户名或手机号码" required autofocus>
      <input type="password" class="form-control" name="password" placeholder="Password" required>
      <div class="checkbox">
        <label>
          <input type="checkbox" name="remember" value="1">
          记住密码 </label>
          <a href="{:U('Home/Public/password');}" class="btn  btn-link  pull-right  sk_fg_24">忘记密码</a>
      </div>
      <button class="btn btn-lg btn-primary btn-block" type="submit">登 录</button>

    </form>
  </div>
</div>
</body>
</html>