<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title">订单座位图</h4>
</div>
<div class="modal-body">
  <ul class="nav nav-tabs" role="tablist" id="myTab">
    <volist name="area" id="vo">
      <li role="presentation"><a href="{$vo}" role="tab" data-toggle="tab">{$vo|areaName}</a></li>
    </volist>
  </ul>
  <div class="tab-content" style="height:450px;">
    <volist name="area" id="vo">
      <div role="tabpanel" class="tab-pane" id="{$vo}">
        <div class="choose" style="position:relative">
          <div style="position:relative;margin:20px" id="seat" >
          
          <div style="margin:0px 0px 0px 30px;" class="seatlist" id="seatlist{$aid}">
            <volist name="seat" id="le" key="k">
              <dl class="clear">
                <volist name="le" id="ri">
                  <dd>
                    <div class="<?php if($ri[s] == 'h'){ echo "noSeat";}else{echo "yesSeat";}?>" id="{$ri.a}"></div>
                  </dd>
                </volist>
              </dl>
            </volist>
          </div>
          
          
          </div>
          <div style="width: 250;border: 1px solid gray;padding: 0px;width: 250px;height: 200px;position: absolute;bottom: 10px;right: 10px;">
            <div style="width: 250px;background: gray;text-align: center;">分区示意图</div>
            <div id="selected_area"></div>
          </div>
        </div>
      </div>
    </volist>
  </div>
  <style>
  .preSeat{}
  .soldSeat{}
  </style>
  <script>
  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  var target = $(e.target).attr("href");
  $.ajax({
      type: "GET",
	  dataType : 'json',
      url: "<?php echo U('Home/Order/seats');?>&area="+target,
      error: function(data){
        alert("座椅区域加载失败!");
      },
      success: function(data){
        $(target).html(data);
		
		var seatArea = "#seatlist"+<?php echo $aid;?>;/*区域ID*/
		var selSeat = eval(data.seat);/*返回的座位信息*/
		$.each(selSeat,function(){
				switch(this.status){
					case '1' :
						$("div[id='"+this.sid+"']").attr("class","preSeat");
						break;
					case '2' :
						$("div[id='"+this.sid+"']").attr("class","soldSeat");
						break;
					default:
						break;
				}
			});
		}
		
		
      }
  })
  /*
  if ($(target).is(':empty')) {	
    $.ajax({
      type: "GET",
      url: "/article/",
      error: function(data){
        alert("There was a problem");
      },
      success: function(data){
        $(target).html(data);
      }
  })
 }*/
});
</script> 
</div>
