<html>
<head>
  <title>支付宝支付</title>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
</head>
<body style="margin: 0px;">
<iframe src="{:U('Wechat/Index/alipay',array('sn'=>$data['sn']))}" frameborder="0"></iframe>
<script type="text/javascript" src="http://wx.12301.cc/public/js/jquery-2.0.3.min.js"></script>
<script>
    $(document).ready(function(){
        var height = $( window ).height(),
            width  = $( window ).width();
        $("iframe").css({height:height, width:width});
    });
</script>

</body></html>