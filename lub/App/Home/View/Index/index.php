<?php if (!defined('LUB_VERSION')) exit(); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<Managetemplate file="Home/Public/cssjs"/>
</head>
<body>
<div class="container">
  <Managetemplate file="Home/Public/menu"/>
  <div class="main row">
    <div class="panel panel-default">
      <div class="panel-body">
       当前用户：{$uinfo['cid']|itemnav} > {$uinfo['nickname']}<span style="float:right;">可用授信额: ￥<span id="cash"></span></span> 
      </div>
    </div>
    <if condition="$notice neq '1'">
    <div class="panel panel-default">
  <div class="panel-body">
    <span class="glyphicon glyphicon-volume-up"></span> <a href="{:U('Home/Index/notice_info',array('id'=>$notice['id']));}" target="_bank">通知：{$notice.title}  [{$notice.createtime|date="Y-m-d",###}]</a>
  </div>
</div>
</if>
    <div class="table-responsive">
      <volist name="data" id="vo">
      <if condition="$vo.type eq '1'">
      <?php $num = count($vo['plan']);?>
      <?php $arae_num = count($vo['area']);?>
      <div class="panel panel-default">
      <div class="panel-heading">产品名称：{$vo.name}</div>
      <table class="table table-hover table-bordered">
          <colgroup>
          <col> 
          <volist name="vo['area']" id="area">
          <col width="120px">
          </volist>
          <col>
          <col>
          <col width="150px">
          </colgroup>
          <thead>
            <tr valign="middle">
              <td rowspan="2" align="center">场次</td>
              <td colspan="{$arae_num}" align="center">剩余座椅</td>
              <td rowspan="2" align="center">销售配额</td>
              <td rowspan="2" align="center">已用配额</td>
              <td rowspan="2" align="center">操作</td>
            </tr>
            <tr>
              <volist name="vo['area']" id="area">
              <td align="center">{$area.name}(<?php echo M('Area')->where(array('id'=>$area['id']))->getField('num');?>)</td>
              </volist>
            </tr>
          </thead>
          <tbody>
            <volist name="vo['plan']"  id="plan">
              <tr>
                <td align="center"> {$plan.id|planShow}</td>
                <volist name="vo['area']" id="area">
                <td align="center"><?php echo M($plan['seat_table'])->where(array('status'=>0,'area'=>$area['id']))->count();?></td>
                </volist>
                <td align="center">{$vo['quota']}</td>
                <td align="center"><a href="{:U('Home/Index/seale',array('plan'=>$plan['id']));}" data-toggle="modal" data-target="#myModal"><?php echo M('QuotaUse')->where(array('plan_id'=>$plan['id'],'channel_id'=>$uinfo['cid']))->getField('number')?></a></td>
                <td align="center"><a href="{:U('Home/Product/index',array('productid'=>$vo['id'],'itemid'=>$vo['item_id'],'plan_id'=>$plan['id'],'games'=>$plan['games'],'type'=>$vo['type']));}">立即出票</a></td>          
              </tr>
              <tr>
                <td align="center"></td>
                <volist name="vo['area']" id="area"><td align="center"></td></volist>
                <td align="center"></td>
                <td align="center"></td>
                <td align="center"></td>
                </tr>
              </volist>   
          </tbody>
      </table>
      </div>
      <else />
      <div class="col-sm-4 col-md-4">
          <div class="thumbnail">
              <img src="{$vo.img}" class="img-responsive img-rounded">
              <div class="caption">
                  <h3>产品名称：{$vo.name}</h3>
                  <p>{$vo.content}</p>
                  <div class="btn-group btn-group-justified">
                      <a href="{:U('Home/Product/scenic',array('type'=>$vo['type'],'productid'=>$vo['id'],'itemid'=>$vo['item_id']))}" class="btn btn-primary" role="button" >立即购票</a>
                  </div>
              </div>
          </div>
      </div>
      </if>
      </volist>
    </div>
  </div>
</div>
<!--页脚-->
<Managetemplate file="Home/Public/footer"/>
<!--页脚-->
</body>
</html>