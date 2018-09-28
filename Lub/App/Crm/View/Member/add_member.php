<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
	<form id="yearCardForm" action="{:U('Crm/Member/add_member',array('menuid'=>$menuid));}" data-toggle="validate" data-alertmsg="false" novalidate="novalidate">
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
                <td><input type="text" name="idcard" data-rule="required;ID_card;remote[get:{:U('Item/Check/public_check_name',array('ta'=>51))}]" class="form-control required" size="25" placeholder="身份证证号码"></td>
            </tr>
            <tr>
                <td align="right">会员类型:</td>
                <td><select class="required" name="group" id="block_entire" data-toggle="selectpicker">
                    <option value="">会员类型</option>
                    <volist name="type" id="vo">
                      <option value="{$vo.id}" data-money="{$vo.money}">{$vo.title} {$vo.money}元</option>
                    </volist>
                  </select>
                </td>
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
<script>
    
</script>
