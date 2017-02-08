<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>出票方式</title>
</head>
<body>
<script src="{$config_siteurl}static/js/LodopFuncs.js?=20160821" type="text/javascript"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0> </object>
<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</objec>
<div class="page unitBox">
<style type="text/css">
.print_type{height: 25px;line-height: 1.5;}
</style>
<div class="pageContent">
  <div class="pageFormContent">
  <div class="print_type">
  <input type="radio" name="type" id="print_type" value="1" <if condition="$procof['print_type'] eq '1'">checked</if>> 一人一票  
  <input type="radio" name="type" id="print_type" value="2" <if condition="$procof['print_type'] eq '2'">checked</if>> 一单一票
  </div>
  <button type="button" style="width:200px; height:130px" onclick="printTicket({$data.sn},{$data.plan_id})">打印门票</button>
  </div>
</div>
</div>
<script>
var LODOP; //声明为全局变量
function printTicket(sn,planid){
	var type = $('#print_type:checked').val();
	$.ajax({
		type:'get',
		dataType : 'json',
		url:'index.php?g=Item&m=Order&a=printTicket&sn='+sn+'&plan_id='+planid+'&user={$data.user}&type='+type,
		success:function(data){
			var selSeat = eval(data.info);/*返回的座位信息*/
			if(data.status == '1'){
				$.each(selSeat,function(){			
					/*打印设置部分*/
					CreateFullBill(this,type);
					/*设置连续打印*/
					LODOP.SET_PRINT_PAGESIZE(2,800,2350,"USER");
					LODOP.SET_PRINT_MODE("POS_BASEON_PAPER",true);
					//LODOP.PREVIEW();
					LODOP.PRINT();	
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
function CreateFullBill(data,type) {
		LODOP=getLodop();
		LODOP.ADD_PRINT_TEXT(40,645,152,25,"演出时间/TIME:");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(65,645,136,25,data.plantime);
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);


		LODOP.ADD_PRINT_TEXT(105,645,134,25,"票价/PRICE：");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",13);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(120,645,180,25,data.price+"元");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);

		LODOP.ADD_PRINT_TEXT(140,645,134,25,"区域/AREA:");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",13);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(160,645,100,25,data.area);
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);

		LODOP.ADD_PRINT_TEXT(190,645,125,25,"座位/SEAT:");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",13);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(210,645,122,25,data.seat);
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);


		/**/
		LODOP.ADD_PRINT_TEXT(40,780,152,25,"演出时间/TIME:");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(65,780,136,25,data.plantime);
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);


		LODOP.ADD_PRINT_TEXT(105,780,134,25,"票价/PRICE：");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",13);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(120,780,180,25,data.price+"元");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);

		LODOP.ADD_PRINT_TEXT(140,780,134,25,"区域/AREA:");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",13);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(160,780,100,25,data.area);
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);

		LODOP.ADD_PRINT_TEXT(190,780,125,25,"座位/SEAT:");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",13);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(210,780,122,25,data.seat);
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
}
</script>
</body>
</html>