<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
  <!--工具条 s-->
  <Managetemplate file="Common/Nav"/>
  <!--帮助 说明-->
</div>
<div class="bjui-pageContent tableContent">
  <div class="panel panel-default">
    <div class="panel-heading">
      <select class="required" name="plan" id="block_plan" data-toggle="selectpicker">
        <option value="">+=^^=售票日期=^^=+</option>
        <volist name="plan" id="vo">
          <?php $ptime =  $vo['plantime']."-".$vo['games'];?>
          <option value="{$ptime}"  <if condition="$today eq $ptime">selected</if>>{$vo.plantime|date="Y-m-d",###} <if condition="$product['type'] eq 1"> 第{$vo.games}场 {$vo.starttime|date="H:i",###}</if>
          </option>
        </volist>
      </select>
      <div class="btn-group" role="group" aria-label="售票">
        <a type="submit" href="#" class="btn btn-success" id="basics"><i class="fa fa-xing"> 基本控座</i></a>
        <a type="submit" class="btn btn-info" href="#" id="release"><i class="fa fa-xing-square"></i> 释放控座</a>
        <a type="submit" class="btn btn-success" href="#" id="base_control"><i class="fa fa-reply-all"></i> 快捷操作</a>
      </div>
    </div>
    <table class="table table-bordered">
      <thead>
            <tr>
              <th align="center" width="120">座位区域</th>
              <th align="center" width="80">总数</th>
              <th align="center" width="80">空闲数</th>
              <th align="center" width="80">已售数</th>
              <th align="center" width="80">控座数</th>
              <th align="center" width="180">操作</th>
            </tr>
      </thead>
      <tbody id="block_area_seat">
      </tbody>
    </table>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
  var plan = '',
      planId = '';
  //自动加载默认选框
  plan = $('#block_plan').children('option:selected').val();
  if(plan != '' || null || undefined){
    var data = 'info={"plan":"'+plan+'"}',
        content = '';
        $.ajax({
            url: '{:U('Item/Block/set_session_plan')}',
            type: 'POST',
            dataType: 'JSON',
            timeout: 1000,
            data:data,
            error: function(){
                layer.msg('服务器请求超时，请检查网络...');
            },
            success: function(rdata){
              if(rdata.statusCode == '200'){
                 planId = rdata.plan;
                 /*写入*/
                 $(rdata.area).each(function(idx,area){
                    content += "<tr><td align='center'>"+area.name+"</td><td align='center'>"+area.number+"</td><td align='center'>"+area.num+"</td><td align='center'>"+area.nums+"</td><td align='center'>"+area.numb+"</td><td align='center'>"
                    +"[<a href='#' onclick='block_select("+planId+",1,"+area.id+");' title='演出控座'>演出控座</a>]"
                    +"[<a href='#' onclick='block_select("+planId+",2,"+area.id+");' title='释放控座'>释放控座</a>]"
                    +"</td></tr>";
                 });
              }
              $("#block_area_seat").html(content);
            }
        });
  }else{
    var error_msg = "<tr><td style='padding:15px;' colspan='6' align='center'><strong style='color:red;font-size:48px;'>请选择售票日期</strong></td></tr>";
    $("#block_area_seat").html(error_msg);
  }
  //改变日期场次
  $('#block_plan').change(function(){
    plan = $(this).children('option:selected').val();
    if(plan != '' || null || undefined){
        var data = 'info={"plan":"'+plan+'"}',
            content = '';
            $.ajax({
                url: '{:U('Item/Block/set_session_plan')}',
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
                        content += "<tr><td align='center'>"+area.name+"</td><td align='center'>"+area.number+"</td><td align='center'>"+area.num+"</td><td align='center'>"+area.nums+"</td><td align='center'>"+area.numb+"</td><td align='center'>"
                        +"[<a href='#' onclick='block_select("+planId+",1,"+area.id+");' title='演出控座'>演出控座</a>]"
                        +"[<a href='#' onclick='block_select("+planId+",2,"+area.id+");' title='释放控座'>释放控座</a>]"
                        +"</td></tr>";
                     });
                  }
                  $(this).alertmsg('ok', '售票场次,切换成功!');
                  $("#block_area_seat").html(content);
                }
            });
    }else{
      var error_msg = "<tr><td style='padding:15px;' colspan='6' align='center'><strong style='color:red;font-size:48px;'>请选择售票日期</strong></td></tr>";
      $("#block_area_seat").html(error_msg);
    }
  });
  $('#basics').click(function (){
    if(planId){
      $(this).dialog({id:'basics_seat', url:'{:U('Item/Block/basics',array('type'=>'1','menuid'=>$menuid))}&plan='+planId, title:'基本控座', mask:true,maxable:false, fresh:false,resizable:false, drawable:false});
    }else{
      $(this).alertmsg('error', '请选择售票日期!');
    }
  });
  $('#release').click(function (){
    if(planId){
      $(this).dialog({id:'release_seat', url:'{:U('Item/Block/basics_release',array('type'=>'1','menuid'=>$menuid))}&plan='+planId, title:'基本控座', mask:true,maxable:false, fresh:false,resizable:false, drawable:false});
    }else{
      $(this).alertmsg('error', '请选择售票日期!');
    }
  });
  /*default_group*/
  $('#base_control').click(function (){
    if(planId){
      $(this).dialog({id:'base_control', url:'{:U('Item/Block/set_control',array('type'=>'1','menuid'=>$menuid))}&plan='+planId, title:'快捷操作', mask:true,maxable:false, fresh:false,resizable:false, drawable:false});
    }else{
      $(this).alertmsg('error', '请选择售票日期!');
    }
  });
});
function block_select(planId,type,area){
    if(planId){
      $(this).dialog({id:'Block_seat', url:'{:U('Item/Block/senior')}&type='+type+'&area='+area+'&plan='+planId, title:'高级控座', max:true, mask:true,maxable:false, fresh:false,resizable:false, drawable:false});
    }else{
      $(this).alertmsg('error', '请选择售票日期!');
    }
  }
</script> 