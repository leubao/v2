<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/Product/edit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">产品名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" value="{$data.name}" size="40" placeholder="应用名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">产品类型:</label>
    <select name="type" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1" <eq name="data.type" value='1'>selected</eq>>剧院产品</option>
      <option value="2" <eq name="data.type" value='2'>selected</eq>>景区产品</option>
    </select>
  </div>
  <div class="juyuan" style="display: none">
  <div class="form-group">
    <label class="col-sm-2 control-label">剧场:</label>
    <select name="type" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1" <eq name="data.type" value='1'>selected</eq>>剧院产品</option>
      <option value="2" <eq name="data.type" value='2'>selected</eq>>景区产品</option>
    </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">座位模板:</label>
    <select name="type" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1" <eq name="data.type" value='1'>selected</eq>>剧院产品</option>
      <option value="2" <eq name="data.type" value='2'>selected</eq>>景区产品</option>
    </select>
  </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">产品描述:</label>
    <textarea name="content" cols="30" >{$data.content}</textarea>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="">状态</option>
      <option value="1" <eq name="data.status" value='1'>selected</eq>>启用</option>
      <option value="0" <eq name="data.status" value='0'>selected</eq>>禁用</option>
    </select>
  </div>
</div>
<input type="hidden" name="id" value="{$data.id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>