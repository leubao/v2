<div class="bjui-pageContent">
<div id="seat-map-w-h-{$areaid}" style="overflow: auto;white-space: nowrap;">
<div id="seat-map-{$areaid}">
	<div id="seat-info-{$areaid}" class="seat-info"></div>
</div>
</div>
</div>
<div class="bjui-pageFooter">
    <ul>
      <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
      <li><button type="submit" class="btn-default" data-icon="save" id="seat_submit">保存</button></li>
    </ul>
</div>
<script>
$(document).ready(function() {
  /*设置座位区域的宽高*/
  	var seatMapW = $(window).width() - 100,
	    seatMapH = $(window).height() - 110,
	    areaid = {$areaid},
	    placeid = {$placeid},
	    template_id = {$template_id},
	    number = '',
		data = '';
 	$('#seat-map-w-h-{$areaid}').width(seatMapW);
  	$('#seat-map-w-h-{$areaid}').height(seatMapH);
	var cart = $('#selected-seats-{$areaid}'),
	    seatid = '{$data.h_seat}', 
	    temseat = '',
		sc = $('#seat-map-{$areaid}').seatCharts({
		    map: ['{$data.seat}'],
			naming : { //定义行列等信息
				top : true,
				columns: ['{$data.columns}'], 
	    		rows: ['{$data.rows}'], 
				getLabel : function (character, row, column) {
					return row+"排"+column+"号";
				}
			},
		click: function () {
	  		var obj = this.data();
			if (this.status() == 'available') {
				if(seatid){
					seatid =  seatid +',' + this.settings.id;
				}else{
					seatid =  this.settings.id+seatid;
				}
				/*更新票数*/
				number = sc.find('available').length-1;
				return 'selected';
			} else if (this.status() == 'selected') {/*已选中*/
				temseat = ','+this.settings.id;
				seatid = seatid.replace(temseat,"");
				number = sc.find('available').length+1;
				return 'available';
			} else if (this.status() == 'unavailable') {/*已售出*/
				return 'unavailable';
			} else {
				return this.style();
			}
		},
		focus  : function() {
			$("#seat-info-{$areaid}").show().html(this.settings.label);
			var cd = getMousePoint(event);
			$("#seat-info-{$areaid}").css({"left":(cd.x+10)+'px',"top":(cd.y-30)+"px"});
			if (this.status() == 'available') {
				return 'focused';
			} else  {
				return this.style();
			}
		}
	});
	//已废弃的座位
	sc.get(['{$data.n_seat}']).status('selected');
	//提交座位
	$("#seat_submit").click(function(){
		data = '{"seatid":"'+seatid+'","areaid":'+areaid+',"template_id":'+template_id+',"placeid":'+placeid+',"number":'+number+'}';
		$.post('{:U('Manage/Place/seatadd',array('menuid'=>$menuid))}',{data:data},function(data){
			if(data.statusCode == '200'){
				$(this).alertmsg('ok', '更新成功！');
				/*关闭弹窗*/
				$(this).dialog("closeCurrent","true");
				/*刷新页面*/
				$(this).navtab('refresh',data.tabid);
            } else {
            	$(this).alertmsg('error', data.message);
            }
		},'json');
	});
});
</script>