<?php dump($data);?>
<div class="bjui-pageContent">
<table class="table table-striped table-bordered">
  <tbody>
    <tr>
      <td>活动名称:</td><td colspan="3">{$data.title}</td>
    </tr>
    <tr>
      <td>开始时间:</td><td>{$data.starttime|datetime}</td>
      <td>结束时间:</td><td>{$data.endtime|datetime}</td>
    </tr>
    <tr>
      <td>活动场景:</td>
      <td colspan="3">

        <input type="checkbox" name="scene[]" value="1"> 窗口
        <input type="checkbox" name="scene[]" value="2"> 渠道版
        <input type="checkbox" name="scene[]" value="3"> 网站
        <input type="checkbox" name="scene[]" value="4"> 微信
        <input type="checkbox" name="scene[]" value="5"> API
        <input type="checkbox" name="scene[]" value="6"> 自助机
      </td>
    </tr>
    <tr>
        <td>活动类型:</td>
        <td colspan="3">

          <select name="type" class="required" id="activity_type" data-toggle="selectpicker" data-rule="required">
            <option value="">活动类型</option>
            <option value="1" data-area="buy">买赠</option>
            <option value="2" data-area="first">首单免</option>
            <option value="3" data-area="area">限制区域销售</option>
          </select>
        </td>
    </tr>
    
    <tr>
        <td>排序:</td><td>{$data.sort}</td>
        <td>状态:</td><td>{$data.status|status}</td>
    </tr>
    <tr>
      <td>备注:</td><td colspan="3">{$data.remark}</td>
    </tr>
    <tr>
      <td>活动地址:</td><td colspan="3">{$data.remark}</td>
    </tr>
  </tbody>
</table>
<!--买赠-->
<div id="buy" style="display: none;">
  <table class="table table-striped table-bordered">
    <tbody>
    <volist name="seat" id="vo"> 
    <tr>
        <td>活动区域：</td>
        <td><input type="checkbox" name="area[{$vo.id}]" value="{$vo.id}"> {$vo.id|areaName} 
        买: <input type="text" name="num[{$vo.id}]" value="" data-rule="digits" size="5"> 
          <input type="hidden" name="ticket_num_{$vo.id}.id" value="{$ticket_id}">
          <input type="text" name="ticket_num_{$vo.id}.name" readonly value="{$ticket_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket_num_{$vo.id}" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称">
        赠送: <input type="text" name="nums[{$vo.id}]" value="" data-rule="digits" size="5">
          <input type="hidden" name="ticket_nums_{$vo.id}.id" value="{$ticket_id}">
          <input type="text" name="ticket_nums_{$vo.id}.name" readonly value="{$ticket_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket_nums_{$vo.id}" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称"></td>
        <td>数量: <input type="text" name="quota[{$vo.id}]" value="" data-rule="digits" size="5">
        <input name="seat[{$vo.id}]" id="seat_{$vo.id}" type="hidden" value="">
        </td>
        <td><a href="{:U('Item/Activity/row_seat',array('area'=>$vo['id']));}" data-toggle="dialog" data-mask="true" data-max="true" data-id="activity_seat">指定区域</a></td>
    </tr>
    </volist>
  </tbody>
  </table>
</div>
<!--首单免-->
<!--区域销售-->
<div id="area" style="display: none;">
  <table class="table table-striped table-bordered">
    <tbody>
      <tr>
        <td>身份证号段:</td><td colspan="3"><input type="text" name="card" value="" size="45"><span class="remark">身份证号前6位,多个区域用“|”分隔开</span></td>
      </tr>
      <tr>
        <td>可售票型:</td><td colspan="3"><input type="hidden" name="ticket.id" value="">
<input type="text" name="ticket.name" readonly value="" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称"><span class="remark">如果是多个票型，请勾选追加</span></td>
      </tr>
      <tr>
        <td>其它设置:</td>
        <td colspan="3">
          <input type="checkbox" name="voucher" value="card"> 身份证入园
        </td>
      </tr>
    </tbody>
  </table>
</div>
</div>
<input name="product_id" value="{$product_id}" type="hidden">
<div class="bjui-pageFooter">
<ul>
  <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
  <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
</ul>
</div