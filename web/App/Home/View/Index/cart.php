<include file="Index:header" />
<div class="sui-container pd80">
  <div class="sui-steps steps-large steps-auto">
    <div class="wrap">
      <div class="finished">
        <label><span class="round"><i class="sui-icon icon-pc-right"></i></span><span>第一步 选择观演日期</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
    <div class="wrap">
      <div class="current">
        <label><span class="round">2</span><span>第二步 填写与核对订单</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
    <div class="wrap">
      <div class="todo">
        <label><span class="round">3</span><span>第三步 订单支付</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
    <div class="wrap">
      <div class="todo">
        <label><span class="round">3</span><span>第四步 完成</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
  </div>
  <!---->
  <div class="header-1">
      <h2>预订信息</h2>
  </div>
  <div class="span12">
    <div class="span5"><h3>观演时间:{$data['plan_id']|planShow=4}</h3></div>
    <div class="span8">
      <table class="sui-table table-bordered mt10">
      <thead>

      <tr>
        <th>区域名称</th>
        <th>数量(张)</th>
        <th>单价(元)</th>
        <th>小计(元)</th>
      </tr>
      </thead>
      <tbody id="price_box">
      <volist name="data['data']" id="vo">
      <?php $money = $vo['num']*$vo['price'];?>
      <tr data-price="{$vo.price}" data-priceid="{$vo.priceid}" data-money="{$money}" data-num="{$vo.num}" data-area="{$vo.areaId}" data-plan="{$data['plan_id']}">
        <td>{$vo.pricename}</td>
        <td>{$vo.num}张</td>
        <td>{$vo.price}元</td>
        <td>{$money}元</td>
      </tr>
      </volist>
      </tbody>
      </table>
    </div>
  </div>  
  <div class="header-1">
      <h2>取票人信息</h2>
  </div>
  <div class="header">
    <h3>取票人</h3>        
    <span class="tip">接收确认短信</span>      
  </div>
  <div class="span6 mt10">
  <form id="myform" class="sui-form form-horizontal sui-validate">
  <div class="control-group">
    <label class="control-label v-top"><b style="color: #f00;">*</b> <span style="padding:0 24px 0 0;">姓</span>名：</label>
    <div class="controls">
      <input type="text" id="name" value="" class="input-xfat input-xlarge" placeholder="姓名">
    </div>
  </div>
  <!--
  <div class="control-group">
    <label class="control-label"><b style="color: #f00;">*</b> 
      身<span style="padding: 0 6px;">份</span>证：
    </label>
    <div class="controls">
      <input type="text" value="" name="card" id="card" class="input-xfat input-xlarge">
    </div>
  </div>
  -->
  <div class="control-group">
    <label class="control-label"><b style="color: #f00;">*</b> <span style="padding:0 24px 0 0;">手</span> 
      机：
    </label>
    <div class="controls">
      <input type="text" value="" id="phone" name="phone" class="input-xfat input-xlarge" placeholder="接收确认短信">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label"> <span style="padding:0 24px 0 0;">备</span> 
      注：
    </label>
    <div class="controls">
      <textarea style="height: 50px;" id="remark" class="input-xfat input-xlarge"></textarea>
    </div>
  </div>
  <div class="row-fluid">
    <label class="checkbox inline">
      <input name="m1" type="checkbox" value="2" checked="" disabled=""> 
      同意 <a href="#" data-toggle="modal" data-target="#myModal" data-keyboard="false">门票预购 协议</a>
    </label>
  </div>
  </form>
  </div>
<!--协议-->
<div id="myModal" tabindex="-1" role="dialog" data-hasfoot="false" class="sui-modal hide fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" data-dismiss="modal" aria-hidden="true" class="sui-close">×</button>
        <h4 id="myModalLabel" class="modal-title">门票预购协议</h4>
      </div>
      <div class="modal-body">
        {$proconf.agreement}
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="sui-btn btn-danger btn-large">关闭</button>
      </div>
    </div>
  </div>
</div>
</div>
  <!--提交订单-->
<div class="defray_box">
  <div class="sui-container pay_box">
      <!-- 现付显示提交订单 -->
      <a id="J-submit" href="#" type="submit" class="btn_submit pull-right">去支付</a>
      <div class="pull-right">
          <div class="total_price">
              订单总金额 : <dfn id="J-price">¥0</dfn>
              <!-- 现付显示 -->
          </div>
      </div>
  </div>
</div>
<script type="text/javascript" src="http://g.alicdn.com/sj/lib/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="http://g.alicdn.com/sj/dpl/1.5.1/js/sui.min.js"></script>
<script src="http://ticket.leubao.com/static/js/layer.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
  $(document).ready(function($) {
    var sub_money = 0;
    $('#price_box tr').each(function(i){
      if (!isNaN(parseFloat($(this).data('money')))) {
        sub_money += parseFloat($(this).data('money'));
      };
    });
    sub_money = sub_money.toFixed(2);
    $("#J-price").html('¥'+sub_money);
    $("#J-submit").click(function(){
      var name = $("#name").val(),
          phone = $("#phone").val();
      if(!name){
        layer.msg("取票人信息不能为空!",{icon: 2});
        return false;
      }
      if(!phone || !phone.match(/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8]))\d{8}$/)){
        layer.msg("手机号码输入有误!",{icon: 2});
        return false;
      }
      post_server(name,phone);
    });
    //表单提交
    function post_server(contact,phone){
        var postData = '',
          pay = '',
          crm = '',
          remark = $("#remark").val() ? $("#remark").val() : "空...",
          sub_type = '1',
          toJSONString = '',
          checkinT = '1',
          guide = '0',
          qditem = '0';
      /*获取支付相关数据*/
      pay = '{"cash":0,"card":0,"alipay":'+parseFloat(sub_money)+'}';
      param = '{"remark":"'+remark+'","settlement":"1","is_pay":"1"}';
      crm = '{"guide":'+guide+',"qditem":'+qditem+',"phone":'+phone+',"contact":"'+contact+'"}';
      postData = 'info={"subtotal":'+parseFloat(sub_money)+',"plan_id":1,"checkin":'+checkinT+',"sub_type":0,"data":['+ toJSONString + '],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';
      /*提交到服务器*/
      $.ajax({
          type:'POST',
          url:'<?php echo U('Home/Index/cart2');?>',
          data:postData,
          dataType:'json',
          timeout: 3500,
          error: function(){
            layer.msg('服务器请求超时，请检查网络...');
          },
          success:function(data){
              if(data.statusCode == "200"){
                  //刷新
                  window.location.href=data.url;
              }else{
                  layer.msg('下单失败,请重试....');
              }
          }
      });
    }
  });
</script>
</body>
</html>