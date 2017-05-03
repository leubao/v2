<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" >
    <title>{$product}销售情况</title>
    <link rel="stylesheet" href="//g.alicdn.com/sui/sui3/0.0.18/css/sui.min.css">
  </head>
  <body>
    <div class="container">
    <h1 align="center">{$product}</h1>
    <h3 align="center" id="plshow"></h3>
	<div class="form-group">
	  <label class="select">
	    <select id="wplan" class="form-control input-lg">
	      <volist name="plan" id="vo">
          <?php $ptime =  $vo['plantime']."-".$vo['games'];?>
          <option value="{$ptime}"  <if condition="$today eq $ptime">selected</if>>{$vo.plantime|date="Y-m-d",###}  第{$vo.games}场 {$vo.starttime|date="H:i",###}
          </option>
          </volist>
	    </select>
	  </label>
	</div>
    <h4 align="center"><a href="">手动刷新</a></h4>
    <table class="table table-condensed table-bordered">
    	<thead>
    		<tr>
    			<th align='center'>区域</th>
    			<th>座椅数</th>
    			<th>已售</th>
    			<th>可售</th>
    		</tr>
    	</thead>
    	<tbody id="work_area">
    	</tbody>
	</table>
	</div>
    <script type="text/javascript" src="//g.alicdn.com/sj/lib/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="//g.alicdn.com/sui/sui3/0.0.18/js/sui.min.js"></script>
    <script>
    	$(document).ready(function(){
		  var plan = '';
		  //自动加载默认选框
		  plan = $('#wplan').children('option:selected').val();
		  if(plan != '' || null || undefined){
		    var data = 'info={"plan":"'+plan+'"}',
		        content = '';
		        $.ajax({
		            url: '{:U('Api/Figure/change_plan',array('param'=>3,'pid'=>$pid))}',
		            type: 'POST',
		            dataType: 'JSON',
		            timeout: 3500,
		            data:data,
		            error: function(){
		                alert('服务器请求超时，请检查网络...');
		            },
		            success: function(rdata){
		              if(rdata.statusCode == '200'){
		                $("#plshow").html(rdata.plan);
		                 /*写入*/
		                $(rdata.area).each(function(idx,area){
		                  content += "<tr><td align='center'>"+area.name+"</td><td>"+area.number+"</td><td>"+area.nums+"</td><td>"+area.num+"</td></tr>";
		                })
		              }
		              $("#work_area").html(content); 
		            }
		        });
		  }else{
		    var error_msg = "<tr><td style='padding:15px;' colspan='3' align='center'><strong style='color:red;font-size:48px;'>请选日期</strong></td></tr>";
		    $("#work_area").html(error_msg);
		  }
		  //改变日期场次
		  $('#wplan').change(function(){
		    plan = $(this).children('option:selected').val();
		    if(plan != '' || null || undefined){
		        var data = 'info={"plan":"'+plan+'"}',
		            content = '';
		          $.ajax({
		            url: '{:U('Api/Figure/change_plan',array('param'=>3,'pid'=>$pid))}',
		            type: 'POST',
		            dataType: 'JSON',
		            timeout: 1500,
		            data:data,
		            error: function(){
		                alert('服务器请求超时，请检查网络...');
		            },
		            success: function(rdata){
		              if(rdata.statusCode == '200'){
		                $("#plshow").html(rdata.plan);
		                 /*写入*/
		                $(rdata.area).each(function(idx,area){
		                  content += "<tr><td align='center'>"+area.name+"</td><td>"+area.number+"</td><td>"+area.nums+"</td><td>"+area.num+"</td></tr>";
		              	})
		              }
		              $("#work_area").html(content); 
		            }
		        });
		    }else{
		        var error_msg = "<tr><td style='padding:15px;' colspan='3' align='center'><strong style='color:red;font-size:48px;'>请选日期</strong></td></tr>";
		        $("#work_area").html(error_msg);
		    }
		  });
		});
    </script>
  </body>
</html>