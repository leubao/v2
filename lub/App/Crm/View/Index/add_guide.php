<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Crm/Index/add_guide',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-3 control-label">姓名:</label>
    <input type="text" name="username" class="form-control required" data-rule="required;" size="20" placeholder="姓名">
  </div>
  
  <div class="form-group">
    <label class="col-sm-3 control-label">手机号码:</label>
    <input type="text" name="phone" class="form-control required" size="20" data-rule="required;name;remote[get:{:U('Item/Check/public_check_name',array('ta'=>19))}]" placeholder="手机号码">
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">身份证:</label>
    <input type="text" name="legally" class="form-control required" data-rule="required;ID_card;remote[get:{:U('Item/Check/public_check_name',array('ta'=>20))}]" size="20" placeholder="身份证号码">
  </div>
  <div class="form-group">
      <label class="col-sm-3 control-label">状态：</label>
      <select name="status" class="required combox">
        <option value="1" selected>启用</option>
        <option value="0">不启用</option>
      </select>
  </div>
</div>
<input name="groupid" value="{$groupid}" type="hidden"> 
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>