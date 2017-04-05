<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>订单支付</title>
    <link rel="stylesheet" href="//g.alicdn.com/sui/sui3/0.0.18/css/sui.min.css">
    <style type="text/css" media="screen">
      .qr{padding: 25px;display: none;}
    </style>
  </head>
  <body>
    
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
      <h1>请选择支付方式</h1>
        <button type="button" class="btn btn-primary btn-xl" id="wxpay">微信支付</button>
        <button type="button" class="btn btn-default btn-xl" id="alipay">支付宝支付</button>
      </div>

      <div class="col-md-6 col-md-offset-3 qr">
        
        <div id="qr_view"></div>
        <p><strong>支付单号:</strong><span id="paysn"></span></p>
      </div>
    </div>
    <script type="text/javascript" src="//g.alicdn.com/sj/lib/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="//g.alicdn.com/sui/sui3/0.0.18/js/sui.min.js"></script>
    <script type="text/javascript" src="static/js/qrcode.min.js"></script>
    <script>
      $('#wxpay').click(function(){
          var sn = "{$sn}",
            postData = 'data={"sn":'+sn+',"paytype":"wxpay"}';
          $.ajax({
            type:'POST',
            url:'<?php echo U('Api/Index/api_payment');?>',
            data:postData,
            dataType:'json',
            timeout: 3500,
            error: function(){
                $.toast({
                  text: '服务器请求超时，请检查网络...',
                  type: 'danger',
                  position: 'center'
                });
            },
            success:function(data){
              if(data.code == '200'){
                $('.qr').css('display','block');
                $('#paysn').html(data.info.prepay_id);
                $('#qr_view').empty();
                if(data.info.code_url){
                  qrView(data.info.code_url);
                }else{
                  $.toast({
                    text: data.info,type: 'danger', position: 'center'
                  });
                }
              }else{
                $.toast({
                  text: data.msg,
                  type: 'danger',
                  position: 'center',
                });
              } 
            }
          });
      });
      $('#alipay').click(function(){
        $.toast({
          text: '亲,非常抱歉暂不支持支付宝支付...',
          type: 'success',
          position: 'center'
        });
      });
      function qrView(qrdata){
        var qrcode = new QRCode('qr_view', { 
          text: qrdata, 
          width: 256, 
          height: 256, 
          colorDark : '#000000', 
          colorLight : '#ffffff', 
          correctLevel : QRCode.CorrectLevel.H 
        });
      }
      function getPayNotify(){

      }
    </script>
  </body>
</html>