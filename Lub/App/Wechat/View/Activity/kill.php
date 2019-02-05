<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>门票预定</title>
  <link rel="stylesheet" href="http://dp.wy-mllj.com/static/layui/css/layui.css">
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

.soldout{ border-radius: 0;height: 100%; line-height: 50px;background: #2F4056;color: #fff;border: none;width: 100%; font-size: 16px; }
.soldout::after{border: none;}
</style>
<div class="section">
  <div class='name'><i class="layui-icon layui-icon-fire" style="color: #F44336;"></i> {$data.title}</div>
  <div class="tips">
    <span class="layui-badge layui-bg-green">一人一单</span>
  </div>
  <div class='pricebox'>
    <span>秒杀价 ￥</span>
    <span class='price'>{$ticket.discount}</span>
    <span class='prices'>￥{$ticket.price}</span>
  </div>
</div>
<div class="layui-form fromsection" lay-filter="reg-form">  
  <div class="layui-form-item">
    <label class="layui-form-label">游玩日期</label>
    <div class="layui-input-block">  
      <input type="text" disabled value="{$rule.plan}" class="layui-input">
    </div>
  </div> 
  <div class="layui-form-item">
    <label class="layui-form-label">数量</label>
    <div class="layui-input-block">
        <div class="stepper">
          <!-- 减号 -->  
          <span id="min">-</span>
          <!-- 数值 -->  
          <input type="number" name="number" class="num" value="1" disabled="" />
          <!-- 加号 -->  
          <span id="add">+</span>
        </div>
    </div>
  </div>          
  <div class="layui-form-item">
    <label class="layui-form-label">联系人</label>
    <div class="layui-input-block">  
      <input type="text" name="username" lay-verify="required|username" autocomplete="off" class="layui-input">
    </div>
  </div>          
  <div class="layui-form-item">
    <label class="layui-form-label">手机号</label>
    <div class="layui-input-block">  
      <input type="tel" name="phone" lay-verify="required|phone" autocomplete="off" class="layui-input">
    </div>
  </div> 
  <div id="idcardBox"></div>
  <div class="layui-form-item layui-form-text">
    <label class="layui-form-label">备注</label>
    <div class="layui-input-block">
      <textarea name="remark" placeholder="请输入内容" class="layui-textarea"></textarea>
    </div>
  </div>
  <input type="hidden" name="plan_id" value="{$rule.plan_id}">
  <input type="hidden" name="ticket" value="{$ticket.id}">
  <input type="hidden" name="money" value="{$ticket.discount}">
  <input type="hidden" name="act" value="{$rule.actid}">
  <input type="hidden" name="product" value="{$data.product_id}">
  <if condition="$rule.quota neq '0'">
  <div class="bottom">
    <div class="total">订单金额：<span class='total-small'>￥</span><span class='total-text' id="total">{$ticket.discount}</span></div>
    <button class="subbtn" lay-submit lay-filter="active-submit">去支付</button>            
  </div>
  <else />
  <div class="bottom">
    <button class="soldout">此轮秒杀已结束,下一轮再来吧</button>            
  </div>
  </if>
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
      <p>身高1.2米以下儿童免费入园。</p>
    </li> 
    <li>
      <div><i class="layui-icon layui-icon-log"></i> 观演时间</div>
      <p>2019年2月9日 20:00-21:10</p>
    </li>
    <li>
      <div><i class="layui-icon layui-icon-tips"></i> 其它说明</div>
      <p>{$data.remark}</p>
    </li> 
  </ul>
</div>
<script src="http://dp.wy-mllj.com/static/layui/layui.js"></script>
<script>
layui.use(['form','layer'], function(){
  var $ = layui.$
  ,form = layui.form
  ,layer= layui.layer;

  function setTotal() {
    var s = 0;
    var t = $('input[class*=num]').val();
    var p = $('span[class*=price]').text();
    s = parseInt(t) * parseFloat(p);
    $("#total").html(s.toFixed(2));
  }
  setTotal();
  form.verify({
    username: function(value, item){
      if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
        return '联系人填写有误';
      }
      if(/(^\_)|(\__)|(\_+$)/.test(value)){
        return '联系人填写有误\'_\'';
      }
      if(/^\d+\d+\d$/.test(value)){
        return '联系人填写有误';
      }
    }
  });
  //监听提交
  form.on('submit(active-submit)', function(data){
    //layer.msg(JSON.stringify(data.field));
    $.ajax({
      type:'POST',
      url:"<?php echo U('Wechat/activity/killorder');?>",
      data:data.field,
      dataType:'json',
      timeout: 1500,
      error: function(){
        layer.msg('服务器请求超时，请检查网络...');
      },
      success:function(data){
          if(data.statusCode == "200"){
            location.href = data.url;
          }else{
            layer.msg(data.msg);
          }
      }
    });
    return false;
  });
});
</script>
</body>
</html>