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
<div class="section">
  <div class='name'><i class="layui-icon layui-icon-fire" style="color: #F44336;"></i> {$data.title}</div>
 
  <div class='pricebox'>
    <span>活动价 ￥</span>
    <span class='price'>{$data.ticket.discount}</span>
    <span class='prices'>￥{$data.ticket.price}</span>
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
    <!-- <div class="layui-form-item">
      <input type="text" name="username" placeholder="请输入联系人" autocomplete="off" class="layui-input">
    </div>          
    <div class="layui-form-item">  
      <input type="tel" name="phone" placeholder="请输入手机号" autocomplete="off" class="layui-input">
    </div>  -->
    <div class="layui-form-item">
        <input type="text" name="identity[]" lay-verify="identity" placeholder="身份证" autocomplete="off" class="layui-input idcard">
    </div> 
    <div id="idcardBox"></div>
    <div class="layui-form-item layui-form-text">
        <textarea name="remark" placeholder="请输入内容" class="layui-textarea"></textarea>
    </div>
  </div>
  <div class="bottom">
    <div class="total">订单金额：<span class='total-small'>￥</span><span class='total-text' id="money">0.00</span></div>
    <button class="subbtn" lay-submit="" lay-filter="pay-submit">去支付</button>            
  </div>
</div>
<script src="static/layui/layui.js"></script>
<script>
layui.use(['form','layer','laytpl'], function(){
  var $ = layui.$
  ,form = layui.form
  ,layer= layui.layer
  ,laytpl = layui.laytpl
  ,global = {$global};
  //前后若干天可选，这里以7天为例
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
      activity = {$data.id},
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
        t.val(parseInt(t.val()) + 1)
        $("#idcardBox").append('<li class="layui-form-item"><input type="text" name="identity[]" lay-verify="identity" placeholder="身份证" autocomplete="off" class="layui-input idcard"></div></li>');
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
    var t = $(this).parent().find('input[class*=num]');
    if(t.val()==""||undefined||null){
      t.val(0);
    }
    t.val(parseInt(t.val()) - 1)
    if(parseInt(t.val()) < 1) {
      t.val(1);
      layer.msg('不能再减啦~',{icon: 5});
    }
    updateNum();
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
    pay = '{"cash":0,"card":0,"alipay":0}';
    param = '{"remark":"'+data.field.remark+'","activity":"'+activity+'","settlement":"2"}';
    crm = '{"guide":"'+global['user']['guide']+'","qditem":"'+global['user']['qditem']+'","phone":"","contact":"","memmber":"'+global['user']['memmber']+'"}';
    var toJSONString = '';
    var field = data.field;
    var idcardList = [];
    var length = $(".idcard").length;
    $(".idcard").each(function(i, el) {
      var fg  = i+1 < length ? ',':' ';/*判断是否增加分割符*/
      var idcard = $(this).val();
      toJSONString = toJSONString + '{"areaId":'+area+',"priceid":'+ticket+',"idcard":"'+idcard+'","price":'+price+',"num":"1"}'+fg;
    });
    // if(is_array_unique(idcardList)){
    //   layer.msg('身份证号码重复!');
    //   return false;
    // }

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
  function is_array_unique(arr){
    return /(\x0f[^\x0f]+)\x0f[\s\S]*\1/.test("\x0f"+arr.join("\x0f\x0f") +"\x0f");
  }
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