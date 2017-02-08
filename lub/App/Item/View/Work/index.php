<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
  <!--工具条 s-->
  <Managetemplate file="Common/Nav"/>
  <!--帮助 说明-->

</div>
<div class="bjui-pageContent tableContent">
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
      <a type="submit" href="#" class="btn btn-success" id="quick"><i class="fa fa-deviantart"> 快捷售票</i></a>
      
      <if condition="$procof['window_channel'] eq '1'">
      <a type="submit" class="btn btn-info" href="#" id="teams"><i class="fa fa-flickr"></i> 团队售票</a> 
      </if>
      <!--
      <a type="submit" href="#" class="btn btn-warning" id="difference"><i class="fa fa-money"> 补差价</i></a>
      -->
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
<script type="text/javascript">
$(document).ready(function(){
  var plan = '',
      planId = '';
  //自动加载默认选框
  plan = $('#work_plan').children('option:selected').val();
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
  $('#work_plan').change(function(){
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
	$('#quick').click(function (){
    if(planId){
      $(this).dialog({id:'work_quick', url:'{:U('Item/Work/quick',array('type'=>'1'))}&plan='+planId, title:'快捷售票', max:true, mask:true,maxable:false, fresh:false,resizable:false, drawable:false});
    }else{
      $(this).alertmsg('error', '请选择售票日期!');
    }
  });
	$('#teams').click(function (){
    if(planId){
      $(this).dialog({id:'work_quick', url:'{:U('Item/Work/quick',array('type'=>'2'))}&plan='+planId, title:'团队售票', max:true, mask:true,resizable:false,maxable:false, fresh:false, drawable:false});
	  }else{
      $(this).alertmsg('error', '请选择售票日期!');
    }
  });
  $('#difference').click(function (){
    if(planId){
      $(this).dialog({id:'work_quick', url:'{:U('Item/Work/difference',array('type'=>'1'))}&plan='+planId, title:'补差价', max:true, mask:true,resizable:false,maxable:false, fresh:false, drawable:false});
    }else{
      $(this).alertmsg('error', '请选择售票日期!');
    }
  });
});
function seat_select(planId,type,area){
    if(planId){
      $(this).dialog({id:'work_seat', url:'{:U('Item/Work/sales')}&type='+type+'&area='+area+'&plan='+planId, title:'选座售票', max:true, mask:true,maxable:false, fresh:false,resizable:false, drawable:false});
    }else{
      $(this).alertmsg('error', '请选择售票日期!');
    }
  }
</script> 