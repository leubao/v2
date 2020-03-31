<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>云鹿票券-电子门票</title>
  <link rel="stylesheet" href="../static/layui/css/layui.css">
  <style type="text/css" media="screen">
 	html, body {background-color: #eee;}
  .alone-version-desc{position: relative; margin-top: 30px; padding: 40px 50px 50px; border-radius: 0; background-color: #fff; text-align: center; transition: all .3s; -webkit-transition: all .3s;}
	.alone-version-desc h2{padding-bottom: 15px; font-size: 18px;}
	.alone-badge{position: relative; display: inline-block; border: 1px solid #e6e6e6; line-height: 28px; padding: 15px; border-radius: 2px;}
	.alone-version-desc ul{height: 220px; margin-top: 15px; text-align: left;}
	.alone-buy{position: relative; text-align: center;}
	.alone-buy .layui-btn{width: 100%;}	
	#qrcode {}
	.layui-text ul li{list-style-type:none;}
	.success{border-top: 5px solid #09bb07;}
	.error{border-top: 5px solid #ddd;}
  </style>
</head>
<body>
 <div class="layui-container">
  <div class="layui-row">
	<div class="alone-version-desc layui-text {$class}">
    <h2>{$ticket['base']['product_name']}</h2>
    <?php if($class == "success"){ ?>
    <volist name="ticket['info']" id="vo">
        <p>
          <span class="alone-badge" id="qrcode"></span>
        </p>
        <i class="layui-badge" style="position: absolute; right: 30px; top: 30px;">{$vo.number}人</i>
        <ul>
          <li>日期 : {$ticket['base']['plantime']}</li>
          <li>票类 : {$vo.priceName}</li>
          <li>人数 : {$vo.number}</li>
          <li>单号 : {$vo.sn}</li>
          <li>可入园时间 : {$ticket['base']['starttime']} - {$ticket['base']['endtime']}</li>
          <?php if(!empty($ticket['seatList'])){ ?>
          <li>座位号: {$ticket['seatList']}</li>
          <?php } ?>
        </ul>
      </div>
    </volist>
    <?php }else{ ?>
    <ul>
      <?php if(!empty($ticket['seatList'])){ ?>
      <li>日期 : {$ticket['base']['plantime']}</li>
      <li>可入园时间 : {$ticket['base']['starttime']} - {$ticket['base']['endtime']}</li>
      <li>座位号: {$ticket['seatList']}</li>
      <?php } ?>
    </ul>
    <p>
      <span class="layui-badge layui-bg-black">{$ticket['message']}</span>
    </p>
    <?php } ?>
  </div>
 </div>
<script src="../static/layui/layui.js"></script>
<script src="../static/js/qrcode.min.js"></script>
<script>
var qrcode = new QRCode("qrcode", {
    text: "{$ticket.sns}",
    width: 170,
    height: 170,
    colorDark : "#000000",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
});
</script> 

</body>
</html>