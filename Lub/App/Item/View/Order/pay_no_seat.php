<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Order/pay_no_seat',array('sn'=>$data['order_sn'],'menuid'=>$menuid));}" method="post" data-toggle="validate">
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
      	<td width="90px">登记日期</td>
        <td>{$data.plan_id|planShow} </td>
        <td>创建时间</td>
        <td>{$data.createtime|date="Y-m-d H:i:s",###} </td>
      </tr>
      
      <tr>
        <td>联系人</td>
        <td>{$data.take}</td>
        <td>手机</td>
        <td>{$data.phone}</td>
      </tr>
      <tr>
        <td>渠道商(业务员)</td>
        <td colspan="3">{$data.channel_id|hierarchy}({$data.guide_id|userName=$data['addsid']})   
        <if condition="$proconf.black eq '1' AND $data['info']['param']['0']['guide_black'] neq 'undefined'">[导游手机号:{$data['info']['param']['0']['guide_black']}]</if>
        <if condition="!empty($data['info']['param']['0']['tour'])">
          [ 客源地: {$data.info.param.0.tour|provinces}]
        </if>
        <if condition="!empty($data['info']['param']['0']['car'])">
          [ 车牌号: {$data.info.param.0.car} ] 
        </if>
        <if condition="!empty($data['info']['param']['0']['teamtype'])">
          [ 团队类型: {$data.info.param.0.teamtype|teamtype} ] 
        </if>
        </td>
      </tr>
      <tr>
        <td>订单金额</td>
        <td>{$data['money']|format_money} </td>
        <td>支付方式</td>
        <td>{$data.pay|pay}</td>
      </tr>
      <if condition="$data.product_type eq '1'">
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
        <td>本次操作类型</td>
        <td colspan="3"> 
          <input type="radio" name="action" value="1">  可售区域排座
          <input type="radio" name="action" value="2">  预留区域排座
          <input type="radio" name="action" value="4">  驳回申请
          <span class="remark">可售区域排座设置未售出且未预留的座位，预留区域排座仅设置指定预留区域的预留座位</span></td>
      </tr>
      <tr>
        <td>预留区域</td>
        <td colspan="3"> 
          <select name="control" data-toggle="selectpicker">
          	<option value="0">请选择预留区域</option>
	        <volist name="control" id="vo">
	          <option value="{$vo.id}">{$vo.name}({$vo.num})</option>
	        </volist>
	      </select>
		</td>
      </tr>
      <tr>
        
        <td>下单人</td>
        <td>{$data['user_id']|userName=$data['addsid']}</td>
      </tr>
      <tr>
        <td>备注1</td>
        <td colspan="3">{$info['param']['0']['remark']}</td>
      </tr>
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