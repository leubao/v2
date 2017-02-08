<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Work/agree',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <table class="table table-striped table-bordered">
  <tbody>
    <tr>
      <td width="90px">销售计划</td>
      <td width="320px">{$data.plan_id|planShow}</td>
      <td width="90px">单号</td>
      <td>{$data.order_sn}</td>
    </tr>
    <tr>
       <td>申请人</td>
	    <td>{$data.applicant|userName}</td> 
	    <td>申请金额</td>
	    <td>{$data.money}</td>      
    </tr>
    <tr>
      <td>退款方式</td>
       <td><if condition="$data['re_type'] eq 1">退还到授信额<else />现金</if></td>    
       <td>渠道商</td>
      <td>{$data.crm_id|crmName}</td>  
    </tr>
    <tr>
      <td>状态</td>
      <td>{$data.status|refund_status}</td>
      <td>创建时间</td>
      <td>{$data.createtime|date="Y-m-d H:i:s",###} </td>
    </tr>
    <tr>
      <td>申请说明</td>
      <td colspan="3">{$data.reason}</td>
    </tr>
    <if condition="$data['status'] eq 1">
    <tr>
      <td>操作</td>
      <td colspan="3"><if condition="$data['status'] eq 1">
            <input type="radio" name="type" value="2" checked="checked"  onclick="$('#r_p').css('display','none')"  />驳回申请
            <input type="radio" name="type" value="1" onclick="$('#r_p').css('display','block')"/>同意申请
          <elseif condition="$data['status'] eq 2" />    
            <input type="radio" name="type" value="1" onclick="$('#r_p').css('display','block')"/>同意申请
          <else />
            无可用操作
          </if></td>
    </tr>
     <tr>
      <td>手续费</td>
      <td colspan="3" id="r_p" style="display: none;">
		    <input type="radio" class="required" name="poundage" value="1" />全额退款
        <input type="radio" class="required" name="poundage" value="2" />扣除10%
        <input type="radio" class="required" name="poundage" value="3" />扣除20%
      </td>
    </tr>
    <tr>
      <td>处理说明</td>
      <td colspan="3"><textarea name="against_reason" cols="30" rows="2"></textarea></td>
    </tr>
    <else />
    <tr id="poundage">
      <td>处理人</td>
      <td>{$data.user_id|userName}</td> 
  	  <td>处理金额</td>
  	  <td>{$data.re_money}</td>  
    </tr>
    <tr>
      <td>处理说明</td>
      <td colspan="3">{$data.against_reason}</td>
    </tr>
    </if>
  </tbody>
</table>
</div>
<input type="hidden" name="sn" value="{$data.order_sn}" />
<input type="hidden" name="id" value="{$data.id}" />
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
</form>