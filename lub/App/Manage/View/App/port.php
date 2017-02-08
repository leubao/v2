<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/App/port',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <fieldset style="height:100%;">
                <legend>所有接口</legend>
                <div style="height:94%; overflow:hidden;">
                   <volist name="port" id="vo" empty="$empty">  
                <input name="port[]" type="checkbox"  value="{$vo[id]}" <if condition="array_keys($proArr,$vo[id])">checked</if>>
                {$vo['name']}
</volist>
                </div>
            </fieldset></div>
</div>
<input name="id" type="hidden" value="{$id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>