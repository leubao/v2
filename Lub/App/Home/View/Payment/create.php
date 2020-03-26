<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>充值中心</title>
  <link rel="stylesheet" href="{$config_siteurl}static/layui/css/layui.css">
</head>
<style>
body{
  background: #f2f2f2;
}
.footer{border-top:1px solid #E8E8E8; padding:15px 0;font-family:tahoma,Arial;font-size:12px;color:#999;line-height:22px;text-align:center; margin-top: 100px}
.footer a,.footer a:hover{color:#999}  
#qrdiv{
  display: none;
  padding: 20px
}
</style>
<body>
<div style="background-color: #393D49; margin-bottom: 15px">
  <div class="layui-main">
    <ul class="layui-nav">
        <li class="layui-nav-item"><a href="/">首页</a></li>
    </ul>
  </div>
</div>
<div class="layui-card layui-main">
  <div class="layui-card-header">充值中心</div>
  <div class="layui-card-body lub-content">
    <form class="layui-form" action="" lay-filter="recharge-form">
      <div class="layui-form-item">
        <label class="layui-form-label">当前商户</label>
        <div class="layui-form-mid layui-word-aux"><b>{$crm.name}</b></div>
      </div>
      <div class="layui-form-item">
        <label class="layui-form-label">当前余额</label>
        <div class="layui-form-mid layui-word-aux">
          ￥ {$crm.cash} 元
        </div>
      </div>
      <div class="layui-form-item">
        <label class="layui-form-label">充值金额</label>
        <div class="layui-input-inline">
          <input type="text" name="money" lay-verify="required|number|money" placeholder="0.00" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">请输入整数金额</div>
      </div>

      <div class="layui-form-item">
        <label class="layui-form-label">支付方式</label>
        <div class="layui-input-block">
          <input type="radio" name="type" value="5" title="微信支付" checked="">
          <input type="radio" name="type" value="4" title="支付宝支付" disabled>
        </div>
      </div>
      <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
          <textarea name="remark" placeholder="请输入内容" class="layui-textarea"></textarea>
        </div>
      </div>
      <input type="hidden" name="crm_id" value="{$crm.id}">      
      <div class="layui-form-item">
        <div class="layui-input-block">
          <button class="layui-btn" lay-submit="" lay-filter="lub-recharge">立即提交</button>
          <button type="reset" lay-filter="lub-reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
      </div>
    </form>
  </div>
  <div id="qrdiv"></div>
</div>
<footer class="footer">
    <p>Copyright &copy;2019 leubao.com All Rights Reserved. 
    </p>
</footer>
<script src="{$config_siteurl}static/layui/layui.js"></script>
<script src="{$config_siteurl}static/js/qrcode.min.js"></script>
<script>
layui.use(['form','element'], function(){
  var $ = layui.$
  ,layer = layui.layer
  ,element = layui.element
  ,form = layui.form;
  form.render(null, 'recharge-form');
  
  /* 监听提交 */
  form.on('submit(lub-recharge)', function(data){
    $('#qrdiv').html('');
    $.ajax({
      url: '{:U('Home/Payment/create');}',
      type: 'POST',
      dataType: 'json',
      data: data.field,
      success: function (ret) {
        if(ret.status == 200){
          new QRCode("qrdiv", {
              text: ret.qr,
              width: 200,
              height: 200,
              colorLight: "#ffffff",
              correctLevel: QRCode.CorrectLevel.H
          });
          layer.open({
            title: '付款二维码',
            type: 1,
            skin: 'layui-layer-demo', //样式类名
            closeBtn: 1, //不显示关闭按钮
            anim: 2,
            shadeClose: false, //开启遮罩关闭
            content: $('#qrdiv')
          });
        }else{
          layer.msg(ret.msg);
        }
      }
    })
    .done(function(ret) {
      if(ret.status == 200){
        var query = setInterval(function () {
          $.ajax({
            url: '{:U('Home/Payment/query')}',
            type: 'GET',
            dataType: 'json',
            data: {sn: ret.sn},
            success: function (res) {
              if(res.status == 200) {
                clearInterval(query);
                //跳转回去
                window.location.href='{:U('Home/report/index')}';
              }else if(res.status == 300) {
                console.log(res.msg)
              }else{
                layer.msg(res.msg);
                document.execCommand('Refresh');
              }
            }
          })
        }, 5000);
      }
      
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });
    
    //提交到后台
    //返回支付二维码
    return false;
  });
});
</script>
</body>
</html>