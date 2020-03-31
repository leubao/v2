<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>订单核销 - 云鹿票券-电子门票</title>
  <link rel="stylesheet" href="../static/layui/css/layui.css">
  <style type="text/css" media="screen">
 	html, body {background-color: #eee;}
  .alone-version-desc{border-top: 5px solid #e64340;position: relative; margin-top: 30px; padding: 40px 50px 50px; border-radius: 0; background-color: #fff; text-align: center; transition: all .3s; -webkit-transition: all .3s;}
	.alone-version-desc h2{padding-bottom: 15px; font-size: 18px;}
	.alone-badge{position: relative; display: inline-block; border: 1px solid #e6e6e6; line-height: 28px; padding: 15px; border-radius: 2px;}
	.alone-version-desc ul{height: 220px; margin-top: 15px; text-align: left;}
	.alone-buy{position: relative; text-align: center;}
	.alone-buy .layui-btn{width: 100%;}	
	#qrcode {}
	.layui-text ul li{list-style-type:none;}
  </style>
</head>
<body>
 <div class="layui-container">
  <div class="layui-row">

	<div class="alone-version-desc layui-text {$class}">
    <form class="layui-form" action="" lay-filter="myform">
      <div class="layui-form-item">
        <div class="layui-inline">
          <label class="layui-form-label">凭证类型</label>
          <div class="layui-input-inline" style="width: 100px;">
            <select name="type" lay-verify="required">
              <option value="qr" selected="true">二维码</option>
              <option value="sn">订单号</option>
              <option value="mobile">手机号</option>
            </select>
          </div>
        </div>
        <div class="layui-inline">
          <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="code" placeholder="订单号" lay-verify="required|sn" autocomplete="off" class="layui-input">
          </div>
        </div>
        <div class="layui-inline">
          <div class="layui-input-inline" style="width: 60px;">
            <button class="layui-btn" lay-submit="" lay-filter="google">查询</button>
          </div>
        </div>
        <div class="layui-inline">
          <div class="layui-input-inline" style="width: 60px;">
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
          </div>
        </div>
      </div>
    </form>
    <input type="hidden" id="sn" value="">
    <div id="contact" class="layui-form-item"></div>
    <table class="layui-table layui-hide" id="ticket" lay-filter="ticket"></table>
    <div class="layui-btn-group ticketTable">
      <button class="layui-btn layui-btn-warm" data-type="checkSmall">部分核销</button>
      <button class="layui-btn layui-btn-danger" data-type="checkAll">全部核销</button>
    </div>
  </div>
 </div>
<script src="../static/layui/layui.js"></script>
<script id="contactTpl" type="text/html">
  <div class="layui-card-body">
    <div class="layui-row">
      <div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
        <div class="layadmin-text">
          单号: <b>{{ d.sn }} </b>
        </div>
      </div>
      <div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
        <div class="layadmin-text">
          游玩日期: <b>{{ d.plan }}</b>
        </div>
      </div>
      <div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
        <div class="layadmin-text">
          联系人: <b>{{ d.contact.user }} </b>
        </div>
      </div>
      <div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
        <div class="layadmin-text">
          联系电话: <b>{{ d.contact.mobile }}</b>
        </div>
      </div>
    </div>
  </div>
</script>
<script>
layui.use(['table','form', 'laytpl'], function(){
  var table = layui.table;
  var form = layui.form
      ,laytpl = layui.laytpl;
  //表单
  form.verify({
    sn: function(value){
      if(value.length < 7){
        return '订单号长度有误';
      }
    }
  });
  //展示已知数据
  table.render({
    elem: '#ticket'
    ,cols: [[
      {type:'checkbox'}
      ,{field: 'id', title: '编号', align:'center', width: 80,}
      ,{field: 'plan', title: '销售计划', align:'center', width: 180}
      ,{field: 'sn', title: '订单号', align:'center', width: 120}
      ,{field: 'ticket', title: '票号', align:'center', minWidth: 100}
      ,{field: 'price', title: '票类', align:'center',minWidth: 100}
    ]]
    ,data: []
    ,id:'idTest'
    ,even: true
  });
  var getTpl = contactTpl.innerHTML
  ,view = document.getElementById('contact');
  //监听提交
  form.on('submit(google)', function(data){
    $.ajax({
       type: "POST",
       url: "{:U('Sales/Ticket/cancel');}",
       async:false,
       data: data.field,
       dataType: "json",
       success: function(res){
         if(res.status){
          table.reload('idTest',{
            data:res.data.ticket
          });
          laytpl(getTpl).render(res.data, function(html){
            view.innerHTML = html;
          });
          $('#sn').val(res.data.sn);
         }else{
            layer.msg(res.msg);
         }
      }
    });
    return false;
  });
  
  var $ = layui.$, active = {
    //部分核销
    checkAll: function() {
      var sn = $('#sn').val();
      if(sn.length < 7){layer.msg('订单号有误请重新输入');table.reload('idTest',{data: []});return false;}
      $.ajax({
         type: "POST",
         url: "{:U('Sales/Ticket/checkin');}",
         async:false,
         data: {'type':'all','sn':sn},
         dataType: "json",
         success: function(data){
           if(data.status){
              $('.layui-input').val("");

              table.reload('idTest',{data: []});
              layer.msg(data.msg);
           }else{
              layer.msg(data.msg);
           }
        }
      });
    }
    //全部核销
    ,checkSmall:function() {
      var checkStatus = table.checkStatus('idTest')
      ,ticket = checkStatus.data
      ,sn = $('#sn').val();
      if(ticket.length == 0){
        layer.msg('请勾选要核销的门票');
        return false;
      }
      if(sn.length < 7){layer.msg('订单号有误请重新输入');table.reload('idTest',{data: []});return false;}
      $.ajax({
         type: "POST",
         url: "{:U('Sales/Ticket/checkin',['type'=>'small']);}",
         async:false,
         data: {'info':ticket,'type':'small','sn':sn},
         dataType: "json",
         success: function(data){
           if(data.status){
              $('.layui-input').val("");
              table.reload('idTest',{data: []});
              layer.msg(data.msg);
           }else{
              layer.msg(data.msg);
           }
        }
      });
      
    }
  };
  
  $('.ticketTable .layui-btn').on('click', function(){
    var type = $(this).data('type');
    active[type] ? active[type].call(this) : '';
  });
});
</script>

</body>
</html>