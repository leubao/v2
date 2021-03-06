<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent" id="payPage">
	<div class="form-group">
    <label class="col-sm-2 control-label">支付单号:</label>
    <input type="text" name="sn" class="form-control" size="40" value="{$ginfo['sn']}" disabled>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">支付方式:</label>
    <input type="radio" name="is_pay" value="4" data-toggle="icheck" data-label="支付宝支付" <if condition="$ginfo['is_pay'] eq 4">checked</if>> 
    <input type="radio" name="is_pay" value="5" data-toggle="icheck" data-label="微信支付" <if condition="$ginfo['is_pay'] eq 5">checked</if>> 
    <input type="radio" name="is_pay" value="6" data-toggle="icheck" data-label="POS机划卡" <if condition="$ginfo['is_pay'] eq 6">checked</if>> 
    <input type="radio" name="is_pay" value="1" data-toggle="icheck" data-label="现金支付" <if condition="$ginfo['is_pay'] eq 1">checked</if>> 
    <input type="radio" name="is_pay" value="3" data-toggle="icheck" data-label="签单" <if condition="$ginfo['is_pay'] eq 3">checked</if>> 
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">金额:</label>
    <input type="text" name="money" class="form-control" size="20" value="{$ginfo['money']}" disabled>
  </div>
  <div class="form-group" id="payMsg">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">授权码:</label>
    <input type="text" name="card" class="form-control" size="20" id="pay_card" value="">
  </div>
  <!--倒计时区域-->
  <div id="countdown">
  
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><a href="#" id="payment_submit" class="btn btn-default" data-icon="save" onclick="pay_post();">提交</a></li>
    </ul>
</div>
<script>
/*
  $(document).ready(function(){
    settime('countdown',90);
  });*/
  /*更新支付方式清空授权码*/
  $("input:radio[name='is_pay']").on('ifChanged', function(e) {
    $("#pay_card").val("");
    $("#pay_card").focus();
  });
  var paymentT = '',
      payMsg = $('#payMsg');
  $("#pay_card").focus();
  $('#pay_card').keydown(function(e){
    var pay_card = $("#pay_card").val(),
        is_pay = $('input[name="is_pay"]:checked').val(),
        keycode = (e.keyCode ? e.keyCode : e.which);
    if(keycode == 13){
      if(isNull(pay_card) == false){
        layer.msg('扫码失败...');
        return false;
      }
      if(is_pay == '1' || is_pay == '3' || is_pay == '6'){
        layer.msg('请选择微信支付或支付宝支付...');
        return false;
      }
      var plan = '{$ginfo.plan}',
          sn = '{$ginfo.sn}',
          payKey = $("#pay_card").val(),
          order_type = '{$ginfo.order_type}';
      postData = 'info={"plan":'+plan+',"sn":'+sn+',"pay_type":'+is_pay+',"seat_type":"1","order_type":'+order_type+',"paykey":'+payKey+'}';
      paymentT = setTimeout("payment_polling("+is_pay+")",5000);
      //向第三方支付提交支付请求
      $.ajax({
        type:'POST',
        url:'<?php echo U('Item/Order/public_payment');?>',
        data:postData,
        dataType:'json',
        timeout: 3500,
        error: function(){
          layer.msg('服务器请求超时，请检查网络...');
        },
        success:function(result){
            payMsg.html("扫码成功,等待客户确认....");
            if(result.statusCode == "200"){
              //倒计时支付结果，等待支付结果
              //settime('countdown',90);
              $(this).dialog('refresh', result.refresh);
              $(this).dialog({id:'print', url:''+result.forwardUrl+'', title:'门票打印',width:'213',height:'208',resizable:false,maxable:false,mask:true});
              //关闭当前窗口
              $(this).dialog('close','payment');
            }
            if(result.statusCode == "300"){
              payMsg.html(result.message);
            }
            if(result.statusCode == "400"){
              layer.msg(result.message);
              $(this).dialog('refresh', 'work_quick');
              //关闭当前窗口
              $(this).dialog('close','payment');
            }
        }
      });
    } 
  });

  function payment_polling(is_pay){
    $.ajax({
      type:'GET',
      url:"{:U('Item/Order/public_payment_results',array('sn'=>$ginfo['sn'],'seat_type'=>1,'order_type'=>$ginfo['order_type']));}&pay_type="+is_pay,
      dataType:'json',
      timeout: 3500,
      error: function(){
        layer.msg('服务器请求超时，请检查网络...');
      },
      success:function(result){
        if(result.statusCode == "200"){
          clearTimeout(paymentT); 
          //刷新
          $(this).dialog('refresh', result.refresh);
          $(this).dialog({id:'print', url:''+result.forwardUrl+'', title:'门票打印',width:'213',height:'208',resizable:false,maxable:false,mask:true});
           //关闭当前窗口
          $(this).dialog('close','payment');
        }
        if(result.statusCode == "300"){
            //刷新
            payMsg.html(result.message);
            paymentT = setTimeout("payment_polling("+is_pay+")",5000);
        }
        if(result.statusCode == "400"){
          clearTimeout(paymentT);
        }
      }
    });
  }
  /**
   * 倒计时
   * @param  {[type]} showmsg   显示区域
   * @param  {int} countdown 总时长
   */
  function settime(showmsg,countdown) {
    if (countdown == 0) { 
      $('#'+showmsg).html(countdown);
      $(this).dialog('refresh', 'work_quick');
      //关闭当前窗口
      $(this).dialog('close','payment');
    } else {
      $('#'+showmsg).html(countdown);
      countdown--; 
    } 
    setTimeout(function() { settime(showmsg,countdown) },1000) 
  } 
  function pay_post() {
    //判断是否是线下收款方式
    var is_pay = $('input[name="is_pay"]:checked').val(),
        postData = '',
        plan = '{$ginfo.plan}',
        sn = '{$ginfo.sn}',
        order_type = '{$ginfo.order_type}',
        url = '<?php echo U('Item/Order/public_payment');?>';
    if(is_pay == '4' || is_pay == '5'){
      layer.msg('当前选择支付方式不允许此项操作,请重新选择支付方式...');
      return false;
    }
    postData = 'info={"plan":'+plan+',"pay_type":'+is_pay+',"seat_type":"1","order_type":'+order_type+',"sn":'+sn+'}';
    post_server(postData,url,'payment');
  }
</script>