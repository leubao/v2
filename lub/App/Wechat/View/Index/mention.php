<Managetemplate file="Wechat/Public/header"/>
<div class="page">
  <header class="bar bar-nav">
    <button class="button button-link button-nav pull-left"  ontouchend="window.history.back()">
      <span class="icon icon-left"></span>
    </button>
    <button class="button button-link button-nav pull-right"  ontouchend="window.location.href='{:U('Wechat/Index/uinfo');}'">
      <span class="icon icon-me"></span>
    </button>
    <h1 class="title">资金提现</h1>
  </header>
  <div class="content">
    <div class="list-block">
      <ul>
        <li>
          <div class="item-content">
            <div class="item-inner">
              <div class="item-input">
                <input type="text" id="money" placeholder="金额">
              </div>
            </div>
          </div>
        </li>
        <li class="align-top">
        <div class="item-content">
          <div class="item-inner">
            <div class="item-title label">备注</div>
            <div class="item-input">
              <textarea name="remark" id="remark">{$money}</textarea>
            </div>
          </div>
        </div>
      </li>
      </ul>
    </div>
    <div class="content-block">
      <div class="row">
        <div class="col-100"><a href="#" type="submit" class="button button-big button-fill button-success sub">提交申请</a></div>
      </div>
    </div>
  </div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
$(function() {
  var openid = '{$user.user.openid}',
      cash = '{$money}',
      msg = '';
  wx.ready(function(){wx.hideOptionMenu();});
  $(".sub").click(function(){
    //验证输入
    var money = $("#money").val(),
        remark = $("#remark").val();
    if(money == ''){msg = "请输入提现金额";}
    if(money > cash){msg = "超出额度,无法完成操作";}
    if(msg != ''){msgs(msg);msg = '';return false;}else{post_server(money, remark, openid);}
  });
  function post_server(money,remark,openid){
    var postData = 'info={"money":"'+money+'","remark":"'+remark+'","openid":"'+openid+'"}';
    /*提交到服务器**/
    $.ajax({
        type:'POST',
        url:'<?php echo U('Wechat/Index/mention');?>',
        data:postData,
        dataType:'json',
        timeout: 1500,
        error: function(){
          layer.msg('服务器请求超时，请检查网络...');
        },
        success:function(data){
            if(data.statusCode == "200"){
              $.alert('申请成功!', function () {
                  location.href = data.url;
              });
            }else{
                $.toast(data.msg);
            }
        }
    });
  }
});
</script>
</body>
</html>