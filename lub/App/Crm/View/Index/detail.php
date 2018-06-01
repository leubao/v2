<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
<if condition="$type neq '4'"> 
  <!-- 企业客户 -->
  <table class="table table-striped table-bordered table-hover">
    <tbody>
      <tr>
        <td width="100px">名称</td>
        <td>{$data.name}</td>
        <td width="100px">添加时间</td>
        <td>{$data.create_time|datetime}</td>
      </tr>
      <tr>
        <td>联系人</td>
        <td>{$data.contacts}</td>
        <td>电话</td>
        <td>{$data.phone}</td>
      </tr>
      <tr>
        <td>分组</td>
        <td>{$data.groupid|crmgroupName}</td>
        <td>管理员</td>
        <td>{$data.salesman|userName}</td>
      </tr>
      <tr>
        <td>余额</td>
        <td>{$data.cash}</td>
        <td>销售配额</td>
        <td>{$data.quota}</td> 
      </tr>
      <tr>
        <td>开户行</td>
        <td>{$data.bank}</td>
        <td>卡号</td>
        <td>{$data.bank_accountl}</td>
      </tr>
      <tr>
        <td>上级</td>
        <td>{$data.f_agents|crmName}</td>
        <td>级别</td>
        <td>{$data.level}</td>
      </tr>
      <tr>
        <td>编号</td>
        <td>{$data.incode}</td>
        <td>邮箱</td>
        <td></td>
      </tr>
      <tr>
        <td>地址</td>
        <td colspan="3">{$data.address}</td>
      </tr>
      <tr>
        <td>备注</td>
        <td colspan="3"></td>
      </tr>
    </tbody>
  </table>
<else />
<!-- 个人客户 -->
  <table class="table table-striped table-bordered table-hover">
    <tbody>
      <tr>
        <td width="100px">名称</td>
        <td>{$data.nickname}</td>
        <td width="100px">添加时间</td>
        <td>{$data.create_time|datetime}</td>
      </tr>
      <tr>
        <td>联系人</td>
        <td>{$data.nickname}</td>
        <td>电话</td>
        <td>{$data.phone}</td>
      </tr>
      <tr>
        <td>分组</td>
        <td>{$data.groupid|crmgroupName}</td>
        <td>管理员</td>
        <td>{$data.salesman|userName}</td>
      </tr>
      <tr>
        <td>余额</td>
        <td>{$data.cash}</td>
        <td>销售配额</td>
        <td>{$data.quota}</td> 
      </tr>
      <tr>
        <td>QQ</td>
        <td></td>
        <td>身份证号</td>
        <td>{$data.legally}</td>
      </tr>
      <tr>
        <td>备注</td>
        <td colspan="3">{$data.remark}</td>
      </tr>
    </tbody>
  </table>
</if>
</div>
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
  </ul>
</div>