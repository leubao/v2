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
        <div class="panel-heading"><span class="glyphicon glyphicon-th-list"></span> 订单列表 </div>
        <div class="panel-body">
          <form class="form-inline" role="form" action="{:U('Home/Order/index');}" method="post">
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
              <select class="form-control" name="product">
                <option value="">选择产品</option>
                <volist name="product" id="v">
                <option value="{$v.id}" <if condition="$where['product_id'] eq $v['id']">selected="selected"</if>>{$v.name}</option>
                </volist>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" name="status">
                <option value="">状态</option>
                <option value="1" <eq name="where.status" value="1">selected="selected"</eq>>预定成功</option>
                <option value="2" <eq name="where['status']" value="2">selected="selected"</eq>>待支付</option>
                <option value="7" <eq name="where.status" value="7">selected="selected"</eq>>取消中</option>
                <option value="3" <eq name="where.status" value="3">selected="selected"</eq>>已撤销</option>
                <option value="0" <eq name="where.status" value="0">selected="selected"</eq>>已作废</option>
                <option value="4" <eq name="where.status" value="4">selected="selected"</eq>>已过期</option>
                <option value="5" <eq name="where.status" value="5">selected="selected"</eq>>待审核</option>
                <option value="9" <eq name="where.status" value="9">selected="selected"</eq>>完结</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" name="datetype">
                <option value="1" <if condition="$datetype eq '1'">selected="selected"</if>>下单日期</option>
                <option value="2" <if condition="$datetype eq '2'">selected="selected"</if>>演出日期</option>
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
            <a type="button" href="{$export_url}" class="btn btn-default">导出</a>
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
          <col>
          <col width="120px">
          <col width="55px">
          <col width="90px">
          </colgroup>
          <thead>
            <tr>
              <td align="center">订单号</td>
              <td align="center">产品名称</td>
              <td align="center">预约日期</td>
              <td align="center">数量</td>
              <td align="center" class="hidden-xs">金额</td>
              <td align="center" class="hidden-xs">业务员</td>
              <td align="center">渠道商</td>
              <td align="center" class="hidden-xs">下单时间</td>
              <td align="center">状态</td>
              <neq name="proconf['refund']" value="4">
                <td align="center">操作</td>
              </neq>
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
                <td align="center" ><?php echo D('Home/Crm')->where(array('id'=>$vo['channel_id']))->getField('name');?></td>
                <td align="center" >{$vo.createtime|date="m-d H:i",###}</td>
                <td align="center" >{$vo['status']|order_status}</td>
                <if condition="$proconf['refund'] neq '4'">
                <td align="center" >
                <if condition="is_order_plan($vo['plan_id'])">
                  <div class="btn-group btn-group-xs" role="group">
                    
                      <a class="btn btn-default print <if condition="$vo['status'] neq '1'">disabled</if>" href="#" data-url="{:U('Home/Order/drawer',array('sn'=>$vo['order_sn'],'plan_id'=>$vo['plan_id']))}"><i class="glyphicon glyphicon-print"></i></a>
                    
                    <if condition="$proconf['refund'] eq '1'">
                      <a class="btn btn-default subtract <if condition="$vo['status'] neq '1'">disabled</if>" href="#" data-sn="{$vo['order_sn']}" data-num="{$vo['number']}" data-subtract="{$vo['subtract']}"><i class="glyphicon glyphicon-scissors"></i></a>
                      <elseif condition="$proconf['refund'] eq '2'" />
                      <a class="btn btn-default <if condition="$vo['status'] neq '1'">disabled</if>" href="{:U('Home/Order/orderinfo',array('sn'=>$vo['order_sn'],'type'=>1));}" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-trash"></i></a>
                      <else />
                      <a type="button" class="btn btn-default subtract <if condition="$vo['status'] neq '1'">disabled</if>" data-sn="{$vo['order_sn']}" data-num="{$vo['number']}" data-subtract="{$vo['subtract']}"><i class="glyphicon glyphicon-scissors"></i></a>
                    </if>
                  </div>
                  <else />已过期</if>
                </td>
                </if>
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
  
  <!--内容主体区域 end-->
    <script>
    /*订单核减*/
    $('.subtract').click(function(){
        var sn = $(this).data('sn'),
            num = $(this).data('num'),/*订单门票数量*/
            subtract = $(this).data('subtract'),
            rstr = "",/*提示信息*/
            postData = 'info={"sn":'+sn+'}';
        if(num < 10 || subtract == '1'){
            layer.msg("订单未能满足核减条件!");
        }else{
          /*提交到服务器*/
          $.ajax({
            type:'POST',
            url:'index.php?g=Home&m=Order&a=subtract',
            data:postData,
            dataType:'json',
            success:function(data){
              if(data.statusCode == "200"){
                  var content = "";
                  $.each(data.area,function(idx,item){
                      content += "<label><input type='checkbox' name='area[]' id='a_"+item.area+"' value='"+item.area+"']}' onclick='subtract("+item.area+");'/>"+item.areaname+"("+item.num+")</label> <input type='text' name='seat_num[]' id='num_"+item.area+"' size='2' disabled> ";
                  });
                  /*写入核减信息*/
                  $("#sns").html(data.sn);
                  $("#snss").attr("value",data.sn);
                  $("#vnums").html(data.num);
                  $("#nums").attr("value",data.num);
                  $("#subtracts").html(content);
                  //存在核减
                  $("#subtract").modal('show');
                }else{
                  //不存在核减
                  layer.msg("订单未能满足核减条件!");
                }
            }
          });
        }     
  });
/*订单核减*/
function subtract(id){
  if($('#a_'+id).is(':checked')){
    $('#num_'+id).removeAttr('disabled');
  }else{
    $('#num_'+id).attr("disabled","disabled");
  }   
}
</script> 
    <!--核减弹窗-->
    <div class="modal fade" id="subtract" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="{:U('Home/Order/subtracts')}" method="post">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">订单核减</h4>
            </div>
            <div class="modal-body">
              <div class="col-md-6">
                <label>订单号：</label>
                <span id="sns"></span> </div>
              <div class="col-md-6">
                <label>可核减数：</label>
                <span id="vnums"></span> </div>
              <div class="col-md-12">
                <label>核减区域:</label>
              </div>
              <div class="col-md-12" id="subtracts"></div>
              <div class="col-md-12">注：单一订单最多核减一次</div>
              <input type="hidden" id="snss" name="sn" value=""/>
              <input type="hidden" id="nums" name="nums" value="" />
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
              <button type="submit" class="btn btn-primary">立即核减</button>
            </div>
          </form>
        </div>
        <!-- /.modal-content --> 
      </div>
      <!-- /.modal-dialog --> 
    </div>
    <!-- /.modal --> 
  
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>