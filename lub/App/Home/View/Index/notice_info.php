<?php if (!defined('LUB_VERSION')) exit(); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<Managetemplate file="Home/Public/cssjs"/>


</head>
<body>
<div class="container">
  <Managetemplate file="Home/Public/menu"/>
  <div class="main row">
    <div class="panel panel-default">
      <div class="panel-body">
      <h1>{$info['title']}<small>   {$info.createtime|date="Y-m-d H:i:s",###}    操作员:{$info['user_id']|userName}</small></h1>
      <hr>
      <p>{$info['content']}</p>
      </div>
    </div>
    
</div>
</div>
<!--页脚-->
<Managetemplate file="Home/Public/footer"/>
<!--页脚-->
</body>
</html>