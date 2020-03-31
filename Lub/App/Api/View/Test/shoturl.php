<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>云鹿票券-测试系统-短链接获取</title>
  <link rel="stylesheet" href="../static/layui/css/layui.css">
  <style type="text/css" media="screen">
  html, body {background-color: #eee;}
  .alone-version-desc{border-top: 5px solid #e64340;position: relative; margin-top: 5px; padding: 40px 50px 50px; border-radius: 0; background-color: #fff; text-align: center; transition: all .3s; -webkit-transition: all .3s;}
  .alone-version-desc h2{padding-bottom: 15px; font-size: 18px;}
  .alone-badge{position: relative; display: inline-block; border: 1px solid #e6e6e6; line-height: 28px; padding: 15px; border-radius: 2px;}
  .alone-version-desc ul{height: 220px; margin-top: 15px; text-align: left;}
  .alone-buy{position: relative; text-align: center;}
  .alone-buy .layui-btn{width: 100%;} 
  #qrcode {}
  .layui-text ul li{list-style-type:none;}
  .mt10{margin-top: 10px;}
  </style>
</head>
<body>
 <div class="layui-container">
  
  <div class="layui-row">
  <blockquote class="layui-elem-quote layui-quote-nm mt10">
    <a href="{:U('Api/test/index')}">回到首页</a>
  </blockquote>
  <div class="alone-version-desc layui-text">
    <form class="layui-form" action="" lay-filter="myform">
      <div class="layui-form-item">
        <div class="layui-inline">
          <label class="layui-form-label">转换URL</label>
          <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="sn" id="sn" placeholder="URL" lay-verify="required|number|sn" autocomplete="off" class="layui-input">
          </div>
        </div>
        <input type="hidden" name="type" value="sn">
        <div class="layui-inline">
          <div class="layui-input-inline" style="width: 60px;">
            <button class="layui-btn" lay-submit="" lay-filter="google">获取</button>
          </div>
        </div>
        <div class="layui-inline">
          <div class="layui-input-inline" style="width: 60px;">
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
          </div>
        </div>
      </div>
    </form>
    <div class="layui-form-item layui-form-text">
      <div class="layui-input-block">
        <textarea name="desc" id="content" placeholder="请输入内容" class="layui-textarea"></textarea>
      </div>
    </div>
  </div>
 </div>
<script src="../static/layui/layui.js"></script>
<script>
layui.use(['form'], function(){
  var form = layui.form;
  var $ = layui.$;
  //表单
  form.verify({
    sn: function(value){
      if(value.length < 7){
        return '订单号长度有误';
      }
    }
  });
  //监听提交
  form.on('submit(google)', function(data){
    $.ajax({
      type: "POST",
      url: "{:U('Api/test/shoturl');}",
      async:false,
      data: data.field,
      dataType: "json",
      success: function(res){
        if(res.status){
          $("#content").val(res.data);
        }else{
          layer.msg(res.msg);
        }
      }
    });
    return false;
  });
});
</script>

</body>
</html>