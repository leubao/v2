<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Cashier/set_config',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent" style="padding: 15px">
  <div class="col-md-8">
    <fieldset>
      <legend>微信支付</legend>
      <div style="height:94%;">
        <table class="table  table-bordered">
          <tbody>
          <tr>
            <td width="120px">APPID:</td>
            <td><input type="text" name="wx_sub_appid" value="{$vo.wx_sub_appid}" size="10"><span class="remark">微信分配的公众账号ID</span></td>
          </tr>
               
          <tr>
            <td>商户号:</td>
            <td>
            <input type="text" name="wx_sub_mch_id" value="{$vo.wx_sub_mch_id}" size="10"><span class="remark">微信分配的子商户公众账号ID</span>
            </td>
          </tr>
          <tr>
            <td width="120px">常量KEY:</td>
            <td>
            <input type="text" name="wx_partnerkey" value="{$vo.wx_partnerkey}" size="20">
            </td>
          </tr>
        </tbody>
        </table>  
      </div>
    </fieldset>
    <fieldset>
      <legend>支付宝支付</legend>
      <div style="height:94%;">
        <table class="table  table-bordered">
          <tbody>
          <tr>
            <td width="120px">APPID:</td>
            <td><input type="text" name="wx_sub_appid" value="{$vo.wx_sub_appid}" size="10"><span class="remark">微信分配的公众账号ID</span></td>
          </tr>
               
          <tr>
            <td>商户号:</td>
            <td>
            <input type="text" name="wx_sub_mch_id" value="{$vo.wx_sub_mch_id}" size="10"><span class="remark">微信分配的子商户公众账号ID</span>
            </td>
          </tr>
          <tr>
            <td width="120px">常量KEY:</td>
            <td>
            <input type="text" name="wx_partnerkey" value="{$vo.wx_partnerkey}" size="20">
            </td>
          </tr>
        </tbody>
        </table>  
      </div>
    </fieldset>   
  </div>              
</div>
  <input name="product_id" value="{$pid}" type="hidden">
  <input name="wx_appid" value="{$vo.wx_appid}" type="hidden">
  <input name="wx_mch_id" value="{$vo.wx_mch_id}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li>
        <button type="button" class="btn-close" data-icon="close">取消</button>
      </li>
      <li>
        <button type="submit" class="btn-default" data-icon="save">保存</button>
      </li>
    </ul>
  </div>
</form>