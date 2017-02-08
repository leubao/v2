<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--left info s-->
<div style="float:left; width:240px; " class="left_seat_info">
  <div class="front">普通席{$area}</div>
  <!--场次信息-->
  <table class="table table-bordered">
    <tbody>
      <tr>
        <td>日期场次：</td>
        <td>2015-07-19 第1场</td>
      </tr>
      <tr>
        <td>演出时间：</td>
        <td>20:00 - 21:10</td>
      </tr>
      <tr>
        <td>应收金额：</td>
        <td>20:00 - 21:10</td>
      </tr>
      <tr>
        <td>检票类型：</td>
        <td><input type="radio" name="custom.isshow" id="j_custom_sex1" data-toggle="icheck" value="true" data-rule="checked" data-label="一人一票&nbsp;">
          <input type="radio" name="custom.isshow" id="j_custom_sex2" data-toggle="icheck" value="false" data-label="一团一票"></td>
      </tr>
    </tbody>
  </table>
  <div class="btn-group btn-group-xs f-right" role="group"> 
    <!--购物车 s--> 
    <a href="" class="btn btn-default btn-xs seat_cart"><i class="fa fa-shopping-cart"></i></a> 
    <!--<a href="" class="btn btn-default btn-xs seat_cart"><i class="fa fa-user-plus"></i></a>--> 
    <a href="{:U('Manage/Index/index_info');}" data-toggle="dialog" data-options="id:area_info" data-mask=true; class="btn btn-default btn-xs seat_cart"><i class="fa fa-info-circle"></i></a> <a href="" class="btn btn-default btn-xs seat_cart"><i class="fa fa-question-circle"></i></a> 
    <!--<button type="button" class="btn btn-success"><input type="checkbox"> 拖动排位</button>-->
    <button type="button" class="btn btn-success"><i class="fa fa-cogs"></i> 自动排位</button>
    <button type="button" class="btn btn-danger"><i class="fa fa-circle-o-notch"></i> 重置排位</button>
    <button type="button" class="btn btn-danger"></button>
  </div>
  <!--价格信息-->
  <table class="table table-bordered">
    <thead>
      <tr>
        <td>票型</td>
        <td>单价</td>
        <td>待排数</td>
        <td>已排数</td>
      </tr>
    </thead>
    <tbody id="price_{$area}">
      <tr data-id="1">
        <td>日期场次</td>
        <td>291.00</td>
        <td><input id="t-t-1-24" type="text" value="1" size="4" data-toggle="spinner" data-min="0" data-max="100" data-step="1"></td>
        <td id="num_1_24"></td>
      </tr>
      <tr data-id="2">
        <td>日期场次</td>
        <td>291.00</td>
        <td><input id="t-t-1-25" type="text" value="1" size="4" data-toggle="spinner" data-min="0" data-max="100" data-step="1"></td>
        <td id="num_1_25"></td>
      </tr>
    </tbody>
  </table>
  <!--支付信息-->
  <table class="table table-bordered">
    <thead>
      <tr>
        <td>支付类型</td>
        <td>金额</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>现金：</td>
        <td><input type="text" size="5"></td>
      </tr>
      <tr>
        <td>划卡：</td>
        <td><input type="text" size="5"></td>
      </tr>
      <tr>
        <td>支付宝扫码：</td>
        <td><input type="text" size="5"></td>
      </tr>
      <tr>
        <td>微信扫码：</td>
        <td><input type="text" size="5"></td>
      </tr>
    </tbody>
  </table>
   <!--提交-->
  <div class="submit_seat"><a href="#" class="btn btn-success" onclick="post_server({$data.id});">设置座位</a></div>
  <!--图列 s-->
  <div id="legend"></div>
  
  <!--left info e--> 
</div>
<!--right seat s-->
<div id="seat-map-w-h-{$area}" style="margin-left: 245px; overflow: auto; white-space: nowrap;">
    <div id="seat-map-{$area}">
      <div id="seat-info-{$area}" class="seat-info"></div>
    </div>
    <!--
    <div class="booking-details">
      <h3> 选座信息：</h3>
      <p>票数: <span id="counter-{$area}"></span></p>
      <p>总计: ￥<span id="total-{$area}">0</span></p>
      <button class="checkout-button">确定购买</button>
    </div>
    -->
</div>
<!--right seat e-->
</div>
<!--选座信息-->
<div id="cart-{$area}">
  <ul id="selected-seats-{$area}">
  </ul>
</div>

<script>
$(document).ready(function() {
  /*设置座位区域的宽高*/
  var seatMapW = $(window).width() - 301;
      seatMapH = $(window).height() - 130;
  $('#seat-map-w-h-{$area}').width(seatMapW);
  $('#seat-map-w-h-{$area}').height(seatMapH);

	var cart = $('#selected-seats-{$area}'),
	   counter = $('#counter-{$area}'),
	   total = $('#total-{$area}'),
	sc = $('#seat-map-{$area}').seatCharts({
		
     map: [  //座位图 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            '__________', 
            'aaaaaaaaaaaa_aaaaaa__aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaa','aaaaaaaaaa', 'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'abcdefghijkmnopq',
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            '__________', 
            'aaaaaaaaaaaaaaaaaa__aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aa__aa__aa',
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            '__________', 
            'aaaaaaaaaaaaaaaaaa__aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aa__aa__aa',
             'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            '__________', 
            'aaaaaaaaaaaa_aaaaaa__aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaa','aaaaaaaaaa', 'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aa__aa__aa',
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            '__________', 
            'aaaaaaaaaaaaaaaaaa__aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aa__aa__aa',
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            '__________', 
            'aaaaaaaaaaaaaaaaaa__aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aaaaaaaaaa', 
            'aa__aa__aa',
        ],
        /*
		seats: { //定义座位属性
			f: {
				price   : 100,
				classes : 'first-class', 
				category: '一等座'
			},
			e: {
				price   : 40,
				classes : 'economy-class', 
				category: '二等座'
			}					
		},*/
    
		naming : { //定义行列等信息
			top : true,
			getLabel : function (character, row, column) {
				return row+"排"+column+"号";
			}
		},
		/*
		legend : { /*定义图例*
			node : $('#legend'),
			items : [
				[ 'f', 'available',   '可售'],
				[ 'e', 'available',   '预定'],
				[ 'f', 'unavailable', '售出']
			]					
		},*/
		click: function () {
      var obj = this.data();
      /*alert(this.settings.label);
      for (var i = obj.length - 1; i >= 0; i--) {
        alert(obj[i]);
      };*/
      /*获取价格政策
      $('#price_{$area}').each(function(){});*/
			if (this.status() == 'available') {/*可选座
        var param = '{"seatid":'+this.settings.label+'}';
				$('<li>'+this.data().category+'<br/>'+this.settings.label+'<br/>￥'+this.data().price+'</li>')
				.attr('id', 'cart-item-'+this.settings.id)
        .attr('data-param',param)
				.data('seatId', this.settings.id)
				.appendTo(cart);*/
				/*更新票数*/
				counter.text(sc.find('selected').length+1);
				/*计算总计金额
				total.text(recalculateTotal(sc)+this.data().price);
        */
				return 'selected';
			} else if (this.status() == 'selected') {/*已选中*/
				counter.text(sc.find('selected').length-1);
				//total.text(recalculateTotal(sc)-this.data().price);
				/*删除已预订座位*/
			//	$('#cart-item-'+this.settings.id).remove();
				return 'available';
			} else if (this.status() == 'unavailable') {/*已售出*/
				return 'unavailable';
			} else {
				return this.style();
			}
		},
		focus  : function() {
      $("#seat-info-{$area}").show().html(this.settings.label);
        var cd = getMousePoint(event);
        $("#seat-info-{$area}").css({"left":(cd.x+10)+'px',"top":(cd.y-30)+"px"});
			if (this.status() == 'available') {
				
				return 'focused';
			} else  {
				return this.style();
			}
		}
	});

	//已售出不可选座
	sc.get(['1_1', '4_4', '7_9', '7_7', '8_7']).status('unavailable');
  /*
  setInterval(function() { 
    $.ajax({ 
        type     : 'get', 
        url      : 'book.php', 
        dataType : 'json', 
        success  : function(response) { 
            //遍历所有座位  
            $.each(response.bookings, function(index, booking) { 
                //将已售出的座位状态设置为已售出 
                sc.status(booking.seat_id, 'unavailable'); 
            }); 
        } 
    }); }, 10000); //每10秒 
*/
});
//右键修改属性

</script>