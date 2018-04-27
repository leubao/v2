<style type="text/css">.border{border: 1px solid #ddd;}</style>
<div class="bjui-pageContent">
    <div class="panel panel-default">
      <div class="panel-body">
        当前售票:{$product.name}<a style="float: right;margin-right: 30px" href="#" onclick="$(this).navtab('refresh');" data-placement="top" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
      </div>
    </div>
    <div style="float:left; width:200px;height:83.5%;">

        <input type="text" data-toggle="datepicker" id="plantime" data-pattern="yyyy-MM-dd" value="{$today}" name="plantime" size="11">
        <div class="btn-group" role="group">
          <a type="button" class="btn btn-default" data-toggle="navtab" href="{:U('Item/Work/index',array('type'=>1));}">散客</a>
          <a type="button" class="btn btn-default" data-toggle="navtab" href="{:U('Item/Work/index',array('type'=>2));}">团队</a>
        </div>
        <div class="border" style="margin-top: 10px">
            <ul id="plan_games" class="ztree">
               
            </ul>
        </div>
    </div>

    <div style="margin-left:12px; width:350px;height:90%; float: left;" class="border">
        <table class="table table-bordered">
        <thead>
         <tr>
            <th align="center" width="150">票型名称</th>
            <th align="center" width="50">票面价</th>
            <th align="center" width="50">结算价</th>
            <th align="center" width="50">可售数</th>
          </tr>
        </thead>
        <tbody id="quick-price">
        </tbody>
      </table>
    </div>

    <div style="margin-left:14px; width:400px; height:90%; float: left; overflow:hidden;">
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
            <tbody id="quick-price-select">
            </tbody>
            <tr>
                <td colspan="2" align='right'>合计:</td>
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
<input type="hidden" id="planID" value="">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
    </ul>
</div>
<script type="text/javascript">
$(document).ready(function(){
    //auto 加载
    scenic_drifting_plan($("#plantime").val(),{$type});
    $('#plantime').on('afterchange.bjui.datepicker', function(e, data) {
        scenic_drifting_plan(FormatDate(data.value),{$type});
        //刷新购物车
        //$(this).bjuiajax('refreshDiv', 'quick-price-select');
        $(this).bjuiajax('refreshLayout','quick-price-select');
        //console.log($(this).bjuiajax('refreshLayout','quick-price-select'));
    });

    //根据配置选择窗口是结算价格结算还是结算价结算
    var ao = {$proconf.settlement};//TODO
    $('#quick-price').on('click','tr',function(event){
        var trId = $(this).data('id'),
            number = '1',
            price = ao == 2 ? $(this).data('discount') : $(this).data('price'),
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
        checkinT = '1',
        plan = $("#planID").val(),
        guide = '0',
        qditem = '0',
        settlement = {$proconf.settlement},
        is_pay = $('#selectPay option:selected').val(),
        length =  $("#quick-price-select tr").length,
        url:'<?php echo U('Item/Order/quickpost',array('type'=>$type));?>'+'&plan='+plan;
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
    $("#quick-price-select tr").each(function(i){
        var fg = i+1 < length ? ',':' ';/*判断是否增加分割符*/
        toJSONString = toJSONString + '{"areaId":'+$(this).data("area")+',"priceid":' +$(this).data("id")+',"price":'+parseFloat($(this).data('price')).toFixed(2)+',"num":"'+$("#quick-num-"+$(this).data("id")).val()+'"}'+fg;
    });
    /*获取支付相关数据*/
    pay = '{"cash":'+parseFloat($('#quick-total').html())+',"card":0,"alipay":0}';
    param = '{"remark":"'+remark+'","settlement":"'+settlement+'","is_pay":"'+is_pay+'"}';
    crm = '{"guide":'+guide+',"qditem":'+qditem+',"phone":'+phone+',"contact":"'+contact+'"}';
    postData = 'info={"subtotal":'+parseFloat($('#quick-total').html())+',"plan_id":'+plan+',"checkin":'+checkinT+',"sub_type":'+sub_type+',"data":['+ toJSONString + '],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';
    $(this).navtab('refresh','119Item');
    post_server(postData,url,'work_quick');
}
</script>