<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Work/edit_pre_order',array('sn'=>$data['order_sn'],'menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <table class="table table-striped table-bordered">
    <tbody>
      <tr>
        <td width="90px">销售计划</td>
        <td width="320px">{$data.plan_id|planShow}</td>
        <td width="90px">单号</td>
        <td>{$data.order_sn}</td>
      </tr>
      <tr>
        <td>创建时间</td>
        <td>{$data.createtime|date="Y-m-d H:i:s",###} </td>
        <td>下单人</td>
        <td>{$data['user_id']|userName=$data['addsid']}</td>
      </tr>
      <tr>
        <td>联系人</td>
        <td>{$data['info']['crm']['0']['contact']} </td>
        <td>手机</td>
        <td>{$data.phone}</td>
      </tr>
      <tr>
        <td>订单类型(场景)</td>
        <td>{$data.type|channel_type}（{$data.addsid|addsid}）</td>
        <td>身份证号</td>
        <td>{$data['id_card']} </td>
      </tr>
      <if condition="$data['type'] neq '1'">
      <tr>
        <td>渠道商(业务员)</td>
        <td colspan="3">{$data.channel_id|hierarchy}({$data.guide_id|userName=$data['addsid']})   
        <if condition="$proconf.black eq '1' AND $data['info']['param']['0']['guide_black'] neq 'undefined'">[导游手机号:{$data['info']['param']['0']['guide_black']}]</if></td>
      </tr>
      </if>
      <tr>
        <td>订单金额</td>
        <td>{$data['info']['subtotal']|format_money} </td>
        <td>支付方式</td>
        <td>{$data.pay|pay}</td>
      </tr>
      <if condition="$type eq '1'">
      <tr>
        <td>区域详情</td>
        <td colspan="3"><volist name="area" id="ar">{$ar.area|areaName}({$ar.num}) </volist></td>
      </tr>
      <else />
      <tr>
        <td>票型详情</td>
        <td colspan="3"><volist name="area" id="ar">{$ar.area|ticketName}({$ar.num})</volist></td>
      </tr>
      </if>
      <tr>
        <td>备注1</td>
        <td colspan="3">{$data.remark}</td>
      </tr>
      <tr>
        <td>备注2</td>
        <td colspan="3"><textarea name="win_rem" cols="55" rows="1" <eq name="data.win_rem" value="0">disabled</eq> >{$data.win_rem}</textarea></td>
      </tr>
    </tbody>
  </table>
  <table class="table table-bordered table-hover mb25">
  <thead>
    <tr>
      <th>编号</th>
      <th>票型</th>
      <th>价格</th>
      <th>区域</th>
      <th>数量</th>
    </tr>
  </thead>
  <tbody>
    <volist name="data['info']['data']['area']" id="vo">
      <tr>
        <td>{$i}</td>
        <td>{$vo.priceid|ticketName}</td>
        <td>{$vo.price}</td>
        <td>{$vo.areaId|areaName}</td>
        <td><input type="hidden" value="{$vo.priceid}" name="priceid[]"><input type="text" name="price_num[]" value="{$vo.num}"></td>
      </tr>
    </volist>
  </tbody>
  </table>
</div>
<input type="hidden" value="{$data.order_sn}" name="sn"></input>
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
    <li>
      <button type="submit" class="btn-default" data-icon="save">提交</button>
    </li>
  </ul>
</div>
</form>
<script>

</script>