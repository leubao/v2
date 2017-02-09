<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>出票方式</title>
</head>
<body>
<script src="{$config_siteurl}static/js/LodopFuncs.js?=<?php echo  rand(100,999);?>" type="text/javascript"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0> </object>
<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</objec>
<div class="page unitBox">
<div class="pageContent">
  <div class="pageFormContent">
  <button type="button" style="width:200px; height:150px" onclick="printTicket({$data.sn},{$data.plan_id})">打印门票</button>
  </div>
</div>
</div>
<script>
var LODOP; //声明为全局变量  
function printTicket(sn,planid){
	$.ajax({
		type:'get',
		dataType : 'json',
		url:'index.php?g=Item&m=Order&a=printTicket&sn='+sn+'&plan_id='+planid+'&user={$data.user}',
		success:function(data){
			var selSeat = eval(data.info);/*返回的座位信息*/
			if(data.status == '1'){
				$.each(selSeat,function(){			
					/*打印设置部分*/
					CreateFullBill(this);
					/*设置连续打印*/
					LODOP.SET_PRINT_PAGESIZE(2,800,180,"USER");
					LODOP.SET_PRINT_MODE("POS_BASEON_PAPER",true);
					LODOP. PRINT();	
					/*关闭当前弹窗*/
					$(this).dialog('close','print');
				});
			}else{
				$(this).alertmsg('error',data.message);
				$(this).dialog('close','print');
			}
		}
	});

	}
/*打印页面控制*/
function CreateFullBill(data) {
		LODOP=getLodop();

		LODOP.ADD_PRINT_TEXT(20,500,106,30,"入场券");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",14);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(49,500,106,25,"有效期");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",14);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(80,500,106,32,"体验项目");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",14);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);



		LODOP.ADD_PRINT_TEXT(20,606,190,32,data.price+"元");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",14);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);

		LODOP.ADD_PRINT_TEXT(48,606,190,32,data.plantime);
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",14);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);



		LODOP.ADD_PRINT_TEXT(265,692,100,20,data.sns);
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",14);
		LODOP.ADD_PRINT_TEXT(265,579,100,20,data.user);
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",14);



		/*打印备注*/
		var type = data.remark_type;
		switch(type){
			case '99':
				/*景区联合售票*/
				var width_s = 606,
					height = 80;
				$.each(data.remark,function(item,ticket){

					LODOP.ADD_PRINT_TEXT(height,606,190,32,ticket.priceName +  ticket.price+"元");
					LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
					LODOP.SET_PRINT_STYLEA(0,"FontSize",14);
					LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
					LODOP.SET_PRINT_STYLEA(0,"Bold",1);
					
					height = height+30;
				});
				break;
				
		}
}
</script>
</body>
</html>