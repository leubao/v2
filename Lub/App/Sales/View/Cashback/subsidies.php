<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Sales/Cashback/subsidies',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <div class="visible-print-block">
      <h3 align="center">渠道商提现确认单</h3>
      <span class="pull-left mb10">NO：{$sn}</span>
      <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
  </div>
  <table class="table table-striped table-bordered table-hover">
  <tbody>
    <tr>
      <td width="100px">收款人:</td>
      <td>{$data.nickname}</td>
      <td width="100px">支付方式</td>
      <td><select name="pay_type" class="required combox">
            <option selected value="0">===请选择===</option>
              <option value="1">现金</option>
              <option value="7">汇款</option>
              <option value="8">支票</option>
              <option value="9">转账</option>
          </select></td>
    </tr>
    <tr>
      <td>金额</td>
      <td colspan="2"><input type="text" name="money" value="" placeholder="金额"></td>
      <td></td>
    </tr>
    <tr>
      <td>备注1</td>
      <td colspan="3"><textarea name="remark" cols="55" rows="1"></textarea></td>
    </tr>
  </tbody>
</table>
</div>
<input name="uid" value="{$data.id}" type="hidden">
<input name="sn" value="{$sn}" type="hidden">
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">取消</button>
    </li>
    <li>
      <button type="submit" class="btn-default" data-icon="save">立即提现</button>
    </li>
  </ul>
</div>
</form>