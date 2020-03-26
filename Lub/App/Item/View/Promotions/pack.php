<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
  <!--工具条 s-->
  <Managetemplate file="Common/Nav"/>
  <!--帮助 说明-->
</div>
<div class="bjui-pageContent">
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <input type="text" data-toggle="datepicker" id="plantime" data-pattern="yyyy-MM-dd" value="{$today}" name="plantime" size="11">
        <select class="required" name="plan" id="promotions_plan" data-toggle="selectpicker">
          <option value="">+=^^=售票日期=^^=+</option>
        </select>
      </div>
      <table class="table table-bordered">
          <thead>
           <tr>
              <th align="center" width="150">票型名称</th>
              <th align="center" width="50">票面价</th>
              <th align="center" width="50">结算价</th>
              <th align="center" width="50">已售数</th>
              <th align="center" width="50">可售数</th>
            </tr>
          </thead>
          <tbody id="promotions-price">

          </tbody>
      </table>
    </div>
  </div>
  <div style="width:450px;float: left;">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th align="center" width="130">票型名称</th>
            <th align="center" width="45">数量</th>
            <th align="center" width="120">结算价</th>
            <th align="center" width="50">小计</th>
            <th align="center" width="45">操作</th>
          </tr>
        </thead>
        <tbody id="promotions-price-select">
        </tbody>
        <tr>
            <td colspan="2" align='right'>合计:</td>
            <td colspan='3'><strong style='color:red;font-size:18px;' id="promotions-total">0.00</strong></td>
        </tr>
    </table>
  </div>
  <div style="margin-left:14px; width:400px;  height: auto; float: left; overflow:hidden;">
        <table class="table table-bordered">
            <tbody id='promotions-crm'>
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
            <tr>
                <td align='right'>支付方式:</td>
                <td><select class="required" name="pay" id="selectPay" data-toggle="selectpicker">
                        <option value="1" selected>现金</option>
                        <option value="6">POS机划卡</option>
                        <option value="3">签单</option>
                        <option value="4">支付宝支付</option>
                        <option value="5">微信支付</option>
                    </select>
                </td>
            </tr>
        </table>
        <!--提交-->
        <div class="submit_seat"><a href="#" class="btn btn-success" onclick="promotions_server();">立即出票</a></div>
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><button type="button" class="btn btn-default" data-toggle="navtab" data-id="{$menuid}Item" data-url="{:U('Item/Promotions/index',array('menuid'=>$menuid))}" data-title="促销活动" data-icon="reply-all">返回</button></li>
    </ul>
</div>
<script type="text/javascript">
$(document).ready(function(){
  var plan = '',
      actid = {$data.id},
      falg = false,
      planId = '';
  activity_plan($("#plantime").val());
  $('#plantime').on('afterchange.bjui.datepicker', function(e, data) {
      activity_plan(FormatDate(data.value));
      //刷新购物车
      $(this).bjuiajax('refreshLayout','promotions-price-select');
  });
  //获取日期，加载销售计划自动加载默认选框
  plan = $('#promotions_plan').children('option:selected').val();
  if(isNull(plan)){
    console.log(plan);
    getActivtyPrice(plan,actid,1,2);
  }else{
    var error_msg = "<tr><td style='padding:15px;' colspan='6' align='center'><strong style='color:red;font-size:48px;'>未找到可售票型</strong></td></tr>";
    $("#promotions-price").html(error_msg);
  }
  //改变日期场次
  $('#promotions_plan').change(function(){
    plan = $(this).children('option:selected').val();
    if(isNull(plan)){
        var data = 'info={"plan":"'+plan+'"}',
            content = '';
        getActivtyPrice(plan,actid,1,2);  
    }else{
        var error_msg = "<tr><td style='padding:15px;' colspan='6' align='center'><strong style='color:red;font-size:48px;'>未找到可售票型</strong></td></tr>";
        $("#promotions-price").html(error_msg);
    }
  });
  //根据配置选择窗口是结算价格结算还是结算价结算
  $('#promotions-price').on('click','tr',function(event){
        var trId = $(this).data('id'),
            number = '1',
            price = PRODUCT_CONF.settlement == 2 ? $(this).data('discount') : $(this).data('price'),
            subtotal = parseFloat(price * parseInt(number)).toFixed(2);/*计算小计金额*/
        //判断是否当前选择之前是否已选择
        $("#promotions-price-select tr").each(function(i){
            if(trId == $(this).data("id")){
               falg = true;
               return false;
            }
        });
        if(falg){
            $(this).alertmsg('error', '票型已选择!若要继续添加,请直接改变票型数量');
        }else{
          var spinner = "<span class='wrap_bjui_btn_box' style='position: relative;'><input type='text' data-toggle='spinner' value='1' size='8' id='promotions-num-"+trId+"' class='form-control' style='padding-right: 13px; width: 80px;'><ul class='bjui-spinner' style='height: 22px;'><li class='up' data-input='promotions-num-"+trId+"' onclick='addNum("+trId+","+price+");'>∧</li><li class='down' onclick='delNum("+trId+","+price+")'>∨</li></ul></span>";
           var row = $("<tr data-id="+trId+" data-price='"+price+"' data-area='"+$(this).data('area')+"'><td>"+$(this).data('name')+"</td> <td>"+spinner+"</td><td>"+price+"</td> <td id='promotions-subtotal-"+trId+"''>"+subtotal+"</td><td align='center'><a href='#' onclick='delRow(this);'><i class='fa fa-trash-o'></i></a><input type='hidden' id='areaid"+trId+"' value="+$(this).data('area')+" name='areaid'/></td></tr>");
           $('#promotions-price-select').append(row);
        }
        //计算合计金额
        $("#promotions-total").html(total());
  });
});
function total(){
    var sum = 0;
    $("#promotions-price-select tr").each(function(i){
        var _val = parseFloat($(this).data("price"));
        sum += _val;
    });
    return sum.toFixed(2);
}
/*删除已选择*/
function delRow(rows){
    $(rows).parent("td").parent("tr").remove();
    $("#promotions-total").html(total());/*合计*/
}
/*删除已选择*/
function delRow(rows){
    $(rows).parent("td").parent("tr").remove();
    $("#promotions-total").html(total());/*合计*/
}
/*计算小计金额*/
function amount(num,price){
    var count = parseFloat(num * price).toFixed(2);
    return count;
}
function total(){
    var sum = 0;
    $("#promotions-price-select tr").each(function(i){
        var _val = parseFloat($("#promotions-subtotal-"+$(this).data("id")).html());
        sum += _val;
    });
    return sum.toFixed(2);
}
/*数量增加与减少*/
function addNum(trId,price){
    var cnum = $("#promotions-num-"+trId).val();//当前数量
    var num1 = parseInt(cnum)+1;
    $("#promotions-num-"+trId).val(num1);
    //金额
    $("#promotions-subtotal-"+trId).html(amount(num1,price));
    $("#promotions-total").html(total());/*合计*/
}

function delNum(trId,price){
    var cnum = $("#promotions-num-"+trId).val();//当前数量
    if(cnum == 1){
        $(this).alertmsg('error','亲，已经是最少了！');
        return false;
    }
    var num1 = parseInt(cnum)-1;
    $("#promotions-num-"+trId).val(num1);
    $("#promotions-subtotal-"+trId).html(amount(num1,price));
    $("#promotions-total").html(total());/*合计*/
}
function promotions_server(){
    var postData = '',
        pay = '',
        crm = '',
        contact = $("#promotions-crm input[name='content']").val() ? $("#promotions-crm input[name='content']").val() : '0',
        phone = $("#promotions-crm input[name='phone']").val() ? $("#promotions-crm input[name='phone']").val() : '0',
        remark = $("#promotions-crm textarea[name='remark']").val() ? $("#promotions-crm textarea[name='remark']").val() : "空...",
        sub_type = $("#promotions-crm input[type='radio']:checked").val() ? $("#promotions-crm input[type='radio']:checked").val() : '1',
        toJSONString = '',
        checkinT = '1',
        plan = $('#promotions_plan').children('option:selected').val(),
        guide = '0',
        qditem = '0',
        activety = {$data.id},
        settlement = 2,
        is_pay = $('#selectPay option:selected').val(),
        length =  $("#promotions-price-select tr").length,
        url = '<?php echo U('Item/Order/quickpost',array('type'=>$type));?>'+'&plan='+plan;
    if(length <= 0){
        $(this).alertmsg('error','请选择要售出的票型!');
        return false;
    }
    <?php if($type == '2'){?>
        guide = $("#promotions-crm input[name='user.id']").val(),
        qditem = $("#promotions-crm input[name='channel.id']").val();
        if(phone == '' || contact == '' || guide == '' || qditem == ''){
          $(this).alertmsg('error','请完善团队信息!');
          return false;
        }
    <?php } ?>
    $("#promotions-price-select tr").each(function(i){
        var fg = i+1 < length ? ',':' ';/*判断是否增加分割符*/
        toJSONString = toJSONString + '{"areaId":'+$(this).data("area")+',"priceid":' +$(this).data("id")+',"price":'+parseFloat($(this).data('price')).toFixed(2)+',"num":"'+$("#promotions-num-"+$(this).data("id")).val()+'"}'+fg;
    });
    /*获取支付相关数据*/
    pay = '{"cash":'+parseFloat($('#promotions-total').html())+',"card":0,"alipay":0}';
    param = '{"remark":"'+remark+'","settlement":"'+settlement+'","activity":"'+activety+'","is_pay":"'+is_pay+'"}';
    crm = '{"guide":'+guide+',"qditem":'+qditem+',"phone":'+phone+',"contact":"'+contact+'"}';
    postData = 'info={"subtotal":'+parseFloat($('#promotions-total').html())+',"plan_id":'+plan+',"checkin":'+checkinT+',"sub_type":'+sub_type+',"data":['+ toJSONString + '],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';
    post_server(postData,url,'activity');
}
</script> 