<div class="pageHeader">
  <div class="searchBar">
    <ul class="searchContent">
      <form onSubmit="return navTabSearch(this);" action="{:U('Report/Report/channel')}" method="post">
        <li>日期
          <input type="text" class="date" readonly name="starttime" value="{$starttime}" />
        </li>
        <li>
         - <input type="text" class="date" readonly name="endtime" value="{$endtime}" />
        </li>
        <input type="hidden" name="channel.id" value="{$channel_id}">
    <input type="text" name="channel.name" readonly value="{$channel_name}" size="10" data-toggle="lookup" data-url="{:U('Manage/Index/public_channel',array('ifadd'=>1));}" data-group="channel" data-width="600" data-height="445" data-title="渠道商" placeholder="渠道商">
        <li>
        
          <button type="submit">检 索</button>
        </li>
      </form>
    </ul>
  </div>
</div>
<div class="pageContent">
  <div class="panelBar">
    <ul class="toolBar">
      <li><a class="icon" href="javascript:$.printBox('w_channel_sum_print')"><span>打印</span></a></li>
    </ul>
  </div>
  <div class="pageFormContent" layoutH="110"><div id="w_channel_sum_print">
  <table class="list"  width="98%" style="text-align: center;">
<caption class="table_print"><h1>渠道商补贴统计</h1><span>(统计日期范围:{$starttime} - {$endtime}  打印时间:<?php echo date('Y年m月d日');?>  操作员:{$user_id|userName})</span></caption>
   <tr>
    <td rowspan="3" align="center">编号</td>
    <td rowspan="3" align="center">渠道商名称</td>
    <td colspan="6" align="center">区域(数量/金额)</td>
    <td colspan="3" align="center">小计(数量/金额)</td>
  </tr>
  <tr>
    <volist name="area" id="ar">
    <td colspan="2" align="center">{$ar.name}</td>
    </volist>
    <td rowspan="2">数量</td>
    <td rowspan="2">票面金额</td>
    <td rowspan="2">结算金额</td>
  </tr>
  <tr>
 <volist name="area" id="ar">
    <td>数量</td>
    <td>金额</td>
    </volist>
  </tr>
  <volist name="data['channel']" id="vo" key="k">
  <tr>
    <td>{$k}</td>
    <td>{$vo.channel_id|crmName}</td>
    <for start="1" end="$area_num" comparison="elt">
    <td>{$vo['area'][$area[$i-1]['id']]['number']}</td>
    <td>{$vo['area'][$area[$i-1]['id']]['money']}</td>
    </for>

    <td>{$vo.num}</td>
    <td>{$vo.money}</td>
    <td>{$vo.moneys}</td>
  </tr>
  </volist>
  <tr>
  <td></td>
  <td style="text-align: right;">合计:</td>
  <for start="1" end="$area_num" comparison="elt">
    <td>{$data['area'][$area[$i-1]['id']]['num']}</td>
    <td>{$data['area'][$area[$i-1]['id']]['money']}</td>
  </for>
  <td>{$data['num']}</td><td>{$data['money']}</td><td>{$data['moneys']}</td>
  </tr>
</table>
  </div>
  </div>
  <div class="panelBar">
    <div class="pages"> </div>
    <div></div>
  </div>
</div>