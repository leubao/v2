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
  <div style="margin-left:14px; width:400px;  height: auto; float: left; overflow:hidden;">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th align="center" width="150">票型名称</th>
                <th align="center" width="90">数量</th>
                <th align="center" width="50">结算价</th>
                <th align="center" width="50">小计</th>
                <th align="center" width="50">操作</th>
              </tr>
            </thead>
            <tbody id="promotions-price-select">
            </tbody>
            <tr>
                <td colspan="2" align='right'>合计:</td>
                <td colspan='3'><strong style='color:red;font-size:18px;' id="promotions-total">0.00</strong></td>
            </tr>
        </table>
        <table class="table table-bordered mt20">
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
                <td align='right'>选择活动:</td>
                <td><select class="required" name="" data-toggle="selectpicker">
                        <option value="1" selected>现金</option>
                        <option value="6">POS机划卡</option>
                        <option value="3">签单</option>
                        <option value="4">支付宝支付</option>
                        <option value="5">微信支付</option>
                    </select>
                </td>
            </tr>
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
        <div class="submit_seat"><a href="#" class="btn btn-success" onclick="quick_server();">立即出票</a></div>
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
      planId = '';
  activity_plan($("#plantime").val());
  $('#plantime').on('afterchange.bjui.datepicker', function(e, data) {
      activity_plan(FormatDate(data.value));
      //刷新购物车
      $(this).bjuiajax('refreshLayout','promotions-price-select');
  });
  //获取日期，加载销售计划
  //自动加载默认选框
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
            falg = false,
            htmlTal = "promotions",
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
           var spinner = "<span class='wrap_bjui_btn_box' style='position: relative;'><input type='text' data-toggle='spinner' value='1' size='8' id='promotions-num-"+trId+"' class='form-control' style='padding-right: 13px; width: 80px;'><ul class='bjui-spinner' style='height: 22px;'><li class='up' data-input='promotions-num-"+trId+"' onclick='addNum("+trId+","+price+",\""+htmlTal+"\");'>∧</li><li class='down' onclick='delNum("+trId+","+price+",\""+htmlTal+"\")'>∨</li></ul></span>";
           var row = $("<tr data-id="+trId+" data-price='"+price+"' data-area='"+$(this).data('area')+"'><td>"+$(this).data('name')+"</td> <td>"+spinner+"</td><td>"+price+"</td> <td id='promotions-subtotal-"+trId+"''>"+subtotal+"</td><td align='center'><a href='#' onclick='delRow(this,\""+htmlTal+"\");'><i class='fa fa-trash-o'></i></a><input type='hidden' id='areaid"+trId+"' value="+$(this).data('area')+" name='areaid'/></td></tr>");
            
           $('#promotions-price-select').append(row);
        }
        //计算合计金额
        $("#promotions-total").html(total('promotions'));
    });

    //快捷售票键盘直接输入数量
    $('#promotions-price-select').click(function(){
        $("#promotions-price-select tr").each(function(i){
            var trIds = $(this).data('id'),
                prices = $(this).data('price');
            $("#promotions-num-"+trIds).keyup(function(){
                var val_num = parseInt(this.value);
                if (isNaN(val_num) || val_num < 1) {                                  
                    val_num = 1;
                }
                this.value = val_num;
                $("#promotions-subtotal-"+trIds).html(amount(val_num,prices));/*小计*/
                $("#promotions-total").html(total());/*合计*/
            })         
        });  
    });
});
</script> 