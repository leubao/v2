<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="__URL__" method="post">
	<div class="searchBar">
		<ul class="searchContent">
			<li>
				<label>名称：</label>
				<input type="text" name="name" class="medium" >
			</li>
		</ul>
		<div class="subBar">
			<ul>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">查询</button></div></div></li>
			</ul>
		</div>
	</div>
	</form>
</div>

<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="{:U('Crm/Guide/add',array('navTabId'=>$navTabId));}" target="dialog" mask="true"><span>新增导游</span></a></li>
			<li><a class="delete" href="{:U('Crm/Guide/delete',array('navTabId'=>$navTabId));}&id={gid}" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择客导游"><span>删除导游</span></a></li>
			<li><a class="edit" href="{:U('Crm/Guide/edit',array('navTabId'=>$navTabId));}&id={gid}" target="dialog" mask="true" warn="请选择导游"><span>修改导游</span></a></li>
		</ul>
	</div>


	<table class="table" width="100%" layoutH="138">
		<thead>
		<tr>
			<th width="60">编号</th>
			<th width="100" orderField="title" <if condition="$_REQUEST._order eq 'title'">class="{$_REQUEST._sort}"</if>>客户名称</th>
			<th>地址</th>
			<th>联系人</th>
			<th width="100">税号</th>
			<th width="80" orderField="sequence" <if condition="$_REQUEST._order eq 'sequence'">class="{$_REQUEST._sort}"</if>>账号</th>
			<th width="100" orderField="status" <if condition="$_REQUEST._order eq 'status'">class="{$_REQUEST._sort}"</if>>开户行</th>
			<th width="100">状态</th>
			<th>相关销售人员</th>
			<th width="100">添加时间</th>
		</tr>
		</thead>
		<tbody>
		<volist id="vo" name="data">
			<tr target="gid" rel="{$vo['id']}">
				<td>{$vo['id']}</td>
				<td><a href="__URL__/index/pid/{$vo['id']}/" target="navTab" rel="__MODULE__">{$vo['name']}</a></td>
				<td>{$vo['address']}</td>
				<td>{$vo['contacts']}</td>
				<td>{$vo['tariff']}</td>
				<td>{$vo['bank_account']}</td>
				<td>{$vo['bank']}</td>
				<td><if condition="$vo['status'] eq '1'">
						<!-- <img src="/statics/images/icon/y.png" /> -->启用
					<else/>
						<!-- <img src="/statics/images/icon/n.png" /> -->不启用
					</if></td>
				<td>{$vo['salesman']}</td>
				<td>{$vo['create_time']}</td>
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

