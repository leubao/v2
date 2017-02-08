<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
  <!--工具条 s-->
  <Managetemplate file="Common/Nav"/>
  <!--帮助 说明-->
</div>
<div class="bjui-pageContent tableContent">
  <div class="panel panel-default">
    <div class="panel-heading">
      <select class="required" name="plan" id="block_entire" data-toggle="selectpicker">
        <option value="">+=^^=售票日期=^^=+</option>
        <volist name="plan" id="vo">
          <?php $ptime =  $vo['plantime']."-".$vo['games'];?>
          <option value="{$vo.id}"  <if condition="$today eq $ptime">selected</if>>{$vo.plantime|date="Y-m-d",###} <if condition="$product['type'] eq 1"> 第{$vo.games}场 {$vo.starttime|date="H:i",###}</if>
          </option>
        </volist>
      </select>
      <div class="btn-group" role="group" aria-label="取消演出">
        <a type="submit" href="#" class="btn btn-danger" id="entire"><i class="fa fa-xing"> 取消演出</i></a>
      </div>
    </div>
   
  </div>
  <div class="col-xs-8"><textarea class="form-control" style="width: 600px" rows="20" id="entire_msg"></textarea></div>
  <div class="col-xs-4">
      <div id="entire_msg_ok" style='color:red;font-size:28px;'></div>
      <div id="entire_msg_order" style='color:red;font-size:10px;'></div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
  var plan = '';
  //自动加载默认选框
  $('#entire').click(function (){
    plan = $('#block_entire').children('option:selected').val();
    if(plan != '' || null || undefined){
      $('#entire_msg').val('开始取消场次....');
      var urls = '{:U('Item/Work/refund_entire',array('type'=>2))}'+'&plan='+plan;
      ajax_cache(urls);
    }else{
      $(this).alertmsg('error', '请选择销售计划!');
    }
  });
});
function ajax_cache(urls){
    ii = 0;
    $.ajax({
      type:"get",
      url:urls,
      dataType:"JSON",
      timeout: 1500,
      error: function(){
          layer.msg('服务器请求超时，请检查网络...');
      },
      success: function(data){
        if(data.stop != '0'){
          var msg = $('#entire_msg').val();
          msg = msg+'\n'+data.msg;
          $('#entire_msg').val(msg);
          setTimeout(function (){ajax_cache(data.urls);}, 2000);
          ii++;
        }else{
          //关闭$(this).navtab('refresh');
          var msgs = ''
          if(data.sns){
            msgs = "取消失败订单:\n"+data.sns;
            $('#entire_msg_order').html(msgs);
          }
          $('#entire_msg_ok').html(data.msg);
          setTimeout(function (){layer.closeAll();}, 1000);
        }
      }
    });
  }
</script> 