<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>{$product.name}</title>
  <link rel="stylesheet" href="../static/layui/css/layui.css">
</head>
<body>
<style>
body{ background: #f2f2f2; padding-bottom: 50px}
/*顶部活动票样式*/
.section{ padding:10px; background: #fff; margin-bottom: 8px}
.fromsection{ padding:15px; background: #fff; margin-bottom: 8px}
.name{ font-size: 18px; }
.tips{ margin: 8px 0 15px}
.pricebox{ color:#F44336}
.pricebox .price{ font-size:20px;margin-right:5px}
.pricebox .prices{color: #666} 
/*演出时间*/ 
.now{border-color: #FF5722 !important;color: #FF5722 !important;}
.sku_title{ display: block;font-size: 16px; margin-bottom:15px;}
.ticket{ border-bottom: 1px solid #f2f2f2; background: #fff;padding: 10px; overflow: hidden;}
.sku_content li{display: inline-block; height: 38px;line-height: 38px;padding: 0 10px;  border: 1px solid #C9C9C9; background-color: #fff; color: #555; white-space: nowrap; text-align: center; font-size: 14px;border-radius: 2px; cursor: pointer; margin-left: 5px;-webkit-tap-highlight-color: rgba(255, 255, 255, 0); margin-bottom: 7px;}
/**/
.left{ float: left; margin-top: 6px;}
.right{ float: right; }
/*数量加减样式*/ 
.stepper { width: 117px; height: 35px; border: 1rpx solid #ccc; border-radius: 5px; border: 1px solid #eee}   
.stepper span{ width: 35px; line-height: 35px; text-align: center;  float: left;}
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
  <div class='name'><i class="layui-icon layui-icon-fire" style="color: #F44336;"></i> {$product.name}</div>
  <div class="tips">
    <span class="layui-badge layui-bg-green">节假日开放</span>
    <span class="layui-badge layui-bg-blue">周末开放</span>
  </div>
  <div class='pricebox'>
    <span>价格 ￥</span>
    <span class='price'>{$product.price}</span>
    <span class='prices'>起</span>
  </div>
</div>
<div class="layui-form" lay-filter="reg-form">  
  <div class="ticket">
    <div class="sku_title">使用日期</div> 
    <div class="sku_content" id="plan"> 
    </div> 
  </div> 
  <div class="ticket">
    <div class="sku_title">选择票型</div>
    <div class="sku_content" id="price"> 
      <li class="sku_value">请选择使用日期</li>
    </div> 
  </div>  
  <div class="ticket">
    <div class="sku_title left">数量(剩余:<span class="stock-num"></span>)</div>
    <div class="stepper right">
      <span class="min">-</span>
      <!-- 数值 -->  
      <input type="number" class="num" value="1" id="num" disabled="" />
      <span class="add">+</span>
    </div>   
  </div>  
  <div class="fromsection"> 
    <div class="layui-form-item">
      <input type="text" name="username" placeholder="请输入联系人" lay-verify="required" autocomplete="off" class="layui-input">
    </div>          
    <div class="layui-form-item">  
      <input type="tel" name="phone" placeholder="请输入手机号" lay-verify="required|phone" autocomplete="off" class="layui-input">
    </div> 
    <div class="layui-form-item layui-form-text">
      <textarea name="remark" placeholder="请输入备注" class="layui-textarea"></textarea>
    </div>
  </div>
  <div class="bottom">
    <div class="total">订单金额：<span class='total-small'>￥</span><span class='total-text' id="money">0.00</span></div>
    <button class="subbtn" lay-submit="" lay-filter="pay-submit">去支付</button>            
  </div>
</div>


<script src="../static/layui/layui.js"></script> 
<script>
layui.use(['form','layer','laytpl'], function(){

  var $ = layui.$
  ,form = layui.form
  ,layer = layui.layer
  ,laytpl = layui.laytpl
  ,global = {$global};

  var getPlantpl = document.getElementById('plantpl').innerHTML;
  var getPricetpl = document.getElementById('pricetpl').innerHTML;

  laytpl(getPlantpl).render(global, function(html){
    document.getElementById('plan').innerHTML = html;
  });

  var plan = '0',
      area = '0',
      ticket = '0',
      price = '0',
      discount = '0',
      num = '0',
      param = '',
      toJSONString = '',
      postData = '',
      activity = '0',
      subtotal = '0';

  $("#plan li").click(function(){
    //检查当前被选择的元素是否已经有已选中的
    $("#plan li").removeClass('now');
    $(this).addClass('now');     
    
    //为当前选择加上
    refreshNum();
    area = 0;
    plan = $(this).data('id');
    num = $(this).data('num');
    $(".stock-num").html(num);
    $("#money").html(price)
    var selectData = global.area;
    
    laytpl(getPricetpl).render(selectData[plan], function(html){
      document.getElementById('price').innerHTML = html;
    });
  });  
  $(document).on("click","#price li",function(){

    //判断是否已经选择计划
    if(!$(this).hasClass("unavailable")){
      if(plan != 0){
        $("#price li").removeClass('now');
        $(this).addClass('now');
        area = $(this).data('area');
        ticket = $(this).data('priceid');
        
        price = $(this).data('price');
        discount = $(this).data('discount');
        num = $(this).data('num');
        //更新可售数量  当为0时 禁用
        $(".stock-num").html(num);
        refreshNum();
        updateNum();
      }else{
        layer.msg("请选择使用日期!",{time: 2000});
      }
    } 
  });
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
    return num;
  }
  //监听提交
  form.on('submit(pay-submit)', function(data){
    if(plan == 0 || price == 0){
      layer.msg("请选择可用日期~");
      return false;
    }
    pay = '{"cash":0,"card":0,"alipay":0}';
    param = '{"remark":"'+data.field.remark+'","activity":"'+activity+'","settlement":"'+global['user']['epay']+'"}';
    crm = '{"guide":"'+global['user']['guide']+'","qditem":"'+global['user']['qditem']+'","phone":"'+data.field.phone+'","contact":"'+data.field.username+'","memmber":"'+global['user']['memmber']+'"}';
    var toJSONString = '{"areaId":'+area+',"priceid":'+ticket+',"price":'+price+',"num":'+num+'}'
    postData = 'info={"subtotal":"'+subtotal+'","plan_id":'+plan+',"checkin":1,"sub_type":0,"type":1,"data":['+ toJSONString + '],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';
    $.ajax({
        type:'POST',
        url:'<?php echo U('Wechat/scenic/create_order');?>',
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
<script id="plantpl" type="text/html">

  {{#  layui.each(d.plan, function(index, item){ }}
    
  <li class="sku_value" data-id="{{ item.id }}" data-num="{{item.num}}">{{item.title}}</li>

  {{#  }); }}

  {{#  if(d.plan.length === 0){ }}
    无数据
  {{#  } }} 

</script>
<script id="pricetpl" type="text/html">
  {{#  layui.each(d, function(index, item){ }}
  <li class="sku_value {{# if(this.num == '0'){ }}unavailable{{# } }}" data-price="{{ this.price }}" data-discount="{{ this.discount }}" data-area="{{ this.area }}" data-priceid="{{ this.id }}" data-num="{{ this.area_num }}">{{this.discount}}元 (<?php if($proconf['price_view'] == '2'){ ?>{{this.name}}{{this.remark}}<?php }else{ ?>{{this.name}} <?php }?>)</li>
  {{#  }); }}
  {{#  if(d.length === 0){ }}
    <li class="sku_value">请选择使用日期</li>
  {{#  } }}
</script>
</body>
</html>