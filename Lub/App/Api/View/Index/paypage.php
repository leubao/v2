<!DOCTYPE html>
<html lang="zh-cn">
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
      <div class="col-md-6 col-md-offset-4">
      <if condition="$status eq '1'">
        <h1>订单已支付完成</h1>
        <a href="javascript:window.opener=null;window.open('','_self');window.close();" class="btn btn-primary btn-xl">关闭支付窗口</a>
      <else />
        <h1>请选择支付方式</h1>
        <button type="button" class="btn btn-primary btn-xl" id="wxpay">微信支付</button>
        <!--
        <button type="button" class="btn btn-default btn-xl" id="alipay">支付宝支付</button>
        -->
      </if>
      </div>
      <div class="col-md-6 col-md-offset-3 qr">
        <div id="qr_view"></div>
        <p><strong>状态:</strong><span id="paymsg"></span></p>
      </div>
    </div>
    <script type="text/javascript" src="//g.alicdn.com/sj/lib/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="//g.alicdn.com/sui/sui3/0.0.18/js/sui.min.js"></script>
    <script type="text/javascript" src="http://static.leubao.com/qrcode.min.js"></script>
    <script>
      $('#wxpay').click(function(){
          var sn = "{$sn}",
            pid = "{$pid}",
            postData = 'data={"sn":'+sn+',"paytype":"wxpay","pid":"'+pid+'"}';
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
                if(data.info){
                  $('.qr').css('display','block');
                  $('#paymsg').html(data.msg);
                  $('#qr_view').empty();
                  qrView(data.info);
                  //setInterval("getPayNotify("+postData+")",3000);
                  getPayNotify(sn,'wxpay',pid);
                }else{
                  $.toast({
                    text: data.msg,type: 'danger', position: 'center'
                  });
                }
              }else{
                $.toast({
                  text: data.msg,type: 'danger',position: 'center'
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
          width: 200, 
          height: 200, 
          colorDark : '#000000', 
          colorLight : '#ffffff', 
          correctLevel : QRCode.CorrectLevel.H 
        });
      }
      function getPayNotify(sn,type,pid){
        $postData = '{"sn":'+sn+',"paytype":"wxpay","pid":"'+pid+'"}';
        // 建立websocket链接
        ws = new WebSocket("ws://www.yx513.net:7272");
        // 当websocket连接建立成功时
        ws.onopen = function() {
            ws.send($postData);
            console.log('链接成功'); 
        };
        // 当收到服务端的消息时
        ws.onmessage = function(e) {
            // e.data 是服务端发来的数据
            $('#paymsg').html(e.data);
            console.log('收到来自服务器消息'+e.data); 
            //页面跳转
            //console.log(e.data);
        };
        // 当websocket关闭时
        ws.onclose = function(e) {
            $('#paymsg').html(e.data);
            console.log('收到来自服务器关闭'+e.data); 
            $.ajax({
              type:'POST',
              url:'<?php echo U('Api/Index/query_pay_order',['type'=>'query']);?>',
              data:'data='+$postData,
              dataType:'json',
              timeout: 3500,
              success:function(data){
                if(data.code == '200'){
                    $('#qr_view').empty();
                }else{
                  $('#qr_view').empty();
                  
                } 
              }
           });
           console.log(e.data);
        };
        /* 当出现错误时
        ws.onerror = function() {
            alert("出现错误");
        };*/
      }
    </script>

  </body>
</html>