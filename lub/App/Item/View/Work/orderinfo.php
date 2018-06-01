<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Work/orderinfo',array('sn'=>$data['order_sn'],'menuid'=>$menuid));}" method="post" data-toggle="validate">
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
        <td>联系人/导游</td>
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
      </if>
      <tr>
        <td>订单金额</td>
        <td>{$data['info']['subtotal']|format_money} </td>
        <td>支付方式</td>
        <td>{$data.pay|pay}</td>
      </tr>
      <if condition="!empty($data['activity'])">
      <tr>
        <td>活动名称</td>
        <td>{$data['activity']|activity_name} 
          <a href="{:U('Item/Order/public_tag_status',array('sn'=>$data['order_sn'],'menuid'=>$menuid));}" data-toggle="doajax" data-confirm-msg="确定要改变订单状态吗?" type="button" class="btn-info">标记</a></td>
        <td></td>
        <td></td>
      </tr>
      </if>
      <if condition="$data['status'] eq '9'">
      <tr>
        <td>出票员</td>
        <td>{$data.order_sn|print_ticket_user}</td>
        <td></td>
        <td></td>
      </tr>
      </if>
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
  <if condition="$type eq '1'">
  <table class="table table-bordered table-hover mb25">
  <thead>
    <tr>
      <th>编号</th>
      <th>票型</th>
      <th>票面单价</th>
      <th>结算单价</th>
      <th>区域</th>
      <th>座位</th>
      <th>订单号/状态/打印次数/更新时间</th>
    </tr>
  </thead>
  <tbody><?php if(empty($data['info']['data']['area'])){ ?>

    <volist name="data['info']['data']" id="vo">
      <tr>
        <td>{$i}</td>
        <td>{$vo.priceid|ticketName}</td>
        <td>{$vo.price}</td>
        <td>{$vo.discount}</td>
        <td>{$vo.areaId|areaName}</td>
        <td>{$vo.seatid|seatShow}</td>
        <td>{$vo.seatid|seatOrder=$data['plan_id'],$vo['areaId']}</td>
      </tr>
    </volist>
    <?php } if(!empty($data['info']['data']['area'])){ ?>
      <volist name="data['info']['data']['area']" id="vo">
        <tr>
          <td>{$i}</td>
          <td></td>
          <td></td>
          <td>{$vo.price}</td>
          <td>{$vo.areaId|areaName}</td>
          <td></td>
          <td></td>
        </tr>
      </volist>
    <?php } ?>
  </tbody>
  </table>
  <else/>
  <table class="table table-bordered table-hover mb25">
  <thead>
    <tr>
      <th>编号</th>
      <th>票型</th>
      <th>票面单价</th>
      <th>结算单价</th>
      <th>票号</th>
      <th>订单号/状态/打印次数/更新时间</th>
    </tr>
  </thead>
  <tbody><?php if(empty($data['info']['data']['area'])){ ?>

    <volist name="data['info']['data']" id="vo">
      <tr>
        <td>{$i}</td>
        <td>{$vo.priceid|ticketName}</td>
        <td>{$vo.price}</td>
        <td>{$vo.discount}</td>
        <td>{$vo.ciphertext}</td>
        <td>{$vo.id|seatOrder=$vo['plan_id'],$vo['areaId']}</td>
      </tr>
    </volist>
    <?php } if(!empty($data['info']['data']['area'])){ ?>
      <volist name="data['info']['data']['area']" id="vo">
        <tr>
          <td>{$i}</td>
          <td></td>
          <td></td>
          <td>{$vo.price}</td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </volist>
    <?php } ?>
  </tbody>
  </table>
  </if>
</div>
<input type="hidden" value="{$data.order_sn}" name="sn"></input>
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
    <if condition="$data['status'] eq '1' ">
    <li>
      <button type="button" class="btn-info" data-icon="print" data-url="{$prshow.url}" data-width="{$prshow.width}" data-height="{$prshow.height}" data-title="{$prshow.title}" data-pageid="{$prshow.pageId}" id="print_window">打印</button>
    </li>
    </if>
    <li>
      <button type="submit" class="btn-default" data-icon="save">提交</button>
    </li>
  </ul>
</div>
</form>
<script>
$('#print_window').click(function(){
    /*关闭订单详情的窗口*/
    $(this).dialog('close','orderinfo');
    $(this).dialog({id:''+$(this).data('pageid')+'', url:''+$(this).data('url')+'', title:''+$(this).data('title')+'',width:''+$(this).data('width')+'',height:''+$(this).data('height')+'',resizable:false,maxable:false,mask:true});
});
</script>