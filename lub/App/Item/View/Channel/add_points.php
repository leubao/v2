<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Channel/add_points',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
   <div class="form-group">
    <label class="col-sm-2 control-label">处罚对象:</label>
    <input type="hidden" name="channel.id" value="">
    <input type="text" name="channel.name" data-rule="required" value="" size="10" data-toggle="lookup" data-url="{:U('Manage/index/public_channel',array('ifadd'=>'2','level'=>'16'));}" data-group="channel" data-width="700" data-height="300">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">类型:</label>
    <input type="radio" name="type" data-toggle="icheck" value="1" checked data-label="标准处罚&nbsp;">
    <input type="radio" name="type" data-toggle="icheck" value="2" data-label="自定义处罚">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">分值:</label>
    <input type="text" name="score" class="form-control required" data-rule="required;" size="15" placeholder="分值">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">说明:</label>
    <textarea name="remark" class="form-control" rows="3" size="40"></textarea>
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>