<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Order/to_print',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
 <div class="form-group">
 <label class="col-sm-4control-label">授权账户:</label>
 </div>
  <div class="form-group">
    <select name="user" class="required" data-toggle="selectpicker" data-rule="required">
        <option value="">===选择授权账户===</option>
        <volist name="user" id="user">
          <option value="{$user.id}">{$user.name}</option>
        </volist>
      </select>
  </div>
  <div class="form-group">
    <input type="password" name="password" size="15" class="form-control required" data-rule="required;" placeholder="输入密码"/>
  </div>
</div>
<input type="hidden" name="sn" value="{$data.sn}">
<input type="hidden" name="plan_id" value="{$data.plan_id}">
<input type="hidden" name="act" value="{$data.act}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">提交</button></li>
    </ul>
</div>
</form>