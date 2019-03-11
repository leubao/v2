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
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> 预约列表 </div>
        <div class="panel-body">
          <form class="form-inline" role="form" action="{:U('Home/Order/pre_order');}" method="post">
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
                <option value="1" <eq name="where.status" value="1">selected="selected"</eq>>预定成功</option>
                <option value="2" <eq name="where['status']" value="2">selected="selected"</eq>>待支付</option>
                <option value="3" <eq name="where.status" value="3">selected="selected"</eq>>已撤销</option>
                <option value="0" <eq name="where.status" value="0">selected="selected"</eq>>已作废</option>
                <option value="5" <eq name="where.status" value="5">selected="selected"</eq>>待审核</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" name="pay">
                <option value="">支付</option>
                <option value="1" <eq name="where['pay']" value="1">selected="selected"</eq>>现金</option>
                <option value="2" <eq name="where['pay']" value="2">selected="selected"</eq>>授信额</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" name="user">
                <option value="">下单人</option>
                <volist name='user' id="user">
                <option value="{$user.id}" <if condition="$where['user_id'] eq $user['id']">selected="selected"</if>>{$user.nickname}</option>
                </volist>
              </select>
            </div>
            <button type="submit" class="btn btn-default">查询</button>
          </form>

        </div>
        <!-- Table -->
        <table class="table table-condensed table-hover table-responsive table-bordered table-vcenter">
          <colgroup>
          <col width="145px">
          <col>
          <col>
          <col>
          <col>
          <col>
          <col width="60px">
          <col width="120px">
          <col width="55px">
          </colgroup>
          <thead>
            <tr>
              <td align="center">订单号</td>
              <td align="center">产品名称</td>
              <td align="center">预约日期</td>
              <td align="center">数量</td>
              <td align="center" class="hidden-xs">金额</td>
              <td align="center" class="hidden-xs">业务员</td>
              <td align="center" class="hidden-xs">支付</td>
              <td align="center">渠道商</td>
              <td align="center" class="hidden-xs">下单时间</td>
              <td align="center">状态</td>
            </tr>
          </thead>
          <tbody>
            <volist name="data" id="vo">
              <tr >
                <td align="center" ><a href="{:U('Home/Order/orderinfo',array('sn'=>$vo['order_sn'],'type'=>1));}" data-toggle="modal" data-target="#myModal">{$vo.order_sn}</a></td>
                <td align="center" >{$vo.product_id|product_name}</td>
                <td align="center" >{$vo.plan_id|planShow}</td>
                <td align="center" >{$vo.number}</td>
                <td align="center" >{$vo.money}</td>
                <td align="center"  class="hidden-xs">{$vo.user_id|userName=$vo['addsid']}</td>
                <td align="center">{$vo.pay|pay}</td>
                <td align="center" ><?php echo D('Home/Crm')->where(array('id'=>$vo['channel_id']))->getField('name');?></td>
                <td align="center" >{$vo.createtime|date="m-d H:i",###}</td>
                <td align="center" >{$vo['status']|order_status}</td>
              </tr>
            </volist>
            <tr>
              <td align="center" ></td>
              <td align="center" ></td>
              <td align="right" >合计:</td>
              <td align="center" >{$info['num']}</td>
              <td align="center" >{$info['money']|format_money}</td>
              <td align="center" ></td>
              <td align="center" ></td>
              <td align="center" ></td>
              <td align="center" ></td>
              <td align="center" ></td>
            </tr>
          </tbody>
        </table>
        <div class="panel-footer">{$page}</div>
      </div>
    </div>
  </div>
  
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>