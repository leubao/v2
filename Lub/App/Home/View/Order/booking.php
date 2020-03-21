<?php if (!defined('LUB_VERSION')) exit(); ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Managetemplate file="Home/Public/cssjs"/>
<style type="text/css" media="screen">
	.panel-body{
		text-align: left;
	}
	.form-inline .form-group{
		margin-bottom: 10px;
		clear: both;
	}
	.form-group label{
		width: 100px;
		text-align: left;
	}
</style>
</head>

<body>
<div class="container">
  <Managetemplate file="Home/Public/menu"/>
  <div class="main row">
    <div class="col-lg-12">
      <div class="panel panel-default">
      	<div class="panel-body">
      		<form class="form-inline" role="form" action="{:U('Home/Order/booking');}" method="post">
	      		<div class="form-group col-md-6">
	      		  <label>预售产品</label>
	              <select class="form-control" name="product">
	                <volist name="product" id="v">
	                <option value="{$v.id}|{$v.areaid}">{$v.name} {$v.area}</option>
	                </volist>
	              </select>
	            </div>
	            <div class="form-group col-md-6">
				    <label>预售日期</label>
				    <input type="text" required value="{$datetime}" readonly class="form-control form_date" id="plantime" name="plantime">
				</div>
	            <div class="form-group col-md-6">
	            	<label>预售数量</label>
					<div class='input-group spinner' data-trigger='spinner'> 
						<div class='input-group-addon'>
							<a href='javascript:;' class='spin-down' data-spin='down'>-</a>
						</div>
						<input type='text' class='qnum form-control' id='number' name='number' data-rule='percent' value='1' size='2'>
						<div class='input-group-addon'>
							<a href='javascript:;' class='spin-up' data-spin='up'>+</a>
						</div>
					</div>
	            </div>
	            <div class="form-group col-md-6">
	            	<label>联系人</label>
					<input type='text' required class='form-control' name='contact' value=''>
	            </div>
	            <div class="form-group col-md-6">
	            	<label>联系电话</label>
					<input type='number' required title="手机号码不正确" class='form-control' name='mobile' value=''>
	            </div>
				<div class="form-group col-md-6">
					<label></label>
	              <button type="submit" class="btn btn-default">立即登记</button>
	            </div>
        	</form>
      	</div>
      </div>
    </div>
  </div>  
</div>
<Managetemplate file="Home/Public/footer"/>
  <script>
  	//订单中点击向上箭头
  $("body").on("click",".spin-up",function(){
    var obj    = $(this).parent().parent();
    var curnum = obj.find(".qnum").val();
    var name = obj.find(".qnum").attr("name");
    var ids  = name.split("_");
    var qnum = parseInt(curnum)+1;
    obj.find(".qnum").val(qnum);
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
        layer.msg("已经最少了...");    
    }
  });
  </script>
</body>
</html>