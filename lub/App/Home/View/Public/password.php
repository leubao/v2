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
</style>
<title>账户登录  - by LubTMP</title>
</head>

<body>
<div class="container">
  <div class="col-md-4 col-xs-12 col-md-offset-4">
    <form class="form-signin" role="form" action="{:U('Home/Public/password');}" method="post">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <h3 class="panel-title">输入绑定的手机号</h3>
			</div>
			<div class="panel-body">
				<input type="text" class="form-control" name="phone" placeholder="手机号码" required autofocus>
			</div>
			<div class="panel-body">
				<button class="btn btn-lg btn-primary btn-block" type="submit">提  交</button>
			</div>	
		</div>
    </form>
  </div>
</div>
</body>
</html>