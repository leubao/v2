<?php if (!defined('LUB_VERSION')) exit(); ?>
<script src="{$config_siteurl}static/js/LodopFuncs.js" type="text/javascript"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0 style="display: none"> </object>
<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
<script type="text/javascript" src="//g.alicdn.com/sj/lib/jquery/dist/jquery.min.js"></script>
<script src="{$config_siteurl}static/layer/layer.js" type="text/javascript"></script>
<style type="text/css">
.print_type{height: 25px;line-height: 1.5;font-size: 10px;}
.print_type input{margin: 0px 0px 0px 3px}
</style>
<div class="print_type">

  <input type="radio" name="type" id="print_type" value="1" <if condition="$proconf['print_type'] eq '1'">checked</if>> 一人一票  
  <input type="radio" name="type" id="print_type" value="2" <if condition="$proconf['print_type'] eq '2'">checked</if> <if condition="$data['genre'] eq '6'">disabled</if>> 一单一票
  </div>

  <button type="button" style="width: 150px;height: 100px;" id="print_ticket" onclick="printTicket()">打印门票</button>      

<script>
var LODOP; //声明为全局变量  
function printTicket(){
  var type = $('#print_type:checked').val();
  $("#print_ticket").attr("disabled", true).val('打印中..');
  $.ajax({
    type:'get',
    dataType : 'json',
    url:'{$get_ticket_url}&user={$data.user}&type='+type,
    timeout: 2500,
    error: function(){
      /*关闭当前弹窗*/
      layer.closeAll();
      layer.msg('服务器请求超时，请检查网络...');
      parent.location.reload();
    },
    success:function(data){
      if(data.status == '1'){
        var selSeat = eval(data.info);/*返回的座位信息*/
        $.each(selSeat,function(){
          /*打印设置部分*/
          CreateFullBill(this,type);
          /*设置连续打印*/
          LODOP.SET_PRINT_PAGESIZE(2,{$printTpl.width},{$printTpl.height},"USER");
          LODOP.SET_PRINT_MODE("POS_BASEON_PAPER",true);
          LODOP.PRINT();  
          

        });
        /*关闭当前弹窗*/
        layer.closeAll();
          //TODO刷新页面会导致数据丢失
        //parent.location.reload();
      }else{
        layer.msg(data.message);
        layer.closeAll();
        parent.location.reload();
      }
    }
  });
}
/*打印页面控制*/
function CreateFullBill(data,type) {
  LODOP=getLodop();
  LODOP.SET_SHOW_MODE("LANGUAGE",0);
  {$printTpl.info}
}
</script>
</body>
</html>
