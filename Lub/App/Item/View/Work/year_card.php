<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
	<form id="yearCardForm" action="{:U('Item/Work/year_card',array('menuid'=>$menuid));}" data-toggle="validate" data-alertmsg="false" novalidate="novalidate">
		
	<table class="table table-bordered mt20">
        <tbody>
            <tr>
                <td align='right'>姓名:</td>
                <td><input type="text" name="content" data-rule="required;" class="form-control required" size="20" placeholder="姓名"></td>
            </tr>
            <tr>
                <td align='right'>联系电话:</td>
                <td><input type="text" name="phone" data-rule="required;mobile" class="form-control required" size="20" placeholder="电话"></td>
            </tr>
            <tr>
                <td align='right'>身份证号:</td>
                <td><input type="text" name="idcard" data-rule="required;ID_card" class="form-control required" size="25" placeholder="身份证证号码"></td>
            </tr>
            <tr>
                <td align='right'>凭证类型:</td>
                <td>
                    <input type="radio" name="type" data-toggle="icheck" value="1" data-rule="checked" data-label="身份证&nbsp;&nbsp;">
                    <input type="radio" name="type" data-toggle="icheck" value="2" data-label="户口本&nbsp;&nbsp;">
                    <input type="radio" name="type" data-toggle="icheck" value="3" data-label="单位介绍信">
                </td>
            </tr>
            <tr>
                <td align='right'>备注:</td>
                <td><textarea name="remark"></textarea></td>
            </tr>
        </tbody>
    </table>
	</form>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
