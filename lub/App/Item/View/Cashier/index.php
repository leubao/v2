<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
  <!--工具条 s-->
  <Managetemplate file="Common/Nav"/>
  <!--帮助 说明-->
</div>
<div class="bjui-pageContent tableContent">
    <div class="col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <select class="required" name="plan" id="work_plan" data-toggle="selectpicker">
            <option value="">+=^^=售票日期=^^=+</option>
            <volist name="plan" id="vo">
              <?php $ptime =  $vo['plantime']."-".$vo['games'];?>
              <option value="{$ptime}"  <if condition="$today eq $ptime">selected</if>>{$vo.plantime|date="Y-m-d",###} <if condition="$product['type'] eq 1"> 第{$vo.games}场 {$vo.starttime|date="H:i",###}</if>
              </option>
            </volist>
          </select>
          <div class="btn-group" role="group" aria-label="售票">
          <a type="submit" href="#" class="btn btn-success" id="custom_goods"><i class="fa fa-deviantart"> 自定义商品</i></a>
          </div>
        </div>
        <table class="table table-bordered">
          <thead>
                <tr>
                  <th align="center" width="120">座位区域</th>
                  <th align="center" width="80">总数</th>
                  <th align="center" width="80">空闲数</th>
                  <th align="center" width="80">已售数</th>
                  <th align="center" width="80">预留数</th>
                  <th align="center" width="180">操作</th>
                </tr>
          </thead>
          <tbody id="work_area_seat">
          </tbody>
        </table>
      </div>
    </div>
    <div style="margin-left:14px; width:400px; height:90%; float: left; overflow:hidden;">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th align="center" width="150">商品名称</th>
                <th align="center" width="90">数量</th>
                <th align="center" width="50">结算价</th>
                <th align="center" width="50">小计</th>
                <th align="center" width="50">操作</th>
              </tr>
            </thead>
            <tbody id="quick-price-select">
            </tbody>
        </table>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th align="center" width="150">票型名称</th>
                <th align="center" width="90">价格</th>
              </tr>
            </thead>
            <tbody id="child_ticket">
               
            </tbody>
        </table>
        <table class="table table-bordered">
            <tr><td colspan="2" align='right'>合计:</td>
                <td colspan='3'><strong style='color:red;font-size:18px;' id="quick-total">0.00</strong></td>
            </tr>
        </table>
  
        <table class="table table-bordered mt20">
            <tbody id='quick-crm'>
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
                  <th align="center">支付方式  <button type="button"  data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-angle-double-down"></i></button></th>
                </tr>
            </thead>
            <tbody class="collapse" id="collapseExample">
                <tr>
                    <td align='right' width="80">
                    <input type="radio" name="pay" value="1" checked="">
                    </td>
                    <td>现金</td>
                </tr>
                <tr>
                    <td align='right'><input type="radio" class="pay" name="pay" value="6"></td>
                    <td>划卡</td>
                </tr>
                <tr>
                    <td align='right'><input type="radio" class="pay" name="pay" value="3"></td>
                    <td>签单</td>
                </tr>
            </tbody>
        </table>
        
        <!--提交-->
        <div class="submit_seat"><a href="#" class="btn btn-success" onclick="post_server();">立即出票</a></div>
    </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
    </ul>
</div>
<script type="text/javascript">
$(document).ready(function(){
  var plan = '',
      planId = '';
  //自动加载默认选框
  plan = $('#work_cashier_plan').children('option:selected').val();
  if(plan != '' || null || undefined){
    var data = 'info={"plan":"'+plan+'"}',
        content = '';
        $.ajax({
            url: '{:U('Item/Work/set_session_plan')}',
            type: 'POST',
            dataType: 'JSON',
            timeout: 3500,
            data:data,
            error: function(){
                layer.msg('服务器请求超时，请检查网络...');
            },
            success: function(rdata){
              if(rdata.statusCode == '200'){
                planId = rdata.plan;
                 /*写入*/
                $(rdata.area).each(function(idx,area){
                  if(PRODUCT_CONF.window_channel == '1'){
                    content += "<tr><td align='center'>"+area.name+"</td><td>"+area.number+"</td><td>"+area.num+"</td><td>"+area.nums+"</td><td>"+area.numb+"</td><td align='center'>"
                    +"[<a href='#' onclick='seat_select("+planId+",1,"+area.id+");' title='门票销售-散客选座'>散客选座</a>]"
                    +"[<a href='#' onclick='seat_select("+planId+",2,"+area.id+");' title='门票销售-团队选座'>团队选座</a>]"
                    +"</td></tr>";
                  }else{
                    content += "<tr><td align='center'>"+area.name+"</td><td>"+area.number+"</td><td>"+area.num+"</td><td>"+area.nums+"</td><td>"+area.numb+"</td><td align='center'>"
                    +"[<a href='#' onclick='seat_select("+planId+",1,"+area.id+");' title='门票销售-散客选座'>散客选座</a>]"
                    +"</td></tr>";
                  }
                });
                content += "<tr><td></td><td></td><td></td><td>已售数:"+rdata.sale.nums+"</td><td>预定数:"+rdata.sale.numb+"</td><td>订单金额:"+rdata.sale.money+"</td></tr>"; 
              }
              $(this).alertmsg('ok', '售票场次,切换成功!');
              $("#work_area_seat").html(content); 
            }
        });
  }else{
    var error_msg = "<tr><td style='padding:15px;' colspan='6' align='center'><strong style='color:red;font-size:48px;'>请选择售票日期</strong></td></tr>";
    $("#work_area_seat").html(error_msg);
  }
  //改变日期场次
  $('#work_cashier_plan').change(function(){
    plan = $(this).children('option:selected').val();
    if(plan != '' || null || undefined){
        var data = 'info={"plan":"'+plan+'"}',
            content = '';
          $.ajax({
            url: '{:U('Item/Work/set_session_plan')}',
            type: 'POST',
            dataType: 'JSON',
            timeout: 1500,
            data:data,
            error: function(){
                layer.msg('服务器请求超时，请检查网络...');
            },
            success: function(rdata){
              if(rdata.statusCode == '200'){
                planId = rdata.plan;
                 /*写入*/
                $(rdata.area).each(function(idx,area){
                  if(PRODUCT_CONF.window_channel == '1'){
                    content += "<tr><td align='center'>"+area.name+"</td><td>"+area.number+"</td><td>"+area.num+"</td><td>"+area.nums+"</td><td>"+area.numb+"</td><td align='center'>"
                    +"[<a href='#' onclick='seat_select("+planId+",1,"+area.id+");' title='门票销售-散客选座'>散客选座</a>]"
                    +"[<a href='#' onclick='seat_select("+planId+",2,"+area.id+");' title='门票销售-团队选座'>团队选座</a>]"
                    +"</td></tr>";
                  }else{
                    content += "<tr><td align='center'>"+area.name+"</td><td>"+area.number+"</td><td>"+area.num+"</td><td>"+area.nums+"</td><td>"+area.numb+"</td><td align='center'>"
                    +"[<a href='#' onclick='seat_select("+planId+",1,"+area.id+");' title='门票销售-散客选座'>散客选座</a>]"
                    +"</td></tr>";
                  }
                });
                content += "<tr><td></td><td></td><td></td><td>已售数:"+rdata.sale.nums+"</td><td>预定数:"+rdata.sale.numb+"</td><td>订单金额:"+rdata.sale.money+"</td></tr>"; 
              }
              $(this).alertmsg('ok', '售票场次,切换成功!');
              $("#work_area_seat").html(content); 
            }
        });
    }else{
        var error_msg = "<tr><td style='padding:15px;' colspan='6' align='center'><strong style='color:red;font-size:48px;'>请选择售票日期</strong></td></tr>";
        $("#work_area_seat").html(error_msg);
    }
  });
  //auto 加载
  scenic_drifting_plan($("#plantime").val(),{$type});
  $('#plantime').on('afterchange.bjui.datepicker', function(e, data) {
      scenic_drifting_plan(FormatDate(data.value),{$type});
      //刷新购物车
      $(this).bjuiajax('refreshLayout','quick-price-select');
      //console.log($(this).bjuiajax('refreshLayout','quick-price-select'));
  });
  /**联票选择器子票型**/
  $('#child_ticket').on('click',' .child_ticket',function(){
      var child_fid = $(this).data('fid'),
          child_price = PRODUCT_CONF.settlement == 2 ? $(this).data('discount') : $(this).data('price'),
          end_price = '';
      if($(this).is(':checked')){
          //x选中
          //alert($(this).data('id'));
          end_price = child_price;
      }else{
          //未选中
          end_price = -child_price;
      }
      //读取当前主票型数量
      $("#quick-price-select tr").each(function(i){
          if($(this).data('id') == child_fid){
              //改变主门票单价
              var main_price = $(this).data('price'),
                  price = main_price*1+end_price*1,
                  num = $("#quick-num-"+child_fid).val();
              
              $(this).data('price',price);
              $("#quick-subtotal-"+child_fid).html(amount(num,price));
              //更新总金额
              $("#quick-total").html(total());
          }else{
              $(this).alertmsg('error','亲,请选择主门票!');
          }
      });
  });
  //根据配置选择窗口是结算价格结算还是结算价结算
  $('#quick-price').on('click','tr',function(event){
      var trId = $(this).data('id'),
          number = '1',
          price = PRODUCT_CONF.settlement == 2 ? $(this).data('discount') : $(this).data('price'),
          falg = false,
          subtotal = parseFloat(price * parseInt(number)).toFixed(2);/*计算小计金额*/
      //判断是否当前选择之前是否已选择
      $("#quick-price-select tr").each(function(i){
          if(trId == $(this).data("id")){
             falg = true;
             return false;
          }
      });
      if(falg){
          $(this).alertmsg('error', '票型已选择!若要继续添加,请直接改变票型数量');
      }else{
         var spinner = "<span class='wrap_bjui_btn_box' style='position: relative;'><input type='text' data-toggle='spinner' value='1' size='8' id='quick-num-"+trId+"' class='form-control' style='padding-right: 13px; width: 80px;'><ul class='bjui-spinner' style='height: 22px;'><li class='up' data-input='quick-num-"+trId+"' onclick='addNum("+trId+","+price+");'>∧</li><li class='down' onclick='delNum("+trId+","+price+")'>∨</li></ul></span>";
         var row = $("<tr data-id="+trId+" data-price='"+price+"' data-area='"+$(this).data('area')+"'><td>"+$(this).data('name')+"</td> <td>"+spinner+"</td><td>"+price+"</td> <td id='quick-subtotal-"+trId+"''>"+subtotal+"</td><td align='center'><a href='#' onclick='delRow(this);'><i class='fa fa-trash-o'></i></a><input type='hidden' id='areaid"+trId+"' value="+$(this).data('area')+" name='areaid'/></td></tr>");
          
         $('#quick-price-select').append(row);
      }
      /*加载可选择联票*/
      var child_data = 'info={"area":'+trId+',"type":5,"plan":'+$("#planID").val()+'}',
          child_content = '';
      $.post('{:U('Item/Work/getprice');}', child_data, function(rdata) {
          if(rdata.statusCode == '200'){
             if(rdata.price != null){
                  $(rdata.price).each(function(idx,ticket){
                      child_content += "<tr><td align='left'><input type='checkbox' class='child_ticket' name='child_ticket' data-fid='"+trId+"' data-discount='"+ticket.discount+"' data-price='"+ticket.price+"' data-id='"+ticket.id+"' data-area='"+trId+"' data-name='"+ticket.name+"'> "+ticket.name+"</td><td>"+ticket.discount+"</td></tr>";
                      });
                  $("#child_ticket").html(child_content); 
             }
          }
      },"json");
      //计算合计金额
      $("#quick-total").html(total());
  });
  //快捷售票选择票型
  $('#quick-price-selects tr').click(function(){
      var trId = $(this).attr('id');
      var ktName = $("#kqtName"+trId).html();
      var ktPrice = parseFloat($("#kqtPrice"+trId).html()).toFixed(2);
      var ktNum = 1; //初始数量
        //计算小计金额
      var subtotal = parseFloat(ktPrice * parseInt(ktNum)).toFixed(2);
        //添加已选择
        var table = $('#kselect_quick');
        var falg = false;
        //判断是否当前选择之前是否已选择
        $("#kselect_quick tr").each(function(){
            if(trId == $(this).attr("id")){
                falg = true;
                return false;
            }
        });
        if(falg){
            alertMsg.error('');
        }else{
            var numTd = "<div class='d1'><input type='text' class='input20' id='kqtNum"+trId+"' value='1' size='2'/> </div> <div class='d2'><div><button onclick='addNum("+trId+")' class='but1'></button> </div> <div><button onclick='delNum("+trId+")' class='but2'></button></div></div>";
            var row = $("<tr id="+trId+"><td>"+ktName+"</td> <td>"+numTd+"</td><td id=kqtPrice"+trId+">"+ktPrice+"</td> <td id=subtotal_quick"+trId+">"+subtotal+"</td><td><a href='#' onclick=delRow(this);>删除</a><input type='hidden' id='areaid"+trId+"' value="+tabsId+" name='areaid'/></td></tr>");
            table.append(row);
        }
        $("#kTotal_quick").html(total());/*合计*/
        $("#kcash_quick").val(total());/*更新收款方式*/
        //计算合计金额
        //var trnum = $('#kselect').find("tr").length;   
  });
  //快捷售票键盘直接输入数量
  $('#quick-price-select').click(function(){
      child_ticket();
      $("#quick-price-select tr").each(function(i){
          var trIds = $(this).data('id'),
              prices = $(this).data('price');
          $("#quick-num-"+trIds).keyup(function(){
              var val_num = parseInt(this.value);
              if (isNaN(val_num) || val_num < 1) {                                  
                  val_num = 1;
              }
              this.value = val_num;
              $("#quick-subtotal-"+trIds).html(amount(val_num,prices));/*小计*/
              $("#quick-total").html(total());/*合计*/
          })         
      });  
  });
});

/*删除已选择*/
function delRow(rows){
    child_ticket();
    $(rows).parent("td").parent("tr").remove();
    $("#quick-total").html(total());/*合计*/
    //$("#kcash_quick").val(total());/*更新收款方式*/
}
/*计算小计金额*/
function amount(num,price){
    var count = parseFloat(num * price).toFixed(2);
    return count;
}
function total(){
    var sum = 0;
    $("#quick-price-select tr").each(function(i){
        var _val = parseFloat($("#quick-subtotal-"+$(this).data("id")).html());
        sum += _val;
    });

    return sum.toFixed(2);
}
/*数量增加与减少*/
function addNum(trId,price){
    var cnum = $("#quick-num-"+trId).val();//当前数量
    var num1 = parseInt(cnum)+1;
    $("#quick-num-"+trId).val(num1);
    child_ticket();
    //金额
    $("#quick-subtotal-"+trId).html(amount(num1,price));
    $("#quick-total").html(total());/*合计*/
    //$("#tcash").val(total());/*更新收款方式*/
}

function delNum(trId,price){
    var cnum = $("#quick-num-"+trId).val();//当前数量
    if(cnum == 1){
        $(this).alertmsg('error','亲，已经是最少了！');
        return false;
    }
    child_ticket();
    var num1 = parseInt(cnum)-1;
    $("#quick-num-"+trId).val(num1);
    $("#quick-subtotal-"+trId).html(amount(num1,price));
    $("#quick-total").html(total());/*合计*/
}
/*向服务器提交数据*/
function post_server(){
    var postData = '',
        pay = '',
        crm = '',
        contact = $("#quick-crm input[name='content']").val() ? $("#quick-crm input[name='content']").val() : '0',
        phone = $("#quick-crm input[name='phone']").val() ? $("#quick-crm input[name='phone']").val() : '0',
        remark = $("#quick-crm textarea[name='remark']").val() ? $("#quick-crm textarea[name='remark']").val() : "空...",
        sub_type = $("#quick-crm input[type='radio']:checked").val() ? $("#quick-crm input[type='radio']:checked").val() : '1',
        toJSONString = '',
        child_ticket = '',
        checkinT = '1',
        plan = $("#planID").val(),
        guide = '0',
        qditem = '0',
        settlement = PRODUCT_CONF.settlement,
        data = '',
        is_pay = $('input[name="pay"]:checked').val(),
        length =  $("#quick-price-select tr").length;
    if(length <= 0){
        $(this).alertmsg('error','请选择要售出的票型!');
        return false;
    }
    <?php if($type == '2'){?>
        guide = $("#quick-crm input[name='user.id']").val(),
        qditem = $("#quick-crm input[name='channel.id']").val();
        if(phone == '' || contact == '' || guide == '' || qditem == ''){
          $(this).alertmsg('error','请完善团队信息!');
          return false;
        }
    <?php } ?>
    /*子票型*/
    var child_length = $("#child_ticket input[type=checkbox]:checked").length;
    $("#child_ticket input[type=checkbox]:checked").each(function(i){
        if($(this).is(':checked')){
            //x选中
            var end_price = PRODUCT_CONF.settlement == 2 ? $(this).data('discount') : $(this).data('price'),
                fg = i+1 < child_length ? ',':' ';/*判断是否增加分割符*/
            child_ticket = child_ticket + '{"fid":'+$(this).data("fid")+',"priceid":' +$(this).data("id")+',"price":"'+parseFloat(end_price).toFixed(2)+'"}'+fg;
        }
    });
    /*主票型*/
    $("#quick-price-select tr").each(function(i){
        var fg = i+1 < length ? ',':' ';/*判断是否增加分割符*/
        toJSONString = toJSONString + '{"areaId":'+$(this).data("area")+',"priceid":' +$(this).data("id")+',"price":'+parseFloat($(this).data('price')).toFixed(2)+',"num":"'+$("#quick-num-"+$(this).data("id")).val()+'"}'+fg;
    });
    /*获取支付相关数据*/
    pay = '{"cash":'+parseFloat($('#quick-total').html())+',"card":0,"alipay":0}';
    param = '{"remark":"'+remark+'","settlement":"'+settlement+'","is_pay":"'+is_pay+'"}';
    crm = '{"guide":'+guide+',"qditem":'+qditem+',"phone":'+phone+',"contact":"'+contact+'"}';
    postData = 'info={"subtotal":'+parseFloat($('#quick-total').html())+',"plan_id":'+plan+',"checkin":'+checkinT+',"sub_type":'+sub_type+',"data":['+ toJSONString + '],"child_ticket":['+child_ticket+'],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';
    /*提交到服务器*/
    $.ajax({
        type:'POST',
        url:'<?php echo U('Item/Order/quickpost',array('type'=>$type));?>'+'&plan='+plan,
        data:postData,
        dataType:'json',
        timeout: 3500,
        error: function(){
          layer.msg('服务器请求超时，请检查网络...');
        },
        success:function(data){
            if(data.statusCode == "200"){
                //刷新
                $(this).dialog('refresh', 'work_quick');
                $(this).dialog({id:'print', url:''+data.forwardUrl+'', title:'门票打印',width:'213',height:'208',resizable:false,maxable:false,mask:true});
            }else{
                $(this).alertmsg('error','出票失败!');
            }
        }
    });
}
</script>