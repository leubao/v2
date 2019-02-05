<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>等待开始...</title>
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
</style>
<body>
<div class="statusBox">
  <!-- 提交失败 -->
  <div class="fail">
    <i class="layui-icon layui-icon-log" style="font-size: 100px; color: #f5222d;"></i>  
    <div class="title">等待开始...</div>
    <div class="description">亲，下一波秒杀开始时间是{$startime}</div>
    
    <button class="layui-btn layui-btn-normal" id="timing"></button>
  </div> 
  <!--  -->
</div>
<script src="http://dp.wy-mllj.com/static/layui/layui.js"></script>
<script type="text/javascript">
layui.use(['util'], function(){
  var $ = layui.$
  ,util = layui.util;
  var endTime = {$time}
  ,serverTime = new Date().getTime();
  console.log(endTime);
  util.countdown(endTime, serverTime, function(date, serverTime, timer){
    var str = date[0] + '天' + date[1] + '时' +  date[2] + '分' + date[3] + '秒';
    layui.$('#timing').html('距离下一波秒杀还有：'+ str);
  });

});
</script>
</body>
</html>