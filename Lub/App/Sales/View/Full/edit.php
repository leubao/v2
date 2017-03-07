<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Sales/Full/edit',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <table class="table table-striped table-bordered table-hover">
  <tbody>
    <tr>
      <td width="100px">姓名</td>
      <td><input type="text" name="nickname" value="{$data.nickname}" placeholder="姓名"></td>
    </tr>
      <td>电话</td>
      <td><input type="text" name="phone" value="{$data.phone}" placeholder="电话"></td>
    <tr>
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