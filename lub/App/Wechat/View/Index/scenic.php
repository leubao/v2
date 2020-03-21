<Managetemplate file="Wechat/Public/header"/>
<style type="text/css" media="screen">
  .content p{font-size:0.8rem;line-height: 0.5rem;}
  .content h4{margin:0.1rem;}
  .content-block{margin:0rem;}
  .mt2{margin-top: 0.2rem}
  .button .button-red{
    background-color: #a40000;
  }
  .button-red.button-fill {
    color: #fff;
    background-color: #a40000;
}
</style>
<script type="text/javascript">
    var globals = {$goods_info};
</script>
<div class="page">
  <header class="bar bar-nav">
    <h1 class="title"><i class="iconfont">&#xe603</i>{$proconf.wx_page_title}</h1>
    <if condition="empty($uinfo['promote'])">
    <button class="button button-link button-nav pull-right"  ontouchend="window.location.href='{:U('Wechat/Index/uinfo');}'">
    </if>
      <span class="icon icon-me"></span>
    </button>
  </header>
  <div class="content">
    <div class="content-padded">
      <h4><strong>【梦里老家】太子惊魂惊悚体验馆</strong></h4>
      <p>一座穿越千年的古镇</p>
      <p>一场非看不可的演出</p>
      <p>尽在【梦里老家】</p>
      <p>&nbsp;</p>
      <p>营业时间:8:30 - 17:30</p>
      <p>咨询电话: 0793-7377777</p>
    </div>
  <div class="content-block" style="margin-top: 1.5rem">
    <p><a href="#" class="button button-big button-fill button-red open-goods-cart">立即购票</a></p>
  </div>
  <div class="content-padded">
    <div valign="bottom" class="card-header color-white no-border no-padding">
      <img class='card-cover' src="d/wap/w3.jpg">
    </div>
  </div>
</div>
  <!--内容-->
</div>
<!-- About Popup -->
<div class="popup goods-cart">
  <header class="bar bar-nav">
    <a href="#" class="icon pull-right close-popup"><i class="iconfont">&#xe609</i></a>
  </header>
  <div class="sku-layout">
    <div class="adv-opts layout-content">
    <div class="goods-models js-sku-views block block-list block-border-top-none">
      <dl class="clearfix block-item">
        <dt class="model-title sku-sel-title">
          <label>游玩日期：</label>
        </dt>
        <dd><?php //dump($uinfo);?>
          <ul class="model-list sku-sel-list" id="plan">
          </ul>
        </dd>
      </dl>
      <dl class="clearfix block-item">
        <dt class="model-title sku-sel-title">
          <label>价格：</label>
        </dt>
        <dd>
          <ul class="model-list sku-sel-list" id="price">
            <li class="tag sku-tag pull-left ellipsis unavailable">请选择售票日期</li>
          </ul>
        </dd>
      </dl>
      <dl class="clearfix block-item">
        <dt class="model-title sku-num pull-left">
          <label>数量</label>
        </dt>
        <dd>
          <dl class="clearfix">
            <div class="quantity">
              <button class="minus disabled" type="button" disabled="true"></button>
              <input type="text" class="txt" value="1" id="num">
              <button class="plus" type="button"></button>
              <div class="response-area response-area-minus"></div>
              <div class="response-area response-area-plus"></div>
              <div class="txtCover"></div>
            </div>
            <div class="stock pull-right font-size-12">
              <dt class="model-title stock-label pull-left">
                <label>剩余: </label>
              </dt>
              <dd class="stock-num"> 0 </dd>
            </div>
      
          </dl>
        </dd>
      </dl>
      
    </div>
    <div class="btn">
      <a href="#" class="button button-fill button-red button-big buy">下一步</a>
    </div>
  </div>
  </div>
</div>
<!--产品信息区域-->
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
  $(function() {
    wx.ready(function(){
        wx.onMenuShareAppMessage({
            title: '{$proconf.wx_share_title}',
            desc: '{$proconf.wx_share_desc}',
            link: '{$urls}',
            imgUrl: '{$config_siteurl}static/images/wshare_{$pid}.jpg',
            trigger: function (res) {
            },
            success: function (res) {
                alert('分享给好友成功');
            },
            cancel: function (res) {
            },
            fail: function (res) {
                alert(JSON.stringify(res));
            }
        });
        wx.onMenuShareTimeline({
            title: '{$proconf.wx_share_title}',
            desc: '{$proconf.wx_share_desc}',
            link: '{$urls}',
            imgUrl: '{$config_siteurl}static/images/wshare_{$pid}.jpg',
            trigger: function (res) {
            },
            success: function (res) {
            },
            cancel: function (res) {
            },
            fail: function (res) {
                alert(JSON.stringify(res));
            }
        });
    });
    var getPlantpl = document.getElementById('plantpl').innerHTML;
    var getPricetpl = document.getElementById('pricetpl').innerHTML;
    laytpl(getPlantpl).render(globals, function(html){
        document.getElementById('plan').innerHTML = html;
    });
    var plan = '0',
        area = '0',
        ticket = '0',
        price = '0',
        discount = '0',
        num = '0',
        name = '',
        phone = '',
        card = '',
        msg = '',
        pay = '',
        crm = '',
        param = '',
        toJSONString = '',
        postData = '',
        subtotal = '0',
        remark = '';
    $("#plan li").click(function(){
      //检查当前被选择的元素是否已经有已选中的
      $(".goods-models li").each(function(){
        if($(this).hasClass("tag-orangef60 active")){ toggle($(this))};
      });
      //为当前选择加上
      active($(this));
      refreshNum();
      plan = $(this).data('plan');
      area = 0; 
      ticket = 0;
      price = 0;
      $(".stock-num").html($(this).data('num'));
      laytpl.fn = plan;
      laytpl(getPricetpl).render(globals, function(html){
        document.getElementById('price').innerHTML = html;
      });
    });
    $(document).on("click","#price li",function(){
       //判断是否已经选择计划
      if(!$(this).hasClass("unavailable")){
        if(plan != 0){
          $("#price li").each(function(){
            if($(this).hasClass("tag-orangef60 active")){ toggle($(this))};
          });
          area = $(this).data('area');
          ticket = $(this).data('priceid');
          price = $(this).data('price');
          discount = $(this).data('discount');
          num = $(this).data('num');
          //更新可售数量  当为0时 禁用
          $(".stock-num").html(num);
          active($(this));
          refreshNum();
        }else{
          $.toast("请选择游玩日期!");
        }
      } 
    });
    //删除选中状态
    function toggle(t){t.toggleClass("tag-orangef60");t.toggleClass("active");}
    //选中
    function active(t){t.addClass("tag-orangef60");t.addClass("active");}
    //删除选中
    function deActive(t){t.removeClass("tag-orangef60");t.removeClass("active");}
    //数量增加减少
    $(".response-area-minus").click(function(){
      if(num > 1){
        num = getNum() - 1;
        updateNum();
      }else{
        updateBtnStatus();
      }
    });
    $(".response-area-plus").click(function(){
      //判断是否选择日期和价格
      if(plan != 0 && price != 0){
        if(num < globals['user']['maxnum']){
          //限制单笔订单最大数量
          num = getNum() + 1;
          updateNum();
        }else{
          $.toast("亲，您一次只能买这么多了!"+globals['user']['maxnum']);
        }
      }else if(plan == 0 && price == 0){
        $.toast("请选择游玩日期和票型!");
      }else{
        $.toast("请选择游玩日期和票型!");
      }
    });
    function changeNum(t){
      $("#num").val();
    }
    //更换场次时重置页面
    function refreshNum(){
      $("#num").val('1');
      getNum();
      updateBtnStatus();
    }
    //更新数量
    function updateNum(){
        $("#num").val(num);
        updateBtnStatus();
    }
    //更新数量增减状态
    function updateBtnStatus(){
      if(num > 1){
        $('.minus').removeClass("disabled");
        $('.minus').removeAttr("disabled");
      }else{
        $('.minus').addClass("disabled"),
        $('.minus').attr("disabled", "true")
      }
    }
    //获取数量
    function getNum(){
      num = parseInt($("#num").val());
      return num;
    }
    
    $(".buy").click(function(){
      $(this).attr("disabled", true).val('提交中..');
      msg = '';
      if(plan.length == 0){
        msg = "请选择销售日期!";
      }
      if(price == 0 || num == 0){
        msg = "请选择票型并选择要购买的数量!";
      }
      if(msg != ''){$.toast(msg);return false;}
      post_server(2);
    });
    /*验证身份证取票 type  1验证 2 不验证  */
    function post_server(type,card){
 
      subtotal = parseFloat(discount * parseInt(num));
      remark = $('#remark').val();
      /*获取支付相关数据*/
      pay = '{"cash":0,"card":0,"alipay":0}';
      param = '{"remark":"'+remark+'","settlement":"'+globals['user']['epay']+'"}';
      crm = '{"guide":"'+globals['user']['guide']+'","qditem":"'+globals['user']['qditem']+'","phone":"'+phone+'","contact":"'+name+'","memmber":"'+globals['user']['memmber']+'"}';
      toJSONString = '{"areaId":'+area+',"priceid":'+ticket+',"price":'+price+',"num":'+num+'}';
      postData = 'info={"subtotal":"'+subtotal+'","plan_id":'+plan+',"checkin":1,"sub_type":0,"type":1,"data":['+ toJSONString + '],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';
      /*提交到服务器**/
      $.ajax({
          type:'POST',
          url:'<?php echo U('Wechat/Index/scenic_order',$param);?>',
          data:postData,
          dataType:'json',
          success:function(data){
              if(data.statusCode == "200"){
                  location.href = data.url;
              }else{
                  $.toast("下单失败!"+data.msg);
              }
          }
      });
    }
  });
  var text = "<p style='text-align:left'>1、该项目不适合高血压、低血压、心脏病患者、孕妇、怀抱婴儿者、酒醉者、刺激承受力差的游客游玩;<br>"+
"2、高度在1.4米以下儿童，不适宜体验该项目;<br>"+
"3、禁止损毁设备、攀爬、翻越建筑和围栏，按照红色指示路线行走，严禁逆行;<br>"+
"4、请勿在项目内部追逐、打闹，遇到鬼怪演员请和睦相处;<br>"+
"5、请妥善保管好您的随身物品;<br>"+
"6、项目内部昏暗，不适宜穿高跟鞋、轮滑鞋等游客游玩;<br>"+
"7、游玩过程中，如无法继续前行，请举起双手向前交叉，工作人员会带您重返文明世界。</p>";
  $(document).on('click','.open-goods-cart', function () {
    $.alert(text, '购票须知', function () {
      $.popup('.goods-cart');
    });
  });
</script>
<script id="plantpl" type="text/html">
{{# for(var i = 0, len = d.plan.length; i < len; i++){ }}
    <li class="tag sku-tag pull-left ellipsis" data-plan="{{ d.plan[i].id }}" data-num="{{d.plan[i].num}}">{{ d.plan[i].title }}</li>
{{# $(".stock-num").html(d.plan[i].num); } }}
</script> 
<script id="pricetpl" type="text/html">
{{# $(d.area[laytpl.fn]).each(function(i){ }}
  <li class="tag sku-tag pull-left ellipsis {{# if(this.num == '0'){ }}unavailable{{# } }}" data-price="{{ this.price }}" data-discount="{{ this.discount }}" data-area="{{ this.area }}" data-priceid="{{ this.id }}" data-num="{{ this.area_num }}">{{this.discount}}元 (<?php if($proconf['price_view'] == '2'){ ?>{{this.name}}{{this.remark}}<?php }else{ ?>{{this.name}} <?php }?>)</li>
{{#    });}}
</script>
</body>
</html>