<?php if (!defined('LUB_VERSION')) exit(); ?>

<div class="bjui-pageHeader">

<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Item/Work/check_oreder',array('menuid'=>$menuid));}" method="post">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <select class="required" name="plan" id="plan" data-toggle="selectpicker">
        <option value="">+=^^=销售计划=^^=+</option>
        <volist name="plan" id="vo">
          <?php $ptime =  $vo['plantime']."-".$vo['games'];?>
          <option value="{$vo.id}"  <if condition="$pinfo['plan'] eq $vo['id']">selected</if>>{$vo.plantime|date="Y-m-d",###} <if condition="$product['type'] eq 1"> 第{$vo.games}场 {$vo.starttime|date="H:i",###}</if>
          </option>
        </volist>
    </select>
    
    <select name="type" data-toggle="selectpicker">
        <option value="">类型</option>
        <option value="1" <eq name="pinfo['type']" value="1"> selected</eq>>座位号</option>
        <option value="2" <eq name="pinfo['type']" value="2"> selected</eq>>二维码</option>
    </select>
    &nbsp;
    <input type="text" name="sn" value="{$pinfo['sn']}">
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>

    <div class="btn-group f-right" role="group"> 
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-default" href="http://www.leubao.com/index.php?g=Manual&a=show&sid=33" target="_blank" data-placement="left" data-toggle="tooltip" title="使用帮助"><i class="fa fa-question-circle"></i></a>
      </div>
  </div>
  <!--检索条件 e-->
</form>

<!--Page end-->
</div>
<div class="bjui-pageContent tableContent">
  <if condition="$data neq '404'">
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
          <td>{$data.user_id|userName}</td>
        </tr>
        <tr>
          <td>联系人</td>
          <td>{$data['info']['crm']['0']['contact']} </td>
          <td>手机</td>
          <td>{$data.phone}</td>
        </tr>
        <tr>
          <td>订单金额</td>
          <td>{$data['info']['subtotal']|format_money} </td>
          <td>支付方式</td>
          <td>{$data.pay|pay}</td>
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
          <td colspan="3">{$data.channel_id|hierarchy}({$data.guide_id|userName})</td>
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
        <tr>
          <td>区域详情</td>
          <td colspan="3"><volist name="area" id="ar">{$ar.area|areaName}({$ar.num}) </volist></td>
        </tr>
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
    <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>编号</th>
        <th>票型</th>
        <th>票面价</th>
        <th>结算价</th>
        <th>区域</th>
        <th>座位</th>
        <th>订单号/状态/打印次数/更新时间</th>
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
              <td>{$vo.seatid|seatShow}</td>
              <td>{$vo.seatid|seatOrder=$data['plan_id'],$vo['areaId']}</td>
            </tr>
          </volist>
    </tbody>
    </table>
  <else />
  <table class="table table-bordered">
  <tbody>
  <tr><td style='padding:15px;' align='center'><strong style='color:red;font-size:48px;'>未找到相关信息</strong></td></tr>
  </tbody>
  </table>
  </if>
</div>
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
  </ul>
</div>