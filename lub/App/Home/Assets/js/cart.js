$(function(){
  $("body").on("change",".qnum", function () { 
    var ids = Array();
    //var id  = $(this).attr("name");
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
    obj.find(".qnum").val(qnum);
    totalprice(ids[1]);
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
	    vMobile = "";
    if($("#contact_input").css("display") == "block"){
	    vMobile = $("#phone").val();
	    if (!vMobile.match(/^(1(([35][0-9])|(47)|[8][01236789]))\d{8}$/)) {
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
		//alert(vMobile);
		if(contact == ''){
			rstr += "取票人不能为空!";
		}
	}
	/*客源地判断*/
	var tour = $("#tourists").val();
	if(!tour){
		rstr += "请选择客源地!";
	}
    if(rstr !=""){
      alert(rstr);
    }else{
      //获取已选择的票型并组合数据
      var 
        pay = " ",
        toJSONString = " ",
        length =  $("#kselect tr").length - 2,
		num = 0,
		nums= 0;
       if(length < 0){
		 alert("请选择要售出的票型!");
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
	  //判断配额
	  $.get("index.php?g=Home&m=Product&a=quota&num="+nums, function(data){
		if(data != 0){
		  var result = $.parseJSON(data);
		  if(result.status == "0"){
			$("#error").text("配额不足，请联系渠道负责人!");
			$("#myModal2").modal('show');  //出票失败的提示
		  }else{
			/*获取支付相关数据*/
			var guide = $("#channel_id").attr("value");/*这里在@大红袍版本中存放的是渠道商ID*/
			var itemid = $("#itemid").attr("value");
			var checkinT = 1;
			crm = '{"guide":'+guide+',"qditem":'+itemid+',"phone":'+vMobile+',"contact":"'+vmima+'"}';
			param = '{"tour":'+tour+'}';
			var postData = 'info={"subtotal":'+parseFloat($("#subtoal").html())+',"checkin":'+checkinT+',"data":['+ toJSONString + '],"crm":['+crm+'],"param":['+param+']}';
		  
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
    }
  });
  //立即预定  付款  但不排座
  $("#pre").bind("click",function(){
	var rstr = "";
    if($("#contact_input").css("display")=="block"){
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
	/*客源地判断*/
	var tour = $("#tourists").val();
	if(!tour){
		rstr += "请选择客源地!";
	}
    if(rstr !=""){
      alert(rstr);
    }else{
		var 
        pay = " ",
        toJSONString = " ",
        length =  $("#kselect tr").length - 2,
		num = 0,
		nums= 0;
        if(length < 0){
		  alert("请选择要售出的票型!");
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
	  /*获取支付相关数据*/
	  var guide = $("#channel_id").attr("value"),/*这里在@大红袍版本中存放的是渠道商ID*/
	      itemid = $("#itemid").attr("value"),
	      checkinT = 1,
		  pre	= 1,
		  param = "";/*付款但不排座*/
	  
	  crm = '{"guide":'+guide+',"qditem":'+itemid+',"phone":'+vMobile+',"contact":"'+vmima+'"}';
	  param = '{"pre":'+pre+',"tour":'+tour+'}';
	  var postData = 'info={"subtotal":'+parseFloat($("#subtoal").html())+',"checkin":'+checkinT+',"data":['+ toJSONString + '],"crm":['+crm+'],"param":['+param+']}';
	
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
			//获取当前商户的可用余额
			money();
			var total = $("#subtoal",window.parent.document).html();
			$("#totalcash").text(total);
			$("#tomoney").attr('value',total);
			$("#sn").attr('value',data.sn);
		  }else{
			//alert('出票失败!');
			$("#error").text("订单创建失败!");
			$("#myModal2").modal('show');  //出票失败的提示
		  }
		}
	  });
	}	  
 });
 //政企预定  不付款  窗口手动排座
 $("#gov").bind("click",function(){
	var rstr = "";
    if($("#contact_input").css("display")=="block"){
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
	/*客源地判断*/
	var tour = $("#tourists").val();
	if(!tour){
		rstr += "请选择客源地!";
	} 
    if(rstr !=""){
      alert(rstr);
    }else{
		var 
        pay = " ",
        toJSONString = " ",
        length =  $("#kselect tr").length - 2,
		num = 0,
		nums= 0;
        if(length < 0){
		  alert("请选择要售出的票型!");
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
	  //判断配额
	  $.get("index.php?g=Home&m=Product&a=quota&num="+nums, function(data){
		if(data != 0){
		  var result = $.parseJSON(data);
		  if(result.status == "0"){
			$("#error").text("配额不足，请联系渠道负责人!");
			$("#myModal2").modal('show');  //出票失败的提示
		  }else{
			/*获取支付相关数据*/
			var guide = $("#channel_id").attr("value"),/*这里在@大红袍版本中存放的是渠道商ID*/
				itemid = $("#itemid").attr("value"),
				checkinT = 1,
				pre	= 1,
				gov	= 1,
				param = "";/*付款但不排座*/
			
			crm = '{"guide":'+guide+',"qditem":'+itemid+',"phone":'+vMobile+',"contact":"'+vmima+'"}';
			param = '{"pre":'+pre+',"gov":'+gov+',"tour":'+tour+'}';
			var postData = 'info={"subtotal":'+parseFloat($("#subtoal").html())+',"checkin":'+checkinT+',"data":['+ toJSONString + '],"crm":['+crm+'],"param":['+param+']}';
		  
			/*提交到服务器*/
			$.ajax({
			  type:'POST',
			  url:'index.php?g=Home&m=Order&a=channelPost',
			  data:postData,
			  dataType:'json',
			  success:function(data){
				//alert(data.info);
				if(data.statusCode == "200"){
				  var content = "订单<a href='index.php?g=Home&m=Order&a=orderinfo&type=2&sn="+data.sn+"' target='_blank'>"+data.sn+"</a>创建成功!等待系统处理!</a>";
				  $("#succ_info").html(content);
				  $("#success").modal('show');
				}else{
				  //alert('出票失败!');
				  $("#error").text("订单创建失败!");
				  $("#myModal2").modal('show');  //出票失败的提示
				}
			  }
			});
		  }
		}
	  }); 
	}	  
 });
  $("#balancePay").bind("click",function(){
	  //判断余额是否足够
	  var integral = parseFloat($("#money").text());
	  var money = parseFloat($("#tomoney").attr('value'));
	  var sn = $("#sn").attr('value');
	  var status = parseFloat(integral - money);
	  if(status < 0){
		 $("#error").text("余额不足，支付失败!");
		 $("#myModal2").modal('show');
	  }else{
		if(sn){
			$("#myModal").modal('hide');  //关闭支付模态框
			//$("#myModal3").modal('show'); //支付状态模态框
			//调用支付方法
			var postData = 'info={"tomoney":'+money+',"sn":'+sn+'}'
			 $.ajax({
				type:'POST',
				url:'index.php?g=Home&m=Order&a=pay',
				data:postData,
				dataType:'json',
				success:function(data){
				 // alert(data.statusCode);
				  if(data.statusCode == "200"){
					 	//成功提示
					  var content = "订单<a href='index.php?g=Home&m=Order&a=orderinfo&type=2&sn="+data.sn+"' target='_blank'>"+data.sn+"</a>创建成功!</a>";
					  $("#succ_info").html(content);
					  $("#success").modal('show');
				  }else{
					//alert('出票失败!');
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
//获取当前商户可用余额
function money(){
	$.get("index.php?g=Home&m=User&a=money", function(data){
    if(data != 0){
      var result = $.parseJSON(data);
      $("#money").html(result.money);
    }
  });
}
//检测配额
function quota(){
	//获取当前总数
	var num = 0;
	$("#kselect tr").each(function(i){
        if(i != 0 ){
          var fg  = i <= length ? ',':' ';/*判断是否增加分割符*/
          var ids = this.id.split("_");
		  num = parseInt(num)+parseInt($("#qnum_"+ids[1]).val());
        }
      });
	//$.post("index.php?g=Home&m=Product&a=quota&num=".num, function(data){
	$.get("index.php?g=Home&m=Product&a=quota&num="+num, function(data){
    if(data != 0){
      var result = $.parseJSON(data);
	  if(result.status == "300"){
		$("#quota").attr('value',1);
	  }else{
		$("#quota").val();
	  }
    }
  });
}

/*刷新页面，恢复初始值*/
function newPage(){
	$('.qk').val("");
	window.location.reload();
}	