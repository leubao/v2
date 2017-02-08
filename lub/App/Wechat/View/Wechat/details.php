<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Wechat/Wechat/setup',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<table class="table  table-bordered">
	    <tbody>
	      <tr>
	        <td width="120px">APPID:</td>
	        <td><input type="text" name="appid" class="form-control" size="30" value="{$vo.appid}" placeholder="appID">
	        </td>
	      </tr>
	      <tr>
	        <td width="120px">appSecret:</td>
	        <td><input type="text" name="appsecret" class="form-control" size="40" value="{$vo.appsecret}" placeholder="appsecret">
	        </td>
	      </tr>
	      <tr>
	        <td width="120px">Token:</td>
	        <td><input type="text" name="token" class="form-control" size="40" value="{$vo.token}" placeholder="Token">
	        </td>
	      </tr>
	      <tr>
	        <td width="120px">encodingASEKey:</td>
	        <td><input type="text" name="encoding" class="form-control" value="{$vo.encoding}" size="40" placeholder="encodingASEKey">
	        </td>
	      </tr>
	      <tr>
	        <td width="120px">URL:</td>
	        <td><input type="text" name="wxurl" class="form-control" value="{$vo.wxurl}" size="40" placeholder="url">
	        </td>
	      </tr>
	      <tr>
	        <td width="120px">商户id:</td>
	        <td><input type="text" name="mchid" class="form-control" value="{$vo.mchid}" size="20" placeholder="商户id">
	        </td>
	      </tr>
	      <tr>
	        <td width="120px">商户支付密钥Key:</td>
	        <td><input type="text" name="mchkey" class="form-control" value="{$vo.mchkey}" size="40" placeholder="商户支付密钥Key">
	        </td>
	      </tr>
	    </tbody>
  	</table>
 </div>
 <input type="hidden" name="product_id" value="{$product}">
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>