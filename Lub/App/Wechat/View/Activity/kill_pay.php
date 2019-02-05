<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>支付成功</title>
  <link rel="stylesheet" href="http://dp.wy-mllj.com/static/layui/css/layui.css">
</head>
<style>
.statusBox{ text-align: center; padding: 100px 0 50px; background: #fff}
.title {font-size: 24px; color: rgba(0,0,0,.85);font-weight: 500;line-height: 32px;margin-bottom: 16px;} 
.description {font-size: 14px;line-height: 22px;color: rgba(0,0,0,.45);margin-bottom: 24px;}
.extra { background: #fafafa;padding: 24px;border-radius: 2px;text-align: left; margin: 0 auto 30px; width: 70%}
.extra_t{font-size: 16px;color: rgba(0, 0, 0, 0.85); font-weight: 500;margin-bottom: 16px;}
.extra_d{font-size: 15px;line-height: 1.5; color: rgba(0,0,0,.65);margin-top: 16px;}
.extra_a{color: #1890ff; margin-left: 10px}
.btnbox{
  padding: 20px;
}
</style>
<body>
<div class="statusBox">
  <!-- 提交失败 -->
  <div class="fail">
    <i class="layui-icon layui-icon-ok-circle" style="font-size: 100px; color: #19be6b;"></i>  
    <div class="title">支付成功</div>
    <div class="description">亲,稍后我们会为您发送取票短信,请注意查收...</div>
    <div class="btnbox">
      <button class="layui-btn layui-btn-normal layui-btn-fluid" id="closeWindow">确定</button>
    </div>
    
  </div> 
  <!--  -->
</div>
<script src="http://dp.wy-mllj.com/static/layui/layui.js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js" charset="utf-8"></script>
<script type="text/javascript">
layui.use(['util'], function(){
  var $ = layui.$
  ,util = layui.util;
  wx.config({$options|json_encode});
  //关闭当前窗口
  document.querySelector('#closeWindow').onclick = function () {
    wx.closeWindow();
  };
});
</script>
</body>
</html>