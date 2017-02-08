<?php if (!defined('LUB_VERSION')) exit(); ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Managetemplate file="Home/Public/cssjs"/>
<title>用户信息管理  - by LubTMP</title>
</head>

<body>
<div class="container">
<Managetemplate file="Home/Public/menu" nickname="{$userInfo['nickname']}"/>
<!--内容主体区域 start-->
<div class="main row">
<div class="col-md-3">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">会员信息管理</h3>
    </div>
    <div class="list-group"> <a href="#" class="list-group-item active">安全设置</a> <a href="#" class="list-group-item">基本资料</a> <a href="#" class="list-group-item">联系人管理</a> </div>
  </div>
</div>
<div class="col-md-9 hidden-xs"> 
  <!--信息维护-->
  <div id="content" style="width:auto;" class="mt20">
    <div class="uitopg">
      <h3 class="uitopg-title"><span class="left">安全设置</span></h3>
      <div class="userinfo">
        <div class="userinfo-pic"> <img id="avatar_img"  src="/images/def_ava_120.png" /> <a href="#" onclick="account.setAvatar();return false;">修改头像</a> </div>
        <dl class="userinfo-base border0">
          <dt>登录帐号：</dt>
          <dd> <strong><span title="sxw1988@126.com">sxw1988@126.com</span> &nbsp;(<span style="color:#F89727">您已通过认证</span>) </strong> </dd>
          <dt>公司名称：</dt>
          <dd> <strong>鼎盛文化产业投资有限公司&nbsp;</strong> </dd>
          <dt>税号：</dt>
          <dd> <strong>12345678&nbsp;</strong> </dd>
          <dt>注册时间：</dt>
          <dd> 2013-08-23 07:33:45&nbsp; </dd>
        </dl>
      </div>
      <div class="secure-level"> 您当前的帐号安全程度 <span class="col-md-3 progress"> <span class=" row  progress-bar progress-bar-danger" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width: 90%"> <span class="sr-only">80% Complete (danger)</span> </span> </span> 安全级别：<strong>中</strong>继续努力 </div>
      <ul class="secure-status">
        <li>
          <h1 class="useraction-title">登录密码</h1>
          <div class="status"> <span class="finished">已设置</span> <a href="/security_setting/input_password.htm">修改</a> </div>
          <span class="desc">安全性高的密码可以使帐号更安全。建议您定期更换密码，设置一个包含字母，符号或数字中至少两项且长度超过6位的密码。</span> </li>
        <li>
          <h1 class="useraction-title">手机绑定</h1>
          <div class="status"> <span class="finished">已绑定</span> <a href="/security_setting/bindMobile.htm?change=1">更换</a> </div>
          <span class="desc">您已绑定了手机<strong class="orange">186****1216</strong>。<span>[您的手机为安全手机，可以找回密码，但不能用于登录]<a style="padding-left:5px;color: #0066CC" href="/security_setting/add_login_mobile.htm">设置登录手机</a></span></span> </li>
        <li>
          <h1 class="useraction-title">备用邮箱</h1>
          <div class="status"> <span class="finished">已绑定</span> <a href="/security_setting/bindEmail.htm?change=1">更换</a> <a href="javascript:void(0);" id="unbindEmailBtn">解绑</a> </div>
          <span class="desc">您已绑定了邮箱<strong class="orange">sxw****@126.com</strong></span> </li>
        
      </ul>
    </div>
  </div>
</div>
<div> 
  <!--内容主体区域 end--> 
  <!--页脚-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>