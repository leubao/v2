<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>门票预定</title>
  <link rel="stylesheet" href="static/layui/css/layui.css">
</head>
<body>
<style>
body{ background: #f2f2f2; padding-bottom: 50px}
/*顶部活动票样式*/
.section{ padding:10px; background: #fff; margin-bottom: 8px}
.fromsection{ padding:10px 20px 10px 0; background: #fff; margin-bottom: 8px}
.name{ font-size: 18px; }
.tips{ margin: 8px 0 15px}
.pricebox{ color:#F44336}
.pricebox .price{ font-size:20px;margin-right:5px}
.pricebox .prices{text-decoration:line-through;color: #666} 
/*数量加减样式*/ 
.stepper { width: 117px; height: 35px; border: 1rpx solid #ccc; border-radius: 5px; border: 1px solid #eee}
.stepper span{ width: 35px; line-height: 35px; text-align: center;  float: left;  }
.stepper input { width: 45px;  height: 35px;  float: left; text-align: center; border: none; border-left: 1px solid #eee;border-right: 1px solid #eee; font-size: 16px} 
/*预定须知*/
.needsection{background: #fff; margin-bottom: 8px}
.info-title{ padding:13px 10px; font-size: 16px ;border-bottom: 1px solid #f2f2f2; }
.info li{  border-bottom: 1px solid #f7f7f7; padding: 10px 13px;}
.info li p{ color: #999; line-height: 22px; font-size: 13px; margin-top: 5px}
/*底部去支付*/
.bottom{position: fixed;bottom: 0; left: 0; height: 50px; width: 100%; background: #fff; overflow: hidden; line-height: 50px; z-index: 99}
.total{ float: left; width: 72%; text-align:right; padding-right:3%;color: #333}
.total-small{color:#f55b5b;}
.total-text{ color:#f55b5b;font-size:20px}
.subbtn{ border-radius: 0;height: 100%; line-height: 50px;background: #f54343;color: #fff;border: none;width: 25%; font-size: 16px; }
.subbtn::after{border: none;}
</style>
<div class="section">
  <div class='name'><i class="layui-icon layui-icon-fire" style="color: #F44336;"></i> 开园活动票</div>
  <div class="tips">
    <span class="layui-badge layui-bg-green">节假日开放</span>
    <span class="layui-badge layui-bg-blue">周末开放</span>
  </div>
  <div class='pricebox'>
    <span>活动价 ￥</span>
    <span class='price'>19.9</span>
    <span class='prices'>￥60</span>
  </div>
</div>
<div class="layui-form fromsection" lay-filter="reg-form">  
  <div class="layui-form-item">
    <label class="layui-form-label">游玩日期</label>
    <div class="layui-input-block">  
      <input type="text" name="date" id="date" lay-verify="date" placeholder="请选择游玩日期" autocomplete="off" class="layui-input">
    </div>
  </div> 
  <div class="layui-form-item">
    <label class="layui-form-label">数量</label>
    <div class="layui-input-block">
        <div class="stepper">
          <!-- 减号 -->  
          <span id="min">-</span>
          <!-- 数值 -->  
          <input type="number" class="num" value="1" disabled="" />
          <!-- 加号 -->  
          <span id="add">+</span>
        </div>
    </div>
  </div>          
  <div class="layui-form-item">
    <label class="layui-form-label">联系人</label>
    <div class="layui-input-block">  
      <input type="text" name="username" lay-verify="required" autocomplete="off" class="layui-input">
    </div>
  </div>          
  <div class="layui-form-item">
    <label class="layui-form-label">手机号</label>
    <div class="layui-input-block">  
      <input type="tel" name="phone" lay-verify="required|phone" autocomplete="off" class="layui-input">
    </div>
  </div> 
  <div class="layui-form-item">
    <label class="layui-form-label">身份证</label>
    <div class="layui-input-block">  
      <input type="text" name="identity[]" lay-verify="identity" placeholder="" autocomplete="off" class="layui-input">
    </div>
  </div> 
  <div id="idcardBox"></div>
  <div class="layui-form-item layui-form-text">
    <label class="layui-form-label">备注</label>
    <div class="layui-input-block">
      <textarea name="remark" placeholder="请输入内容" class="layui-textarea"></textarea>
    </div>
  </div>
  <div class="bottom">
    <div class="total">订单金额：<span class='total-small'>￥</span><span class='total-text' id="total">19.9</span></div>
    <button class="subbtn" lay-submit="" lay-filter="active-submit">去支付</button>            
  </div>
</div>
<div class="needsection">
  <div class="info-title">预定须知</div>
  <ul class="info">
    <li>
      <div><i class="layui-icon layui-icon-rmb"></i> 退票须知</div>
      <p>请谨慎选票，一经售出不退不换，敬请谅解。</p>
    </li>
    <li>
      <div><i class="layui-icon layui-icon-tree"></i> 优惠政策</div>
      <p>身高1米以下儿童免费入园。</p>
    </li> 
    <li>
      <div><i class="layui-icon layui-icon-log"></i> 入园时间</div>
      <p>1月20日 18:00-21:30</p>
    </li>       
  </ul>
</div>
<script src="static/layui/layui.js"></script>
<script>
layui.use(['form','layer', 'laydate'], function(){
  var $ = layui.$
  ,form = layui.form
  ,layer= layui.layer
  ,laydate = layui.laydate;
  //前后若干天可选，这里以7天为例
  laydate.render({
    elem: '#date'
    ,min: -7
    ,max: 7
  });
  $("#add").click(function() {
    var t = $(this).parent().find('input[class*=num]');
    if(t.val()==""||undefined||null){
      t.val(0);
    }
    t.val(parseInt(t.val()) + 1)
    setTotal(); 
    $("#idcardBox").append('<li class="layui-form-item"><label class="layui-form-label">身份证</label><div class="layui-input-block"><input type="text" name="identity[]" lay-verify="identity" placeholder="" autocomplete="off" class="layui-input"></div></li>')
  })
  $("#min").click(function() {
    var t = $(this).parent().find('input[class*=num]');
    if(t.val()==""||undefined||null){
      t.val(0);
    }
    t.val(parseInt(t.val()) - 1)
    if(parseInt(t.val()) < 1) {
      t.val(1);
      layer.msg('不能再减啦~',{icon: 5});
    }
    setTotal();
    $("#idcardBox").find("li:last").remove();
  })
  function setTotal() {
    var s = 0;
    var t = $('input[class*=num]').val();
    var p = $('span[class*=price]').text();
    s = parseInt(t) * parseFloat(p);
    $("#total").html(s.toFixed(2));
  }
  setTotal();
});
</script>
</body>
</html>