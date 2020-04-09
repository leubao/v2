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
<!--内容主体区域 start-->
<div class="main row">
  <div class="row">
    <volist name="data" id="vo">
      <div class="col-md-12">
        <div class="panel panel-info">
          <div class="panel-heading">
            <h3 class="panel-title">{$vo.name}</h3>
          </div>
          <div class="panel-body">
            <volist name="vo['plan']"  id="plan"> <a href="{:U('Home/Product/index',array('pid'=>$vo['id'],'itemid'=>$vo['item_id'],'type'=>$vo['type'],'plan_id'=>$plan['id'],'games'=>$plan['games']))}" >{$plan.plantime|date="Y-m-d",###}第{$plan.games}场</a>  &nbsp;&nbsp;&nbsp;</volist>
          </div>
        </div>
      </div>
    </volist>
  </div>
</div>
<div> 
  <!--内容主体区域 end--> 
  <!--页脚-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>
