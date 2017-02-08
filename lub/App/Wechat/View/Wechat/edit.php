<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Wechat/Wechat/edit',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">微信名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="30" value="{$data.name}" placeholder="微信名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">微信号:</label>
    <input type="text" name="wxid" class="form-control required" data-rule="required;" size="30" value="{$data.wxid}" placeholder="微信号">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">appID:</label>
    <input type="text" name="appid" class="form-control required" data-rule="required;" size="30" value="{$data.appid}" placeholder="appID">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">appsecret:</label>
    <input type="text" name="appsecret" class="form-control required" data-rule="required;" size="40" value="{$data.appsecret}" placeholder="appsecret">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Token:</label>
    <input type="text" name="token" class="form-control required" data-rule="required;" size="40" value="{$data.token}" placeholder="Token(令牌)">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">encodingASEKey:</label>
    <input type="text" name="encodingaeskey" class="form-control required" data-rule="required;" size="40" value="{$data.encodingaeskey}" placeholder="encodingaeskey">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">微信支付商户号:</label>
    <input type="text" name="mchid" class="form-control required" data-rule="required;" size="40" value="{$data.mchid}" placeholder="mchid">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">支付key:</label>
    <input type="text" name="mchkey" class="form-control required" data-rule="required;" size="40" value="{$data.mchkey}" placeholder="mchkey">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">类型:</label>
    <select name="type" class="form-control" style="width:auto;">
      <option value="1" <if condition="$data['type'] eq 1">selected</if>>普通订阅号</option>                
      <option value="2" <if condition="$data['type'] eq 2">selected</if>>认证订阅号/普通服务号</option>                
      <option value="3" <if condition="$data['type'] eq 3">selected</if>>认证服务号</option>                
      <option value="4" <if condition="$data['type'] eq 4">selected</if>>企业号</option>        
    </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
	    <option value="">状态</option>
	    <option value="1" <if condition="$data['status'] eq 1">selected</if>>启用</option>
      <option value="0" <if condition="$data['status'] eq 0">selected</if>>禁用</option>
	</select>
  </div>
</div>
<input type="hidden" name="id" value="{$data.id}"></input>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>