<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
  <Managetemplate file="Common/Nav" />
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Order/Index/pre_order',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <input type="hidden" name="channel.id" value="{$channel_id}">
    <input type="text" name="channel.name" readonly value="{$channel_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel',array('ifadd'=>1));}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">

    <input type="hidden" name="plan.id" value="{$plan_id}">
    <input type="text" name="plan.name" readonly value="{$plan_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_date_plan',array('ifadd'=>1));}" data-group="plan" data-width="600" data-height="445" data-title="销售计划(场次)" placeholder="销售计划(场次)">
    
    <input type="text" value="" name="sn" class="form-control" data-rule="length[5~]" size="10" placeholder="单号">&nbsp;
    <select name="status" data-toggle="selectpicker">
        <option value="">状态</option>
        <option value="1" <if condition="$status eq '1'">selected</if>>预定成功</option>
        <option value="5" <if condition="$status eq '5'">selected</if>>待审核</option>
        <option value="2" <if condition="$status eq '2'">selected</if>>待支付</option>
        <option value="3" <if condition="$status eq '3'">selected</if>>已撤销</option>
    </select>
   &nbsp;
    <select name="pay" data-toggle="selectpicker">
        <option value="">支付</option>
        <option value="1" <if condition="$pay eq '1'">selected</if>>现金</option>
        <option value="2" <if condition="$pay eq '2'">selected</if>>授信额</option>
    </select>
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
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
        <th align="center">所属计划</th>
        <th align="center">渠道商</th>
        <th align="center" width="40px">数量</th>
        <th align="center" width="80px">金额</th>
        <th align="center" width="80px">支付方式</th>
        <th align="center" width="70px">下单人</th>
        <th align="center" width="70px">联系人</th>
        <th align="center" width="65px">状态</th>
        <th align="center" width="70px">核单人</th>
        <th align="center" width="100px">创建时间</th>
        <th align="center" width="100px">更新时间</th>
      </tr>
    </thead>
    <tbody id="pre-order-list">
    <volist name="data" id="vo">
      <tr data-id="{$vo.order_sn}" data-num="{$vo.number}" data-money="{$vo.money}">
        <td align="center"><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn']))}"  data-id="orderinfo" data-width="900" data-height="600" data-title="订单详情">{$vo.order_sn}</a></td>
        <if condition="$vo.type eq '2'">
        <td align="center">{$vo.plan_id|date="Y-m-d",###}</td>
        <else />
        <td align="center">{$vo.plan_id|planShow}</td>
        </if>
        <td align="center">{$vo.channel_id|hierarchy}</td>
        <td align="center">{$vo.number}</td>
        <td align="right">{$vo.money|format_money}</td>
        <td align="center">{$vo['pay']|pay}</td>
        <td align="center">{$vo['user_id']|userName}</td>
        <td align="center">{$vo.contact}</td>
        <td align="center">{$vo['status']|order_status}</td>
        <td align="center">{$vo['admin_id']|userName}</td>
        <td align="center">{$vo.createtime|date="m-d H:i",###}</td>
        <td align="center">{$vo.uptime|date="m-d H:i",###}</td>
       </tr>
    </volist>
     <tr>
     <td></td><td></td><td align="right">当前页合计:</td>
     <td id="sub-pre-num" align="center">0</td>
     <td id="sub-pre-money" align="right">0.00</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
    </tbody>
  </table>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_pre_num = 0,
      sub_pre_money = 0;
  $('#pre-order-list tr').each(function(i){
    if($(this).data('num') != null){
      sub_pre_num += parseInt($(this).data('num'));
      sub_pre_money += parseFloat($(this).data('money'));
    }
  });
  sub_pre_money = sub_pre_money.toFixed(2);
  $("#sub-pre-num").html(sub_pre_num);
  $("#sub-pre-money").html(sub_pre_money);
});
</script>
<div class="bjui-pageFooter">
  <div class="pages">
    <span>共 {$totalCount} 条</span>
  </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>