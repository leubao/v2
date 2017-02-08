<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
  <table class="table table-bordered table-condensed">
    <tbody>
      <tr>
        <td><label class="control-label x85">姓名:</label>{$data.nickname}</td>
        <td><label class="control-label x85">用户名:</label>{$data.username}</td>
      </tr>
      <tr>
        <td><label class="control-label x85">电话:</label>{$data.phone}</td>
        <td><label class="control-label x85">E-mail:</label>{$data.email}</td>
      </tr>
      <tr>
        <td><label class="control-label x90">最后登陆时间:</label>{$data.last_login_time|date="Y-m-d H:i:s",###}</td>
        <td><label class="control-label x90">最后登陆IP:</label>{$data.last_login_ip}</td>
      </tr>
      <tr>
        <td><label class="control-label x85">状态:</label>{$data.status|status}</td>
        <td><label class="control-label x85">账号绑定情况:</label>...</td>
      </tr>
      <tr>
        <td><label class="control-label x85">默认产品:</label>{$data.defaultpro|productName}</td>
        <td><label class="control-label x85">所属角色:</label>{$data.role_id|roleName}</td>
      </tr>
      <tr>
        <td><label class="control-label x85">所属商户:</label>{$data.item_id|itemName}</td>
        <td><label class="control-label x85">登录场景:</label>{$data.is_scene|scene}</td>
      </tr>
      <if condition="$data.is_scene eq '2' ">
      <tr>
        <td><label class="control-label x85">分组:</label>{$data.group_id|status}</td>
        <td><label class="control-label x85">管理员:</label>{$data.salesman|userName}</td>
      </tr>
      <tr>
        <td><label class="control-label x85">所属公司:</label>{$data.cid|crmName}</td>
        <td><label class="control-label x85">支付权限:</label><if condition="$data.is_pay eq '1' ">授信额<elseif condition="$data.is_pay eq '2'"/>网银支付<else />授信+网银</if></td>
      </tr>
      <tr>
        <td><label class="control-label x85">可用金额:</label>{$data.cash}</td>
        <td></td>
      </tr>
      </if>
      <tr>
        <td><label class="control-label x85">备注:</label>{$data.remark}</td>
        <td></td>
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