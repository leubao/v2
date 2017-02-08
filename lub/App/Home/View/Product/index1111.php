<?php if (!defined('LUB_VERSION')) exit(); ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Managetemplate file="Home/Public/cssjs"/>
<title>售票  - by LubTMP</title>
</head>

<body>
<div class="container">
<Managetemplate file="Home/Public/menu"/>
<include file="Home/Public/menu" nickname="{$userInfo['nickname']}"/>
<!--内容主体区域 start-->
<div class="main row">
<!--面包屑导航-->
<ol class="breadcrumb">
  <li><a href="{:U('Home/Index/index');}">首页</a></li>
  <li><a href="{:U('Home/Index/product');}">售票</a></li>
  <li class="active">{$productid|product_name}</li>
</ol>
<div class="row">
  <div class="col-md-2">
    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-globe"></span> 座椅区域</h3>
      </div>
      <div class="panel-body">
        <p>说明：选择座位区域</p>
      </div>
      <div class="list-group" id="area">
        <volist name="area['seat']" id="li"> <a href="javascript:void(0);" onclick="showtype(this)" class="list-group-item" id="list_{$li}" title="{$li|areaName}">{$li|areaName}<span class="badge" id="num_c12">50</span><!-- <input type="hidden" value="50" name="hidenum_c12" /> --></a> </volist>
      </div>
    </div>
  </div>
  <div class="col-md-5">
    <div class="table-responsive" id="ticketType">
      <table class="table table-bordered table-hover table-condensed">
        <thead>
          <tr>
            <td>票型</td>
            <td>单价</td>
            <td>可售数</td>
          </tr>
        </thead>
        <tbody id="tro">
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-5">
    <div class="panel panel-warning">
      <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-list-alt"></span> 订单信息</h3>
      </div>
      <div class="panel-body">
        <p>说明：请务必填写导游姓名，商家以此计算导游返佣。联系人请填写游客信息</p>
      </div>
      <ul class="list-group form-inline">
      <!--  
        <li class="list-group-item">
          <div class="form-group">
            <label for="guide" class="sr-only">导游姓名</label>
            <input type="text" class="form-control" id="guidename" placeholder="导游姓名" disabled="disabled">
            <input type="hidden" id="guideid" name="guide" >
          </div>
          <button type="button" id="findguide" class="btn btn-default" data-toggle="modal">查找导游</button>
        </li>-->
        <li class="list-group-item">
          <div class="form-group">
              <input type="radio" name="contact_option" id="optionsRadios1" checked onclick="$('#contact_select').css('display','block'),$('#contact_input').css('display','none');">常用联系人
              <input type="radio" name="contact_option" id="optionsRadios2" onclick="$('#contact_select').css('display','none'),$('#contact_input').css('display','block');">设置联系人
          </div>
        </li>
        <li class="list-group-item" id="contact_select">            
          <select class="form-control" name="contact" id="contact">
            <option value="">常用联系人</option>
            <volist name="list" id="vo">
              <option value="{$vo.id}" data-phone="{$vo.phone}" data-name="{$vo.name}">{$vo.name}</option>
            </volist>  
          </select>
        </li>
        <li class="list-group-item" id="contact_input" style="display:none"> 
        <div class="form-group"><input type="text" name="contacts" class="form-control" id="contacts" placeholder="联系人"></div>
        <div class="form-group"><input type="text" name="phone" class="form-control" id="phone" placeholder="手机号"></div>          
        </li>
      </ul>
    </div>
    <div class="panel panel-default table-responsive" id="selectTickt">
      <table class="table table-bordered table-hover table-condensed" id="kselect">
        <thead>
          <tr>
            <td>票型</td>
            <td>单价</td>
            <td style="width:120px">数量</td>
            <td>小计</td>
            <td>操作</td>
          </tr>
        </thead>
        <tbody id="cart">
        </tbody>
      </table>
      <div class="panel-footer" >合计：<span id="subtoal">0.00</span></div>
    </div>
    <div class="btn-group"> 
      <!-- data-target="#myModal" -->
      <button type="button" id="print" class="btn btn-default" data-toggle="modal" ><span class="glyphicon glyphicon-qrcode"></span>立即出票</button>
      <button type="button" class="btn btn-default" onclick="return Formcheck();">立即预定</button>
    </div>
  </div>
</div>
<div> 
  <!--内容主体区域 end--> 
  
  <!--弹出窗口 strat--> 
  <!-- Button trigger modal --> 
  <!-- Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:560px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">支付方式</h4>
        </div>
        <div class="modal-body">
          <div class="panel panel-default">
            <div class="panel-body"> 当前订单总计：<strong>￥</strong><strong id="totalcash">0.00</strong> </div>
          </div>
          
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#balance" role="tab" data-toggle="tab">余额支付</a></li>
            <li><a href="#silver" role="tab" data-toggle="tab">个人网银</a></li>
            <li><a href="#firm" role="tab" data-toggle="tab">企业网银</a></li>
          </ul>
          
          <!-- Tab panes -->
          <div class="tab-content">
            <div class="tab-pane active" id="balance">
            <p></p>
            <form action="">
              <div class="form-group">
                <p>当前账户可用余额：<strong>￥999.00</strong></p>
              </div>
              <div class="form-group">
                <input class="form-control" type="text" value="22" readonly>
              </div>
              <button type="button" class="btn btn-success" id="balancePay" data-loading-text="正在提交..." data-toggle="modal">立即支付</button>
              </div>
            </form>
            <div class="tab-pane" id="silver">
              <ul>
                <li>
                  <input id="bank-icbc" type="radio" name="bank" value="ICBCB2C" hidefocus="" checked="checked">
                  <label for="bank-icbc" class="bank-icbc"></label>
                </li>
                <li>
                  <input id="bank-ccb" type="radio" name="bank" value="CCB" hidefocus="">
                  <label for="bank-ccb" class="bank-ccb"></label>
                </li>
                <li>
                  <input id="bank-abchina" type="radio" name="bank" value="ABC" hidefocus="">
                  <label for="bank-abchina" class="bank-abchina"></label>
                </li>
                <li>
                  <input id="bank-psbc" type="radio" name="bank" value="POSTGC" hidefocus="">
                  <label for="bank-psbc" class="bank-psbc"></label>
                </li>
                <li>
                  <input id="bank-bankcomm" type="radio" name="bank" value="COMM" hidefocus="">
                  <label for="bank-bankcomm" class="bank-bankcomm"></label>
                </li>
                <li>
                  <input id="bank-cmbchina" type="radio" name="bank" value="CMB" hidefocus="">
                  <label for="bank-cmbchina" class="bank-cmbchina"></label>
                </li>
                <li>
                  <input id="bank-boc" type="radio" name="bank" value="BOCB2C" hidefocus="">
                  <label for="bank-boc" class="bank-boc"></label>
                </li>
                <li>
                  <input id="bank-cebbank" type="radio" name="bank" value="CEBBANK" hidefocus="">
                  <label for="bank-cebbank" class="bank-cebbank"></label>
                </li>
                <li>
                  <input id="bank-ecitic" type="radio" name="bank" value="CITIC" hidefocus="">
                  <label for="bank-ecitic" class="bank-ecitic"></label>
                </li>
                <li>
                  <input id="bank-spdb" type="radio" name="bank" value="SPDB" hidefocus="">
                  <label for="bank-spdb" class="bank-spdb"></label>
                </li>
                <li>
                  <input id="bank-cmbc" type="radio" name="bank" value="CMBC" hidefocus="">
                  <label for="bank-cmbc" class="bank-cmbc"></label>
                </li>
                <li>
                  <input id="bank-cib" type="radio" name="bank" value="CIB" hidefocus="">
                  <label for="bank-cib" class="bank-cib"></label>
                </li>
                <li>
                  <input id="bank-pingan" type="radio" name="bank" value="SPABANK" hidefocus="">
                  <label for="bank-pingan" class="bank-pingan"></label>
                </li>
                <li>
                  <input id="bank-cgbchina" type="radio" name="bank" value="GDB" hidefocus="">
                  <label for="bank-cgbchina" class="bank-cgbchina"></label>
                </li>
                <li>
                  <input id="bank-srcb" type="radio" name="bank" value="SHRCB" hidefocus="">
                  <label for="bank-srcb" class="bank-srcb"></label>
                </li>
                <li>
                  <input id="bank-bankofshanghai" type="radio" name="bank" value="SHBANK" hidefocus="">
                  <label for="bank-bankofshanghai" class="bank-bankofshanghai"></label>
                </li>
                <li>
                  <input id="bank-nbcb" type="radio" name="bank" value="NBBANK" hidefocus="">
                  <label for="bank-nbcb" class="bank-nbcb"></label>
                </li>
                <li>
                  <input id="bank-hccb" type="radio" name="bank" value="HZCBB2C" hidefocus="">
                  <label for="bank-hccb" class="bank-hccb"></label>
                </li>
                <li>
                  <input id="bank-bankofbeijing" type="radio" name="bank" value="BJBANK" hidefocus="">
                  <label for="bank-bankofbeijing" class="bank-bankofbeijing"></label>
                </li>
                <li>
                  <input id="bank-bjrcb" type="radio" name="bank" value="BJRCB" hidefocus="">
                  <label for="bank-bjrcb" class="bank-bjrcb"></label>
                </li>
                <li>
                  <input id="bank-fudian-bank" type="radio" name="bank" value="FDB" hidefocus="">
                  <label for="bank-fudian-bank" class="bank-fudian-bank"></label>
                </li>
                <li>
                  <input id="bank-wzcb" type="radio" name="bank" value="WZCBB2C-DEBIT" hidefocus="">
                  <label for="bank-wzcb" class="bank-wzcb"></label>
                </li>
              </ul>
              <button type="button" class="btn btn-success">立即支付</button>
            </div>
            <div class="tab-pane" id="firm">
              <ul>
                <li>
                  <input id="bank-ccb-enterprise" type="radio" name="bank" value="CCBBTB" hidefocus="" checked="checked">
                  <label for="bank-ccb-enterprise" class="bank-ccb"></label>
                </li>
                <li>
                  <input id="bank-abchina-enterprise" type="radio" name="bank" value="ABCBTB" hidefocus="">
                  <label for="bank-abchina-enterprise" class="bank-abchina"></label>
                </li>
                <li>
                  <input id="bank-spdb-enterprise" type="radio" name="bank" value="SPDBB2B" hidefocus="">
                  <label for="bank-spdb-enterprise" class="bank-spdb"></label>
                </li>
                <li>
                  <input id="bank-icbc-enterprise" type="radio" name="bank" value="ICBCBTB" hidefocus="">
                  <label for="bank-icbc-enterprise" class="bank-icbc"></label>
                </li>
              </ul>
              <button type="button" class="btn btn-success">立即支付</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--错误提示-->
  <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:560px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">错误提示</h4>
        </div>
        <div class="modal-body"> 
          
          <!-- Tab panes -->
          <div class="tab-content">
            <div class="tab-pane active" id="balance">
              <p></p>
              <div class="form-group">
                <p><strong>您选择的票型已经售完,请选择其他种类。</strong></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--支付提示-->
  <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:560px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">支付确认</h4>
        </div>
        <div class="modal-body"> 
          <!-- Tab panes -->
          <div class="tab-content">
            <div class="tab-pane active" id="balance">
              <p></p>
              <div class="form-group">
                <p>
                  <button type="button" class="btn btn-lg btn-success paybtn" id="paysuccess">完成支付</button>
                  <button type="button" class="btn btn-lg btn-warning paybtn">支付遇到问题</button>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--查找导游-->
  <div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:560px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">查找导游</h4>
        </div>
        <div class="modal-body"> 
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
            
            <li><a href="#guideinfo" role="tab" data-toggle="tab">信息查询</a></li>
          </ul>
          
          <!-- Tab panes -->
          <div class="tab-content">
            <div class="tab-pane" id="guideinfo">
              <div class="form-inline" style="margin-top:10px;">
                <div class="form-group">
                  <input type="text" class="form-control" name="name" id="guidesname" placeholder="导游姓名"/>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-addon">或</div>
                    <input type="text" name="phone" class="form-control" id="guidesphone" placeholder="手机号码"/>
                  </div>
                </div>
                <button class="btn btn-default" id="guidesearch"> 查找 </button>
              </div>
              <!--导游信息显示-->
              <div id="chooseguide"> </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--弹出窗口 end-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>
<script src="{$config_siteurl}lub/App/Home/Assets/js/cart.js"></script>
<script type="text/javascript">
/*立即出票*/
$(function(){
  $("#print").bind("click",function(){
	 //手动输入联系人
	var rstr = "";
	if($("#optionsRadios2").attr("checked")){
	    var vMobile = $("#phone").val();
	    if (!vMobile.match(/^(1(([35][0-9])|(47)|[8][01236789]))\d{8}$/)) {
	      rstr += "手机格式不正确!";
	    } 
	    var vmima = $("#contacts").val();
	    if (vmima == '') {
	        rstr += "姓名不能为空!";
	    }
	}else{
		var contact = $("#contact").val();
		var vMobile = $("#contact").find('option:selected').attr('data-phone');
		var vmima = $("#contact").find('option:selected').data('name');
		//alert(vMobile);
		if(contact == ''){
			rstr += "取票人不能为空!";
		}
	}
    if(rstr !=""){
      alert(rstr);
    }else{
      //获取已选择的票型并组合数据
      var 
        pay = " ",
        toJSONString = " ",
        length =  $("#kselect tr").length - 2;
        if(length < 0){
          alert('请选择要售出的票型!');
          return false;
        }
      $("#kselect tr").each(function(i){
        if(i != 0 ){
          var fg  = i <= length ? ',':' ';/*判断是否增加分割符*/
          var ids = this.id.split("_");
          toJSONString = toJSONString + '{"areaId":'+$("#areaid"+ids[1]).val()+',"priceid":' +ids[1]+',"price":'+parseFloat($("#price_"+ids[1]).html())+',"num":"'+$("#qnum_"+ids[1]).val()+'"}'+fg;
        }
      })
      /*获取支付相关数据*/
     // var phone   = $("#phone").val();
     // var contact = $("#contact").val();
      var checkinT = 1;
      crm = '{"guide":0,"qditem":0,"phone":'+vMobile+',"contact":'+vmima+'}';
      var postData = 'info={"subtotal":'+parseFloat($("#subtoal").html())+',"checkin":'+checkinT+',"data":['+ toJSONString + '],"param":['+crm+']}';
      /*提交到服务器*/
      $.ajax({
        type:'POST',
    	url:'index.php?g=Home&m=Order&a=channelPost',
        data:postData,
        dataType:'json',
        success:function(data){
          //alert(data.info);
          if(data.statusCode == "200"){
            $("#myModal").modal('show');
            var total = $("#subtoal",window.parent.document).html();
            $("#totalcash").text(total);
          }else{
            //alert('出票失败!');
            $("#myModal2").modal('show');  //出票失败的提示
          }
        }
      });
    }
  });

  $("#balancePay").bind("click",function(){
    $("#myModal").modal('hide');  //关闭支付模态框
    $("#myModal3").modal('show'); //支付状态模态框
    //调用支付方法
    //跳转页面
    window.open("index.php?g=Home&m=Product&a=paysuccess");
  });
  //支付成功
  $("#paysuccess").bind("click",function(){
    window.location.href="index.php?g=Home&m=Product&a=paysuccess";
  });
  //查找导游
  $("#findguide").bind("click",function(){
    $("#myModal4").modal('show');
  });

  //导游查找按钮
  $("#guidesearch").bind("click",function(){
    name  = $("#guidesname").val();
    phone = $("#guidesphone").val();
    $.get("index.php?g=Home&m=Product&a=guidecheck&name="+name+"&phone="+phone+"&itemid={$info['itemid']}", function(data){
      if(data != 0){
        var result = $.parseJSON(data);
        var content = "";
        $.each(result,function(idx,item){ 
          var id    = item.id;
          var name  = item.name;
          content = "<hr><ul><li><span class='guidetitle'>"+name+"</span><a href='javascript:void(0)' onclick=guideback('"+id+"','"+name+"'); ><img src='{$config_siteurl}lub/App/Home/Assets/images/check.jpg' width='16' height='16'/></a></li></ul>";
        });
      }else{         
        content = "<hr><ul><li><span>暂无相关导游，请重新查询！</span></li><ul>";
      }
      $("#chooseguide").html(content);      
    });  
  });
})
/*根据座位区域，显示相关票型*/
function showtype(t){
  var id1    = t.id.split("_");
  var areaid = id1[1];
  $.get("index.php?g=Home&m=Product&a=quickPrice&areaid="+areaid+"&productid={$info['productid']}", function(data){
    if(data != 0){
      var result = $.parseJSON(data);
      var content = ""; 
      $.each(result,function(idx,item){ 
        var id    = item.id;
        var name  = item.name;
        var price = item.price;
        
        //if($("#tro_"+id).length <= 0){
          content += "<tr id='tro_"+id+"_"+areaid+"' class='tro'><td>"+name+"</td><td>"+price+"</td><td>50</td></tr>";
        //}
      });
      $("#tro").html(content);
    }
  });
  //确定当前选中的区域为选中的状态
  $('#area a').each(function () {
    $(this).attr("class","list-group-item");
  });
  $(t).attr("class","list-group-item active");
}
</script>