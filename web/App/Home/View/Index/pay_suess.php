<include file="Index:header" />
<div class="sui-container pd80">
  <div class="sui-steps steps-large steps-auto">
    <div class="wrap">
      <div class="finished">
        <label><span class="round"><i class="sui-icon icon-pc-right"></i></span><span>第一步 选择观演日期</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
    <div class="wrap">
      <div class="finished">
        <label><span class="round"><i class="sui-icon icon-pc-right"></i></span><span>第二步 填写与核对订单</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
    <div class="wrap">
      <div class="finished">
        <label><span class="round"><i class="sui-icon icon-pc-right"></i></span><span>第三步 订单支付</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
    <div class="wrap">
      <div class="current">
        <label><span class="round">4</span><span>第四步 完成</span></label><i class="triangle-right-bg"></i><i class="triangle-right"></i>
      </div>
    </div>
  </div>
  <div class="span12 pay_suess">
    <!--成功提示-->
    <if condition="$data.status eq '1'">
    <div class="sui-msg msg-large msg-naked msg-success">
      <div class="msg-con">你已成功预定{$data['plan_id']|planShow=4} 《{$data['product_id']|product_name}》观演门票
        <p>
          <h3>订单号:{$data.order_sn}</h3>
        </p>
      </div>
      <s class="msg-icon"></s>
    </div>
    <else />
    <!--等待付款icon-warning -->
    <div class="sui-msg msg-large msg-naked msg-info">
      <div class="msg-con">你预定{$data['plan_id']|planShow=4} 《{$data['product_id']|product_name}》观演门票等待付款...
        <p>
          <h3>订单号:{$data.order_sn}</h3>
        </p>
      </div>
      <s class="msg-icon"></s>
    </div>
    </if>
    <div class="mt10">
    <if condition="$data.status eq '1'">
      <a href="{:U('Home/Index/shop');}" class="sui-btn google_btn btn-success">完成购买</a>
      <a href="{:U('Home/Index/oinfo',array('sn'=>$data['order_sn']));}" class="sui-btn google_btn btn-danger">订单详情</a>
    <else />
      <a href="{:U('Home/Index/pay_suess',array('sn'=>$data['order_sn']));}" class="sui-btn google_btn btn-danger">刷新页面</a>
    </if>
    </div>
    </div>  
    <div class="sui-msg mt10 msg-naked msg-tips">
      <div class="msg-con">重要提示
      <p>
          {$proconf.agreement}
        </p>
      </div>
      <s class="msg-icon"></s>
    </div>
  
</div>
<script type="text/javascript" src="http://g.alicdn.com/sj/lib/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="http://g.alicdn.com/sj/dpl/1.5.1/js/sui.min.js"></script>
<script src="http://new.leubao.com/static/js/layer.js" type="text/javascript" charset="utf-8"></script>
</body>
</html>