<Managetemplate file="Wechat/Public/header"/>
<script type="text/javascript">
    var global= {$goods_info};
</script>
<div class="page">
  <header class="bar bar-nav">
    <button class="button button-link button-nav pull-left"  ontouchend="window.history.back()">
      <span class="icon icon-left"></span>
    </button>
    <button class="button button-link button-nav pull-right"  ontouchend="window.location.href='{:U('Wechat/Index/uinfo');}'">
      <span class="icon icon-me"></span>
    </button>
    <h1 class="title">票券兑换</h1>
  </header>
  <div class="content">
    <div class="list-block">
      <ul>
        <li>
          <div class="item-content">
            <div class="item-inner">
              <div class="item-title label">游玩日期</div>
              <div class="item-input">
                <input type="text" name="datetime" id="daydate"/>
              </div>
            </div>
          </div>
        </li>
        <li>
          <div class="item-content">
            <div class="item-inner">
              <div class="item-title label">数量</div>
              <div class="item-input">
                <input type="number" id="number" value="1" placeholder="数量">
              </div>
            </div>
          </div>
        </li>
        <li>
          <div class="item-content">
            <div class="item-inner">
              <div class="item-title label">联系人</div>
              <div class="item-input">
                <input type="text" id="name" value="" placeholder="联系人">
              </div>
            </div>
          </div>
        </li>
        <li>
          <div class="item-content">
            <div class="item-inner">
              <div class="item-title label">电话</div>
              <div class="item-input">
                <input type="text" id="phone" value="" placeholder="手机号">
              </div>
            </div>
          </div>
        </li>

        <li class="align-top">
          <div class="item-content">
            <div class="item-inner">
              <div class="item-title label">备注</div>
              <div class="item-input">
                <textarea id="remark"></textarea>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <div class="content-block">
      <div class="row">
        <div class="col-100">
          <a href="#" type="submit" class="button button-big button-fill button-success buy">立即兑换</a>
        </div>
      </div>
    </div>
  </div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
$(function() {
  $("#daydate").calendar({
    value: ['{$date}'],
    minDate: '{$mindate}'
  });
  wx.ready(function(){wx.hideOptionMenu();});
  var phone = '',
      name = '',
      datetime = ''
      msg = '',
      count = {$info.number},
      num = 0,
      plan = '',
      area = '0',
      ticket = {$info.param.ticket.ticket},
      price = {$info.param.ticket.ticket},
      discount = {$info.param.ticket.discount},
      pay = '',
      crm = '',
      param = '',
      toJSONString = '',
      postData = '',
      subtotal = '0';
  $(".buy").click(function(){
      name = $("#name").val().replace(/ /g,''),
      phone = $("#phone").val(),
      num = $("#number").val();
      plan = $("#daydate").val();
      $(this).addClass('disabled').text('提交中..');
      
      msg = '';
      if(plan.length == 0){
        msg = "请选择体验日期!";
      }
      if(price == 0 || num == 0){
        msg = "购买数量有误!";
      }
      if(num > count){
        msg = "购买数量有误,超过可兑换数量!";
      }
      if(name.length == 0){
        msg = "姓名不能为空";
      }
      if(!/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[0-9])[0-9]{8}$/.test(phone)){
        msg = "手机号码格式不正确!";
      }
      if(msg != ''){$.toast(msg);return false;}
      post_server(2);
  });
  /*验证身份证取票 type  1验证 2 不验证  */
  function post_server(type,card){

    subtotal = '0';//parseFloat(discount * parseInt(num));
    remark = $('#remark').val();
    /*获取支付相关数据*/
    pay = '{"cash":0,"card":0,"alipay":0}';
    param = '{"remark":"'+remark+'","settlement":"'+global['user']['epay']+'"}';
    crm = '{"guide":"'+global['user']['guide']+'","qditem":"'+global['user']['qditem']+'","phone":"'+phone+'","contact":"'+name+'","memmber":"'+global['user']['memmber']+'"}';
    toJSONString = '{"areaId":'+area+',"priceid":'+ticket+',"price":'+price+',"num":'+num+'}';
    postData = 'info={"subtotal":"'+subtotal+'","plan_id":"'+plan+'","number":'+num+',"checkin":1,"sub_type":0,"type":1,"data":['+ toJSONString + '],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';
    /*提交到服务器**/
    $.ajax({
        type:'POST',
        url:'<?php echo U('Wechat/Index/useticket',$param);?>',
        data:postData,
        dataType:'json',
        success:function(data){
          if(data.statusCode == "200"){
              location.href = data.url;
              $.toast("兑换成功!");
          }else{
              $.toast("下单失败!"+data.msg);
              location.href = data.url;
              $('.buy').removeClass('disabled').text('立即兑换');
          }
        }
    });
  }
});

</script>
</body>
</html>