<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>云鹿票务跳转提示</title>
  <link rel="stylesheet" href="{$config_siteurl}static/layui/css/layui.css">
</head>
<style>
.statusBox{ text-align: center; padding: 100px 0 50px; background: #fff}
.title {font-size: 24px; color: rgba(0,0,0,.85);font-weight: 500;line-height: 32px;margin-bottom: 16px;} 
.description {font-size: 14px;line-height: 22px;color: rgba(0,0,0,.45);margin-bottom: 24px;}
.extra { background: #fafafa;padding: 24px;border-radius: 2px;text-align: left; margin: 0 auto 30px; width: 70%}
.extra_t{font-size: 16px;color: rgba(0, 0, 0, 0.85); font-weight: 500;margin-bottom: 16px;}
.extra_d{font-size: 15px;line-height: 1.5; color: rgba(0,0,0,.65);margin-top: 16px;}
.extra_a{color: #1890ff; margin-left: 10px}
</style>
<body>
<div class="statusBox">
  <!-- 提交成功 -->
  <?php if(isset($message)) {?>
  <div class="success">
    <i class="layui-icon layui-icon-ok-circle" style="font-size: 100px; color: #5FB878;"></i>  
    <div class="title"><?php echo($message); ?></div>
    <div class="description">页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></div>
  </div>
  <?php }else{?> 

  <!-- 提交失败 -->
  <div class="fail">
    <i class="layui-icon layui-icon-close-fill" style="font-size: 100px; color: #f5222d;"></i>  
    <div class="title"><?php echo($error); ?></div>
    <div class="description">页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></div>
  </div> 
  <?php }?>
  <!--  -->
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
