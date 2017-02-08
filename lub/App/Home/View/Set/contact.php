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
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> 常用联系人管理
          <div class="btn-group btn-group-xs" style="float:right;"> <a href="{:U('Home/Set/contact_add');}" class="btn btn-success" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-plus"></span>添加</a></div>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" action="{:U('Home/Set/contact');}" method="post">
            <div class="form-group">
              <input size="16" type="text" value="" readonly class="form-control form_date" name="start_time">
            </div>
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-addon">至</div>
                <input size="16" type="text" value="" readonly class="form-control form_date" name="end_time">
              </div>
            </div>
            <div class="form-group">
              <select class="form-control" name="status">
                <option value="">状态</option>
                <option value="1">启用</option>
                <option value="2">停用</option>
              </select>
            </div>
            <button type="submit" class="btn btn-default">查询</button>
          </form>
        </div>
        <!-- Table -->
        <table class="table table-condensed table-hover table-responsive table-bordered table-vcenter">
          <colgroup>
          <col>
          <col>
          <col>
          <col width="60px">
          <col width="170px">
          </colgroup>
          <thead>
            <tr>
              <td align="center">姓名</td>
              <td align="center">手机</td>
              <td align="center">添加时间</td>
              <td align="center">状态</td>
              <td align="center">操作</td>
            </tr>
          </thead>
          <tbody>
            <volist name="data" id="vo">
              <tr >
                <td align="center" >{$vo.name}</td>
                <td align="center" >{$vo.phone}</td>
                <td align="center" >{$vo.createtime|date="Y-m-d h:i",###}</td>
                <td align="center" ><if condition="$vo['status'] eq 1"><span class="label label-success">启用</span>
                    <else />
                    <span class="label label-danger">停用</span></if></td>
                <td align="center"><div  class="btn-group btn-group-xs"><a href="{:U('Home/Set/contact_edit',array('id'=>$vo['id']));}" data-toggle="modal" data-target="#myModal" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span></a><a href="{:U('Home/Set/contact_del',array('id'=>$vo['id']));}" class="btn btn-default"><span class="glyphicon glyphicon-trash"></span></a></div></td>
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