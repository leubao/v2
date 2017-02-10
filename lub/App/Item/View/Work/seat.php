<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
  <!--left info s-->
  <div style="float:left; width:320px; " class="left_seat_info">
    <div class="front">{$data.name}</div>
    <!--场次信息-->
    <table class="table table-bordered">
      <tbody>
        <tr>
          <td>日期场次：</td>
          <td>{$plan.plantime|date="Y-m-d",###} 第{$plan.games}场</td>
        </tr>
        <tr>
          <td>演出时间：</td>
          <td>{$plan.starttime|date="H:i",###} - {$plan.endtime|date="H:i",###}</td>
        </tr>
        <tr>
          <td>应收金额：</td>
          <td><strong style='color:red;font-size:18px;' id="work-seat-total-{$area}">0.00</strong></td>
        </tr>
        <tr>
          <td>检票类型：</td>
          <td><input type="radio" name="checkinTy{$aid}" data-toggle="icheck" value="1" checked data-label="一人一票&nbsp;">
            <input type="radio" name="checkinTy{$aid}" data-toggle="icheck" value="2" disabled data-label="一团一票"></td>
        </tr>
      </tbody>
    </table>
    <div class="btn-group btn-group-xs f-right" role="group"> 
      <!--购物车 s--> 
      <a href="#" class="btn btn-default btn-xs seat_work-cart" data-toggle="dialog" data-id="mydialog3" data-target="#work-cart-{$area}" data-title="已选择的座位"><i class="fa fa-shopping-cart"></i></a> 
      <!--<a href="" class="btn btn-default btn-xs seat_work-cart"><i class="fa fa-user-plus"></i></a>--> 
      <a href="{:U('Manage/Index/index_info');}" data-toggle="dialog" data-options="id:area_info" data-mask=true; class="btn btn-default btn-xs seat_work-cart"><i class="fa fa-info-circle"></i></a> <a href="" class="btn btn-default btn-xs seat_work-cart"><i class="fa fa-question-circle"></i></a> 
      <a type="button" class="btn tn-default btn-xs" onclick="$(this).dialog('refresh');" data-placement="top" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
      <!--<button type="button" class="btn btn-success"><input type="checkbox"> 拖动排位</button>-->
      <button type="button" class="btn btn-success" onclick="autoSeat();"><i class="fa fa-cogs"></i> 自动排位</button>
      <a type="button" class="btn btn-danger" onclick="$(this).dialog('refresh');"><i class="fa fa-circle-o-notch"></i> 重置排位</a>
      
    </div>
    <!--价格信息-->
    <!--价格信息-->
    <table class="table table-bordered mt20">
      <thead>
        <tr>
          <th align="center" width="110">票型名称</th>
          <th align="center" width="50">票面价</th>
          <th align="center" width="50">结算价</th>
          <th align="center">待排数</td>
          <th align="center" width="50">已排数</td>
        </tr>
      </thead>
      <tbody id="work-price-{$area}">
      <volist name="price" id="price">
        <tr data-pid="{$price.id}" data-price="{$price.price}" data-discount="{$price.discount}" data-area="{$area}">
          <td align="center">{$price.name}</td>
          <td>{$price.price}</td>
          <td>{$price.discount}</td>
          <td><input id="work-num-{$area}-{$price.id}" type="text" value="0" size="6" data-toggle="spinner" data-min="0" data-max="100" data-step="1"></td>
          <td id="work-nums-{$area}-{$price.id}" align="center">0</td>
        </tr>
        </volist>
      </tbody>
    </table>
    <!--客户信息-->
    <table class="table table-bordered mt20">
        <tbody id='work-crm'>
        <if condition="$type eq '2'">
        <tr>
            <td align='right'>导游:</td>
            <td><input type="hidden" name="user.id" value="">
                <input type="text" name="user.name" disabled value="" size="20" data-toggle="lookup" data-url="{:U('Manage/Index/public_user',array('type'=>5,'ifadd'=>2));}" data-group="user" data-width="600" data-height="445" data-title="导游" placeholder="导游">
            </td>
        </tr>
        <tr>
            <td align='right'>渠道商:</td>
            <td><input type="hidden" name="channel.id" value="">
                <input type="text" name="channel.name" disabled value="" size="20" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel');}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">
            </td>
        </tr>
        <tr>
            <td align='right'>补贴对象:</td>
            <td><input type="radio" name="sub_type" data-toggle="icheck" value="1" data-rule="checked" checked data-label="渠道商&nbsp;&nbsp;">
                <input type="radio" name="sub_type" data-toggle="icheck" value="2" data-label="导游"></td>
        </tr>
        </if>
        <tr>
            <td align='right'>联系人:</td>
            <td><input type="text" name="content" class="form-control required" size="20" placeholder="联系人"></td>
        </tr>
        <tr>
            <td align='right'>联系电话:</td>
            <td><input type="text" name="phone" class="form-control required" size="20" placeholder="电话"></td>
        </tr>
        <tr>
            <td align='right'>备注:</td>
            <td><textarea name="remark"></textarea></td>
        </tr>
        </tbody>
    </table>
    <table class="table table-bordered mt20">
            <thead>
                <tr>
                  <th align="center" width="80">操作</th>
                  <th align="center">支付方式</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align='right'>
                    <input type="radio" name="pay" value="1" checked="">
                    </td>
                    <td>现金</td>
                </tr>
                <tr>
                    <td align='right'><input type="radio" class="pay" name="pay" value="6"></td>
                    <td>POS机划卡</td>
                </tr>
                <tr>
                    <td align='right'><input type="radio" class="pay" name="pay" value="3"></td>
                    <td>签单</td>
                </tr>
                <tr>
                    <td align='right'><input type="radio" id="alipay_sweep" class="pay" name="pay" value="4"></td>
                    <td>支付宝支付</td>
                </tr>
                <tr>
                    <td align='right'><input type="radio" id="wxpay_sweep" class="pay" name="pay" value="5"></td>
                    <td>微信支付</td>
                </tr>
            </tbody>
        </table>
    <!--提交-->
    <div class="submit_seat"><a href="#" class="btn btn-success" onclick="post_server();">立即出票</a></div>
    <!--图列 s-->
    <div id="legend"></div>
    <!--left info e--> 
  </div>
  <!--right seat s-->
  <div id="work-seat-map-w-h-{$area}" style="margin-left: 325px; overflow: auto; white-space: nowrap;">
      <div id="work-seat-map-{$area}">
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
<div style="display: none">
<div id="work-cart-{$area}">
  <ul id="selected-seats-{$area}">
  </ul>
</div>
</div>
<style>
div.seatCharts-seat.selected{}
</style>
<script>
$(document).ready(function() {
  /*js 单行选中*/
  $("#work-price-{$area} tr").each(function(){
      //绑定onclick事件
      $(this).click(function(){
          /*查询是否存在已选择*/
          $("#work-price-{$area} tr").filter(".selected").removeClass("selected");
          $(this).addClass('selected');
      });
  });
  /*设置座位区域的宽高*/
  var seatMapW = $(window).width() - 415,
      seatMapH = $(window).height() - 140,
      areaId  = '{$area}',
      areaname = '{$data.name}',
      planId = '{$plan.id}',
      seat_data = '',
      num = '',
      fg = '',
      count = '',
      counts = '',
      work_num = '',
      work_nums = '',
      seatLength = {$data.num},/*当前区域座椅个数*/
      Position = 0;
  $('#work-seat-map-w-h-{$area}').width(seatMapW);
  $('#work-seat-map-w-h-{$area}').height(seatMapH);
  var work_cart = $('#selected-seats-{$area}'),
      counter = $('#counter-{$area}'),
      total = $('#work-seat-total-{$area}'),
      sc = $('#work-seat-map-{$area}').seatCharts({
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
        var obj = this.data(),
            ticket = $("#work-price-{$area} tr[class='selected']"),
            Current = this;/*鼠标当前位置 从当前位置开始自动排位*/
        /*获取价格政策selected*/
        if(ticket.data('pid') != null || undefined){
          var ticket_num = $("#work-num-"+areaId+"-"+ticket.data().pid),
              ticket_nums = $("#work-nums-"+areaId+"-"+ticket.data().pid),
              work_num = parseInt(ticket_num.val()),
              work_nums = parseInt(ticket_nums.html());
        }else{
          $(this).alertmsg('error', '请选择要售出的票型!');
          return this.style();
          return false;
        }
        
        if (this.status() == 'available' || this.status() == 'unpre') {
            // 获取待排数 work_num 待排数 work_nums 已排数
            if(work_num <= '0' || isNaN(work_num)){
              $(this).alertmsg('error', '请设置待排数!注意：待排数必须是正整数!');
              return 'available';
              return false;
            }
            $('<li>'+areaname+'<br/>'+this.settings.label+'<br/>￥'+ticket.data().price+'</li>')
            .attr('id', 'work-cart-item-'+this.settings.id)
            .attr('data-area',areaId)
            .attr('data-priceid',ticket.data().pid)
            .attr('data-seat',this.settings.id)
            .attr('data-price',ticket.data().price)
            .attr('data-discount',ticket.data().discount)
            .appendTo(work_cart);//alert(param);
            //更新票数
            //var fg = i < length ? ',':' ';/*判断是否增加分割符*/
            /*计算总计金额*/
            total.text(work_total());
            /*更新待排数和已排数*/
            count = work_num-1;
            counts = work_nums+1;
            ticket_num.spinner('setValue',count);
            ticket_nums.html(counts);

            /*不知道干嘛
            for(var int = 0;int < seatLength; int++)
            {
              if($(seatArea+" div")[int] == Current){
                window.Position = int;
                break;
              }
            }*/
            return 'selected';
        } else if (this.status() == 'selected') {
            /*删除已预订座位*/
            $('#work-cart-item-'+this.settings.id).remove();
            /*更新待排数和已排数*/
            count = work_num+1,
            counts = work_nums-1;
            ticket_num.spinner('setValue',count);
            ticket_nums.html(counts);
            total.text(work_total());
            return 'available';
        } else if (this.status() == 'unavailable') {/*已售出*/
            return 'unavailable';
        } else {
            return this.style();
        }
      },
      focus: function() {
        $("#seat-info-{$area}").show().html(this.settings.label);
          var cd = getMousePoint(event);
          $("#seat-info-{$area}").css({"left":(cd.x+10)+'px',"top":(cd.y-30)+"px"});
        if (this.status() == 'available') {
          return 'focused';
        } else  {
          return this.style();
        }
      },
      /*
      autoSeat:function(){

      }*/
  });
  //异步加载座椅状态
  $.ajax({ 
      type     : 'get', 
      url      : '{:U('Item/Work/seats');}&area='+areaId+'&plan='+planId, 
      dataType : 'json', 
      timeout  : 1500,
      error: function(){
          layer.msg('服务器请求超时，请检查网络...');
      },
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
                sc.status(preseat, 'unpre'); 
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
  //autoSeat();
});
//计算总金额 
function work_total(){
    var sum = 0;
    //根据配置选择窗口是结算价格结算还是结算价结算
    $("#selected-seats-{$area} li").each(function(i){
      var _val = PRODUCT_CONF.settlement == 2 ? parseFloat($(this).data().discount) : parseFloat($(this).data().price);
      sum += _val;
    });
    if(isNaN(sum)){
      sum = 0;
    }
    return sum.toFixed(2);
}
/*当前座位单双号判断 seat_num string 座位号*/
function is_twin(seat_num){
  var seat = seat_num.split("-");
  if(seat['1']%2 == '0'){
    return '2';
  }else{
    return '1';
  }
}
/*自动选座*/
function autoSeat(){
  /*var selectType = document.getElementsByClassName('selected');获取选中行;*/
  var ticket = $("#work-price-{$area} tr[class='selected']");
  /*获取价格政策selected*/
  if(ticket.data('pid') != null || undefined){
    var ticket_num = $("#work-num-"+areaId+"-"+ticket.data().pid),
        ticket_nums = $("#work-nums-"+areaId+"-"+ticket.data().pid),
        work_num = parseInt(ticket_num.val()),
        work_nums = parseInt(ticket_nums.html());
  }else{
    $(this).alertmsg('error', '请选择要售出的票型!');
    //return this.style();
    return false;
  }
  // 获取待排数 work_num 待排数 work_nums 已排数
  if(work_num <= '0' || isNaN(work_num)){
    $(this).alertmsg('error', '请设置待排数!注意：待排数必须是正整数!');
    //return 'available';
    return false;
  }
  for(var int = work_num; int <  seatLength; int++){
    if($(this).is('.available')) {
        $('<li>'+areaname+'<br/>'+this.settings.label+'<br/>￥'+ticket.data().price+'</li>')
        .attr('id', 'work-cart-item-'+this.settings.id)
        .attr('data-area',areaId)
        .attr('data-priceid',ticket.data().pid)
        .attr('data-seat',this.settings.id)
        .attr('data-price',ticket.data().price)
        .attr('data-discount',ticket.data().discount)
        .appendTo(work_cart);//alert(param);
        //更新票数
        //var fg = i < length ? ',':' ';/*判断是否增加分割符*/
        /*计算总计金额*/
        total.text(work_total());
        /*更新待排数和已排数*/
        count = work_num-1;
        counts = work_nums+1;
        ticket_num.spinner('setValue',count);
        ticket_nums.html(counts);
    }
  }
  
  //alert($("#work-seat-map-{$area} span").length);
  var Inum = 0;
  /*for(var int = this.Position; int <  seatLength; int++){
    if (this.status() == 'available') {
      alert("12");
    }
  }*/
  $("#work-seat-map-{$area} span").each(function(i){
      //this.status();
      //alert($(this).attr('class'));
     // alert(this.status);
      
   // alert(i);
  });
  return true;  
}
/*向服务器提交数据*/
function post_server(){
    var postData = '',
        pay = '',
        crm = '',
        contact = $("#work-crm input[name='content']").val() ? $("#work-crm input[name='content']").val() : '0',
        phone = $("#work-crm input[name='phone']").val() ? $("#work-crm input[name='phone']").val() : '0',
        remark = $("#work-crm textarea[name='remark']").val() ? $("#work-crm textarea[name='remark']").val() : "空...",
        sub_type = $("#work-crm input[type='radio']:checked").val() ? $("#work-crm input[type='radio']:checked").val() : '1',
        toJSONString = '',
        checkinT = '1',/*一人一票*/
        plan = {$plan['id']},
        areaId  = '{$area}',
        guide = '0',
        qditem = '0',
        type = {$type},
        settlement = PRODUCT_CONF.settlement,
        is_pay = $('input[name="pay"]:checked').val(),
        length =  $("#selected-seats-{$area} li").length,
        cash = parseFloat($('#work-seat-total-{$area}').html());
    if(length <= 0){
        $(this).alertmsg('error','未找到要售出的座位!');
        return false;
    }
    <?php if($type == '2'){?>
        guide = $("#work-crm input[name='user.id']").val(),
        qditem = $("#work-crm input[name='channel.id']").val();
        if(phone == '' || contact == '' || guide == '' || qditem == ''){
          $(this).alertmsg('error','请完善团队信息!');
          return false;
        }
    <?php } ?>
    $("#selected-seats-{$area} li").each(function(i){
        var fg = i+1 < length ? ',':' ';/*判断是否增加分割符*/
        toJSONString = toJSONString + '{"areaId":'+areaId+',"priceid":' +$(this).data().priceid+',"seatid":"'+$(this).data().seat+'","price":"'+parseFloat($(this).data('price')).toFixed(2)+'"}'+fg;
    });
    /*获取支付相关数据*/
    pay = '{"cash":'+cash+',"card":0,"alipay":0}';
    param = '{"remark":"'+remark+'","settlement":"'+settlement+'","is_pay":"'+is_pay+'"}';
    crm = '{"guide":'+guide+',"qditem":'+qditem+',"phone":"'+phone+'","contact":"'+contact+'"}';
    postData = 'info={"subtotal":'+cash+',"plan_id":'+plan+',"checkin":'+checkinT+',"sub_type":'+sub_type+',"type":'+type+',"data":['+ toJSONString + '],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';
    /*提交到服务器**/
    $.ajax({
        type:'POST',
        url:'<?php echo U('Item/Order/seatPost',array('plan'=>$plan['id'],'type'=>$type));?>',
        data:postData,
        dataType:'json',
        timeout: 3500,
        error: function(){
            layer.msg('服务器请求超时，请检查网络...');
        },
        success:function(data){
            if(data.statusCode == "200"){
                //刷新
                $(this).dialog('refresh', data.refresh);
                $(this).dialog({id:'print', url:''+data.forwardUrl+'', title:'门票打印',width:'213',height:'208',resizable:false,maxable:false,mask:true});
            }else{
                $(this).alertmsg('error','出票失败!');
            }
        }
    });
}
</script>