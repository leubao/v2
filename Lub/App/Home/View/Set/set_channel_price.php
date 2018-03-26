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
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> 设置下级分销价格</div>
        </div>
        <form class="form-inline" role="form" action="{:U('Home/Set/set_channel_price');}" method="post">
        <div class="panel-body">
          
          <table class="table table-condensed table-hover table-responsive table-bordered table-vcenter">
          <colgroup>
          <col>
          <col>
          <col>
          </colgroup>
          <thead>
            <tr>
              <td align="center">名称</td>
              <td align="center">票面价</td>
              <td align="center">采购价</td>
              <td align="center">分销结算价</td>
              <td align="center">补贴</td>
            </tr>
          </thead>
          <tbody>
            <volist name="ticket" id="vo">
              <tr >
                <td align="center" ><input type="hidden" name="crm_id[]" value="{$vo.crm_id}">{$vo.name}<input type="hidden" name="id[]" value="{$vo.id}"></td>
                <td align="right" >{$vo.price}<input type="hidden" name="ticket_id[]" value="{$vo.ticket_id}"></td>
                <td align="right" >{$vo.buy}</td>
                <td align="center"><input type="text" class="form-control" name="discount[]" <?php if($points){ ?> disabled <?php } ?> value="{$vo.discount}" placeholder="结算价"></td>
                <td align="center"><input type="text" class="form-control" name="rebate[]" <?php if($points){ ?> disabled <?php } ?> value="{$vo.rebate}" placeholder="补贴金额"></td>
              </tr>
            </volist>
          </tbody>
        </table>
            
        </div>
        <div class="panel-footer"><button type="submit" class="btn btn-success" <?php if($points){ ?> disabled <?php } ?> >提交</button></div>
        </form>
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