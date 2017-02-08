<?php if (!defined('LUB_VERSION')) exit(); ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Managetemplate file="Home/Public/cssjs"/>
</head>

<body>
<div class="container">
  <Managetemplate file="Home/Public/menu"/>
  <!--内容主体区域 start-->
  <div class="main row">
    <div class="col-lg-12">
      <div class="panel panel-default"> 
        <!-- Default panel contents -->
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> 授信额充值      <div class="btn-group btn-group-xs" style="float:right;"> <a href="{:U('Home/Pay/to_up_cash');}" class="btn btn-success" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-plus"></span>充值</a></div></div>
        <div class="panel-body">
          <form class="form-inline" role="form" action="{:U('Home/Report/index');}" method="post">
            <div class="form-group">
              <input size="16" type="text" value="{$start_time}" readonly class="form-control form_date" name="start_time">
            </div>
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-addon">至</div>
                <input size="16" type="text" value="{$end_time}" readonly class="form-control form_date" name="end_time">
              </div>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="sn" id="sn" placeholder="订单号" value="{$where['order_sn']}">
            </div>
            <div class="form-group">
              <select class="form-control" name="type">
                <option value="">类型</option>
                <option value="1" <if condition="$where['type'] eq 1">selected</if>>充值</option>
                <option value="2" <if condition="$where['type'] eq 2">selected</if>>花费</option>
                <option value="3" <if condition="$where['type'] eq 3">selected</if>>补贴</option>
                <option value="4" <if condition="$where['type'] eq 4">selected</if>>退票</option>
              </select>
            </div>
            <button type="submit" class="btn btn-default">查询</button>
            <button type="re" class="btn btn-default">重置</button>
          </form>
        </div>
        
      </div>
    </div>
  </div>
  
  <!--内容主体区域 end--> 
 <!--页脚-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>