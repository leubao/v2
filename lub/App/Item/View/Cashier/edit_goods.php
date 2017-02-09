<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Cashier/edit_goods',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <div class="form-group">
    <label class="col-sm-2 control-label">商品名称:</label>
    <input type="text" name="title" class="form-control required" data-rule="required;" size="30" value="{$data.title}">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">销售场景:</label>
    <input type="checkbox" name="scene[]" value="1" <if condition="in_array('1',explode(',',$data['scene']))">checked</if>> 窗口
    <input type="checkbox" name="scene[]" value="2" <if condition="in_array('2',explode(',',$data['scene']))">checked</if>> 渠道版
    <input type="checkbox" name="scene[]" value="3" <if condition="in_array('3',explode(',',$data['scene']))">checked</if>> 网站
    <input type="checkbox" name="scene[]" value="4" <if condition="in_array('4',explode(',',$data['scene']))">checked</if>> 微信
    <input type="checkbox" name="scene[]" value="5" <if condition="in_array('5',explode(',',$data['scene']))">checked</if>> API
    <input type="checkbox" name="scene[]" value="6" <if condition="in_array('6',explode(',',$data['scene']))">checked</if>> 自助机
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">关联产品:</label>
    <input type="hidden" name="product.id" value="{$data.product}">
    <input type="text" name="product.name" data-rule="required" value="<volist name="data['products']" id="vo">{$vo|productName} </volist>" size="30" data-toggle="lookup" data-url="{:U('Manage/index/public_product',array('ifadd'=>'1'));}" data-group="product" data-width="700" data-height="300">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
        <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
        <option value="0" <if condition="$data['status'] eq 0">selected</if>>禁用</option>
    </select>
  </div>
</div>
<input name="id" type="hidden" value="{$data.id}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>