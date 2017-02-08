<form id="pagerForm" action="{:U('Order/Index/index')}" method="post">
		<input type="hidden" name="pageNum" value="1" /><!--【必须】value=1可以写死-->
		<input type="hidden" name="numPerPage" value="10" /><!--【可选】每页显示多少条-->
		<input type="hidden" name="id" value="asc" /><!--【可选】升序降序-->
		<input type="hidden" name="groupid" value="{$groupid}" />
		<input type="hidden" name="navTabId" value="{$navTabId}" />
		<input type="hidden" name="start_time" value="{$starttime}">
		<input type="hidden" name="end_time" value="{$endtime}">
		<input type="hidden" name="orgLookup.ids" value="{$map['user_id']}" />
		<input type="hidden" name="orgLookup.id" value="{$map['channel_id']}" />
		<input type="hidden" name="orgLookup.planid" value="{$map['plan_id']}" />
		<input type="hidden" name="type" value="{$map['type']}" />
	</form>
<div class="pageHeader">
	<div class="searchBar">
		<ul class="searchContent">
			<form onsubmit="return navTabSearch(this);" action="{:U('Order/Index/index')}" method="post">
			<li class="dateRange">日期<input type="text" class="date" value="{$starttime}" readonly="true" name="start_time" /></li>
			<li class="dateRange">至<input type="text" class="date" readonly="true" value="{$endtime}" name="end_time" /></li>
			<li class="dateRange"><input type="hidden" name="orgLookup.id" value=""/>
            <input type="text" name="orgLookup.name" value="{$map['channel_id']|crmName}" suggestFields="orgNum,orgName" suggestUrl="{:U('Item/Work/channel');}" lookupGroup="orgLookup" disabled/>
            <a class="btnLook" href="{:U('Item/Work/channel');}" lookupGroup="orgLookup" rel="page3" width="500" height="365">查找渠道商</a></li>
			<li class="dateRange"><input type="hidden" name="orgLookup.ids" value=""/>
            <input type="text" name="orgLookup.names" value="{$map['user_id']|userName}" suggestFields="orgNum,orgName" suggestUrl="{:U('Item/Work/guide');}" lookupGroup="orgLookup" disabled/>
            <a class="btnLook" href="{:U('Item/Work/guide',array('type'=>'1'));}" lookupGroup="orgLookup" rel="page2">查找下单人</a>
            </li>
			<li class="dateRange">
				<input type="hidden" name="orgLookup.planid" value="{$map['plan_id']}"/>
            	<input size="35" type="text" name="orgLookup.planname" value="{$map['plan_id']|planShow}" suggestFields="orgNum,orgName" suggestUrl="{:U('Item/Index/planShow');}" lookupGroup="orgLookup" disabled/>
            	<a class="btnLook" href="{:U('Item/Index/date_plan');}" lookupGroup="orgLookup" rel="page3" width="500" height="365">选择日期场次</a></li>		
			</li>
			<li>
        <select name="type">
          <option value="">订单类型</option>
          <option value="1">散客</option>
          <option value="2">窗口团队</option>
          <option value="4">渠道团队</option>
          <option value="4">政企团队</option>
        </select></li>
			<li><button type="submit">检 索</button></li>
            </form>
            <form onsubmit="return navTabSearch(this);" action="{:U('Order/Index/index');}" method="post">
		        <li class="dateRange">
		          订单号：
		          <input type="text" name="sn" class="" >
		        </li>
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
    <if condition="$proconf['win_subtract'] eq '1'">
      <li><a class="delete" href="{:U('Item/Work/subtract',array('navTabId'=>$navTabId));}&sn={sn}" target="dialog" mask="true" rel="{$navTabId}"><span>订单核减</span></a></li>
    </if>
    </ul>
  </div>
	<table class="table" width="100%" layoutH="113">
		<thead>
			<tr>
				<th width="80" align="center">订单号</th>
				<th width="60" align="center">订单（场景）类型</th>
				<th width="20" align="center">数量</th>
				<th width="60">金额</th>
				<th width="100" align="center">所属计划</th>
				<th width="80" align="center">创建时间</th>
                <th width="20" align="center">状态</th>
                <th width="80" align="center">操作</th>
			</tr>
		</thead>
		<tbody>
        <volist name="data" id="vo">
			<tr target="sn" rel="{$vo.order_sn}">
				<td><a title="订单详情" target="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn']))}" width="900" height="600">{$vo.order_sn}</a></td>
				<td>{$vo.addsid|addsid}（{$vo.type|channel_type}）</td>
				<td>{$vo.number}</td>
				<td>{$vo.money}</td>
				<td>{$vo.plan_id|planShow}</td>
				<td>{$vo.createtime|date="Y-m-d H:i:s",###}</td>
				<td>{$vo['status']|order_status}</td>
				<td><a title="订单详情" target="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn'],'tt'=>1))}" class="btnView">查看</a>
                    <a title="门票打印" rel="page1" target="dialog" href="{:U('Item/Order/drawer',array('sn'=>$vo['order_sn'],'plan_id'=>$vo['plan_id']))}" minable="false" maxable="false" mask="true" mixable="false" resizable="false" drawable="false" width="225" height="225" class="btnInfo">出票</a>
                    </td>
			</tr>
            </volist>
            </tbody>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>共{$totalCount}条</span>
		</div>
		<div class="pagination" targetType="navTab" totalCount="{$totalCount}" numPerPage="{$numPerPage}" pageNumShown="10" currentPage="{$currentPage}"></div>
	</div>
</div>