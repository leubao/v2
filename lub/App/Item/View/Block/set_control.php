<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Block/set_control',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
<table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td width="100px">当前计划</td>
          <td><strong>{$ginfo.plan|planShow}</strong></td>
        </tr>
        <tr>
          <td width="100px">操作类型</td>
          <td><input type="radio" name="type" value="2" data-rule="checked" data-toggle="icheck" data-label="控座 &nbsp;"><input type="radio" name="type" value="1" data-rule="checked" data-toggle="icheck" data-label="释放座位"></td>
        </tr>
        <tr>
          <td width="100px">操作模板</td>
          <td>
          <volist name="data" id="vo">
          <p>
          <input type="checkbox" name="control[]" value="{$vo.id}"> {$vo.name}
          </p>
          </volist>
        </td>
        </tr>
        </tbody>
    </table>
</div>
<input type="hidden" name="plan" value="{$ginfo['plan']}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">设置</button></li>
    </ul>
</div>
</form>