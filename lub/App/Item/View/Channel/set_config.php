<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Channel/set_config',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent" style="padding: 15px">
  <table class="table  table-bordered">
    <tbody>
      <tr>
        <td width="120px">渠道考核制度:</td>
        <td><input type="radio" name="kpi_channel" data-toggle="icheck" value="1" <eq name="vo['kpi_channel']" value="1"> checked</eq> data-label="开启&nbsp;">
      <input type="radio" name="kpi_channel" data-toggle="icheck" value="0" <eq name="vo['kpi_channel']" value="0"> checked</eq> data-label="关闭">
        </td>
      </tr>
      <tr>
        <td width="120px">初始考核分:</td>
        <td>
        <input type="text" name="channel_score" value="{$vo.channel_score}" size="10"><span class="remark">初始考核分确定后更改无效</span>
        </td>
      </tr>
      <tr>
        <td width="120px">全年任务数:</td>
        <td>
        <input type="text" name="channel_ticket_num" value="{$vo.channel_ticket_num}" size="20"><span class="remark">门票张数</span>
        </td>
      </tr>
      <tr>
        <td width="120px">任务数考核周期:</td>
        <td>
        <input type="text" name="channel_ticket_cycle" value="{$vo.channel_ticket_cycle}" size="20"><span class="remark">如3,35%,-15|4,55%,-30[表示前三个月完成总任务的35%,未完成一次扣绩效分15分]</span>
        </td>
      </tr>
      <tr>
        <td width="120px">账户最低余额:</td>
        <td><input type="text" name="money_low" value="{$vo.money_low}" size="20"><span class="remark">账户余额最低限如20000</span></td>
      </tr>
      <tr>
        <td width="120px">低额持续时间:</td>
        <td>
        <input type="text" name="money_low_time" value="{$vo.money_low_time}" size="20"><span class="remark">低于最低余额持续时间[单位为小时]</span>
        </td>
      </tr>
      <tr>
        <td width="120px">余额扣分准则:</td>
        <td>
        <input type="text" name="money_low_cycle" value="{$vo.money_low_cycle}" size="20"><span class="remark">如-10[在约定时间内未完成充值扣10分]</span>
        </td>
      </tr>
      <tr>
        <td width="120px">渠道商单场配额:</td>
        <td>
        <input type="text" name="channel_quota" value="{$vo.channel_quota}" size="20"><span class="remark">渠道商单场票权</span>
        </td>
      </tr>
    </tbody>
  </table>                   
</div>
  <input name="product_id" value="{$pid}" type="hidden">
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