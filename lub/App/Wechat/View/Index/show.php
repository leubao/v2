<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>{$product.name}</title>
  <link rel="stylesheet" href="../static/layui/css/layui.css">
  <link rel="stylesheet" href="../static/css/layuiwap.css?v=20200507">
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
    <img src="../d/wap/21.jpg">
  </div>
</div>
<div class="section">
  <div class='name'><i class="layui-icon layui-icon-fire" style="color: #F44336;"></i> {$product.name}</div>
 
  <!-- <div class='pricebox'>
    <span>活动价 ￥</span>
    <span class='price'>{$data.ticket.discount}</span>
    <span class='prices'>￥{$data.ticket.price}</span>
  </div> -->
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
      <input type="text" name="username" placeholder="请输入联系人" autocomplete="off" class="layui-input">
    </div>          
    <div class="layui-form-item">  
      <input type="tel" name="phone" placeholder="请输入手机号" autocomplete="off" class="layui-input">
    </div>
    <div class="layui-form-item">
        <input type="text" name="identity[]" placeholder="身份证" autocomplete="off" class="layui-input idcard">
    </div> 
    <div id="idcardBox"></div>
    <div class="layui-form-item layui-form-text">
        <textarea name="remark" placeholder="请输入内容" class="layui-textarea"></textarea>
    </div>
  </div>
  <div class="needsection">
    <div class="info-title">预定须知</div>
    <ul class="info">
      <li>
        <div><i class="layui-icon {$vo.icon}"></i> 1、1.2米以下儿童可免费进场但无座；1.2米以上请购成人票。</div>
        <p>{$vo.content}</p>
      </li>
      <li>
        <div><i class="layui-icon {$vo.icon}"></i> 2、门票一旦售出，恕不退换。</div>
        <p>{$vo.content}</p>
      </li>  
      <li>
        <div><i class="layui-icon {$vo.icon}"></i> 3、请观众提前30分钟检票入场，对号入座，如遇开演请听从工作人员安排。</div>
        <p>{$vo.content}</p>
      </li>
      <li>
        <div><i class="layui-icon {$vo.icon}"></i> 4、任何通过微信购买门票的人均认为已经阅读、理解并接受了以上条款。</div>
        <p>{$vo.content}</p>
      </li>  
      
    </ul>
  </div>
  <div class="bottom">
    <div class="total">订单金额：<span class='total-small'>￥</span><span class='total-text' id="money">0.00</span></div>
    <button class="subbtn" lay-submit="" lay-filter="pay-submit">去支付</button>            
  </div>
</div>
<script src="static/layui/layui.js"></script>
<script>
layui.use(['form','layer','laytpl','carousel'], function(){
  var $ = layui.$
  ,form = layui.form
  ,layer= layui.layer
  ,laytpl = layui.laytpl
  ,carousel = layui.carousel
  ,global = {$goods_info};console.log(global);
  //前后若干天可选，这里以7天为例
  var getPlantpl = document.getElementById('plantpl').innerHTML;
  var getPricetpl = document.getElementById('pricetpl').innerHTML;

  laytpl(getPlantpl).render(global, function(html){
    document.getElementById('plan').innerHTML = html;
  });

  carousel.render({
    elem: '#carousel'
    ,width: '100%' //设置容器宽度
    ,arrow: 'none' //始终显示箭头
    ,indicator: 'none'
    //,anim: 'updown' //切换动画方式
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
  $(".add").click(function() {
    //判断是否选择日期和价格
    if(plan != 0 && price != 0){
      if(num < global['user']['maxnum']){
        //限制单笔订单最大数量*/
        num = getNum() + 1;
        updateNum();
        var t = $(this).parent().find('input[class*=num]');
        if(t.val()==""||undefined||null){
          t.val(0);
        }
        t.val(parseInt(t.val()) + 1);
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
  })
  $(".min").click(function() {
    num = getNum() - 1;
    updateNum();
    var t = $(this).parent().find('input[class*=num]');
    if(t.val()==""||undefined||null){
      t.val(0);
    }
    t.val(parseInt(t.val()) - 1)
    if(parseInt(t.val()) < 1) {
      t.val(1);
      layer.msg('不能再减啦~',{icon: 5});
    }
    $("#idcardBox").find("li:last").remove();
  })

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
    // 活动直接底价结算
    subtotal = parseFloat(discount * parseInt(num));
    $("#money").html(subtotal)
  }
  //获取数量
  function getNum(){
    num = parseInt($("#num").val());
    return num;
  }
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
    pay = '{"cash":0,"card":0,"alipay":0}';
    param = '{"remark":"'+data.field.remark+'","activity":"","settlement":"'+global['user']['epay']+'"}';
    crm = '{"guide":"'+global['user']['guide']+'","qditem":"'+global['user']['qditem']+'","phone":"'+data.field.phone+'","contact":"'+data.field.username+'","memmber":"'+global['user']['memmber']+'"}';
    var toJSONString = '';
    var field = data.field;

    //计算金额
    if(global['user']['epay'] == '1'){
      subtotal = parseFloat(price * parseInt(num));
    }else{
      subtotal = parseFloat(discount * parseInt(num));
    }

    toJSONString = '{"areaId":'+area+',"priceid":'+ticket+',"price":'+price+',"num":'+num+'}';
    postData = 'info={"subtotal":"'+subtotal+'","plan_id":'+plan+',"checkin":1,"sub_type":0,"type":1,"data":['+ toJSONString + '],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';

    $.ajax({
      type:'POST',
      url:'<?php echo U('Wechat/Index/order');?>',
      data:postData,
      timeout: 1500,
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
  <li class="sku_value {{# if(this.num == '0'){ }}unavailable{{# } }}" data-price="{{ this.money }}" data-discount="{{ this.moneys }}" data-area="{{ this.area }}" data-priceid="{{ this.priceid }}" data-num="{{ this.num }}">{{this.moneys}}元 (<?php if($proconf['price_view'] == '2'){ ?>{{this.name}}{{this.remark}}<?php }else{ ?>{{this.name}} <?php }?>)</li>
  {{#  }); }}
  {{#  if(d.length === 0){ }}
    <li class="sku_value">请选择使用日期</li>
  {{#  } }}
</script>
</body>
</html>