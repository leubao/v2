<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
    <form id="j_pwschange_form" class="form-horizontal" action="{:U('');}" data-toggle="validate" method="post">
        <input type="hidden" name="id" value="{$userInfo.id}">
        <div class="form-group">
            <label for="j_pwschange_oldpassword" class="control-label x85">旧密码：</label>
            <input type="password" data-rule="required" name="password" value="" placeholder="旧密码" size="20">
        </div>
        <div class="form-group" style="margin: 20px 0 20px; ">
            <label for="j_pwschange_newpassword" class="control-label x85">新密码：</label>
            <input type="password" data-rule="新密码:required" name="new_password" value="" placeholder="新密码" size="20">
        </div>
        <div class="form-group">
            <label for="j_pwschange_secpassword" class="control-label x85">确认密码：</label>
            <input type="password" data-rule="required;match(new_password)" name="new_pwdconfirm" value="" placeholder="确认新密码" size="20">
        </div>
    </form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close">取消</button></li>
        <li><button type="submit" class=" btn btn-default">保存</button></li>
    </ul>
</div>