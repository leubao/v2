<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Sales/Cashback/back',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <table class="table table-striped table-bordered table-hover">
  <tbody>
    <tr>
      <td width="100px">姓名</td>
      <td>{$data.user_id|userName}</td>
      <td width="100px">申请时间</td>
      <td>{$data.createtime|datetime}</td>
    </tr>
    <tr>
      <td>微信openid</td>
      <td>{$data.openid}</td>
      <td>申请单号</td>
      <td>{$data.sn}</td> 
    </tr>
    <if condition="$data.status eq 1">
      <tr>
        <td>支付方式</td>
        <td>{$data.pay_type|pay}</td>
        <td>审核人</td>
        <td>{$data.userid|userName}</td> 
      </tr>
      <tr>
        <td>审核时间</td>
        <td>{$data.uptime|datetime}</td>
        <td></td>
        <td></td> 
      </tr>
    </if>
    <tr>
      <td>提现金额</td>
      <td colspan="3">{$data.money}</td>
    </tr>
    <tr>
      <td>备注1</td>
      <td colspan="3">{$data.remark}</td>
    </tr>
    <tr>
      <td>备注2</td>
      <if condition="$data.status eq 3">
      <td colspan="3"><textarea name="remark" cols="55" rows="1"></textarea></td>
      <else />
      <td colspan="3">{$data.win_remark}</td>
      </if>
    </tr>
  </tbody>
</table>
</div>
<input name="id" value="{$data.id}" type="hidden">
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">取消</button>
    </li>
    <if condition="$data.status eq 3">
    <li>
      <button type="submit" class="btn-default" data-icon="save">立即审核</button>
    </li>
    </if>
  </ul>
</div>
</form>