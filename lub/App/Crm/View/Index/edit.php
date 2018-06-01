<form class="form-horizontal" action="{:U('Crm/Index/edit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">商户名称:</label>
    <input type="text" name="name" value="{$data.name}" class="form-control required" data-rule="required;" size="40" placeholder="商户名称">
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
    <input type="text" name="address" class="form-control required" value="{$data.address}" data-rule="required;" size="40" placeholder="地址">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">联系人:</label>
    <input type="text" name="contacts" class="form-control required" value="{$data.contacts}" data-rule="required;" size="25" placeholder="联系人">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">联系人电话:</label>
    <input type="text" name="phone" class="form-control required" value="{$data.phone}" data-rule="required;" size="30" placeholder="联系电话">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">管理员:</label>
    <input type="hidden" name="user.id" value="{$data.salesman}">
    <input type="text" name="user.name" disabled value="{$data.salesman|userName}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_user');}" data-group="user" data-width="600" data-height="445">
  </div>
  
  <div class="form-group">
    <label class="col-sm-2 control-label">所属分组:</label>
    <select name="groupid" class="required" data-toggle="selectpicker" data-rule="required">
    <volist name="group" id="vo">
      <option value="{$vo.id}" <if condition="$vo['id'] eq $data['groupid']">selected</if>>{$vo.name}</option>
    </volist>
  </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">门票打印:</label>
    <input type="radio" name="prints" data-toggle="icheck" value="1" <eq name="data.param.prints" value="1"> checked</eq> data-label="开启&nbsp;">
    <input type="radio" name="prints" data-toggle="icheck" value="0" <eq name="data.param.prints" value="0"> checked</eq> data-label="关闭">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">补贴方式:</label>
    <input type="radio" name="rebate" data-toggle="icheck" value="1" <eq name="data.param.rebate" value="1"> checked</eq> data-label="授信额&nbsp;">
    <input type="radio" name="rebate" data-toggle="icheck" value="2" <eq name="data.param.rebate" value="2"> checked</eq> data-label="现金">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">支付方式:</label>
    <input type="checkbox" name="isPay[]" data-toggle="icheck" value="1" <if condition="in_array('1',explode(',',$data['param']['ispay']))">checked</if> data-label="授信额&nbsp;">
    <input type="checkbox" name="isPay[]" data-toggle="icheck" value="2" <if condition="in_array('2',explode(',',$data['param']['ispay']))">checked</if> data-label="窗口支付&nbsp;">
    <input type="checkbox" name="isPay[]" data-toggle="icheck" value="3" <if condition="in_array('3',explode(',',$data['param']['ispay']))">checked</if> data-label="在线支付">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">退票审核:</label>
    <input type="radio" name="refund" data-toggle="icheck" value="1" <eq name="vo['refund']" value="1"> checked</eq> data-label="无审核&nbsp;">
    <input type="radio" name="refund" data-toggle="icheck" value="2" <eq name="vo['refund']" value="2"> checked</eq> data-label="审核">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="">状态</option>
      <option value="1" <eq name="data.status" value="1">selected</eq>>启用</option>
      <option value="0" <eq name="data.status" value="0">selected</eq>>禁用</option>
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