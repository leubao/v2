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
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> {$id|crmName}资金流水
          <div class="btn-group btn-group-xs" style="float:right;"> <a href="{:U('Home/Pay/to_up_cash',array('cid'=>$id,'channel'=>$crm));}" class="btn btn-success" data-toggle="modal" data-target="#myModal">充值</a>
          <a href="{:U('Home/Pay/arefund',array('cid'=>$id,'channel'=>$crm));}" class="btn btn-default" data-toggle="modal" data-target="#myModal">退款</a>
          </div>
        </div>
        <div class="panel-body">
          <form class="form-inline" role="form" action="{:U('Home/pay/index');}" method="post">
            <div class="form-group">
              <input size="16" type="text" value="{$starttime}" readonly class="form-control form_date" name="starttime">
            </div>
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-addon">至</div>
                <input size="16" type="text" value="{$endtime}" readonly class="form-control form_date" name="endtime">
              </div>
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
            <input type="hidden" name="crm" value="{$crm}"/>
            <input type="hidden" name="id" value="{$id}"/>
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
              <td align="center">操作类型</td>
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
                <td align="center">{$vo.type|operation}</td>
                <td align="center">￥{$vo.cash}</td>
                <td align="center">{$vo.user_id|userName}</td>
                <td align="center"><?php echo D('Home/Crm')->where(array('id'=>$vo['crm_id']))->getField('name');?></td>
                <td align="center">{$vo.order_sn}<span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="top" title="{$vo.remark}"></span></td>
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