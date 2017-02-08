<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/Logs/print_log',array('menuid'=>$menuid));}" method="post">
 <!--Page --> 
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}"> 

<!--Page end-->
<!--条件检索 s-->
  <div class="bjui-searchBar">
    <label>日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    <label>&nbsp;类型:</label>
    <select name="type" data-toggle="selectpicker">
        <option value="">全部</option>
        <option value="1" <if condition="$type eq '1'">selected</if>>首次打印</option>
        <option value="2" <if condition="$type eq '2'">selected</if>>二次打印</option>
    </select>
    &nbsp;
    <label>&nbsp;场景:</label>
    <select name="scene" data-toggle="selectpicker">
        <option value="">全部</option>
        <option value="1" <if condition="$scene eq '1'">selected</if>>窗口</option>
        <option value="3" <if condition="$scene eq '3'">selected</if>>渠道版</option>
        <option value="7" <if condition="$scene eq '7'">selected</if>>自助机</option>
    </select>
    <input type="hidden" name="user.id" value="{$user}">
    <input type="text" name="user.name" disabled value="{$username}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_user');}" data-group="user" data-width="600" data-height="445" data-title="操作员" placeholder="操作员">
    &nbsp;
    <input type="text" value="{$where['sn']}" name="sn" class="form-control" size="10" placeholder="单号">&nbsp;
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
  </div>
  <!--检索条件 e-->
  </form>
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true"  id="w_print_log">
    <thead>
      <tr>
        <th align="center">订单号</th>
        <th align="center">打印次数</th>
        <th align="center">操作员</th>
        <th align="center">授权员</th>
        <th align="center">数量</th>
        <th align="center">打印场景</th>
        <th align="center">操作时间</th>
        <th align="center">备注</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr class="subtotal" data-num="{$vo.number}" >
        <td align="center">{$vo.order_sn}</td>
        <td align="center"><if condition="$vo['type'] eq 1">首次打印<elseif condition="$vo['type'] eq 2" /><font color="#FF0000">二次打印</font><else /><font color="#20b2aa">同步通信</font></if></td>
        <td align="center"><if condition="$vo['scene'] eq 7">{$uid|crmName}<else />{$vo.uid|userName}</if></td>
        <td align="center"><if condition="$vo['type'] eq 2">{$vo.user_id|pwd_name}<else/> 首次打印</if></td>
        <td align="center">{$vo.number}</td>
        <td align="center">{$vo['scene']|scene}</td>
        <td align="center">{$vo.createtime|datetime}</td>
        <td align="center">{$vo.remark}</td>
       </tr>
    </volist>
     <tr>
        <td align="center"></td>
        <td align="center"></td>
        <td align="center"></td>
        
        <td align="right">合计:</td>
        <td align="center" id="print-log-num">0</td>
        <td align="center"></td>
        <td align="center"></td>
       </tr>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages">
    <span>共 {$totalCount} 条</span>
  </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_num = 0;
  $("#w_print_log .subtotal").each(function(i) {
    if($(this).data('num') != null){
      sub_num += parseInt($(this).data('num'));
    }
  });
  $("#print-log-num").html(sub_num);
});
</script>