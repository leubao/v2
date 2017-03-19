<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Sales/Full/edit',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <table class="table table-striped table-bordered table-hover">
  <tbody>
    <tr>
      <td width="100px">姓名</td>
      <td><input type="text" name="nickname" value="{$data.nickname}" placeholder="姓名"></td>
    </tr>
    <tr>
      <td>分组</td>
      <td><select name="groupid" class="required combox">
            <option selected value="0">===请选择===</option>
            <volist name="group" id="v">
              <option value="{$v.id}" <if condition="$data['groupid'] eq $v['id']">selected</if>>{$v.name}</option>
            </volist>
          </select></td>
    </tr>
    <tr>
      <td>所属行业</td>
      <td><select name="industry" data-toggle="selectpicker">
        <option value="">所属行业</option>
        <option value="1" <if condition="$data.industry eq '1'">selected</if>>导游</option>
        <option value="2" <if condition="$data.industry eq '2'">selected</if>>运输</option>
        <option value="3" <if condition="$data.industry eq '3'">selected</if>>餐饮</option>
        <option value="4" <if condition="$data.industry eq '4'">selected</if>>商户</option>
        <option value="5" <if condition="$data.industry eq '5'">selected</if>>住宿</option>
        <option value="6" <if condition="$data.industry eq '6'">selected</if>>其它</option>
    </select></td>
    </tr>
    <tr>
      <td>状态</td>
      <td><select name="status" class="required" data-toggle="selectpicker" data-rule="required">
        <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
        <option value="2" <if condition="$data['status'] eq 2">selected</if>>停用</option>
    </select></td>
    </tr>
    <tr>
      <td>备注</td>
      <td><textarea name="remark" cols="45" rows="1">{$data.remark}</textarea></td>
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
    <li>
      <button type="submit" class="btn-default" data-icon="save">更新</button>
    </li>
  </ul>
</div>
</form>