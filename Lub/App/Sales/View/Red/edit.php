<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Sales/Red/edit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <div class="form-group">
    <label class="col-sm-2 control-label">活动名称:</label>
    <input type="text" name="act_name" class="form-control required" data-rule="required;" value="{$data.act_name}" size="30" placeholder="不超过10个汉字，含标点">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">商户名称:</label>
    <input type="text" name="send_name" class="form-control required" data-rule="required;" value="{$data.send_name}" size="30" placeholder="不超过10个汉字，含标点">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">祝福语:</label>
    <textarea name="wishing" class="form-control" rows="3" size="40">{$data.wishing}</textarea>
    <span class="remark">不超过20个汉字，含标点</span>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">说明:</label>
    <textarea name="remark" class="form-control" rows="3" size="40">{$data.remark}</textarea>
    <span class="remark">不超过20个汉字，含标点</span>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">场景:</label>
    <select name="scene_id" data-toggle="selectpicker">
      <option value="">场景</option>
      <option value="PRODUCT_1" <if condition="$data['scene_id'] eq PRODUCT_1">selected</if>>商品促销</option>
      <option value="PRODUCT_2" <if condition="$data['scene_id'] eq PRODUCT_2">selected</if>>抽奖</option>
      <option value="PRODUCT_3" <if condition="$data['scene_id'] eq PRODUCT_3">selected</if>>虚拟物品兑奖</option>
      <option value="PRODUCT_4" <if condition="$data['scene_id'] eq PRODUCT_4">selected</if>>企业内部福利</option>
      <option value="PRODUCT_5" <if condition="$data['scene_id'] eq PRODUCT_5">selected</if>>渠道分润</option>
      <option value="PRODUCT_6" <if condition="$data['scene_id'] eq PRODUCT_6">selected</if>>保险回馈</option>
      <option value="PRODUCT_7" <if condition="$data['scene_id'] eq PRODUCT_7">selected</if>>彩票派奖</option>
      <option value="PRODUCT_8" <if condition="$data['scene_id'] eq PRODUCT_8">selected</if>>税务刮奖</option>
    </select>
    <span class="remark">金额超过200元必须选择</span>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
      <option value="0" <if condition="$data['status'] eq 0">selected</if>>禁用</option>
  </select>
  </div>
</div>
<input name="id" value="{$data.id}" type="hidden">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>