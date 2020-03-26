<Managetemplate file="Wechat/Public/header"/>
<style type="text/css" media="screen">

<if condition="$data['status'] eq '1'">
  .page{background-color: #ff6600}
  <else/>
  .page{background-color: #9ea7b4}
  </if>
</style>
<div class="page">
	<div class="content">
      <div class="list-block">
      <ul>
        <ul>
          <li class="item-content">
            <div class="item-inner">
              <div class="item-title">产品名称 : {$data.product_id|product_name}</div>
            </div>
          </li>
          <li class="item-content">
            <div class="item-inner">
              <div class="item-title">订单号 : {$data.order_sn}</div>
            </div>
          </li>
          <li class="item-content">
            <div class="item-inner">
              <div class="item-title">场次 : {$data.plan_id|planShow}</div>
            </div>
          </li>
          <li class="item-content">
            <div class="item-inner">
              <div class="item-title">数量 : {$data.number} 人</div>
            </div>
          </li>
      </ul>
    </div>  		
    <div class="card">
      <div class="card-header">体验项目:</div>
      <div class="card-content">
        <div class="list-block">
          <ul>
            <li class="item-content">
              <div class="item-inner">
                <div class="item-title-row" <if condition="$data['status'] eq '9'">style="text-decoration:line-through;"</if>>
                  <div class="item-title" style="text-align: center;font-size: 30px">太子惊魂</div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="card-footer">
        <span>状态:<if condition="$data['status'] eq '1'">未使用<else />已使用</if></span>
        <span>{$data['uptime']|date="Y-m-d H:m:s",###}</span>
      </div>
    </div>            
    <div class="card">
          <div class="card-header">使用说明</div>
          <div class="card-content">
              <div class="card-content-inner">
                  <ul>
                      <li>1. 向《太子惊魂》惊悚体验馆工作人员出示此页面;</li>
                      <li>2. 核销密码请由检票人员输入;</li>
                      <li>3. 最终解释权归梦里老家演艺小镇所有!</li>
                  </ul>
              </div>
          </div>
      </div>
      <if condition="$data['status'] eq '1'">
  		<div class="content-block">
        <p><a href="#" id="led" class="button button-big button-fill button-success external">立即使用</a></p>
      </div>
      </if>
	</div>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript"> 
    $(document).on('click','#led', function () {
        $.prompt('请输入核销密码', '门票核销',function (value) {
          if(value !== '8888'){
            $.toast('核销密码有误');
          }else{
            $.get('<?php echo U('Wechat/index/check_ticket',array('sn'=>$data['order_sn']));?>',function(data){
              if(data.statusCode == '200'){
                $.toast('核销成功');
              }else{
                $.toast('核销失败');
              }
            })
            window.location.reload();
          }
        });
    });
</script>
</body>
</html>