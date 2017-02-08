<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <title>Login Page | Amaze UI Example</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no">
  <meta name="renderer" content="webkit">
  <meta http-equiv="Cache-Control" content="no-siteapp" />
  <link rel="alternate icon" type="image/png" href="assets/i/favicon.png">
  <link rel="stylesheet" href="{$config_siteurl}static/v2/css/amazeui.min.css"/>
  <style>
    .header {
      text-align: center;
    }
    .header h1 {
      font-size: 200%;
      color: #333;
      margin-top: 30px;
    }
    .header p {
      font-size: 14px;
    }
  </style>
</head>
<body>
<div class="header">
  <div class="am-g">
    <h1>Web ide</h1>
    <p>Integrated Development Environment<br/>代码编辑，代码生成，界面设计，调试，编译</p>
  </div>
  <hr />
</div>
<div class="am-g">
  <div class="am-u-md-8 am-u-sm-centered">
    <form class="am-form" method="post" action="{:U('Home/Public/tologin');}">
      <fieldset class="am-form-set">
        <input type="text" name="username" placeholder="用户名" required autofocus>
        <input type="password" name="password" placeholder="密码" required >
      </fieldset>
      <button type="submit" class="am-btn am-btn-primary am-btn-block">登 录</button>
    </form>
  </div>
</div>
</body>
</html>