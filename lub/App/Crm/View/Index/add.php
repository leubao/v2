<form class="form-horizontal" action="{:U('Crm/Index/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">商户名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="商户名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">所属类型:</label>
    <input type="radio" name="type" value="1" <eq name="data['type']" value="1"> checked</eq>> 旅行社
    <input type="radio" name="type" value="2" <eq name="data['type']" value="2"> checked</eq>> 酒店
    <input type="radio" name="type" value="3" <eq name="data['type']" value="3"> checked</eq>> OTA
    <input type="radio" name="type" value="4" <eq name="data['type']" value="4"> checked</eq>> 代售点
    <input type="radio" name="type" value="5" <eq name="data['type']" value="5"> checked</eq>> 其它
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">地址:</label>
    <input type="text" name="address" class="form-control required" data-rule="required;" size="40" placeholder="地址">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">联系人:</label>
    <input type="text" name="contacts" class="form-control required" data-rule="required;" size="25" placeholder="联系人">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">联系人电话:</label>
    <input type="text" name="phone" class="form-control required" data-rule="required;" size="30" placeholder="联系电话">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">销售配额:</label>
    <input type="text" name="quota" class="form-control required" data-rule="required;" size="10" placeholder="1000">
  </div>
  <input type="hidden" name="level" value="{$Config['level_1']}">
  <div class="form-group">
    <label class="col-sm-2 control-label">管理员:</label>
    <input type="hidden" name="user.id" value="">
    <input type="text" name="user.name" disabled value="" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_user');}" data-group="user" data-width="600" data-height="445">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">直接出票:</label>
      <select name="print" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1">启用</option>
      <option value="0" selected>禁用</option>
  </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">门票打印:</label>
    <input type="radio" name="prints" data-toggle="icheck" value="1" <eq name="vo['prints']" value="1"> checked</eq> data-label="开启&nbsp;">
    <input type="radio" name="prints" data-toggle="icheck" value="0" <eq name="vo['prints']" value="0"> checked</eq> data-label="关闭">
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label">补贴方式:</label>
    <input type="radio" name="agent" data-toggle="icheck" value="1" <eq name="vo['rebate']" value="1"> checked</eq> data-label="授信额&nbsp;">
    <input type="radio" name="agent" data-toggle="icheck" value="2" <eq name="vo['rebate']" value="2"> checked</eq> data-label="现金">
  </div>
   <div class="form-group">
    <label class="col-sm-2 control-label">支付方式:</label>
    <input type="checkbox" name="isPay" data-toggle="icheck" value="1" <eq name="vo['isPay']" value="1"> checked</eq> data-label="授信额&nbsp;">
    <input type="checkbox" name="isPay" data-toggle="icheck" value="2" <eq name="vo['isPay']" value="2"> checked</eq> data-label="窗口支付&nbsp;">
    <input type="checkbox" name="isPay" data-toggle="icheck" value="3" <eq name="vo['isPay']" value="3"> checked</eq> data-label="在线支付">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">退票审核:</label>
    <input type="radio" name="refund" data-toggle="icheck" value="1" <eq name="vo['refund']" value="1"> checked</eq> data-label="无审核&nbsp;">
    <input type="radio" name="refund" data-toggle="icheck" value="2" <eq name="vo['refund']" value="2"> checked</eq> data-label="审核">
  </div>
</div>
<input type="hidden" name="groupid" value="{$groupid}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>