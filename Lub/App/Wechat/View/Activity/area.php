<Managetemplate file="Wechat/Public/header"/>
<style type="text/css" media="screen">
  .content p{font-size:0.8rem;line-height: 0.5rem;}
  .content h4{margin:0.1rem;}
  .content-block{margin:0rem;}
  .mt2{margin-top: 0.2rem}
</style>
<script type="text/javascript">
    var globals = {$goods_info};
</script>
<div class="page">
  <header class="bar bar-nav">
    <h1 class="title"><i class="iconfont">&#xe603</i>{$info.title}</h1>
    <if condition="empty($uinfo['promote'])">
    <button class="button button-link button-nav pull-right"  ontouchend="window.location.href='{:U('Wechat/Index/uinfo');}'">
    </if>
      <span class="icon icon-me"></span>
    </button>
  </header>
  <div class="content">
    <div class="content-padded">
      <div valign="bottom" class="card-header color-white no-border no-padding">
        <img class='card-cover' src="https://img.alizhiyou.com/310.jpg">
      </div>
    </div>
    <div class="content-block" style="margin-top: 1.5rem">
      <p><a href="#" class="button button-big button-fill button-warning open-goods-cart">立即购票</a></p>
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
          <label>演出时间：</label>
        </dt>
        <dd>
          <ul class="model-list sku-sel-list" id="plan">
          </ul>
        </dd>
      </dl>
      <dl class="clearfix block-item">
        <dt class="model-title sku-sel-title">
          <label>演出票价：</label>
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
      <div class="block-item block-item-messages">
        <div class="sku-message">
          <dl class="clearfix">
            <dt class="pull-left">
              <label for="ipt-0"><sup class="required">*</sup>姓名</label>
            </dt>
            <dd class="comment-wrapper clearfix">
              <input data-valid-type="text" required="" tabindex="1" id="name" name="name" type="text" class="txt js-message font-size-14">
            </dd>
          </dl>
          <dl class="clearfix">
            <dt class="pull-left">
              <label for="ipt-1"><sup class="required">*</sup>电话</label>
            </dt>
            <dd class="comment-wrapper clearfix">
              <input data-valid-type="text" required="" tabindex="2" id="phone" name="phone" type="text" class="txt js-message font-size-14">
            </dd>
          </dl>
          <dl class="clearfix">
            <dt class="pull-left">
              <label for="ipt-2"><sup class="required">*</sup>身份证</label>
            </dt>
            <dd class="comment-wrapper clearfix">
              <input data-valid-type="text" tabindex="3" id="card" name="card" type="text" class="txt js-message font-size-14">
            </dd>
          </dl>
          <dl class="clearfix">
            <dt class="pull-left">
              <label for="ipt-2">留言</label>
            </dt>
            <dd class="comment-wrapper clearfix item-input">
             <input data-valid-type="text" tabindex="3" id="remark" name="remark" type="text" class="txt js-message font-size-14" value="">
            </dd>
          </dl>
        </div>
      </div>
    </div>
    <div class="btn">
      <a href="#" class="button button-fill button-warning button-big buy">下一步</a>
    </div>
  </div>
  </div>
</div>
<!--产品信息区域-->
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
  $(function() {
    wx.ready(function(){
      /*
        wx.checkJsApi({
            jsApiList: [
                'onMenuShareAppMessage',
            ]
        });
        wx.showMenuItems({
            menuList: ['menuItem:share:appMessage','menuItem:share:timeline']
        });*/
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
          $.toast("请选择演出日期!");
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
      if(plan != 0 && area != 0 && price != 0){
        if(num < globals['maxnum']){
          //限制单笔订单最大数量
          num = getNum() + 1;
          updateNum();
        }else{
          $.toast("亲，您一次只能买这么多了!");
        }
      }else if(plan == 0 && area == 0){
        $.toast("请选择演出日期和演出票价!");
      }else if(area == 0){
        $.toast("请选择演出票价!");
      }else{
        $.toast("请选择演出日期和演出票价!");
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
        $('.minus').addClass("disabled");
        $('.minus').attr("disabled", "true");
      }
    }
    //获取数量
    function getNum(){
      num = parseInt($("#num").val());
      return num;
    }
    
    $(".buy").click(function(){
      $(this).attr("disabled", true).val('提交中..');
      //验证输入
      name = $("#name").val().replace(/ /g,''),
      phone = $("#phone").val(),
      card = $("#card").val().replace(/ /g,''),
      activety_area = {$idcard},
      activety = {$info['id']},
      msg = '';
      if(plan.length == 0){
        msg = "请选择销售日期!";
      }
      if(price == 0 || num == 0){
        msg = "请选择票型并选择要购买的数量!";
      }
      if(name.length == 0){
        msg = "姓名不能为空";
      }
      if(!/^(0|86|17951)?(13[0-9]|15[012356789]|19[0-9]|18[0-9]|14[57]|17[0-9])[0-9]{8}$/.test(phone)){
        msg = "手机号码格式不正确!";
      }
      //判断身份证是否可用
      console.log(check_idcard_area(card,activety_area,activety));
      if(!check_ID(card)){
        $.toast('身份证号有误!');
        return false;
      }else if(!check_idcard_area(card,activety_area,activety)){
        $.toast('该地区不参加活动或用户已参加过活动!');
        return false;
      }
      if(msg != ''){$.toast(msg);return false;}{post_server(2,card);}
    });
    function check_ID(code) {
      var city={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江 ",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北 ",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏 ",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外 "};
      var tip = "";
      var pass= true;

      if(!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|x)$/i.test(code)){
          tip = "身份证号格式错误";
          pass = false;
      }else if(!city[code.substr(0,2)]){
          tip = "地址编码错误";
          pass = false;
      }else{
          //18位身份证需要验证最后一位校验位
          if(code.length == 18){
              code = code.split('');
              //∑(ai×Wi)(mod 11)
              //加权因子
              var factor = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ];
              //校验位
              var parity = [ 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 ];
              var sum = 0;
              var ai = 0;
              var wi = 0;
              for (var i = 0; i < 17; i++)
              {
                  ai = code[i];
                  wi = factor[i];
                  sum += ai * wi;
              }
              var last = parity[sum % 11];
              var check = code[17].toUpperCase();
              if(last != check){
                  tip = "校验位错误";
                  pass =false;
              }
          }
      }
      //if(!pass) alert(tip);
      return pass;
    }
    /*验证身份证取票 type  1验证 2 不验证  */
    function post_server(type,card){
      //计算金额活动直接底价结算
      //
      
      subtotal = parseFloat(discount * parseInt(num));
      
      remark = $('#remark').val();
      $.ajax({
        type:'GET',
        url:'<?php echo U('Wechat/Index/quota');?>'+'&plan='+plan+'&cid='+globals['user']['qditem']+'&num='+num,
        dataType:'json',
        timeout: 1500,
        error: function(){
          $.toast('服务器请求超时，请检查网络...');
        },
        success:function(data){
            if(data.statusCode == "200"){
              /*获取支付相关数据*/
              pay = '{"cash":0,"card":0,"alipay":0}';
              param = '{"remark":"'+remark+'","id_card":"'+card+'","activity":"'+activety+'","settlement":"2"}';
              crm = '{"guide":"'+globals['user']['guide']+'","qditem":"'+globals['user']['qditem']+'","phone":"'+phone+'","contact":"'+name+'","memmber":"'+globals['user']['memmber']+'"}';
              toJSONString = '{"areaId":'+area+',"priceid":'+ticket+',"idcard":"'+card+'","price":'+price+',"num":'+num+'}';
              postData = 'info={"subtotal":"'+subtotal+'","plan_id":'+plan+',"checkin":1,"sub_type":0,"type":1,"data":['+ toJSONString + '],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';
              /*提交到服务器**/
              $.ajax({
                  type:'POST',
                  url:'<?php echo U('Wechat/Activity/acty_order',$param);?>',
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
            }else{
              $.toast('配额不足，请联系渠道负责人');
            }
        }
      }); 
    }
  });
  var text = "<p style='text-align:left'>1、本票型针对屏南地区居民票，下单时必须输入屏南身份证号。<br>"+
  "2、活动期间一个身份证号一次最多可购一张票。门票一经售出，恕不退换，下单时请慎重。<br >"+
  "3、如果是屏南地区居民但身份证号非屏南号段，请至人工窗口购买。<br>"+
  "4、本系统根据订单顺序安排位置，不能保证位置一定相连，如有位置需求，请至人口窗口购买。<br>"+
  "5、1.2米以下儿童免票进场不占座，1.2米以上请购成人票。</p>";
  $(document).on('click','.open-goods-cart', function () {
    $.alert(text, '购票须知', function () {
      $.popup('.goods-cart');
    });
  });
  function check_idcard_area(code,area,actid) {
    var length = 0,retu = '';
    for (var i = 0; i < area.length; i++) {
        length = area[i].length;
        var site = code.substr(0,length);
        if(site === area[i]){
          //发送到服务器验证 TODO
          $.ajax({
            url: 'index.php?g=Api&m=Check&a=public_check_idcard',
            type: 'GET',
            dataType: 'json',
            async:false,
            data: {'ta': '31','idcard': code, 'actid': actid},
            error: function(){
              $.toast('服务器请求超时，请检查网络...');
            },
            success:function(rdata){
              if(rdata.status){
                retu = true;
              }else{
                retu = false;
              }
            }
          });
        }
    }
    if(retu){
      return true;
    }else{
      return false;
    }
}
</script>
<script id="plantpl" type="text/html">
{{# for(var i = 0, len = d.plan.length; i < len; i++){ }}
    <li class="tag sku-tag pull-left ellipsis" data-plan="{{ d.plan[i].id }}" data-num="{{d.plan[i].num}}">{{ d.plan[i].title }}</li>
{{# $(".stock-num").html(d.plan[i].num); } }}
</script> 
<script id="pricetpl" type="text/html">
{{# $(d.area[laytpl.fn]).each(function(i){ }}
  <li class="tag sku-tag pull-left ellipsis {{# if(this.num == '0'){ }}unavailable{{# } }}" data-price="{{ this.money }}" data-discount="{{ this.moneys }}" data-area="{{ this.area }}" data-priceid="{{ this.priceid }}" data-num="{{ this.num }}">{{this.moneys}}元 ({{this.name}})</li>
{{#    });}}
</script>
</body>
</html>