<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Order/Index/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    
    <input type="hidden" name="user.id" value="{$user_id}">
    <input type="text" name="user.name" readonly value="{$user_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_user',array('type'=>1,'ifadd'=>1));}" data-group="user" data-width="600" data-height="445" data-title="下单人" placeholder="下单人">

    <input type="hidden" name="channel.id" value="{$channel_id}">
    <input type="text" name="channel.name" readonly value="{$channel_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel',array('ifadd'=>1));}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">

    <input type="hidden" name="plan.id" value="{$plan_id}">
    <input type="text" name="plan.name" readonly value="{$plan_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_date_plan',array('ifadd'=>1));}" data-group="plan" data-width="600" data-height="445" data-title="销售计划(场次)" placeholder="销售计划(场次)">
    
    <input type="text" value="" name="sn" class="form-control" data-rule="length[5~]" size="10" placeholder="单号">&nbsp;
    <button type="button" class="showMoreSearch" data-toggle="moresearch" data-name="custom"><i class="fa fa-angle-double-down"></i></button>
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-primary" href="{:U('Order/Index/public_export_order',$export_map);}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出订单信息吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
    </div>
  </div>
  <div class="bjui-moreSearch">
    <label>&nbsp;状态:</label>
    <select name="status" data-toggle="selectpicker">
        <option value="">全部</option>
        <option value="1" <if condition="$status eq '1'">selected</if>>预定成功</option>
        <option value="9" <if condition="$status eq '9'">selected</if>>完结</option>
        <option value="5" <if condition="$status eq '5'">selected</if>>待审核</option>
        <option value="7" <if condition="$status eq '7'">selected</if>>取消中</option>
        <option value="2,0" <if condition="$status eq '0'">selected</if>>已作废</option>
    </select>
    <label>&nbsp;支付方式:</label>
    <select name="pay" data-toggle="selectpicker">
        <option value="">全部</option>
        <option value="1" <if condition="$pay eq '1'">selected</if>>现金</option>
        <option value="2" <if condition="$pay eq '2'">selected</if>>授信额</option>
        <option value="3" <if condition="$pay eq '3'">selected</if>>签单</option>
        <option value="5" <if condition="$pay eq '5'">selected</if>>微信支付</option>
        <option value="4" <if condition="$pay eq '4'">selected</if>>支付宝</option>
        <option value="6" <if condition="$pay eq '6'">selected</if>>划卡</option>
    </select>
    <label>&nbsp;手机:</label>
    <input type="text" value="{$map.phone}" name="phone" size="10">
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th align="center" width="115px">订单号</th>
        <th align="center" width="90px">场景(类型)</th>
        <th align="center" width="30px">数量</th>
        <th align="center" width="80px">金额</th>
        <th align="center">所属计划</th>
        <th align="center" width="120px">创建时间</th>
        <th align="center" width="70px">下单人</th>
        <th align="center" width="60px">状态</th>
        <th align="center">操作</th>
      </tr>
    </thead>
    <tbody id="order-list">
    <volist name="data" id="vo">
      <tr data-id="{$vo.order_sn}" data-num="{$vo.number}" data-money="{$vo.money}">
        <td align="center"><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn'],'menuid'=>$menuid))}"  data-id="orderinfo" data-width="900" data-height="600" data-title="订单详情">{$vo.order_sn}</a></td>
        <td align="center">{$vo.addsid|addsid}（{$vo.type|channel_type}）</td>
        <td align="center">{$vo.number}</td>
        <td align="right">{$vo.money}</td>
        <td align="center">{$vo.plan_id|planShow}</td>
        <td align="center">{$vo.createtime|date="Y-m-d H:i:s",###}</td>
        <td align="center">{$vo['user_id']|userName=$vo['addsid']}</td>
        <td align="center">{$vo['status']|order_status}</td>
        <td align="center"><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn'],'menuid'=>$menuid))}" data-id="orderinfo" data-width="900" data-height="600" data-title="订单详情">查看</a>
        {$vo['type']|print_buttn_show=$vo['pay'],$vo['order_sn'],$vo['plan_id'],$vo['money']}
        </td>
       </tr>
    </volist>
     <tr>
     <td></td>
     <td align="right">当前页合计:</td>
     <td id="sub-num" align="center">0</td>
     <td id="sub-money" align="right">0.00</td>
     <td></td><td></td><td></td><td></td><td></td></tr>
    </tbody>
  </table>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_num = 0,
      sub_money = 0;
  $('#order-list tr').each(function(i){
    if($(this).data('num') != null){
      sub_num += parseInt($(this).data('num'));
      sub_money += parseFloat($(this).data('money'));
    }
  });
  sub_money = sub_money.toFixed(2);
  $("#sub-num").html(sub_num);
  $("#sub-money").html(sub_money);
});
</script>
<div class="bjui-pageFooter">
  <div class="pages">
    <span>共 {$totalCount} 条</span>
  </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>