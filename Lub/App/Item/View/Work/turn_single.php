<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
    <form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Work/turn_single',array('menuid'=>$menuid));}" method="get">
        <input type="hidden" name="navTabId" value="{$navTabId}" />
        <div class="bjui-searchBar">
            <label>单号：</label><input type="text" value="{$sn}" name="sn" size="10" />&nbsp;
            <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
            <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>&nbsp;
        </div>
    </form>
</div>
<div class="bjui-pageContent tableContent">
	<form class="form-horizontal" action="{:U('Item/Work/turn_single',array('sn'=>$sn,'menuid'=>$menuid));}" method="post" data-toggle="validate">
		<?php //dump($oinfo); dump($ticket);?>
	  <table class="table table-striped table-bordered">
	    <tbody>
	      <tr>
	        <td width="90px">原票型</td>
	        <td width="320px">
				<select name="old_ticket" id="old_ticket" data-toggle="selectpicker" data-rule="required;">
					<option value="">请选择</option>	
				<volist name="ticket" id="vo">
		          <option value="{$vo.priceid}" data-plan="{$vo.plan_id}">{$vo.title}</option>
		        </volist>
		        </select>
	        </td>
	      </tr>
	      <tr>
	        <td width="90px">转换数量</td>
	        <td width="320px">
	        	<input type="text" value="" name="number" class="form-control required" data-rule="required;">
	        </td>
	      </tr>
	      <tr>
	        <td width="90px">新票型</td>
	        <td width="320px"><input type="hidden" name="ticket.id" value="{$ticket_id}" data-rule="required;">
	    <input type="text" name="ticket.name" readonly value="{$ticket_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>2,'ifpro'=>1));}" data-group="ticket" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称"></td>
	      </tr>
	    </tbody>
	  </table>
	  <input type="hidden" value="{$activity}" name="act"></input>
	  <input type="hidden" value="" name="plan" id="selectPlan"></input>
	  <input type="hidden" value="{$sn}" name="sn"></input>
	</form>
</div>

<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
    <li>
      <button type="submit" class="btn-default" data-icon="save">提交</button>
    </li>
  </ul>
</div>
<script>
	$(function() {
		$("#old_ticket").change(function(){
			var plan = '';
		    plan = $(this).children('option:selected').data('plan');
		    console.log(plan);
		    $('#selectPlan').val(plan);
		});
	});
</script>