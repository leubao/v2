<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>{$data.title}</title>
  <link rel="stylesheet" href="../static/layui/css/layui.css">
  <link rel="stylesheet" href="../static/css/layuiwap.css">
</head>
<body>
<style>
.content img{
  display: inline-block;
  height: auto;
  max-width: 100%;
}
.tip{
  color: #F44336;
  font-size: 10px
}
</style>
<div class="layui-carousel" id="carousel">
  <div carousel-item>
    <img src="{$data.thumb}">
  </div>
</div>

<div class="section">
  <div class='name'><i class="layui-icon layui-icon-fire" style="color: #F44336;"></i> {$data.title}
	</div>

  <div class="tips">
    <volist id="vo" name="data.tag">
  	<span class="layui-badge layui-bg-blue">{$vo}</span>
    </volist>
  </div>
  <div class='pricebox'>
    <span>价格 ￥</span>
    <span class='price'>{$product.price}元</span>
    <notempty name="$data.oprice">
    <span class='prices'>原价{$data.oprice}元</span>
    </notempty>
  </div>
</div>
<div class="layui-form" lay-filter="reg-form">  
  <div class="ticket">
    <div class="sku_title left">购买数量
      <span class="tip">({$data.desc})</span></div>
    <div class="stepper right">
      <span class="min">-</span>
      <!-- 数值 -->  
      <input type="number" class="num" value="1" id="num" disabled="" />
      <span class="add">+</span>
    </div>   
  </div>  
  <div class="fromsection"> 
    <div class="layui-form-item">
      <input type="text" name="username" placeholder="请输入联系人" autocomplete="off" class="layui-input">
    </div>          
    <div class="layui-form-item">  
      <input type="tel" name="phone" placeholder="请输入手机号" autocomplete="off" class="layui-input">
    </div> 
    <div class="layui-form-item layui-form-text">
      <input type="text" name="remark" placeholder="请输入备注" autocomplete="off" class="layui-input">
    </div>
  </div>
  <div class="content">
    {$data.content}
  </div>
  <notempty name="data.know">
  <div class="needsection">
	  <div class="info-title">预定须知</div>
	  <ul class="info">
      <volist id='vo' name="data.know">
	  	<li>
	      <div><i class="layui-icon {$vo.icon}"></i> {$vo.title}</div>
	      <p>{$vo.content}</p>
	    </li>
      </volist>      
	  </ul>
	</div>
    </notempty>
  <div class="bottom">
    <div class="total">订单金额：<span class='total-small'>￥</span><span class='total-text' id="money">0.00</span></div>
    <button class="subbtn" lay-submit="" lay-filter="pay-submit">去支付</button>            
  </div>
</div>

<script src="../static/layui/layui.js"></script> 
<script type="text/javascript" src="http://res2.wx.qq.com/open/js/jweixin-1.6.0.js" charset="utf-8"></script>

<script>
layui.use(['form','layer','laytpl','carousel'], function(){

  var $ = layui.$
  ,form = layui.form
  ,layer = layui.layer
  ,carousel = layui.carousel
  ,laytpl = layui.laytpl
  ,global = {$global};
  wx.config({
    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
    appId: '{$options.appId}', // 必填，公众号的唯一标识
    timestamp: '{$options.timestamp}', // 必填，生成签名的时间戳
    nonceStr: '{$options.nonceStr}', // 必填，生成签名的随机串
    signature: '{$options.signature}',// 必填，签名
    jsApiList: ['updateAppMessageShareData','updateTimelineShareData'] // 必填，需要使用的JS接口列表
  });
  wx.checkJsApi({
    jsApiList: ['updateAppMessageShareData','updateTimelineShareData'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
    success: function(res) {
      //console.log(res);
    }
  });

  wx.ready(function(){
    var shareData = { 
      title: '{$data.title}', // 分享标题
      desc: '{$data.desc}', // 分享描述
      link: '{$urls}', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
      imgUrl: '{$data.thumb}', // 分享图标
      success: function (ret) {
        // 设置成功
       // console.log(ret);
      }
    };
    wx.updateAppMessageShareData(shareData);
    wx.updateTimelineShareData(shareData);
  });

  wx.error(function(res){
    console.log('err');
    console.log(res)
  });
  carousel.render({
    elem: '#carousel'
    ,width: '100%' //设置容器宽度
    ,arrow: 'none' //始终显示箭头
    ,indicator: 'none'
    //,anim: 'updown' //切换动画方式
  });

  var plan = {$plan.id},
      area = {$area.id},
      ticket = {$area.id},
      price = '{$area.price}',
      discount = '{$area.discount}',
      num = '{$area.area_num}',
      param = '',
      toJSONString = '',
      postData = '',
      activity = '0',
      subtotal = '{$area.discount}';
  //数量增加减少
  $(".min").click(function(){
    if(num > 1){
      num = getNum() - 1;
      updateNum();
    }else{
    }
  });
  $(".add").click(function(){
    //判断是否选择日期和价格
    if(plan != 0 && price != 0){
      if(num < global['user']['maxnum']){
        //限制单笔订单最大数量*/
        num = getNum() + 1;
        updateNum();
      }else{
        layer.msg("亲，您一次只能买这么多了!");
      }
    }else if(plan == 0 && area == 0){
      layer.msg("请选择日期和票价!");
    }else if(area == 0){
      layer.msg("请选择票价!");
    }else{
      layer.msg("请选择日期和票价!");
    }
  });
  refreshNum();
  function changeNum(t){
    $("#num").val();
  }
  //更换场次时重置页面
  function refreshNum(){
    $("#num").val('1');
    getNum();
  }
  //更新数量
  function updateNum(){
    $("#num").val(num);
    if(global.user.epay == 2){
      subtotal = parseFloat(discount * parseInt(num));
    }else{
      subtotal = parseFloat(price * parseInt(num));
    }
    
    $("#money").html(subtotal)
  }
  //获取数量
  function getNum(){
    num = parseInt($("#num").val());
    updateNum();
    return num;
  }
  //监听提交
  form.on('submit(pay-submit)', function(data){

    if(plan == 0 || price == 0){
      layer.msg("请选择可用日期~");
      return false;
    }
    if(data.field.username.length == 0){
      layer.msg("请输入联系人~");
      return false;
    }
    if(!/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[0-9])[0-9]{8}$/.test(data.field.phone)){
      layer.msg("手机号码格式不正确~");
      return false;
    }
    $(this).attr('disabled',"true");
    pay = '{"cash":0,"card":0,"alipay":0}';
    param = '{"remark":"'+data.field.remark+'","activity":"'+activity+'","settlement":"'+global['user']['epay']+'"}';
    crm = '{"guide":"'+global['user']['guide']+'","qditem":"'+global['user']['qditem']+'","phone":"'+data.field.phone+'","contact":"'+data.field.username+'","memmber":"'+global['user']['memmber']+'"}';
    var toJSONString = '{"areaId":'+area+',"priceid":'+ticket+',"price":'+price+',"num":'+num+'}'
    postData = 'info={"subtotal":"'+subtotal+'","plan_id":'+plan+',"checkin":1,"sub_type":0,"type":1,"data":['+ toJSONString + '],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';
    $.ajax({
        type:'POST',
        url:'<?php echo U('Wechat/Shop/create_order');?>',
        data:postData,
        dataType:'json',
        success:function(data){
            if(data.statusCode == "200"){
                location.href = data.url;
            }else{
                layer.msg("下单失败:"+data.msg);
            }
        }
    });
  });
});
</script>
</body>
</html>