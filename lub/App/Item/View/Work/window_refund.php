<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
  <Managetemplate file="Common/Nav"/>
  <!--Page -->
  <form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Work/window_refund',array('menuid'=>$menuid));}" method="post">
    <!--条件检索 s-->
    <div class="bjui-searchBar">
      <label>订单号:</label>
      <input type="text" value="{$sn}" name="sn" class="form-control" size="20" placeholder="单号">
      
      &nbsp;
      <button type="submit" class="btn-default" data-icon="search">查询</button>
      &nbsp; <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a></div>
    </div>
    <!--检索条件 e-->
  </form>
  <!--Page end--> 
</div>
<div class="bjui-pageContent tableContent">
<if condition="$error neq ''">
<strong style='color:red;font-size:48px;'>{$error}</strong>
<else />
<table class="table table-bordered table-hover td50 w900">
  <tbody>
    <tr>
      <td width="90px">销售计划</td>
      <td width="320px">{$data.plan_id|planShow}</td>
      <td width="90px">单号</td>
      <td>{$data.order_sn}</td>
    </tr>
    <tr>
      <td>创建时间</td>
      <td>{$data.createtime|date="Y-m-d H:i:s",###} </td>
      <td>下单人</td>
      <td>{$data.user_id|userName}</td>
    </tr>
    <tr>
      <td>订单类型(场景)</td>
      <td>{$data.type|channel_type}（{$data.addsid|addsid}）</td>
      <if condition="$data['type'] eq '1'">
      <td></td>
      <td></td>
      <else />
      <td>渠道商</td>
      <td>{$data.channel_id|crmName}({$data.guide_id|userName})</td>
      </if>
    </tr>
    <tr>
      <td>订单金额</td>
      <td>{$data.money} </td>
      <td>数量</td>
      <td>{$data.number}</td>
    </tr>
  </tbody>
</table>
<form>
  <if condition="$data['product_type'] eq '1'">
  <table class="table table-bordered table-hover table-striped table-top w900">
    <thead>
      <tr>
        <th width="26"><input type="checkbox" class="checkboxCtrl" data-group="ids[]" data-toggle="icheck"></th>
        <th align="center">区域</th>
        <th align="center">座位号</th>
        <th align="center">票型名称</th>
        <th align="center">票面价</th>
        <th align="center">结算价</th>
        <th align="center">订单号/状态/打印次数/更新时间</th>
      </tr>
    </thead>
    <tbody id="refund_seat">
      <volist name="data['info']['data']" id="vo">
        <tr data-id="{$vo['id']}">
          <td><input type="checkbox" name="ids" data-toggle="icheck" data-title="{$vo['areaId']|areaName}{$vo['seatid']|seatShow}" data-area="{$vo['areaId']}" value="{$vo['seatid']}"></td>
          <td align="center">{$vo['areaId']|areaName}</td>
          <td align="center">{$vo['seatid']|seatShow}</td>
          <td align="center">{$vo['priceid']|ticketName}</td>
          <td align="right">{$vo['price']}</td>
          <td align="right">{$vo['discount']}</td>
          <td align="center">{$vo.seatid|seatOrder=$data['plan_id']}</td>
        </tr>
      </volist>
    </tbody>
  </table>
  <else />

  <table class="table table-bordered table-hover table-striped table-top w900">
    <thead>
      <tr>
        <th width="26"><input type="checkbox" class="checkboxCtrl" data-group="ids[]" data-toggle="icheck"></th>
        <th align="center">票号</th>
        <th align="center">票型名称</th>
        <th align="center">票面价</th>
        <th align="center">结算价</th>
        <th align="center">订单号/状态/打印次数/更新时间</th>
      </tr>
    </thead>
    <tbody id="refund_seat">
      <volist name="data['info']['data']" id="vo">
        <tr data-id="{$vo['id']}">
          <td><input type="checkbox" name="ids" data-toggle="icheck" data-title="票号{$vo['ciphertext']}" data-area="{$vo['priceid']}" value="{$vo['id']}"></td>
          <td align="center">{$vo['ciphertext']}</td>
          <td align="center">{$vo['priceid']|ticketName}</td>
          <td align="right">{$vo['price']}</td>
          <td align="right">{$vo['discount']}</td>
          <td align="center">{$vo.id|seatOrder=$data['plan_id']}</td>
        </tr>

        <if condition="!empty($data['info']['child_ticket'])">
          <?php $child_ticket = explode(',',$vo['child_ticket']);?>
          <volist name="data['info']['child_ticket']" id="v">
          <if condition="in_array($v['priceid'],$child_ticket)">
            <tr>
              <td></td>
              <td align="center">{$vo['ciphertext']}</td>
              <td align="center">{$v['priceid']|ticketName}</td>
              <td align="right">{$v['price']}</td>
              <td align="right">{$v['discount']}</td>
              <td align="center"><a data-toggle="doajax" data-confirm-msg="确定要退票吗？" href="{:U('Item/Work/refunds',array('sn'=>$data['order_sn'],'order'=>5,'fid'=>$vo['ciphertext'],'priceid'=>$v['priceid']));}">退票</a></td>
            </tr>
          </if>
          </volist>
       </if>

      </volist>

    </tbody>
  </table>
  </if>
  </div>
  </if>
  <div class="bjui-pageFooter">
    <ul>
      <li>
        <button type="button" id="refund_all" class="btn btn-red row-del" data-icon="recycle"> 整单退票</button></li>
      <li>
        <button type="button" id="recycle" class="btn btn-default" data-icon="recycle">退勾选门票</button>
      </li>
    </ul>
  </div>
</form>
<script type="text/javascript">
  $('#refund_all').click(function(){
    var urls = "{:U('Item/Work/refunds',array('sn'=>$data['order_sn'],'order'=>1,'menuid'=>$menuid));}";
    $.ajax({
      type:"get",
      url:urls,
      dataType:"JSON",
      timeout: 1500,
      error: function(){
        layer.msg('服务器请求超时，请检查网络...');
      },
      success: function(data){
        msg = data.message;
        if(data.statusCode == '200'){
          $(this).navtab('refresh');
          $(this).alertmsg('ok', msg);
        }else{
          $(this).alertmsg('error', msg);
          return false;
        }
      }
    });
  });
  $('#recycle').click(function(){
      var ii = 0;
      var refund_seat_checked = $("#refund_seat tr input[type=checkbox]:checked");
      if(refund_seat_checked.length > 0){
        refund_seat_checked.each(function(i){
          if(this.checked){
            /*提交退票请求 TODO   循环速度太快  提示信息不清楚*/
              var area = $(this).data('area'),
                  seat = $(this).val(),
                  title = $(this).data('title'),
                  urls = "{:U('Item/Work/refunds',array('sn'=>$data['order_sn'],'order'=>3,'menuid'=>$menuid));}&seatid="+seat+"&area="+area;
              $.ajax({
                type:"get",
                url:urls,
                dataType:"JSON",
                timeout: 3500,
                error: function(){
                  layer.msg('服务器请求超时，请检查网络...');
                },
                success: function(data){
                  msg = title+data.message;
                  if(data.statusCode == '200'){
                    $(this).alertmsg('ok', msg);
                    return true;
                  }else{
                    $(this).alertmsg('error', msg);
                    return false;
                  }
                }
              });  
          }
        });
        setTimeout(function (){$(this).navtab('refresh');}, 1000);
      }else{
        $(this).alertmsg('error',"请选择要退的门票");
        return false;
      }
  });
</script>