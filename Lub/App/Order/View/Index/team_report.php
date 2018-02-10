<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
  <div id="w_team_report_print">
    <div class="visible-print-block">
        <h3 align="center">团队接待回单</h3>
        <span class="pull-left mb10">NO：{$data.order_sn}</span>
        <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
    </div>
    <table class="table table-striped table-bordered table-hover">
      <tbody>
        <tr>
          <td width="100px">产品名称</td>
          <td width="500px">{$data.product_id|productName}</td> 
          <td width="100px">创建日期</td>
          <td>{$data.createtime|datetime}</td>
        </tr>
        <tr>
          <td>渠道商:</td>
          <td>{$data.channel_id|crmName}</td>
          <td>下单人:</td>
          <td>{$data.user_id|userName}</td>
        </tr>
        <tr>
          <td>销售计划</td>
          <td>{$data.plan_id|planShow}</td>
          <td>数量</td>
          <td>{$data.number}</td>
        </tr>
        <!--
        <tr>
          <td>金额</td>
          <td colspan="2">{$data.money|num_to_rmb}</td>
          <td>{$data.money|format_money}</td>
        </tr>-->
        <tr>
          <td>取票人</td>
          <td>{$data.take}</td>
          <td>电话</td>
          <td>{$data.phone}</td> 
        </tr>
        <tr>
          <td>开票人</td>
          <td>{$user_id|userName}</td>
          <td></td>
          <td></td> 
          
        </tr>
        <tr>
          <td>备注</td>
          <td colspan="3">{$data.remark}</td>
        </tr>
        <tr>
          <td>团队信息</td>
          <td colspan="3">团号:{$param.0.teamno}; 车牌号:{$param.0.car}; 导游:{$crm.0.contact}; 联系电话:{$crm.0.phone};<if condition="!empty($param['0']['teamtype'])">
          [ 团队类型: {$param.0.teamtype|teamtype} ] 
        </if></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered table-hover mb25">
      <thead>
        <tr>
          <th>编号</th>
          <th>区域</th>
          <th>票型</th>
          <th>数量</th>
          <th>单价</th>
        </tr>
      </thead>
      <tbody>
        <volist name="ticket" id="vo">
            <tr>
              <td>{$i}</td>
              <td>{$vo.areaId|areaName}</td>
              <td>{$vo.priceid|ticketName}</td>
              <td>{$vo.number}</td>
              <td>{$vo.price}</td>
            </tr>
          </volist>
      </tbody>
    </table>
  </div>
</div>
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
    <if condition="$data['status'] eq '9' ">
    <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_team_report_print')"><i class="fa fa-print"> 打印回单</i></a></li>
    </if>
    </ul>
</div>