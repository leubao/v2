<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">支付单号:</label>
    <input type="text" name="sn" class="form-control" size="40" value="{$ginfo['sn']}" disabled>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">支付方式:</label>
    <input type="radio" name="is_pay" value="4" data-toggle="icheck" data-label="支付宝支付" <if condition="$ginfo['is_pay'] eq 4">checked</if>> 
    <input type="radio" name="is_pay" value="5" data-toggle="icheck" data-label="微信支付" <if condition="$ginfo['is_pay'] eq 5">checked</if>> 
    <input type="radio" name="is_pay" value="6" data-toggle="icheck" data-label="银联支付" <if condition="$ginfo['is_pay'] eq 6">checked</if>> 
    <input type="radio" name="is_pay" value="1" data-toggle="icheck" data-label="现金支付" <if condition="$ginfo['is_pay'] eq 1">checked</if>> 
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">金额:</label>
    <input type="text" name="money" class="form-control" size="20" value="{$ginfo['money']}" disabled>
  </div>
  <div class="form-group" id="payMsg">
  </div>
  <div class="form-group">
    <input type="text" name="card" class="form-control" size="20" id="pay_card" value="">
  </div>
  <!--倒计时区域-->
  <div id="countdown">
  
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><a href="#" class="btn btn-default" data-icon="save" onclick="pay_post();">提交</a></li>
    </ul>
</div>
<script>
/*
  $(document).ready(function(){
    settime('countdown',90);
  });*/
  var paymentT = '',
      payMsg = $('#payMsg');
  $("#pay_card").focus();
  $('#pay_card').keydown(function(e){
    var pay_card = $("#pay_card").val(),
        keycode = (e.keyCode ? e.keyCode : e.which);
    if(keycode == 13){
      if(isNull(pay_card) == false){
        layer.msg('扫码失败...');
        return false;
      }
      var plan = '{$ginfo.plan}',
          sn = '{$ginfo.sn}',
          is_pay = '{$ginfo.is_pay}',
          payKey = $("#pay_card").val();
      postData = 'info={"plan":'+plan+',"sn":'+sn+',"pay_type":'+is_pay+',"seat_type":"1","paykey":'+payKey+'}';
      paymentT = setTimeout("payment_polling()",5000);
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
  function payment_polling(){
    $.ajax({
      type:'GET',
      url:"{:U('Item/Order/public_payment_results',array('sn'=>$ginfo['sn'],'pay_type'=>$ginfo['is_pay'],'seat_type'=>1));}",
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
            paymentT = setTimeout("payment_polling()",5000);
        }
        if(result.statusCode == "400"){
          clearTimeout(paymentT); 
          layer.msg(result.message);
          $(this).dialog('refresh', 'work_quick');
           //关闭当前窗口
          $(this).dialog('close','payment');
        }
        console.log(result.statusCode);
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
        url = '<?php echo U('Item/Order/public_payment');?>';
    if(is_pay == '4' || is_pay == '5'){
      layer.msg('当前选择支付方式不允许此项操作,请重新选择支付方式...');
      return false;
    }
    postData = 'info={"pay_type":'+is_pay+',"seat_type":"1","sn":'+sn+'}';
    post_server(postData,url);
  }
  //提交到服务器 TODO  通用提交到服务器
  function post_server(postData,url){
    $.ajax({
      type:'POST',
      url:url,
      data:postData,
      dataType:'json',
      timeout: 3500,
      error: function(){
        layer.msg('服务器请求超时，请检查网络...');
      },
      success:function(data){
          if(data.statusCode == "200"){
              //刷新
              $(this).dialog('refresh', data.refresh);
              $(this).dialog({id:'print', url:''+data.forwardUrl+'', title:'门票打印',width:'213',height:'208',resizable:false,maxable:false,mask:true});

          }else{
              $(this).alertmsg('error','出票失败!');
          }
          $(this).dialog('close','payment');
      }
    });
  }
</script>