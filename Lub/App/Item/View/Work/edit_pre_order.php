<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Work/edit_pre_order',array('sn'=>$data['order_sn'],'menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
  <table class="table table-striped table-bordered">
    <tbody>
      <tr>
        <td width="90px">单号</td>
        <td>{$data.order_sn}</td>
        <td width="90px">状态</td>
        <td>{$data['status']|order_status}</td>
      </tr>
      <tr>
        <td width="90px">销售计划</td>
        <td  colspan="3">
          <select class="required" name="plan" data-toggle="selectpicker">
        <volist name="plan" id="vo">
          <?php $ptime =  $vo['plantime']."-".$vo['games'];?>
          <option value="{$vo.id}"  <if condition="$vo['id'] eq $data['plan_id']">selected</if>>{$vo.plantime|date="Y-m-d",###} <if condition="$product['type'] eq 1"> 第{$vo.games}场 {$vo.starttime|date="H:i",###}</if>
          </option>
        </volist></select>
        {$data.plan_id|planShow}</td>
        
      </tr>
      <tr>
        <td>创建时间</td>
        <td>{$data.createtime|date="Y-m-d H:i:s",###} </td>
        <td>下单人</td>
        <td>{$data['user_id']|userName=$data['addsid']}</td>
      </tr>
      <tr>
        <td>联系人</td>
        <td>{$info['crm']['0']['contact']} </td>
        <td>手机</td>
        <td>{$info['crm']['0']['phone']}</td>
      </tr>
      <tr>
        <td>渠道商(业务员)</td>
        <td colspan="3">{$data.channel_id|hierarchy}({$info['crm']['0']['guide']|userName=$data['addsid']})
        <if condition="!empty($info['param']['0']['tour'])">
          [ 客源地: {$info.param.0.tour|provinces}]
        </if>
        <if condition="!empty($info['param']['0']['car'])">
          [ 车牌号: {$info.param.0.car} ] 
        </if>
        <if condition="!empty($info['param']['0']['teamtype'])">
          [ 团队类型: {$info.param.0.teamtype|teamtype} ] 
        </if>
        </td>
      </tr>
      <if condition="!empty($info['param']['0']['activity'])">
      <tr>
        <td>活动名称</td>
        <td>{$info['param']['0']['activity']|activity_name} 
        </td>
        <td></td>
        <td>
         
        </td>
      </tr>
      </if>
      <tr>
        <td>订单金额</td>
        <td>{$info['subtotal']|format_money} </td>
        <td>支付方式</td>
        <td>{$data.pay|pay}
          <select class="required" name="pay" data-toggle="selectpicker">
            <option value="">请选择</option>
            <option value="1" <eq name="data.pay" value="1">selected</eq>>现金 </option>
            <option value="2" <eq name="data.pay" value="2">selected</eq>>授信额</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>区域详情</td>
        <td colspan="3"><volist name="info['data']" id="ar">{$ar.areaId|areaName}({$ar.num}) </volist></td>
      </tr>
      <tr>
        <td>本次操作类型</td>
        <td colspan="3"> <input type="radio" name="model" value="staging">  暂存预约
          <input type="radio" name="model" value="push">  推送订单
          <span class="remark">暂存不生成有效订单,推送生成有效订单，并完成相应扣款</span></td>
      </tr>
      <tr>
        <td>备注1</td>
        <td colspan="3">{$info['param']['0']['remark']}</td>
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
    <volist name="info['data']" id="vo">
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
<input type="hidden" value="pre" name="action"></input>
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