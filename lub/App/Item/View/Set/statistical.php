<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
  <!--工具条 s-->
  <Managetemplate file="Common/Nav"/>
  <!--帮助 说明-->
</div>
<div class="bjui-pageContent tableContent">
  <div class="panel panel-default">
    <div class="panel-heading">
      <select class="required" name="plan" id="checkin_plan" data-toggle="selectpicker">
        <option value="">+=^^=售票日期=^^=+</option>
        <volist name="plans" id="vo">
          <?php $ptime =  $vo['plantime']."-".$vo['games'];?>
          <option value="{$ptime}"  <if condition="$today eq $ptime">selected</if>>{$vo.plantime|date="Y-m-d",###} <if condition="$product['type'] eq 1"> 第{$vo.games}场 {$vo.starttime|date="H:i",###}</if>
          </option>
        </volist>
      </select>
    </div>
    <table class="table table-bordered">
      <thead>
            <tr>
              <th align="center" width="120">座位区域</th>
              <th align="center" width="80">总数</th>
              <th align="center" width="80">空闲数</th>
              <th align="center" width="80">已售数</th>
              <th align="center" width="80">检票数</th>
              <th align="center" width="80">未检数</th>
            </tr>
      </thead>
      <tbody id="checkin_area_seat">
      </tbody>
    </table>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
  var plan = '',
      planId = '';
  //自动加载默认选框
  plan = $('#checkin_plan').children('option:selected').val();
  if(plan != '' || null || undefined){
    var data = 'info={"plan":"'+plan+'"}',
        content = '';
    $.post("{:U('Item/Work/set_session_plan')}",data,function(rdata){
        //设置planId
        if(rdata.statusCode == '200'){
           planId = rdata.plan;
           /*写入*/
           $(rdata.area).each(function(idx,area){
              var wjnum = area.nums - area.cnum;
              content += "<tr><td align='center'>"+area.name+"</td><td align='center'>"+area.number+"</td><td align='center'>"+area.num+"</td><td align='center'>"+area.nums+"</td><td align='center'>"+area.cnum+"</td><td align='center'>"+wjnum+"</td></tr>";
           });
        }
        $("#checkin_area_seat").html(content); 
    },"json");
  }else{
    var error_msg = "<tr><td style='padding:15px;' colspan='6' align='center'><strong style='color:red;font-size:48px;'>请选择售票日期</strong></td></tr>";
    $("#checkin_area_seat").html(error_msg);
  }
  //改变日期场次
  $('#checkin_plan').change(function(){
    plan = $(this).children('option:selected').val();
    if(plan != '' || null || undefined){
        var data = 'info={"plan":"'+plan+'"}',
            content = '';
        $.post("{:U('Item/Work/set_session_plan')}",data,function(rdata){
            //设置planId
            if(rdata.statusCode == '200'){
               planId = rdata.plan;
               /*写入*/
               $(rdata.area).each(function(idx,area){
                var wjnum = area.nums - area.cnum;
                  content += "<tr><td align='center'>"+area.name+"</td><td align='center'>"+area.number+"</td><td align='center'>"+area.num+"</td><td align='center'>"+area.nums+"</td><td align='center'>"+area.cnum+"</td><td align='center'>"+wjnum+"</td></tr>";
               });
            }
            $(this).alertmsg('ok', '售票场次,切换成功!');
            $("#checkin_area_seat").html(content); 
        },"json");

    }else{
        var error_msg = "<tr><td style='padding:15px;' colspan='6' align='center'><strong style='color:red;font-size:48px;'>请选择售票日期</strong></td></tr>";
        $("#checkin_area_seat").html(error_msg);
    }
  });
});
function seat_select(planId,type,area){
    if(planId){
      $(this).dialog({id:'checkin_seat', url:'{:U('Item/Work/sales')}&type='+type+'&area='+area+'&plan='+planId, title:'选座售票', max:true, mask:true,maxable:false, fresh:false,resizable:false, drawable:false});
    }else{
      $(this).alertmsg('error', '请选择售票日期!');
    }
  }
</script> 