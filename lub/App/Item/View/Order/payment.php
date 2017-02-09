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
  <div class="form-group">
    <input type="text" name="card" class="form-control" size="20" id="pay_card" value="">
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">提交</button></li>
    </ul>
</div>
<script>
  $("#pay_card").focus();
  $('#pay_card').keydown(function(e){
    var pay_card = $("#pay_card").val(),
        keycode = (e.keyCode ? e.keyCode : e.which);
    if(keycode ==13){
      if(isNull(pay_card) == false){
        layer.msg('扫码失败...');
        return false;
      }
      var plan = '{$ginfo.plan}',
          sn = '{$ginfo.sn}',
          is_pay = '{$ginfo.is_pay}',
          payKey = $("#pay_card").val();
      postData = 'info={"plan":'+plan+',"sn":'+sn+',"is_pay":'+is_pay+',"paykey":'+payKey+'}';
      $.ajax({
        type:'POST',
        url:'<?php echo U('Item/Order/public_payment');?>',
        data:postData,
        dataType:'json',
        timeout: 3500,
        error: function(){
          layer.msg('服务器请求超时，请检查网络...');
        },
        success:function(data){
            if(data.statusCode == "200"){
                //刷新
                $(this).dialog('refresh', 'work_quick');
                $(this).dialog({id:'print', url:''+data.forwardUrl+'', title:data.title,width:data.width,height:data.height,resizable:false,maxable:false,mask:true});
            }else{
                $(this).alertmsg('error','出票失败!');
            }
        }
      });
    } 
  });
</script>