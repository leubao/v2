$(function(){
  $("body").on("change",".qnum", function () { 
    var ids = Array();
    var id  = this.name;
    var ids = id.split("_"); 
    totalprice(ids[1]);
  });
  //点击票型
  $("body").on("click",".tro",function(){
    var ids = Array();
    var id  = this.id;
    var ids = id.split("_");
    //订单信息中显示票型、价格等信息
    if($("#cart_"+ids[1]).length <= 0){
      var obj = $(this);
      var data_name  = obj.find("td").eq(0).text();
      var data_price = obj.find("td").eq(1).text();
      detail = "<tr id='cart_"+ids[1]+"'><td>"+data_name+"</td><td id='price_"+ids[1]+"'>"+data_price+"</td><td>";
      detail += "<div class='input-group spinner' data-trigger='spinner'> <div class='input-group-addon'><a href='javascript:;' class='spin-down' data-spin='down'>-</a></div>";
      detail += "<input type='text' class='qnum form-control' id='qnum_"+ids[1]+"'' name='qnum_"+ids[1]+"' data-rule='percent' value='1' size='2'>";
      detail += "<div class='input-group-addon'><a href='javascript:;' class='spin-up' data-spin='up'>+</a></div></div>";
      detail += "</td><td class='total' id='total_"+ids[1]+"'>"+data_price+"</td><td><a href='javascript:void(0)' onclick='delcart("+ids[1]+")'>删除</a><input id='areaid"+ids[1]+"' value='"+ids[2]+"' name='areaid' type='hidden'></td></tr>";
      $("#cart").append(detail);
    }
    //切换票型的选中状态
    $('.tro').each(function () {
      $(this).attr("class","tro");
    });
    $(this).attr("class","tro active");
    totalprice(ids[1]);
  });

  //订单中点击向上箭头
  $("body").on("click",".spin-up",function(){
    var obj    = $(this).parent().parent();
    var curnum = obj.find(".qnum").val();
    var name = obj.find(".qnum").attr("name");
    var ids  = name.split("_");
    var qnum = parseInt(curnum)+1;
    if(qnum > PRO_CONF.channel_order){
    	layer.msg("超出单笔订单门票总数限额...");
    }else{
    	obj.find(".qnum").val(qnum);
    	totalprice(ids[1]);
    }
  });
  //订单中点击向下箭头
  $("body").on("click",".spin-down",function(){
    var obj    = $(this).parent().parent();    
    var curnum = obj.find(".qnum").val();
    var name = obj.find(".qnum").attr("name");
    var ids  = name.split("_");
    var qnum = parseInt(curnum)-1;
    if(qnum > 0){
      obj.find(".qnum").val(qnum);
    }else{
      if(confirm("确定要删除此条订单信息吗？")){
        $("#cart_"+ids[1]).remove();//删除
      }       
    }
    totalprice(ids[1]);  //总价
  });  
});
/*计算当前已选择的票型数量*/

/*计算合计与小计*/
function totalprice(id){
  var num   = $("input[name=qnum_"+id+"]").val();
  var price = parseFloat($("#price_"+id).text()).toFixed(2);
  var totalprice = parseFloat(parseInt(num)*price).toFixed(2);
  $("#total_"+id).text(totalprice);   //为每个票型订单详情后的小计赋值
  var totals = 0;
  $(".total").each(function(){
    totals += Number($(this).html());
  });
  var totals = parseFloat(totals).toFixed(2);
  $("#subtoal").text(totals);        //为“合计”赋值
}
/*删除订单信息*/
function delcart(id){
  if(confirm("确定要删除此条订单信息吗？")){
    $("#cart_"+id).remove();//删除
    totalprice(id);
  }
}
/**zj**/
/*立即出票*/
$(function(){
  $("#print").bind("click",function(){
  	var rstr = "",
	    vmima = "",
	    vMobile = "",
	    plan = $('#planID').val(),
	    id_card = $("#id_card").val(),
	    remark = $("#remark").val();
    if($(".contact_input").css("display") == "block"){
	    vMobile = $("#phone").val();
	    if (!vMobile.match(/^((1[3,5,8][0-9])|(14[5,7])|(17[0,3,6,7,8]))\d{8}$/)) {
	      rstr += "手机格式不正确!";
	    } 
	    vmima = $("#contacts").val();
	    if (vmima == '') {
	        rstr += "姓名不能为空!";
	    }
	}else{
		var contact = $("#contact").val();
		vMobile = $("#contact").find('option:selected').attr('data-phone');
		vmima = $("#contact").find('option:selected').data('name');
		id_card = $("#contact").find('option:selected').data('idcard');
		if(contact == ''){
			rstr += "取票人不能为空!";
		}
	}
	/*是否开启黑名单*/
	if(PRO_CONF.black == '1'){
		var guide_black = $("#guide_black").val();
	    if (!guide_black.match(/^((1[3,5,8][0-9])|(14[5,7])|(17[0,3,6,7,8]))\d{8}$/)) {
	      layer.msg("导游手机号码不正确!");
	      return false;
	    }else{
	    	if(black(guide_black)){
	    		layer.msg("抱歉,该导游已被系统列入黑名单，请联系管理员!");
	      		return false;
	    	}
	    }
	}
	/*判断身份号码是否正确*/
	if(id_card){
		if(checkIdCard(id_card) == false){
			rstr += "请您正确输入身份证号码，或者不输入!";
		}
	}
	/*客源地判断*/
	var tour = $("#tourists").val();
	if(!tour){ rstr += "请选择客源地!";}
	if(!remark){ remark = "空.."; }
    if(rstr !=""){
      layer.msg(rstr);
    }else{
      //获取已选择的票型并组合数据
      var 
        pay = " ",
        toJSONString = " ",
        length =  $("#kselect tr").length - 2,
		num = 0,
		nums= 0;
       if(length < 0){
		 layer.msg("请选择要售出的票型!");

         return false;
       }
       $("#kselect tr").each(function(i){
       if(i != 0 ){
       		var fg  = i <= length ? ',':' ';/*判断是否增加分割符*/
      	 	var ids = this.id.split("_");
			nums = parseInt(nums)+parseInt($("#qnum_"+ids[1]).val());
    		toJSONString = toJSONString + '{"areaId":'+$("#areaid"+ids[1]).val()+',"priceid":' +ids[1]+',"price":'+parseFloat($("#price_"+ids[1]).html())+',"num":"'+$("#qnum_"+ids[1]).val()+'"}'+fg;
        }
      });
      //检测总数
      if(nums <= PRO_CONF.channel_order){
       	  //判断配额
      	  $.get("index.php?g=Home&m=Product&a=quota&num="+nums+"&plan="+plan, function(data){
      		if(data != 0){
      		  var result = $.parseJSON(data);
      		  if(result.statusCode == "0"){
      			$("#error").text("配额不足，请联系渠道负责人!");
      			$("#myModal2").modal('show');  //出票失败的提示
      		  }else{
      			  /*获取支付相关数据 */
      			  var guide = $("#guideid").attr("value");/*渠道商登录时为业务员ID默认为当前登录用户导游登录时为导游id,*/
      			  var itemid = $("#channel_id").attr("value");/*渠道商登录时为渠道商id导游登录时默认为散客 导游的id*/
      			  var checkinT = 1;
      			  crm = '{"guide":'+guide+',"qditem":'+itemid+',"phone":'+vMobile+',"contact":"'+vmima+'"}';
      			  param = '{"tour":'+tour+',"remark":"'+remark+'","id_card":"'+id_card+'","guide_black":"'+guide_black+'","settlement":"'+USER_INFO.group.settlement+'"}';
      			  var postData = 'info={"subtotal":'+parseFloat($("#subtoal").html())+',"plan_id":'+plan+',"checkin":'+checkinT+',"data":['+ toJSONString + '],"crm":['+crm+'],"param":['+param+']}'; 
      			  /*提交到服务器*/
      			  $.ajax({
      			    type:'POST',
      			    url:'index.php?g=Home&m=Order&a=channelPost',
      			    data:postData,
      			    dataType:'json',
      			    success:function(data){
      				  	if(data.statusCode == "200"){
      				  	  $("#myModal").modal('show');
      				  	  money();
      				  	  var total = $("#subtoal",window.parent.document).html();
      				  	  $("#totalcash").text(total);
      				  	  $("#tomoney").attr('value',total);
      				  	  $("#sn").attr('value',data.sn);
      				  	}else{
      				  	  $("#error").text("订单创建失败!");
      				  	  $("#myModal2").modal('show');  //出票失败的提示
      				  	}
      			    }
      			  });
      		  }
      		}
      	  });
      }else{
	  	layer.msg("超出单笔订单门票总数限额...");
      }
    }
  });
  //立即预定  付款  但不排座
  $("#pre").bind("click",function(){
	var rstr = "",
		id_card = $("#id_card").val(),
		plan = $('#planID').val(),
		remark = $("#remark").val();
    if($(".contact_input").css("display")=="block"){
	    var vMobile = $("#phone").val();
	    if (!vMobile.match(/^((1[3,5,8][0-9])|(14[5,7])|(17[0,3,6,7,8]))\d{8}$/)) {
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
			id_card = $("#contact").find('option:selected').data('idcard');
		if(contact == ''){
			rstr += "取票人不能为空!";
		}
	}
	if(PRO_CONF.black == '1'){
		var guide_black = $("#guide_black").val();
	    if (!guide_black.match(/^((1[3,5,8][0-9])|(14[5,7])|(17[0,3,6,7,8]))\d{8}$/)) {
	      layer.msg("导游手机号码不正确!");
	      return false;
	    }else{
	    	if(black(guide_black)){
	    		layer.msg("抱歉,该导游已被系统列入黑名单，请联系管理员!");
	      		return false;
	    	}
	    }
	}
	/*判断身份号码是否正确*/
	if(id_card){
		if(checkIdCard(id_card) == false){
			rstr += "请您正确输入身份证号码，或者不输入!";
		}
	}
	/*客源地判断*/
	var tour = $("#tourists").val();
	if(!tour){
		rstr += "请选择客源地!";
	}
	if(!remark){
		remark = "空..";
	}
    if(rstr !=""){
      layer.msg(rstr);
    }else{
		var 
        pay = " ",
        toJSONString = " ",
        length =  $("#kselect tr").length - 2,
		num = 0,
		nums= 0;
        if(length < 0){
		  	layer.msg("请选择要售出的票型!");
	        return false;
        }
        $("#kselect tr").each(function(i){
	        if(i != 0 ){
	          	var fg  = i <= length ? ',':' ';/*判断是否增加分割符*/
	          	var ids = this.id.split("_");
			  	nums = parseInt(nums)+parseInt($("#qnum_"+ids[1]).val());
	          	toJSONString = toJSONString + '{"areaId":'+$("#areaid"+ids[1]).val()+',"priceid":' +ids[1]+',"price":'+parseFloat($("#price_"+ids[1]).html())+',"num":"'+$("#qnum_"+ids[1]).val()+'"}'+fg;
	        }
        });
        if(nums <= PRO_CONF.channel_order){
		  /*获取支付相关数据*/
		  var guide = $("#guideid").attr("value");/*渠道商登录时为业务员ID默认为当前登录用户导游登录时为导游id,*/
      		  itemid = $("#channel_id").attr("value");/*渠道商登录时为渠道商id导游登录时默认为散客导游的id*/
		      checkinT = 1,
			  pre	= 1,
			  param = "";/*付款但不排座*/
		  crm = '{"guide":'+guide+',"qditem":'+itemid+',"phone":'+vMobile+',"contact":"'+vmima+'"}';
		  param = '{"pre":'+pre+',"tour":'+tour+',"remark":"'+remark+'","id_card":"'+id_card+'","guide_black":"'+guide_black+'","settlement":"'+USER_INFO.group.settlement+'"}';
		  var postData = 'info={"subtotal":'+parseFloat($("#subtoal").html())+',"plan_id":'+plan+',"checkin":'+checkinT+',"data":['+ toJSONString + '],"crm":['+crm+'],"param":['+param+']}';
		
		  /*提交到服务器*/
		  $.ajax({
			type:'POST',
			url:'index.php?g=Home&m=Order&a=channelPost',
			data:postData,
			dataType:'json',
			success:function(data){
			  if(data.statusCode == "200"){
				$("#myModal").modal('show');
				//获取当前商户的可用余额
				money();
				var total = $("#subtoal",window.parent.document).html();
				$("#totalcash").text(total);
				$("#tomoney").attr('value',total);
				$("#sn").attr('value',data.sn);
			  }else{
				$("#error").text("订单创建失败!");
				$("#myModal2").modal('show');  //出票失败的提示
			  }
			}
		  });
        }else{
        	layer.msg("超出单笔订单门票总数限额...");
	        return false;
        }
	}	  
 });
 //政企预定  不付款  窗口手动排座
 $("#gov").bind("click",function(){
	var rstr = "",
		plan = $('#planID').val(),
		remark = $("#remark").val();
    if($(".contact_input").css("display")=="block"){
	    var vMobile = $("#phone").val();
	    if (!vMobile.match(/^((1[3,5,8][0-9])|(14[5,7])|(17[0,3,6,7,8]))\d{8}$/)) {
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
		var id_card = $("#contact").find('option:selected').data('idcard');
		if(contact == ''){
			rstr += "取票人不能为空!";
		}
	}
	if(PRO_CONF.black == '1'){
		var guide_black = $("#guide_black").val();
	    if (!guide_black.match(/^((1[3,5,8][0-9])|(14[5,7])|(17[0,3,6,7,8]))\d{8}$/)) {
	      layer.msg("导游手机号码不正确!");
	      return false;
	    }else{
	    	if(black(guide_black)){
	    		layer.msg("抱歉,该导游已被系统列入黑名单，请联系管理员!");
	      		return false;
	    	}
	    }
	}
	/*客源地判断*/
	var tour = $("#tourists").val();
	if(!tour){rstr += "请选择客源地!";}
	if(!remark){remark = "空";}
    if(rstr != ""){
      layer.msg(rstr);
    }else{
		var 
        pay = " ",
        toJSONString = " ",
        length =  $("#kselect tr").length - 2,
		num = 0,
		nums= 0;
        if(length < 0){
		  layer.msg("请选择要售出的票型!");

          return false;
        }
      $("#kselect tr").each(function(i){
        if(i != 0 ){
          var fg  = i <= length ? ',':' ';/*判断是否增加分割符*/
          var ids = this.id.split("_");
		  nums = parseInt(nums)+parseInt($("#qnum_"+ids[1]).val());
          toJSONString = toJSONString + '{"areaId":'+$("#areaid"+ids[1]).val()+',"priceid":' +ids[1]+',"price":'+parseFloat($("#price_"+ids[1]).html())+',"num":"'+$("#qnum_"+ids[1]).val()+'"}'+fg;
        }
      });
      if(nums <= PRO_CONF.channel_order){
      	  //判断配额
      	  $.get("index.php?g=Home&m=Product&a=quota&num="+nums+"&plan="+plan, function(data){
      		if(data != 0){
      		  var result = $.parseJSON(data);
      		  if(result.statusCode == "0"){
      			layer.msg("配额不足，请联系渠道负责人!");
      		  }else{
      			/*获取支付相关数据*/
				var guide = $("#guideid").attr("value");/*渠道商登录时为业务员ID默认为当前登录用户导游登录时为导游id,*/
      			    itemid = $("#channel_id").attr("value");/*渠道商登录时为渠道商id导游登录时默认为散客导游的id*/
					checkinT = 1,
					pre	= 1,
					gov	= 1,
					param = "";/*付款但不排座*/
					crm = '{"guide":'+guide+',"qditem":'+itemid+',"phone":'+vMobile+',"contact":"'+vmima+'"}';
					param = '{"pre":'+pre+',"gov":'+gov+',"tour":'+tour+',"remark":"'+remark+'","guide_black":"'+guide_black+'","settlement":"'+USER_INFO.group.settlement+'"}';
				var postData = 'info={"subtotal":'+parseFloat($("#subtoal").html())+',"plan_id":'+plan+',"checkin":'+checkinT+',"data":['+ toJSONString + '],"crm":['+crm+'],"param":['+param+']}';
				/*提交到服务器*/
				$.ajax({
					type:'POST',
					url:'index.php?g=Home&m=Order&a=channelPost',
					data:postData,
					dataType:'json',
					success:function(data){
						if(data.statusCode == "200"){
						  $("#myModal").modal('show');
				  	  	  var total = $("#subtoal",window.parent.document).html();
				  	  	  $("#totalcash").text(total);
				  	  	  $("#tomoney").attr('value',total);
				  	  	  $("#sn").attr('value',data.sn);
						}else{
						  $("#error").text("订单创建失败!");
						  $("#myModal2").modal('show');  //出票失败的提示
						}
					}
				});
      		  }
      		}
      	  }); 
      }else{
      	layer.msg("超出单笔订单门票总数限额...");
      }
	}	  
});
$("#balancePay").bind("click",function(){
	  //判断余额是否足够
	  var integral = parseFloat($("#money").text());
	  var money = parseFloat($("#tomoney").attr('value'));
	  var sn = $("#sn").attr('value');
	  var status = parseFloat(integral - money);
	  var pay_type =  '2',
		  seat_type = '1';

	  if(status < 0){
		 $("#error").text("余额不足，支付失败!");
		 $("#myModal2").modal('show');
	  }else{
		if(sn){
			$("#myModal").modal('hide');  //关闭支付模态框
			//$("#myModal3").modal('show'); //支付状态模态框
			//调用支付方法
			var postData = 'info={"tomoney":'+money+',"sn":'+sn+',"pay_type":'+pay_type+',"seat_type":'+seat_type+'}'
			 $.ajax({
				type:'POST',
				url:'index.php?g=Home&m=Order&a=pay',
				data:postData,
				dataType:'json',
				success:function(data){
				  if(data.statusCode == "200"){
					 	//成功提示
					  var content = "订单<a href='index.php?g=Home&m=Order&a=orderinfo&type=2&sn="+data.sn+"' target='_blank'>"+data.sn+"</a>创建成功!</a>";
					  $("#succ_info").html(content);
					  $("#success").modal('show');
				  }else{
					$("#error").text("订单支付失败!");
					$("#myModal2").modal('show');  //出票失败的提示
				  }
				}
			  });
		}else{
			$("#error").text("参数错误!");
			$("#myModal2").modal('show'); 
		}
	  } 
});
/*政府订单支付*/
$("#govPay").bind("click",function(){
	var sn = $("#sn").attr('value'),
		pay_type =  $("input[name='pay_type']:checked").val(),
		seat_type = $("input[name='seat_type']:checked").val(),
		money = parseFloat($("#tomoney").attr('value'));
	if(sn){
		$("#myModal").modal('hide');  //关闭支付模态框
		var postData = 'info={"tomoney":'+money+',"sn":'+sn+',"pay_type":'+pay_type+',"seat_type":'+seat_type+'}'
		$.ajax({
			type:'POST',
			url:'index.php?g=Home&m=Order&a=pay',
			data:postData,
			dataType:'json',
			success:function(data){
			  if(data.statusCode == "200"){
				  //成功提示
				  var content = "订单<a href='index.php?g=Home&m=Order&a=orderinfo&type=2&sn="+data.sn+"' target='_blank'>"+data.sn+"</a>创建成功!</a>";
				  $("#succ_info").html(content);
				  $("#success").modal('show');
			  }else{
				$("#error").text("订单提交失败!");
				$("#myModal2").modal('show');  //出票失败的提示
			  }
			}
		  });
	}else{
		$("#error").text("参数错误!");
		$("#myModal2").modal('show');
	}
});
//支付成功
$("#paysuccess").bind("click",function(){
//刷新当前页面
window.location.href="index.php?g=Home&m=Product&a=paysuccess";
});
//查找导游
$("#findguide").bind("click",function(){
	$("#myModal4").modal('show');
});
//指纹录入
$("#fingerprint").bind("click",function(){
	fingerprint();
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
	      var name  = item.nickname;
	      var phone = item.phone;
	      content = "<tr><td>"+name+"</td><td>"+phone+"</td><td><a href='javascript:void(0)' onclick=guideback('"+id+"','"+name+"'); class='btn btn-default btn-xs'>选择</a></td></tr>";
	    });
	  }else{         
	    content = "<hr><ul><li><span>暂无相关导游，请重新查询！</span></li><ul>";
	  }
	  $("#chooseguide").html(content);
	});  
});
})
/*导游查找结果带回*/
function guideback(id,name){
  $("#myModal4").modal('hide');  //隐藏导游查找弹出框
  $("#guidename").val(name);
  $("#guideid").val(id);
}
//获取当前商户可用余额
function money(){
	$.get("index.php?g=Home&m=User&a=money", function(data){
    if(data != 0){
      var result = $.parseJSON(data);
      $("#money").html(result.money);
    }
  });
}
/*刷新页面，恢复初始值*/
function newPage(){
	$('.qk').val("");
	window.location.reload();
}
/*黑名单校验*/
function black(phone){
	var retu = true;
	$.ajax({
	    type:'GET',
	    url:'index.php?g=Home&m=Product&a=public_black',
	    data:'p='+phone,
	    dataType:'json',
	    async : false,
	    success:function(data){
	    	if(data.statusCode == '200'){retu = false;}
	    }
	});
	return retu;
}
/*
 * 身份证15位编码规则：dddddd yymmdd xx p
 * dddddd：6位地区编码
 * yymmdd: 出生年(两位年)月日，如：910215
 * xx: 顺序编码，系统产生，无法确定
 * p: 性别，奇数为男，偶数为女
 * 
 * 身份证18位编码规则：dddddd yyyymmdd xxx y
 * dddddd：6位地区编码
 * yyyymmdd: 出生年(四位年)月日，如：19910215
 * xxx：顺序编码，系统产生，无法确定，奇数为男，偶数为女
 * y: 校验码，该位数值可通过前17位计算获得
 * 
 * 前17位号码加权因子为 Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ]
 * 验证位 Y = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ]
 * 如果验证码恰好是10，为了保证身份证是十八位，那么第十八位将用X来代替
 * 校验位计算公式：Y_P = mod( ∑(Ai×Wi),11 )
 * i为身份证号码1...17 位; Y_P为校验码Y所在校验码数组位置
 */
function checkIdCard(idCard){
 //15位和18位身份证号码的正则表达式
 var regIdCard=/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;

 //如果通过该验证，说明身份证格式正确，但准确性还需计算
 if(regIdCard.test(idCard)){
  if(idCard.length==18){
   var idCardWi=new Array( 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ); //将前17位加权因子保存在数组里
   var idCardY=new Array( 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ); //这是除以11后，可能产生的11位余数、验证码，也保存成数组
   var idCardWiSum=0; //用来保存前17位各自乖以加权因子后的总和
   for(var i=0;i<17;i++){
    idCardWiSum+=idCard.substring(i,i+1)*idCardWi[i];
   }

   var idCardMod=idCardWiSum%11;//计算出校验码所在数组的位置
   var idCardLast=idCard.substring(17);//得到最后一位身份证号码

   //如果等于2，则说明校验码是10，身份证号码最后一位应该是X
   if(idCardMod==2){
    if(idCardLast=="X"||idCardLast=="x"){
    	return true;
     //alert("恭喜通过验证啦！");
    }else{
    	return false;
     //alert("身份证号码错误！");
    }
   }else{
    //用计算出的验证码与最后一位身份证号码匹配，如果一致，说明通过，否则是无效的身份证号码
    if(idCardLast==idCardY[idCardMod]){
    	return true;
    // alert("恭喜通过验证啦！");
    }else{
    	return false;
     //alert("身份证号码错误！");
    }
   }
  } 
 }else{
 	return false;
  //alert("身份证格式不正确!");
 }
}