<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--left info s-->
<div style="float:left; width:240px; " class="left_seat_info">
  <div class="front">{$data.name}</div>
  <div class="btn-group btn-group-xs f-right" role="group"> 
    <!--购物车 s
    <a href="" class="btn btn-default btn-xs seat_cart"><i class="fa fa-shopping-cart"></i></a> 
    <a href="" class="btn btn-default btn-xs seat_cart"><i class="fa fa-user-plus"></i></a>
    <a href="{:U('Manage/Index/index_info');}" data-toggle="dialog" data-options="id:area_info" data-mask=true; class="btn btn-default btn-xs seat_cart"><i class="fa fa-info-circle"></i></a> <a href="" class="btn btn-default btn-xs seat_cart"><i class="fa fa-question-circle"></i></a> 
    <!--<button type="button" class="btn btn-success"><input type="checkbox"> 拖动排位</button>
    <button type="button" class="btn btn-success"><i class="fa fa-cogs"></i> 自动排位</button>-->
    <a type="button" class="btn btn-danger" onclick="$(this).dialog('refresh');"><i class="fa fa-circle-o-notch"></i> 重置排位</a>
    <button type="button" class="btn btn-danger"></button>
  </div>
  <!--价格信息-->
  <table class="table table-bordered">

    <tbody id="group_seat_{$data.id}">
        <tr>
          <td>日期场次：</td>
          <td>{$plan.plantime|date="Y-m-d",###} 第{$plan.games}场</td>
        </tr>
        <tr>
          <td>演出时间：</td>
          <td>{$plan.starttime|date="H:i",###} - {$plan.endtime|date="H:i",###}</td>
        </tr>
      <tr>
        <td>待排数</td>
        <td><input id="seat_num_{$data.id}" type="text" value="{$ginfo.num}" size="10" readonly></td>
      </tr>
      <tr>
        <td>已排数</td>
        <td id="seat_nums_{$data.id}">0</td>
      </tr>
    </tbody>
  </table>
  <!--提交-->
  <div class="submit_seat"><a href="#" class="btn btn-success" onclick="post_server();">设置座位</a></div>
  <!--图列 s-->
  <div id="legend"></div>
  <!--left info e--> 
</div>
<!--right seat s-->
<div id="seat-map-w-h-{$data.id}" style="margin-left: 245px; overflow: auto;/*white-space: nowrap;*/ ">
    <div id="seat-map-{$data.id}">
      <div id="seat-info-{$data.id}" class="seat-info"></div>
    </div>
    <!--
    <div class="booking-details">
      <h3> 选座信息：</h3>
      <p>票数: <span id="counter-{$data.id}"></span></p>
      <p>总计: ￥<span id="total-{$data.id}">0</span></p>
      <button class="checkout-button">确定购买</button>
    </div>
    -->
</div>
<!--right seat e-->
</div>
<!--选座信息-->
<div style="display: none">
<div id="seat-cart-{$data.id}">
  <ul id="set-selected-seats-{$data.id}">
  </ul>
</div>
</div>
<style>
div.seatCharts-seat.selected{}
</style>
<script>
$(document).ready(function() {
  /*设置座位区域的宽高*/
  var seatMapW = $(window).width() - 314,
      seatMapH = $(window).height() - 130,
      areaId  = '{$ginfo.area}',
      
      planId = '{$ginfo.plan}',
      priceid = '{$ginfo.priceid}',
      price = '{$ginfo.price}',
      areaname = '{$data.name}',
      seat_data = '',
      num = '',
      fg = '',
      count = '',
      counts = '',
      seatLength = {$data.num},/*当前区域座椅个数*/
      seat_num = '',
      seat_nums = '';
  $('#seat-map-w-h-{$data.id}').width(seatMapW);
  $('#seat-map-w-h-{$data.id}').height(seatMapH);
  var seat_cart = $('#set-selected-seats-{$data.id}'),
      counter = $('#counter-{$data.id}'),
      total = $('#total-{$data.id}'),
      sc = $('#seat-map-{$data.id}').seatCharts({
      map: ['{$data.seats.seat}'],
     /* seats: { //定义座位属性
        a: {
          classes : 'seat05', 
          category: '一等座'
        },
        e: {
          classes : 'economy-class', 
          category: '二等座'
        }         
      },
      */
      naming : {
        top : true,
        columns: ['{$data.seats.columns}'], 
        rows: ['{$data.seats.rows}'], 
        getLabel : function (character, row, column) {
          return row+"排"+column+"号";
        }
      },
    
      click: function () {
        var obj = this.data();
        seat_num = parseInt($("#seat_num_"+areaId).val());
        seat_nums = parseInt($("#seat_nums_"+areaId).html());
        if (this.status() == 'available' || this.status() == 'pre' ) {
            if(seat_num <= '0' || isNaN(seat_num)){
              $(this).alertmsg('error', '请设置待排数!注意：待排数必须是正整数!');
              return this.style();
              return false;
            }
            /*写入购物车*/
            $('<li>'+areaname+'<br/>'+this.settings.label+'<br/>￥'+price+'</li>')
            .attr('id', 'seat-cart-item-'+this.settings.id)
            .attr('data-area',areaId)
            .attr('data-priceid',priceid)
            .attr('data-seat',this.settings.id)
            .attr('data-price',price)
            .appendTo(seat_cart);

            
            /*更新待排数和已排数*/
            count = seat_num-1,
            counts = seat_nums+1;
            $("#seat_num_"+areaId).val(count);
            $("#seat_nums_"+areaId).html(counts);
            return 'selected';
        } else if (this.status() == 'selected') {/*已选中*/
            count = seat_num+1,
            counts = seat_nums-1;
            $("#seat_num_"+areaId).val(count);
            $("#seat_nums_"+areaId).html(counts);
            /*删除已预订座位*/
            $('#seat-cart-item-'+this.settings.id).remove();
            
            return 'available';
        } else if (this.status() == 'unavailable') {/*已售出*/
            return 'unavailable';
        } else {
            return this.style();
        }
      },
      focus  : function() {
        $("#seat-info-{$data.id}").show().html(this.settings.label);
          var cd = getMousePoint(event);
          $("#seat-info-{$data.id}").css({"left":(cd.x+10)+'px',"top":(cd.y-30)+"px"});
        if (this.status() == 'available') {
          return 'focused';
        } else  {
          return this.style();
        }
      }
  });
//异步加载座椅状态
  $.ajax({ 
      type     : 'get', 
      url      : '{:U('Item/Work/seats');}&area='+areaId+'&plan='+planId, 
      dataType : 'json', 
      success  : function(rdata) { 
          if(rdata.statusCode == '200'){
            //写入已排数量
            num = rdata.num ? rdata.num : 0;
            $("#work_nums_"+areaId).html(num);
            //当前分组的可以选择
            if(rdata.work_seat != null){
              $.each(rdata.work_seat, function(index, workseat) {
                sc.status(workseat, 'unavailable'); 
              });
            }
            if(rdata.work_pre_seat != null){
              $.each(rdata.work_pre_seat, function(index, preseat) {
                sc.status(preseat, 'pre'); 
              });
            }
            if(rdata.work_end_seat != null){
              $.each(rdata.work_end_seat, function(index, endseat) {
                sc.status(endseat, 'unavailable'); 
              });
            }
            if(rdata.nwork_seat != null){
              $.each(rdata.nwork_seat, function(index, nworkseat) {
                sc.status(nworkseat, 'unavailable'); 
              });
            }
          }else{
            /*TODO 关闭弹窗*/
            $(this).alertmsg('error', '座椅状态加载失败，请重新打开页面!');

          }

          /*遍历所有座位 
          $.each(rdata.work_seat, function(index, booking) { alert(booking);
              //将已售出的座位状态设置为已售出 
             // sc.status(booking.seat_id, 'unavailable'); 
          }); */ 
      } 
  });
});

/*向服务器提交数据*/
function post_server(){
    var postData = '',
        toJSONString = '',
        plan = {$plan['id']},
        areaId  = '{$ginfo.area}',
        sn = '{$ginfo.sn}',
        length =  $("#set-selected-seats-{$ginfo.area} li").length;
    if(length <= 0){
        $(this).alertmsg('error','未找到要售出的座位!');
        return false;
    }
    if($("#seat_num_"+areaId).val() != 0){$(this).alertmsg('error', '存在待排座位!');return false;}
    $("#set-selected-seats-{$ginfo.area} li").each(function(i){
        var fg = i+1 < length ? ',':' ';/*判断是否增加分割符*/
        toJSONString = toJSONString + '{"areaId":'+areaId+',"priceid":' +$(this).data().priceid+',"seatid":"'+$(this).data().seat+'","price":"'+parseFloat($(this).data('price')).toFixed(2)+'"}'+fg;
    });
    postData = 'info={"sn":'+sn+',"data":['+ toJSONString + '],"aid":'+areaId+'}';
    /*提交到服务器**/
    $.ajax({
        type:'POST',
        url:'<?php echo U('Item/Order/row_seat',array('plan'=>$plan['id']));?>',
        data:postData,
        dataType:'json',
        success:function(data){
            if(data.statusCode == "200"){
                $(this).dialog("closeCurrent","true");
                $(this).alertmsg('ok', '排座成功！');
            }else{
                $(this).alertmsg('error','排座失败!');
            }
        }
    });
}
</script>