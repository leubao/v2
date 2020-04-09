<?php if (!defined('LUB_VERSION')) exit(); ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Managetemplate file="Home/Public/cssjs"/>
</head>

<body>
<div class="container">
<Managetemplate file="Home/Public/menu"/>
<include file="Home/Public/menu" nickname="{$userInfo['nickname']}"/> 
<!--内容主体区域 start-->
<div class="main row">
<!--面包屑导航-->
<ol class="breadcrumb">
  <li><a href="{:U('Home/Index/index');}">首页</a></li>
  <li><a href="{:U('Home/Index/product');}">售票</a></li>
  <li class="active">{$pid|product_name}</li>
</ol>
<div class="row">
  <div class="col-md-2">
    <div class="panel panel-success">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-cog"></span>设置</h3>
      </div>
      <div class="list-group"> 
        <a href="" class="list-group-item">常用联系人设置</a> 
      </div>
    </div>
  </div>
  <div class="col-md-10">
    <div class="table-responsive" id="content_show">
      <form action="{:U('Home/Set/contact_add')}" method="post" role="form">
        <div class="form-group">
          <input type="text" name="name" class="form-control" id="contact" placeholder="联系人">
        </div>
        <div class="form-group">
          <input type="text" name="phone" class="form-control" id="phone" placeholder="手机号">
        </div>
        <div class="form-group">
          状态：<input type="radio" name="status"  value="1" checked>启用
              <input type="radio" name="status"  value="0">不启用
 
        </div>
        <button type="submit" class="btn btn-default">提交</button>
      </form>
    </div>
  </div>
</div>
<div> 
  <!--内容主体区域 end--> 
  <!--弹出窗口 end-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>
<script type="text/javascript">
$(function(){
  $('.list-group a').click(function(){
    
  })
})  
</script>