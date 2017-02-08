<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--left info s-->
<div style="float:left; width:240px; " class="left_seat_info">
  <div class="front">{$data.name}</div>
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

    <tbody id="group_seat_{$data.id}">
      <tr>
        <td>待排数</td>
        <td><input id="group_num_{$data.id}" type="text" value="0" size="10" data-toggle="spinner" data-min="0" data-max="100" data-step="1"></td>
      </tr>
      <tr>
        <td>已排数</td>
        <td id="group_nums_{$data.id}">0</td>
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
<div id="seat-map-w-h-{$data.id}" style="margin-left: 245px; overflow: auto; white-space: nowrap;">
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
<div id="cart-{$data.id}">
  <ul id="selected-seats-{$data.id}">
  </ul>
  <input id="cart-seat-{$data.id}" type="hidden" value="">
</div>
<style>
div.seatCharts-seat.selected{}
</style>
<script>
$(document).ready(function() {
  /*设置座位区域的宽高*/
  var seatMapW = $(window).width() - 301,
      seatMapH = $(window).height() - 130,
      areaId  = '{$data.id}',
      seat_data = '',
      num = '',
      fg = '',
      count = '',
      counts = ''
      group_num = '',
      group_nums = '';
  $('#seat-map-w-h-{$data.id}').width(seatMapW);
  $('#seat-map-w-h-{$data.id}').height(seatMapH);
  var cart = $('#selected-seats-{$data.id}'),
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
        group_num = parseInt($("#group_num_"+areaId).val());
        group_nums = parseInt($("#group_nums_"+areaId).html());
        if (this.status() == 'available') {
            if(group_num <= '0' || isNaN(group_num)){
              $(this).alertmsg('error', '请设置待排数!注意：待排数必须是正整数!');
              return this.style();
              return false;
            }
            //alert(seat_data);
            seat_data = $("#cart-seat-"+areaId).val();
            if(seat_data == '' || null || undefined ){
              seat_data = this.settings.id;
            }else{
              seat_data = this.settings.id+','+seat_data;
            }
            $("#cart-seat-"+areaId).val(seat_data);
            /*更新待排数和已排数*/
            count = group_num-1,
            counts = group_nums+1;
            $("#group_num_"+areaId).spinner('setValue',count);
            $("#group_nums_"+areaId).html(counts);
            return 'selected';
        } else if (this.status() == 'selected') {/*已选中*/
            count = group_num+1,
            counts = group_nums-1;
            $("#group_num_"+areaId).spinner('setValue',count);
            $("#group_nums_"+areaId).html(counts);
            /*删除已移除的座位*/
            if(seat_data == '' || null || undefined ){
              seat_data = $("#cart-seat-"+areaId).val();
            }
            seat_data = seat_data.replace(eval('/'+','+this.settings.id+'|'+this.settings.id+','+'|'+this.settings.id+'/'),'');
            if(seat_data == '' || null || undefined ){
              $("#cart-seat-"+areaId).val('');
            }else{
              $("#cart-seat-"+areaId).val(seat_data);
            }
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
      url      : '{:U('Item/Set/seats');}&aid='+areaId+'&fid=', 
      dataType : 'json', 
      success  : function(rdata) { 
          if(rdata.statusCode == '200'){
            //当前分组座位写入已选择座位
            $("#cart-seat-"+areaId).val(rdata.group_seat_str);
            //写入已排数量
            num = rdata.num ? rdata.num : 0;
            $("#group_nums_"+areaId).html(num);
            //当前分组的可以选择
            if(rdata.group_seat != ''){
              $.each(rdata.group_seat, function(index, currentGroup) {
                sc.status(currentGroup, 'selected'); 
              });
            }
            if(rdata.ngroup_seat != ''){
              $.each(rdata.ngroup_seat, function(index, otherGroup) {
                sc.status(otherGroup, 'unavailable'); 
              });
            }
          }else{
            /*TODO 关闭弹窗*/
            $(this).alertmsg('error', '座椅状态加载失败，请重新打开页面!');

          }
      } 
  });
});
/*数据提交*/
function post_server(){
  var postData = '',
      select_seat = $("#cart-seat-{$data.id}").val();
  if(select_seat == '' || null || undefined ){$(this).alertmsg('error', '未找到已选择座位!');return false;}
  select_seat = '"'+select_seat+'"';
  /*存入临时座位表*/
  $("#seat_{$data.id}").val(select_seat);
  $(this).alertmsg('ok', '更新成功！');
  /*postData = 'info={"data":'+ select_seat + ',"aid":'+areaId+',"template":'+templateId+',"group":'+groupId+'}';
  $.ajax({
    url: '{:U('Item/Set/set_seat');}',
    type: 'POST',
    dataType: 'json',
    data: postData,
    success:function(data){
      if(data.statusCode == "200"){

        $(this).alertmsg('ok', '更新成功！');
        //$("#ab").click();
      }else{
        $(this).alertmsg('error', '更新失败！');
      }
    }
  });*/
}
</script>