<include file="Index:header" />
<div class="sui-container" style="margin-top: 20px">
  <!---->
  <div class="header-1">
      <h2>订单查询</h2>
  </div>
  <div class="span12">
    <div class="span4 offset3 mt10">
      <form class="sui-form form-horizontal">
        <div class="control-group" style="font-size: 18px">
          <label data-toggle="radio" class="radio-pretty inline checked">
            <input type="radio" checked="checked" name="type" value="2"><span>手机号</span>
          </label>
          <label data-toggle="radio" class="radio-pretty inline">
            <input type="radio" name="type" value="1"><span>订单号</span>
          </label>
        </div>
        <div class="control-group">
          <div class="controls">
            <input type="text" class="input-xlarge" name="sn" id="sn" style="height: 30px">
          </div>
        </div>
        <button type="button" class="sui-btn btn-danger google_btn" id="submit">查  询</button>
      </form>
    </div>
    <!--详情-->
    <div id="info"></div>
  </div>

</div>
<script type="text/javascript" src="http://g.alicdn.com/sj/lib/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="http://g.alicdn.com/sj/dpl/1.5.1/js/sui.min.js"></script>
<script src="http://new.leubao.com/static/js/layer.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
  $(document).ready(function($) {
    $("#submit").click(function(){
      var type = $('input[name="type"]:checked').val(),
          data = $("#sn").val();
      if(!type){
        layer.msg("请选择查询类型!",{icon: 2});
        return false;
      }
      if(type == 1){
        if(!data || !data.match(/^[0-9]*$/)){
          layer.msg("订单号输入有误!",{icon: 2});
          return false;
        }
      }else{
        if(!data || !data.match(/^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8]))\d{8}$/)){
          layer.msg("手机号码输入有误!",{icon: 2});
          return false;
        }
      }
      post_server(type,data);
    });
    //表单提交
    function post_server(type,data){
      var postData = 'info={"type":"'+type+'","data":"'+data+'"}';
      /*提交到服务器*/
      $.ajax({
          type:'POST',
          url:'<?php echo U('Home/Index/google');?>',
          data:postData,
          dataType:'json',
          timeout: 3500,
          error: function(){
            layer.msg('服务器请求超时，请检查网络...');
          },
          success:function(data){
              if(data.statusCode == "200"){
                  //刷新
                  
              }else{
                  layer.msg('未找到相关订单...');
              }
          }
      });
    }
  });
</script>
</body>
</html>