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

    <tbody id="block_seat_{$data.id}">
      <tr>
        <td>待排数</td>
        <td><input id="block_num_{$data.id}" type="text" value="0" size="10" data-toggle="spinner" data-min="0" data-max="100" data-step="1"></td>
      </tr>
      <tr>
        <td>已排数</td>
        <td id="block_nums_{$data.id}">0</td>
      </tr>
    </tbody>
  </table>
  <!--提交-->
  <div class="submit_seat"><if condition="$type eq '1'"><a href="#" class="btn btn-success" onclick="post_server(1);">控制座位</a><else /> <a href="#" class="btn btn-success" onclick="post_server(2);">释放座位</a></if></div>
  <!--left info e--> 
</div>
<!--right seat s-->
<div id="block-seat-map-w-h-{$data.id}" style="margin-left: 245px; overflow: auto; /*white-space: nowrap;*/">
    <div id="block-seat-map-{$data.id}">
      <div id="seat-info-{$data.id}" class="seat-info"></div>
    </div>
</div>
<!--right seat e-->
</div>
<!--选座信息-->
<div id="cart-{$data.id}">
  <input id="block-cart-seat-{$data.id}" type="hidden" value="">
</div>
<style>
div.seatCharts-seat.selected{}
</style>
<script>
$(document).ready(function() {
  /*设置座位区域的宽高*/
  var seatMapW = $(window).width() - 314,
      seatMapH = $(window).height() - 130,
      areaId  = '{$area}',
      areaname = '{$data.name}'
      planId = '{$plan.id}',
      block_seat_data = '',
      num = '',
      fg = '',
      count = '',
      counts = '',
      block_num = '',
      block_nums = '',
      if_block = {$type},
      seatLength = {$data.num},/*当前区域座椅个数*/
      Position = 0;
  $('#block-seat-map-w-h-{$area}').width(seatMapW);
  $('#block-seat-map-w-h-{$area}').height(seatMapH);
  var sc = $('#block-seat-map-{$area}').seatCharts({
      map: ['{$data.seats.seat}'],
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
            block_num = parseInt($("#block_num_"+areaId).val());
            block_nums = parseInt($("#block_nums_"+areaId).html());

        if(block_seat_data == '' || null || undefined){
          block_seat_data = this.settings.id;
        }else{
          block_seat_data = this.settings.id+','+block_seat_data;
        }
        
        if (this.status() == 'available') {
            // 获取待排数 work_num 待排数 work_nums 已排数
            if(block_num  <= '0' || isNaN(block_num )){
              $(this).alertmsg('error', '请设置待排数!注意：待排数必须是正整数!');
              return 'available';
              return false;
            }
            
            /*删除已移除的座位
              释放座位时  选择座位为不释放
            */
            if(if_block == '1'){
              $("#block-cart-seat-"+areaId).val(block_seat_data);
            }else{
              block_seat_data = block_seat_data.replace(eval('/'+','+this.settings.id+'|'+this.settings.id+','+'|'+this.settings.id+'/'),'');
              if(block_seat_data ==  '' || null || undefined){
                $("#cart-seat-"+areaId).val('');
              }else{
                $("#cart-seat-"+areaId).val(block_seat_data);
              }
            }
            
            /*更新待排数和已排数*/
            count = block_num-1;
            counts = block_nums+1;
            $("#block_num_"+areaId).spinner('setValue',count);
            $("#block_nums_"+areaId).html(counts);

            return 'selected';
        } else if (this.status() == 'selected') {
            /*释放座位时 取消选择的座位为要释放的座位
            */
            $("#block-cart-seat-"+areaId).val(block_seat_data);
            if(if_block == '1'){
              block_seat_data = block_seat_data.replace(eval('/'+','+this.settings.id+'|'+this.settings.id+','+'|'+this.settings.id+'/'),'');
              if(block_seat_data ==  '' || null || undefined){
                $("#cart-seat-"+areaId).val('');
              }else{
                $("#cart-seat-"+areaId).val(block_seat_data);
              }
            }else{
              $("#block-cart-seat-"+areaId).val(block_seat_data);
            }
            count = block_num+1;
            counts = block_nums-1;
            $("#block_num_"+areaId).spinner('setValue',count);
            $("#block_nums_"+areaId).html(counts);
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
  //异步加载座椅状态
  $.ajax({ 
      type     : 'get', 
      url      : '{:U('Item/Work/seats');}&area='+areaId+'&plan='+planId+'&type=2',
      timeout  : 1000,
      dataType : 'json', 
      error: function(){
        layer.msg('服务器请求超时，请检查网络...');
       },
      success  : function(rdata) { 
          if(rdata.statusCode == '200'){
            //当前分组座位写入已选择座位
            //$("#block-cart-seat-"+areaId).val(rdata.work_pre_seat);
            //写入已排数量
            num = rdata.pre_count ? rdata.pre_count : 0;
            var block_type = if_block == 1 ? 'unavailable' : 'selected';
            $("#block_nums_"+areaId).html(num);
            //当前分组的可以选择
            if(rdata.work_seat != null){
              $.each(rdata.work_seat, function(index, workseat) {
                sc.status(workseat, 'unavailable'); 
              });
            }
            if(rdata.work_pre_seat != null){
              $.each(rdata.work_pre_seat, function(index, preseat) {
                sc.status(preseat, block_type); 
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
      } 
  });
});
/*数据提交*/
function post_server(type){
  var postData = '',
      areaId = {$area},
      select_seat = $("#block-cart-seat-"+areaId).val();
  if(select_seat == '' || null || undefined ){$(this).alertmsg('error', '未找到已选择座位!');return false;}
  select_seat = '"'+select_seat+'"'
  postData = 'info={"seat":'+ select_seat + ',"area":'+areaId+',"plan":'+planId+'}';
  var urls = type == '1' ? '{:U('Item/Block/control_block');}' : '{:U('Item/Block/release');}';
  $.ajax({
    url: urls,
    type: 'POST',
    dataType: 'json',
    data: postData,
    timeout: 1500,
    error: function(){
      layer.msg('服务器请求超时，请检查网络...');
    },
    success:function(data){
      if(data.statusCode == "200"){
        $(this).alertmsg('ok', '更新成功！');
      }else{
        $(this).alertmsg('error', '更新失败！');
      }
    }
  });
}

</script>