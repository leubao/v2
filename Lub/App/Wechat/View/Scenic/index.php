<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>产品列表</title>
  <link rel="stylesheet" href="../static/layui/css/layui.css">
</head>
<body>
<style>
body{ background: #f2f2f2; padding-top: 10px}
/*顶部活动票样式*/
.section{ padding:10px; background: #fff; margin-bottom: 8px;float: left;width: -webkit-fill-available;}
.fromsection{ padding:10px 20px 10px 0; background: #fff; margin-bottom: 8px}
.name{ font-size: 18px; }
.tips{ margin: 8px 0 15px}
.pricebox{ color:#F44336}
.pricebox .price{ font-size:20px;margin-right:5px}
.pricebox .prices{color: #666}
.text{width: 80%; float: left;}
.btn{float: right;}
</style>
<div class="layui-container"> 
  <volist name="list" id="vo">
  <div class="section">
    <div class='name'>{$vo.name}</div>
    <div class="text">
      
      <div class='pricebox'>
        <span> ￥</span>
        <span class='price'>{$vo.price}</span>
        <span class='prices'>起</span>
      </div>
    </div>
    <div class="btn">
      <a href="{:U('wechat/scenic/view',array('pid'=>$vo['id']));}" class="layui-btn layui-btn-sm layui-btn-danger">立即预订</a>
    </div>
  </div>
  </volist>
</div>
<script src="../static/layui/layui.js"></script>
<script>
layui.use(['layer'], function(){
  var $ = layui.$
  ,layer= layui.layer;
});
</script>
</body>
</html>