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
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span>补贴明细 &nbsp;&nbsp;<a href=""><span class="label label-warning">自选时间段报表</span></a></div>
        <div class="panel-body">
          <form class="form-inline" role="form" action="{:U('Home/Report/rakeback');}" method="post">
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
              <input type="text" class="form-control" name="sn" id="sn" value="{$where['order_sn']}" placeholder="订单号">
            </div>
            <div class="form-group">
              <select class="form-control" name="status">
                <option value="">状态</option>
                <option value="2" <if condition="2 eq $where['status']">selected </if>>下单成功</option>
                <option value="3" <if condition="3 eq $where['status']">selected </if>>财务审核成功</option>
                <option value="4" <if condition="4 eq $where['status']">selected </if>>补贴发放成功</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" name="ur">
                <option value="">操作员</option>
                <volist name="user" id="ur">
                <option value="{$ur.id}" <if condition="$ur['id'] eq $where['user_id']">selected </if>>{$ur.nickname}</option>
                </volist>
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
          <col>
          <col>
          <col>
          </colgroup>
          <thead>
            <tr>
              <td align="center">操作时间</td>
              <td align="center">状态</td>
              <td align="center">金额</td>
              <td align="center">操作员</td>
              <td align="center">渠道商</td>
              <td align="center">订单号</td>
            </tr>
          </thead>
          <tbody>
            <volist name="data" id="vo">
              <tr >
                <td align="center">{$vo.createtime|date="Y-m-d H:i",###}</td>
                <td align="center">{$vo.status|rebate}</td>
                <td align="center">￥{$vo.money}</td>
                <td align="center">{$vo.user_id|userName}</td>
                <td align="center"><?php echo D('Home/Crm')->where(array('id'=>$vo['qd_id']))->getField('name');?></td>
                <td align="center"><a href="{:U('Home/Order/orderinfo',array('sn'=>$vo['order_sn'],'type'=>1));}" data-toggle="modal" data-target="#myModal">{$vo.order_sn}</a></td>
              </tr>
            </volist>
          </tbody>
        </table>
        <div class="panel-footer">{$page}</div>
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