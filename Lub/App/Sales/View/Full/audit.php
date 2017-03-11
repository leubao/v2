<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Sales/Full/audit',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <table class="table table-striped table-bordered table-hover">
  <tbody>
    <tr>
      <td width="100px">姓名</td>
      <td>{$data.nickname}</td>
      <td width="100px">添加时间</td>
      <td>{$data.create_time|datetime}</td>
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
      <td>销售配额</td>
      <td>共享全员销售配额</td> 
    </tr>
    <tr>
      <td>开户行</td>
      <td>{$data.info.bank}</td>
      <td>卡号</td>
      <td>{$data.info.bank_accountl}</td>
    </tr>
    <tr>
      <td>审核</td>
      <td><if condition="$data.status eq 3">
        <input type="radio" name="status" data-toggle="icheck" value="1" data-label="通过&nbsp;">
        <input type="radio" name="status" data-toggle="icheck" value="0" data-label="拒绝&nbsp;">
        <else />
        完成审核
        </if></td>
      <td>分销类型</td>
      <td><select name="type" class="required combox">
            <option selected value="0">===请选择===</option>
            <option selected value="8" <if condition="$data['type'] eq '8'">selected</if>>全员销售</option>
            <option selected value="9" <if condition="$data['type'] eq '9'">selected</if>>三级分销</option>
          </select></td>
    </tr>
    <tr>
      <td>客户分组</td>
      <td>
        <select name="groupid" class="required combox">
            <option selected value="0">===请选择===</option>
          <volist name="group" id="v">
            <option value="{$v.id}" <if condition="$data['groupid'] eq $v['id']">selected</if>>{$v.name}</option>
          </volist>
        </select>
      </td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>备注</td>
      <td colspan="3"><textarea name="remark" cols="55" rows="1">{$data.remark}</textarea></td>
    </tr>
  </tbody>
</table>
</div>
<input name="phone" value="{$data.phone}" type="hidden">
<input name="name" value="{$data.nickname}" type="hidden">
<input name="id" value="{$data.id}" type="hidden">
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">取消</button>
    </li>
    <li>
      <button type="submit" class="btn-default" data-icon="save">立即审核</button>
    </li>
  </ul>
</div>
</form>