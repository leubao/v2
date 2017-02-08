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
  <div class="main">
  	<div class="panel panel-default">
  		<table class="table table-bordered table-condensed">
			<tr><th width="120px">用户名:</th><td>{$data.username}</td></tr>
			<tr><th width="120px">姓  名:</th><td>{$data.nickname}</td></tr>
			<tr><th width="120px">电话:</th><td>{$data.phone}</td></tr>
			<tr><th width="120px">Email:</th><td>{$data.email}</td></tr>
			<tr><th width="120px">商户:</th><td>{$data.item_id|itemName}</td></tr>
			<tr><th width="120px">产品:</th><td>{$data.product|product_name}</td></tr>
			<tr><th width="120px">支付方式:</th><td><if condition="$data['is_pay'] eq 1">授信额
                              <elseif condition="$data['is_pay'] eq 2"/>网银支付
                              <else />授信+网银</if></td></tr>
            <tr><th width="120px">专属链:</th><td><div class="row col-xs-6"><input type="text" class="form-control" size="10" value="{$url}"></div></td></tr>

		</table>
	</div>
  </div>
  <!--页脚-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>