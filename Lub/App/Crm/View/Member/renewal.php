<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
	<form id="yearCardForm" action="{:U('Crm/Member/renewal',array('menuid'=>$menuid));}" data-toggle="validate" data-alertmsg="false" novalidate="novalidate">
	<table class="table table-bordered mt20">
        <tbody>
            <tr>
                <td align='right'>姓名:</td>
                <td><input type="text" name="content" class="form-control" size="20" placeholder="姓名" value="{$data.nickname}" disabled="true"></td>
            </tr>
            <tr>
                <td align='right'>到期时间:</td>
                <td>{$data.endtime|date='Y-m-d',###}</td>
            </tr>
            <tr>
                <td align='right'>当前类型:</td>
                <td>{$data.group_id|memGroup}</td>
            </tr>
            <tr>
                <td align="right">续期类型:</td>
                <td><select class="required" name="group" id="block_entire" data-toggle="selectpicker">
                    <option value="">续期类型</option>
                    <volist name="type" id="vo">
                      <option value="{$vo.id}" data-money="{$vo.money}">{$vo.title} {$vo.money}元</option>
                    </volist>
                  </select>
                </td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" name="renewal" value="{$data.group_id}">
    <input type="hidden" name="id" value="{$data.id}">
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
