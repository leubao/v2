<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Cashier/add_goods',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <div class="form-group">
    <label class="col-sm-2 control-label">商品名称:</label>
    <input type="text" name="title" class="form-control required" data-rule="required;" size="30" value="">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">价格:</label>
    <input type="text" name="price" class="form-control required" data-rule="required;" size="15" value="">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">结算价格:</label>
    <input type="text" name="discount" class="form-control required" data-rule="required;" size="15" value="">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">销售补贴:</label>
    <input type="text" name="rebate" class="form-control required" data-rule="required;" size="15" value="">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">销售场景:</label>
    <input type="checkbox" name="scene[]" value="1"> 窗口
    <input type="checkbox" name="scene[]" value="2"> 渠道版
    <input type="checkbox" name="scene[]" value="3"> 网站
    <input type="checkbox" name="scene[]" value="4"> 微信
    <input type="checkbox" name="scene[]" value="5"> API
    <input type="checkbox" name="scene[]" value="6"> 自助机
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">关联产品:</label>
    <input type="hidden" name="product.id" value="">
    <input type="text" name="product.name" data-rule="required" value="" size="30" data-toggle="lookup" data-url="{:U('Manage/index/public_product',array('ifadd'=>'1'));}" data-group="product" data-width="700" data-height="300">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1">启用</option>
      <option value="0">禁用</option>
  </select>
  </div>
</div><div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>