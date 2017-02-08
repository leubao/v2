<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Channel/set_config',array('menuid'=>$menuid))}" method="post" data-toggle="validate">
<div class="bjui-pageContent tableContent">
  <table class="table  table-bordered">
    <tbody>
      <tr>
        <td width="120px">窗口渠道售票:</td>
        <td><input type="radio" name="window_channel" data-toggle="icheck" value="1" <eq name="vo['window_channel']" value="1"> checked</eq> data-label="开启&nbsp;">
        <input type="radio" name="window_channel" data-toggle="icheck" value="0" <eq name="vo['window_channel']" value="0"> checked</eq> data-label="关闭">
        <span class="remark">窗口是否可售团队票</span>
        </td>
      </tr>
      <tr>
        <td width="120px">代理商制度:</td>
        <td><input type="radio" name="agent" data-toggle="icheck" value="1" <eq name="vo['agent']" value="1"> checked</eq> data-label="开启&nbsp;">
      <input type="radio" name="agent" data-toggle="icheck" value="0" <eq name="vo['agent']" value="0"> checked</eq> data-label="关闭">
        </td>
      </tr>
      <tr>
        <td width="120px">销售配额:</td>
        <td>
        <input type="radio" name="quota" data-toggle="icheck" value="1" <eq name="vo['quota']" value="1"> checked</eq> data-label="开启&nbsp;">
        <input type="radio" name="quota" data-toggle="icheck" value="0" <eq name="vo['quota']" value="0"> checked</eq> data-label="关闭">
        </td>
      </tr>
      <tr>
        <td width="120px">退过期票:</td>
        <td>
        <input type="radio" name="plan_refund" data-toggle="icheck" value="0" <eq name="vo['plan_refund']" value="0"> checked</eq> data-label="开启&nbsp;">
        <input type="radio" name="plan_refund" data-toggle="icheck" value="1" <eq name="vo['plan_refund']" value="1"> checked</eq> data-label="关闭">
        </td>
      </tr>
      <tr>
        <td width="120px">黑名单管理:</td>
        <td>
        <input type="radio" name="black" data-toggle="icheck" value="1" <eq name="vo['black']" value="1"> checked</eq> data-label="开启&nbsp;">
        <input type="radio" name="black" data-toggle="icheck" value="0" <eq name="vo['black']" value="0"> checked</eq> data-label="关闭">
        </td>
      </tr>
      <tr>
        <td width="120px">渠道销售配额:</td>
        <td>
       <input type="text" name="channel_quota" value="{$vo.channel_quota}" size="10"><span class="remark">开启销售配额后，单计划默认配额</span>
        </td>
      </tr>
      <tr>
        <td width="120px">渠道订单限额:</td>
        <td>
       <input type="text" name="channel_order" value="{$vo.channel_order}" size="10"><span class="remark">渠道版单笔定单最大预定数</span>
        </td>
      </tr>
      <tr>
        <td width="120px">散客订单限额:</td>
        <td>
       <input type="text" name="retail_order" value="{$vo.retail_order}" size="10"><span class="remark">官网、微信售票单笔定单最大预定数</span>
        </td>
      </tr>
      <tr>
        <td width="120px">窗口结算方式:</td>
        <td>
        <input type="radio" name="settlement" data-toggle="icheck" value="1" <eq name="vo['settlement']" value="1"> checked</eq> data-label="票面价计算&nbsp;">
        <input type="radio" name="settlement" data-toggle="icheck" value="2" <eq name="vo['settlement']" value="2"> checked</eq> data-label="结算价计算">
        </td>
      </tr>
      <tr>
        <td width="120px">渠道出票:</td>
        <td>
        <input type="radio" name="channel_print" data-toggle="icheck" value="1" <eq name="vo['channel_print']" value="1"> checked</eq> data-label="开启&nbsp;">
      <input type="radio" name="channel_print" data-toggle="icheck" value="0" <eq name="vo['channel_print']" value="0"> checked</eq> data-label="关闭">
        </td>
      </tr>
      <tr>
        <td width="120px">报表统计方式:</td>
        <td>
        <input type="radio" name="report" data-toggle="icheck" value="1" <eq name="vo['report']" value="1"> checked</eq> data-label="按销售日期&nbsp;">
      <input type="radio" name="report" data-toggle="icheck" value="0" <eq name="vo['report']" value="0"> checked</eq> data-label="按场次日期">
        </td>
      </tr>
       <tr>
        <td width="120px">全员销售:</td>
        <td>
        <input type="radio" name="full_sales" data-toggle="icheck" value="1" <eq name="vo['full_sales']" value="1"> checked</eq> data-label="开启&nbsp;">
      <input type="radio" name="full_sales" data-toggle="icheck" value="0" <eq name="vo['full_sales']" value="0"> checked</eq> data-label="关闭">
        </td>
      </tr>
      <tr>
        <td width="120px">渠道停止售票时间:</td>
        <td>
        <input type="text" name="channel_time" value="{$vo.channel_time}" size="10"><span class="remark">如开演前30分钟，开演后-10分钟,0为开演即停止售票</span>
        </td>
      </tr>
      <tr>
        <td width="120px">默认(计划)时间:</td>
        <td>
        <label for="j_dialog_operation" class="control-label x90">开始时间:</label>
        <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="plan_start_time" value="{$vo.plan_start_time}">
        <label for="j_dialog_operation" class="control-label x90">结束时间:</label>
        <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="plan_end_time" value="{$vo.plan_end_time}">
        </td>
      </tr>
      <tr>
        <td width="120px">窗口核减:</td>
        <td>
        <input type="radio" name="win_subtract" data-toggle="icheck" value="1" <eq name="vo['win_subtract']" value="1"> checked</eq> data-label="开启&nbsp;">
        <input type="radio" name="win_subtract" data-toggle="icheck" value="0" <eq name="vo['win_subtract']" value="0"> checked</eq> data-label="关闭">
        </td>
      </tr>
      <tr>
        <td width="120px">渠道核减:</td>
        <td>
        <input type="text" data-toggle="datepicker" data-pattern='HH:mm' name="subtract_time" value="{$vo.subtract_time}">
        <span class="remark">渠道最后核减时间</span>
        </td>
      </tr>
      <tr>
        <td width="120px">订单短信(详情):</td>
        <td>
        <input type="checkbox" name="ticket_sms" data-toggle="icheck" value="1" <eq name="vo['ticket_sms']" value="1"> checked</eq> data-label="发送票型信息&nbsp;">
        <input type="checkbox" name="area_sms" data-toggle="icheck" value="1" <eq name="vo['area_sms']" value="1"> checked</eq> data-label="发送区域信息">
        <span class="remark">区域和票型任选其一，注意结合模板短信使用</span>
        </td>
      </tr>
    </tbody>
  </table>                   
</div>
<input name="type" value="1" type="hidden">
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