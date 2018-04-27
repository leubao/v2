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
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> 促销活动</div>
        
        <!-- Table -->
        <table class="table table-condensed table-hover table-responsive table-bordered table-vcenter">
          <colgroup>
          <col width="160px">
          <col>
          <col>
          <col width="60px">
          <col width="170px">
          </colgroup>
          <thead>
            <tr>
              <td align="center">编号</td>
              <td align="center">活动名称</td>
              <td align="center">活动时间</td>
              <td align="center">说明</td>
              <td align="center">操作</td>
            </tr>
          </thead>
          <tbody>
            <volist name="data" id="vo">
              <tr >
                <td align="center" >{$i}</td>
                <td align="center" >{$vo.title}</td>
                <td align="center" >{$vo.starttime|date="Y-m-d",###} - {$vo.endtime|date="Y-m-d",###}</td>
                <td align="center" >{$vo.remark}</td>
                <td align="center">
                <div class="btn-group btn-group-xs">
                <a href="{:U('Home/Promotions/work',array('id'=>$vo['id']))}" class="btn btn-default">售票</a>
                </div>
                </td>
              </tr>
            </volist>
          </tbody>
        </table>
        <div class="panel-footer">{$page}</div>
      </div>
    </div>
  </div>  
  <!--内容主体区域 end--> 
  <script>$('.form_date').datetimepicker({ format: 'yyyy-mm-dd',weekStart: 1,todayBtn:  1,autoclose: 1,todayHighlight: 1,startView: 2,minView: 2,forceParse: 0});</script> 
  <!--页脚-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>