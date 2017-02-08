<?php if (!defined('LUB_VERSION')) exit(); ?>

<form class="form-horizontal" action="{:U('Item/Product/auth',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
  <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td width="100px">日期场次:</td>
          <td><input type="hidden" name="plan.id" value="{$plan_id}">
    <input type="text" name="plan.name" readonly value="{$plan_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/date_plan',array('ifadd'=>1));}" data-group="plan" data-width="600" data-height="445" data-title="销售计划(场次)" placeholder="销售计划(场次)"></td>
        </tr>
        <tr>
          <td width="100px">订单处理:</td>
          <td>
          <input type="checkbox" name="del_order" value="1"> 删除订单数据
          </td>
        </tr>
        <!--状态0为作废订单1正常2为渠道版订单未支付情况3已取消5已支付但未排座6政府订单7申请退票中9门票已打印-->
        <tr>
          <td width="100px">订单状态:</td>
          <td>
          <input type="checkbox" name="status[]" value="1"> 预定成功
          <input type="checkbox" name="status[]" value="2"> 待支付订单
          <input type="checkbox" name="status[]" value="3"> 已取消订单
          <input type="checkbox" name="status[]" value="5"> 待排座
          <input type="checkbox" name="status[]" value="6"> 政企订单
          <input type="checkbox" name="status[]" value="7"> 退票申请中
          <input type="checkbox" name="status[]" value="9"> 已打印
          <input type="checkbox" name="status[]" value="0"> 作废订单
          </td>
        </tr>
        <tr>
          <td width="100px">报表数据:</td>
          <td><input type="checkbox" name="del_credit" value="1"> 删除授信数据</td>
        </tr>
        <tr>
          <td width="100px">补贴数据:</td>
          <td><input type="checkbox" name="del_credit" value="1"> 删除补贴数据</td>
        </tr>
        <tr>
          <td width="100px">报表数据:</td>
          <td><input type="checkbox" name="del_credit" value="1"> 删除报表数据</td>
        </tr>
        <tr>
          <td width="100px">说明</td>
          <td style="color: red;"><strong>设置相应场景可售数量,不限定数量默认值即可</strong></td>
        </tr>
        <tr>
          <td width="100px">微信/官网</td>
          <td>
          <volist name="data['param']['seat']" id="vo">
      {$vo|areaName}<input type="text" name="wechat_num[{$vo}]" value="{$data['param']['wechat'][$vo]}" size="5">
      </volist>
        </td>
        </tr>
        <tr>
          <td width="100px">API</td>
          <td>
          <volist name="data['param']['seat']" id="vo">
      {$vo|areaName}<input type="text" name="api_num[]" value="0" size="5">
      </volist></td>
        </tr>
        <tr>
          <td width="100px">自助机</td>
          <td>
          <volist name="data['param']['seat']" id="vo">
      {$vo|areaName}<input type="text" name="help_num[]" value="0" size="5">
      </volist></td>
        </tr>
        <tr>
          <td width="100px">说明</td>
          <td style="color: red;"><strong>设置出票场景,在不确定正常演出时,建议关闭渠道版出票和自助机出票</strong></td>
        </tr>
        <tr>
          <td width="100px">出票设置</td>
          <td>
          <input type="checkbox" name="window_print" value="1" checked readonly> 窗口出票
        <input type="checkbox" name="help_print" value="1" <eq name="data['help_print']" value="1"> checked</eq>> 自助机出票
        <input type="checkbox" name="channel_print" value="1" <eq name="data['channel_print']" value="1"> checked</eq>> 渠道版出票
        </tr>
        </tbody>
    </table>
  </div>
  <input name="id" value="{$data.id}" type="hidden">
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