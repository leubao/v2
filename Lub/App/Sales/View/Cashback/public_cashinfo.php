<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
  <div class="visible-print-block">
      <h3 align="center">渠道商提现确认单</h3>
      <span class="pull-left mb10">NO：{$data.sn}</span>
      <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
  </div>
  <table class="table table-striped table-bordered table-hover w900">
  <tbody>
    <tr>
      <td width="100px">收款人:</td>
      <td width="500px">{$data.user_id|userName}</td>
      <td width="100px">身份证号码:</td>
      <td>{$data.user_id|userCard}</td>
    </tr>
    <tr>
      <td>支付方式</td>
      <td>{$data.pay_type|pay}</td>
      <td>日期</td>
      <td>{$data.createtime|datetime}</td> 
    </tr>
    <tr>
      <td>金额</td>
      <td colspan="2">{$data.money|num_to_rmb}</td>
      <td>{$data.money|format_money}</td>
    </tr>
    <tr>
      <td>经办人/审核员</td>
      <td>{$data.userid|userName}</td>
      <td>收款人</td>
      <td>{$data.user_id|userName}</td> 
    </tr>
    <tr>
      <td>备注1</td>
      <td colspan="3">{$data.remark}</td>
    </tr>
  </tbody>
</table>
<table class="table table-bordered table-hover mb25">
  <thead>
    <tr>
      <th>编号</th>
      <th>金额</th>
      <th>状态</th>
      <th>创建时间</th>
      <th>更新时间</th>
    </tr>
  </thead>
  <tbody>
    <volist name="data['info']['data']" id="vo">
        <tr>
          <td>{$i}</td>
          <td>{$vo.priceid|ticketName}</td>
          <td>{$vo.price}</td>
          <td>{$vo.discount}</td>
          <td>{$vo.areaId|areaName}</td>
        </tr>
      </volist>
  </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
    <!--
    <if condition="$data['status'] eq '1' ">
    <li>
      <button type="button" class="btn-info" data-icon="print" id="print_window">打印</button>
    </li>
    </if>
    -->
  </ul>
</div>
<script>
$('#print_window').click(function(){
    var url = '{:U('Item/Order/drawer',array('sn'=>$data['order_sn'],'plan_id'=>$data['plan_id']))}';
    /*关闭订单详情的窗口*/
    $(this).dialog('close','orderinfo');
    $(this).dialog({id:'print', url:''+url+'', title:'门票打印',width:'213',height:'208',resizable:false,maxable:false,mask:true});
});
</script>