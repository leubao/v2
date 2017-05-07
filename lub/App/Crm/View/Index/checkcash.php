<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--工具条 s-->
<div class="toolBar">
  <div class="btn-group" role="group"> 
    <a type="button" class="btn btn-success" href="{:U('Crm/Index/recharge',array('id'=>$cid,'channel'=>$channel,'groupid'=>$groupid));}" data-toggle="dialog" data-id="crm_recharge" data-mask="true"><i class="fa fa-plus"></i> 充值</a> 
    <a type="button" class="btn btn-danger" href="{:U('Crm/Index/refund',array('id'=>$cid,'channel'=>$channel,'groupid'=>$groupid));}}" data-toggle="dialog" data-id="crm_refund" data-mask="true"><i class="fa fa-pencil"></i> 退款</a>
    
    <a type="button" class="btn btn-primary" href="{:U('Crm/Index/export_credit');}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出订单信息吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>
  </div>
  <!--帮助 说明--> 
  <div class="btn-group f-right" role="group"> <a type="button" class="btn btn-default" data-placement="bottom" data-toggle="tooltip" onclick="$(this).dialog('refresh');" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
   <button type="button" class="btn btn-default"><i class="fa fa-question-circle"></i></button>
  </div>
</div>
<!--工具条 e--> 
<!--查询条 s-->
  <form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Crm/Index/checkcash',array('id'=>$cid,'channel'=>$channel,'groupid'=>$groupid))}" method="post">
    <input type="hidden" name="pageCurrent" value="{$currentPage}" />
    <input type="hidden" name="pageSize" value="{$numPerPage}" />
    <input name="id" type="hidden" value="{$cid}">
    <div class="bjui-searchBar">
      <label>日期:</label>
      <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
      <label>至</label>
      <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
      &nbsp;
      <label>类型:</label>
      <select name="type" data-toggle="selectpicker">
        <option value="0" <eq name="type" value="0"> selected</eq>>全部</option>
        <option value="1" <eq name="type" value="1"> selected</eq>>充值</option>
        <option value="2" <eq name="type" value="2"> selected</eq>>消费</option>
        <option value="3" <eq name="type" value="3"> selected</eq>>补贴</option>
        <option value="4" <eq name="type" value="4"> selected</eq>>退票</option>
        <option value="4" <eq name="type" value="5"> selected</eq>>退款</option>
      </select>
      &nbsp;
      <button type="submit" class="btn-default" data-icon="search">查询</button>
      &nbsp; <a class="btn btn-orange" href="javascript:;" onclick="$(this).navtab('reloadForm', true);" data-icon="undo">清空查询</a>
    </div>
    
  </form>
<!-- 查询条 e-->
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th align="center">操作时间</th>
        <th width="80" align="center">金额</th>
        <th width="55" align="center">类型</th>
        <th width="80" align="center">余额</th>
        <th align="center">单号</th>
        <th width="90" align="center">操作员</th>
        <th width="110" align="center">渠道商</th>
        <th align="center">备注</th> 
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">
        <td align="center">{$vo.createtime|date="Y-m-d H:i:s",###}</td>
        <td align="right">{$vo.cash}</td>
        <td align="center">{$vo.type|operation}</td>
        <td align="right">{$vo.balance}</td>
        <td align="center">{$vo.order_sn}</td>
        <td align="center">{$vo.user_id|userName}</td>
        <td align="center">{$vo.crm_id|crmName}</td>
        <td>{$vo.remark}</td>
      </tr>
    </volist>
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages"> <span>共 {$totalCount} 条</span> </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>