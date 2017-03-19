<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
  <table class="table table-striped table-bordered table-hover">
  <tbody>
    <tr>
      <td width="100px">姓名</td>
      <td width="200px">{$data.nickname}</td>
      <td width="100px">微信ID</td>
      <td>{$data.openid}</td>
    </tr>
    <tr>
      <td>分组</td>
      <td>{$data.groupid|crmgroupName}</td>
      <td>电话</td>
      <td>{$data.phone}<span class="glyphicon glyphicon-phone-alt call"></span></td>
    </tr>
    <tr>
      <td>余额</td>
      <td>{$data.cash}</td>
      <td>添加时间</td>
       <td>{$data.create_time|datetime}</td>
    </tr>
    <tr>
      <td>行业</td>
      <td>{$data.industry|industry}</td>
      <td>编号</td>
      <td>{$data.legally}</td>
    </tr>
    <tr>
      <td>审核</td>
      <td>{$data.status|status}</td>
      <td>分销类型</td>
      <td>{$data.type|sales_type}</td>
    </tr>
    <tr>
      <td>备注</td>
      <td colspan="3">{$data.remark}</td>
    </tr>
  </tbody>
</table>
</div>
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
  </ul>
</div>