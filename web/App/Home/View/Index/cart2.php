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
      <div class="current">
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
      <h2>订单信息</h2>
  </div>
  <div class="span8">
    <div class="span5"><h3><strong>观演时间:</strong> {$data['plan_id']|planShow=4}</h3></div>
    <div class="span5"><h3><strong>订 单  号:</strong> {$data['sn']}</h3></div>
    <div class="span5"><h3><strong>订单金额:</strong> <dfn id="J-price" style="color: #f60">{$data['subtotal']|format_money}元</dfn></h3></div>
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
  <div class="pull-right">
      <a type="button" href="{:U('Home/Index/pay',['sn'=>$data['sn']]);}" target="_blank" class="sui-btn google_btn btn-primary mt10 paybtn"><span>支付宝支付</span></a>
      <a type="button" href="{:U('Home/Index/pay',['sn'=>$data['sn']]);}" target="_blank" class="sui-btn google_btn btn-success mt10 paybtn"><span>微信支付</span></a>
  </div>
  <!--支付方式 s-->
  <form method="post" action="{:U('Home/Index/pay')}" id="form" target="_blank">
    <ul class="sui-nav nav-tabs nav-xlarge" style="clear: both;">
      <li class="active"><a href="#aliPay" data-toggle="tab">支付宝</a></li>
    </ul>
    <div class="tab-content tab-wraped">
      <div id="aliPay" class="tab-pane active">
        <span></span>   
      </div>
      <button type="submit" id="pay" class="sui-btn google_btn btn-primary mt10 paybtn"><span>支付宝支付</span></button>
      <button type="submit" id="pay" class="sui-btn google_btn btn-success mt10 paybtn"><span>微信支付</span></button>
    </div>
  </form>
  
  <!--支付方式 e-->
</div>
<script type="text/javascript" src="http://g.alicdn.com/sj/lib/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="http://g.alicdn.com/sj/dpl/1.5.1/js/sui.min.js"></script>
<script src="http://ticket.leubao.com/static/js/layer.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
  $(document).ready(function($) {
      $(".paybtn").click(function(){
        //禁用支付按钮
        $(this).attr({"disabled":"disabled"});
        //弹出支付窗口
        var url = '{:U('Home/index/pay_suess',array('sn'=>$data['sn']));}';
        layer.confirm('', {
          btn: ['已完成支付','支付中遇到问题'] //按钮
        }, function(){
          window.location.href=url;
        }, function(){
          window.location.href=url;
        });
      });
    });
</script>
</body>
</html>