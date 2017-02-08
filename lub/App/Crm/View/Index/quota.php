<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Crm/Index/quota',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <table class="table table-striped table-bordered">
    <tbody>
      <tr>
        <td width="100px">当前客户</td>
        <td><strong>{$data.id|crmName}</strong></td>
      </tr>
      <tr>
        <td>产品列表</td>
        <td>
        <volist name="quota" id="vo">
        {$vo.product_id|productName} 
        <input type="text" name="quota[{$vo.product_id}]" class="form-control" size="5" value="{$vo.quota}" placeholder="0"><br>
        </volist>
        </td>
      </tr>
      
    </tbody>
  </table>
  
</div>
<input type="hidden" name="crm_id" value="{$data.id}"/>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>