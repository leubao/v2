<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<title>fullPage.js制作魅蓝note2页面演示_dowebok</title>
<link href="http://g.alicdn.com/sj/dpl/1.5.1/css/sui.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="static/css/jquery.fullPage.css">
<link rel="stylesheet" type="text/css" href="static/web/css/style.css">
<script type="text/javascript" src="http://g.alicdn.com/sj/lib/jquery/dist/jquery.min.js"></script>
</head>

<body>
<div class="ml-nav">
	<div class="center-wrap clearfix">
		<h2 class="ml-logo">魅蓝note2</h2>
		<p class="ml-link">
			<a href="javascript:">概述</a>
			<a href="javascript:">参数</a>
			<a href="javascript:">图库</a>
			<a href="javascript:">机型比较</a>
			<a href="javascript:">意外险</a>
		</p>
		<a class="buy-now" href="javascript:">立即购买</a>
	</div>
</div>

<div id="dowebok">
	<div class="section header fp-auto-height">
		<div class="center-wrap clearfix">
			<h1 class="meizu-logo"><a href="http://www.dowebok.com/">MEIZU</a></h1>
			<ul class="nav">
				<li><a href="javascript:">在线商店</a></li>
				<li><a href="javascript:">产品</a></li>
				<li><a href="javascript:">专卖店</a></li>
				<li><a href="javascript:">Flyme</a></li>
				<li><a href="javascript:">服务</a></li>
				<li><a href="javascript:">社区</a></li>
			</ul>
			<div class="login">
				<a href="javascript:">注册</a>|<a href="javascript:">登陆</a>
			</div>
		</div>
	</div>

	<div class="section banner active">
		<div class="center-wrap">
			<div class="desc banner-desc">
				<h1>青年良品</h1>
				<p>追求极致，轻易不说完美&mdash;&mdash;半年时间内，我们便更新了魅蓝 note2。它从各个方面都更进一步；无论是 R 角弧线、还是更佳的相机算法、甚或是全新的 mBack 键……如此多的改进，只为给你一台更加完美的「青年良品」。</p>
			</div>
		</div>
	</div>

	<div class="section thin">
		<div class="center-wrap">
			<div class="desc thin-desc">
				<h1>多彩纤薄机身</h1>
				<p>魅蓝 note2 延续了多彩配色风格，并首次引入魅族 MX 系列的金属灰配色，深邃，且富有质感。魅蓝 note2 适于年轻个性，也完美契合轻熟商务。仅 149g 的重量，薄至 8.7mm 的厚度，更性感的 R 角弧度让单手握持毫无繁重。</p>
			</div>
			<div class="thin-img">
				<img alt="thin" src="images/phone-blue.png" class="thin-img1">
				<img alt="thin" src="images/phone-white.png" class="thin-img2">
			</div>
		</div>
	</div>
	
	<script src="static/web/js/selectUi.js" type="text/javascript" charset="utf-8" async defer></script>
	<script src='static/web/js/lq.datetimepick.js' type='text/javascript'></script>
	<style type="text/css" media="screen">
	body{font-family:'microsoft yahei';}
	em{font-style:normal;font-size:14px;}
	.form-group {position: relative;width:140px;}
	.form-group-txt{height:32px;line-height:32px;padding:0 10px;}
	.form-group-select {/*padding-left: 1px;*/}
	.form-control,
	.simulation-input {
		width: 100%;
		line-height: 16px;
		font-size: 12px;
		color: #4b555b;
		background: none;
		outline: none;
		border: 1px solid #d3dcdd;
		background-color: #fff;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		-ms-box-sizing: border-box;
		box-sizing: border-box;
		margin: 0 -1px;
		padding: 7px 8px;
		*padding-left: 0;
		*padding-right: 0;
		*text-indent: 8px;
		*float: left;
		transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;
	}
	.float-left{float:left;}

	</style>
	<script>
$(function (){

	$("#calendar").lqdatetimepicker({
			css : 'datetime-day',
			dateType : 'D',
			selectback : function(){
			}
		});
});
	</script>
	<!--门票预定 s-->
	<div class="section">
		<div class="left">
			<div id="calendar"></div>
		</div>
		<div class="right"></div>
	</div>
	<!--门票预定 e-->

	<div class="section">
        <div class="slide"><h3>第二屏的第一屏</h3></div>
        <div class="slide"><h3>第二屏的第二屏</h3></div>
        <div class="slide"><h3>第二屏的第三屏</h3></div>
    </div>

	

	<div class="section flyme">
		<div class="center-wrap">
			<div class="desc flyme-desc">
				<h1>基于 Android 5.1 内核的全新 Flyme 4.5 系列</h1>
				<p>Android 5.1 的优秀特性需结合 64 位处理器才能最大化释放。在全新的 Flyme 4.5 系统上，可切实感受到最先进的 Android 内存管控机制与电池续航控制。</p>
			</div>
		</div>
		<div class="flyme-img">
			<img class="flyme-pic1" src="images/p1.png">
			<img class="flyme-pic2" src="images/p2.png">
			<img class="flyme-pic3" src="images/p3.png">
		</div>
	</div>
	<!--页脚-->
	<div class="section footer fp-auto-height">
		<div class="footer-link">
			<div class="center-wrap">
				<div class="clearfix">
					<dl>
						<dt>在线商店</dt>
						<dd><a href="javascript:">MX4 Pro</a></dd>
						<dd><a href="javascript:">MX4</a></dd>
						<dd><a href="javascript:">耳机</a></dd>
						<dd><a href="javascript:">保护壳</a></dd>
					</dl>

					<dl>
						<dt>Flyme OS</dt>
						<dd><a href="javascript:">云服务</a></dd>
						<dd><a href="javascript:">固件下载</a></dd>
						<dd><a href="javascript:">软件商店</a></dd>
						<dd><a href="javascript:">查找手机</a></dd>
					</dl>

					<dl>
						<dt>关于我们</dt>
						<dd><a href="javascript:">关于魅族</a></dd>
						<dd><a href="javascript:">加入我们</a></dd>
						<dd><a href="javascript:">联系我们</a></dd>
						<dd><a href="javascript:">法律声明</a></dd>
					</dl>

					<dl>
						<dt>关注我们</dt>
						<dd><a href="javascript:">新浪微博</a></dd>
						<dd><a href="javascript:">腾讯微博</a></dd>
						<dd><a href="javascript:">QQ空间</a></dd>
						<dd><a href="javascript:">官方微信</a></dd>
					</dl>

					<dl>
						<dt>客服热线</dt>
						<dd>400-788-3333</dd>
					</dl>
				</div>
			</div>
		</div>
		
		<div class="copyright center-wrap">
			<p>©2015 Meizu Telecom Equipment Co., Ltd. All rights reserved. 备案号：粤ICP备13003602号-2 经营许可证编号：粤B2-20130198</p>
		</div>
	</div>
</div>


<script src="static/js/jquery.fullPage.min.js"></script>

  <script type="text/javascript" src="http://g.alicdn.com/sj/dpl/1.5.1/js/sui.min.js"></script>
<script>
$(function(){
	var $mlNav = $('.ml-nav');
	$('#dowebok').fullpage({
		verticalCentered: !1,
		navigation: !0,
		onLeave: function(index, nextIndex, direction){
			if(index == 2 && direction == 'up'){
				$mlNav.animate({
					top: 80
				}, 680);
			} else if(index == 1 && direction == 'down') {
				$mlNav.animate({
					top: 0
				}, 400);
			} else if(index == 3 && direction == 'up') {
				$mlNav.animate({
					top: 0
				}, 500);
			} else {
				$mlNav.animate({
					top: -66
				}, 400);
			}
		}
	});
});
</script>

</body>
</html>