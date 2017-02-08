<!DOCTYPE html>
<html>
<head>
	<title>云鹿票务跳转提示</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<link href="http://g.alicdn.com/sj/dpl/1.5.1/css/sui.min.css" rel="stylesheet">
</head>
<style type="text/css">
body{background-color:#efefef ;}
.sui-container{background-color: #FFFFFF;}
.m-t-20{margin-top: 20px;}
.m-t-30{margin-top: 30px;}
.lable-h-25{height: 25px;line-height: 25px;}
.auto-center{margin-left: auto;margin-right: auto;}
.h-450{height: 450px;}
.tip-icon{width: 128px;height: 128px; margin-right: 20px;}
.yes{background: url('./static/web/img/yes.png') no-repeat; float: left; }
.no{background: url('./static/web/img/no.png') no-repeat; }
.content{padding: 110px 0 0 110px;}
.msg{float: left;padding: 20px 0 0 10px;}
</style>
<body>
  <div class="sui-container m-t-30 h-450">
    <span class="span8 offset2 m-t-20 content">
   		
		<?php if(isset($message)) {?>
		<div class="tip-icon yes"></div>
		<div class="msg">
   		<h1><?php echo($message); ?></h1>
		<?php }else{?>
		<div class="tip-icon no"></div>
		<div class="msg">
   		<h1><?php echo($error); ?></h1>
		<?php }?>
   		
   		<p>页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></p>
   		</div>
    </span>
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
</html
