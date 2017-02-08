<!DOCTYPE html>
<html>
  <head>
    <title>{$product.title}</title>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="description" content="">

	<link rel="stylesheet" href="//cdn.bootcss.com/weui/0.4.3/style/weui.min.css">
	<link rel="stylesheet" href="//cdn.bootcss.com/jquery-weui/0.8.0/css/jquery-weui.min.css">
	<style type="text/css">
	/* ==========================================================================
   Component: Table
 ============================================================================ */
table {
  max-width: 100%;
  background-color: transparent;
  empty-cells: show;
}
table code {
  white-space: normal;
}
th {
  text-align: left;
}
.am-table {
  width: 100%;
  margin-bottom: 1.6rem;
  border-spacing: 0;
  border-collapse: separate;
}
.am-table > thead > tr > th,
.am-table > tbody > tr > th,
.am-table > tfoot > tr > th,
.am-table > thead > tr > td,
.am-table > tbody > tr > td,
.am-table > tfoot > tr > td {
  padding: 0.1rem;
  line-height: 1.6;
  vertical-align: top;
  border-top: 1px solid #ddd;
}
.am-table > thead > tr > th {
  vertical-align: bottom;
  border-bottom: 1px solid #ddd;
}
.am-table > caption + thead > tr:first-child > th,
.am-table > colgroup + thead > tr:first-child > th,
.am-table > thead:first-child > tr:first-child > th,
.am-table > caption + thead > tr:first-child > td,
.am-table > colgroup + thead > tr:first-child > td,
.am-table > thead:first-child > tr:first-child > td {
  border-top: 0;
}
.am-table > tbody + tbody tr:first-child td {
  border-top: 2px solid #ddd;
}
/* Bordered version */
.am-table-bordered {
  border: 1px solid #ddd;
  border-left: none;
}
.am-table-bordered > thead > tr > th,
.am-table-bordered > tbody > tr > th,
.am-table-bordered > tfoot > tr > th,
.am-table-bordered > thead > tr > td,
.am-table-bordered > tbody > tr > td,
.am-table-bordered > tfoot > tr > td {
  border-left: 1px solid #ddd;
  /*&:first-child {
          border-left: none;
        }*/
}
.am-table-bordered > tbody > tr:first-child > th,
.am-table-bordered > tbody > tr:first-child > td {
  border-top: none;
}
.am-table-bordered > thead + tbody > tr:first-child > th,
.am-table-bordered > thead + tbody > tr:first-child > td {
  border-top: 1px solid #ddd;
}
/* Border-radius version */
.am-table-radius {
  border: 1px solid #ddd;
  border-radius: 2px;
}
.am-table-radius > thead > tr:first-child > th:first-child,
.am-table-radius > thead > tr:first-child > td:first-child {
  border-top-left-radius: 2px;
  border-left: none;
}
.am-table-radius > thead > tr:first-child > th:last-child,
.am-table-radius > thead > tr:first-child > td:last-child {
  border-top-right-radius: 2px;
  border-right: none;
}
.am-table-radius > tbody > tr > th:first-child,
.am-table-radius > tbody > tr > td:first-child {
  border-left: none;
}
.am-table-radius > tbody > tr > th:last-child,
.am-table-radius > tbody > tr > td:last-child {
  border-right: none;
}
.am-table-radius > tbody > tr:last-child > th,
.am-table-radius > tbody > tr:last-child > td {
  border-bottom: none;
}
.am-table-radius > tbody > tr:last-child > th:first-child,
.am-table-radius > tbody > tr:last-child > td:first-child {
  border-bottom-left-radius: 2px;
}
.am-table-radius > tbody > tr:last-child > th:last-child,
.am-table-radius > tbody > tr:last-child > td:last-child {
  border-bottom-right-radius: 2px;
}
/* Zebra-striping */
.am-table-striped > tbody > tr:nth-child(odd) > td,
.am-table-striped > tbody > tr:nth-child(odd) > th {
  background-color: #f9f9f9;
}
/* Hover effect */
.am-table-hover > tbody > tr:hover > td,
.am-table-hover > tbody > tr:hover > th {
  background-color: #e9e9e9;
}
.am-table-compact > thead > tr > th,
.am-table-compact > tbody > tr > th,
.am-table-compact > tfoot > tr > th,
.am-table-compact > thead > tr > td,
.am-table-compact > tbody > tr > td,
.am-table-compact > tfoot > tr > td {
  padding: 0.4rem;
}
.am-table-centered > thead > tr > th,
.am-table-centered > tbody > tr > th,
.am-table-centered > tfoot > tr > th,
.am-table-centered > thead > tr > td,
.am-table-centered > tbody > tr > td,
.am-table-centered > tfoot > tr > td {
  text-align: center;
}
.am-table > thead > tr > td.am-active,
.am-table > tbody > tr > td.am-active,
.am-table > tfoot > tr > td.am-active,
.am-table > thead > tr > th.am-active,
.am-table > tbody > tr > th.am-active,
.am-table > tfoot > tr > th.am-active,
.am-table > thead > tr.am-active > td,
.am-table > tbody > tr.am-active > td,
.am-table > tfoot > tr.am-active > td,
.am-table > thead > tr.am-active > th,
.am-table > tbody > tr.am-active > th,
.am-table > tfoot > tr.am-active > th {
  background-color: #ffd;
}
.am-table > thead > tr > td.am-disabled,
.am-table > tbody > tr > td.am-disabled,
.am-table > tfoot > tr > td.am-disabled,
.am-table > thead > tr > th.am-disabled,
.am-table > tbody > tr > th.am-disabled,
.am-table > tfoot > tr > th.am-disabled,
.am-table > thead > tr.am-disabled > td,
.am-table > tbody > tr.am-disabled > td,
.am-table > tfoot > tr.am-disabled > td,
.am-table > thead > tr.am-disabled > th,
.am-table > tbody > tr.am-disabled > th,
.am-table > tfoot > tr.am-disabled > th {
  color: #999999;
}
.am-table > thead > tr > td.am-primary,
.am-table > tbody > tr > td.am-primary,
.am-table > tfoot > tr > td.am-primary,
.am-table > thead > tr > th.am-primary,
.am-table > tbody > tr > th.am-primary,
.am-table > tfoot > tr > th.am-primary,
.am-table > thead > tr.am-primary > td,
.am-table > tbody > tr.am-primary > td,
.am-table > tfoot > tr.am-primary > td,
.am-table > thead > tr.am-primary > th,
.am-table > tbody > tr.am-primary > th,
.am-table > tfoot > tr.am-primary > th {
  color: #0b76ac;
  background-color: rgba(14, 144, 210, 0.115);
}
.am-table > thead > tr > td.am-success,
.am-table > tbody > tr > td.am-success,
.am-table > tfoot > tr > td.am-success,
.am-table > thead > tr > th.am-success,
.am-table > tbody > tr > th.am-success,
.am-table > tfoot > tr > th.am-success,
.am-table > thead > tr.am-success > td,
.am-table > tbody > tr.am-success > td,
.am-table > tfoot > tr.am-success > td,
.am-table > thead > tr.am-success > th,
.am-table > tbody > tr.am-success > th,
.am-table > tfoot > tr.am-success > th {
  color: #5eb95e;
  background-color: rgba(94, 185, 94, 0.115);
}
.am-table > thead > tr > td.am-warning,
.am-table > tbody > tr > td.am-warning,
.am-table > tfoot > tr > td.am-warning,
.am-table > thead > tr > th.am-warning,
.am-table > tbody > tr > th.am-warning,
.am-table > tfoot > tr > th.am-warning,
.am-table > thead > tr.am-warning > td,
.am-table > tbody > tr.am-warning > td,
.am-table > tfoot > tr.am-warning > td,
.am-table > thead > tr.am-warning > th,
.am-table > tbody > tr.am-warning > th,
.am-table > tfoot > tr.am-warning > th {
  color: #F37B1D;
  background-color: rgba(243, 123, 29, 0.115);
}
.am-table > thead > tr > td.am-danger,
.am-table > tbody > tr > td.am-danger,
.am-table > tfoot > tr > td.am-danger,
.am-table > thead > tr > th.am-danger,
.am-table > tbody > tr > th.am-danger,
.am-table > tfoot > tr > th.am-danger,
.am-table > thead > tr.am-danger > td,
.am-table > tbody > tr.am-danger > td,
.am-table > tfoot > tr.am-danger > td,
.am-table > thead > tr.am-danger > th,
.am-table > tbody > tr.am-danger > th,
.am-table > tfoot > tr.am-danger > th {
  color: #dd514c;
  background-color: rgba(221, 81, 76, 0.115);
}
		.am-daymoney{
		    width: 100%;
		    display: block;    
		}
		.am-daymoney table{
		    font-size: 12px;
		    _display: inline;
		    border-spacing: 0 7px;
		    border-collapse: collapse;
		    width: 100%;    
		}
		.am-daymoney caption{
		    font-size: 1.9rem;
		    color: #fff;
		    background: #3bb4f2;     
		}
		.am-daymoney tbody td{
		    cursor:pointer
		}
		.am-daymoney tbody td:hover{
		    background: #eee !important;
		}
		.am-daymoney thead tr{   
		    height: 25px;  
		    color: #0c80ba;
		}
		.am-daymoney tbody tr{
		    height: 55px;  
		}
		.am-daymoney-prev{
		    cursor:pointer
		}
		.am-daymoney-next{
		    cursor:pointer
		}
		.am-daymoney-month-head{
		    color: #000;
		}
		.daymoney-month-body{
		    color: #F60;
		}
		.am-primary .am-daymoney-month-head{
		    color:#0c80ba; 
		}
	</style>
  </head>

  <body ontouchstart>

	<div class="am-daymoney" id="calendar" style=" width: 100%;display: inline-block; height: auto;left:50%;">
    </div> 

    <div>
        <hr data-am-widget="divider" style="" class="am-divider am-divider-default" />
        <button id="prev" type="button" class="am-btn am-btn-primary">上月</button>
        <button id="next" type="button" class="am-btn am-btn-primary">下月</button>
        <button id="set" type="button" class="am-btn am-btn-primary">设置价格</button>
    </div>
    <div class="am-modal am-modal-alert" tabindex="-1" id="my-alert">
        <div class="am-modal-dialog">
            <div class="am-modal-hd">Amaze UI</div>
            <div class="am-modal-bd" id="alert">
            </div>
            <div class="am-modal-footer">
                <span class="am-modal-btn">确定</span>
            </div>
        </div>
    </div>
    <!--end -->
	<div id="inline-calendar"></div>
  	<script src="//cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
  	<script src="//cdn.bootcss.com/jquery-weui/0.8.0/js/jquery-weui.min.js"></script>
  	<script type="text/javascript" src="static/js/daymoney.js"></script>
  	<script type="text/javascript">

  	$(function() {
	    var daydata = '[{"day":"2016-04-10|228"},{"day":"2016-04-11|228"},{"day":"2016-04-12|228"},{"day":"2016-04-13|228"},{"day":"2016-04-14|258"},{"day":"2016-04-15|228"},{"day":"2016-04-16|228"},{"day":"2016-04-17|308"},{"day":"2016-04-19|228"},{"day":"2016-04-20|228"},{"day":"2016-04-22|228"},{"day":"2016-04-23|228"},{"day":"2016-04-24|228"},{"day":"2016-04-25|228"},{"day":"2016-04-26|228"},{"day":"2016-04-27|228"},{"day":"2016-04-28|558"},{"day":"2016-04-29|228"},{"day":"2016-05-01|228"},{"day":"2016-08-08|228"},{"day":"2016-08-09|228"},{"day":"2016-08-10|228"},{"day":"2016-08-11|228"},{"day":"2016-08-12|228"},{"day":"2016-08-13|228"},{"day":"2016-08-15|228"}]';
	    var daymoney = $("#calendar").daymoney({
	        'date':'{$default}', //加载时默认显示的月份，不填则显示当前月份
	        daydata: daydata, 		//日期价格数据
	        events: 'click', 		//监听事件，默认为click
	        'style': {
	            disabled: "am-disabled", 	//禁用日期样式[当前日期之前]
            	active: "am-active", 		//激活日期样式[当前日期之后]
            	today: "am-primary", 		//当天日期样式
	        },
	        'load': function(obj) { 		//加载完毕时触发
	            //console.log('价格日历组件加载完毕');
	        },
	        'click': function(obj) {
	        	//提交订单
	        	ajax({
	        		url:'{:U('Wechat/Shop/order')}',


	        	});
	        	/*点击日期触发
	            var html = obj.data('date') + "的价格是:" + obj.data('money');
	            $('#alert').html(html);
	            $('#my-alert').modal('toggle');*/
	        }
	}).init();
    //使用daymoney对象方法
    $("#prev").click(function(){
    	daymoney.prev();
    });
    $("#next").click(function(){
    	daymoney.next();
    });
    $("#set").click(function(){
    	daymoney.setmoney('2016-08-21',125,function(data){
            $('#alert').html('已将'+data.day+'价格设置为'+data.money);
            $('#my-alert').modal('toggle');   		
    	});
    });
	});
  	</script>
  </body>
  </html>