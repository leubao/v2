<form id="pagerForm" action="{:U('Crm/Index/group')}" method="post">
	<input type="hidden" name="pageNum" value="1" /><!--【必须】value=1可以写死-->
	<input type="hidden" name="numPerPage" value="15" /><!--【可选】每页显示多少条-->
	<input type="hidden" name="id" value="asc" /><!--【可选】升序降序-->
	<input type="hidden" name="navTabId" value="{$navTabId}" />
</form>	
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
			<li><a class="add" href="{:U('Crm/Index/groupadd',array('navTabId'=>$navTabId));}"  target="dialog" mask="true" width="700"><span>新增</span></a></li>
			<li><a class="delete" href="{:U('Crm/Index/groupdelete',array('navTabId'=>$navTabId));}&id={gid}" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择客户分组"><span>删除</span></a></li>
			<li><a class="edit" href="{:U('Crm/Index/groupedit',array('navTabId'=>$navTabId));}&id={gid}" target="dialog" mask="true" width="700" warn="请选择客户分组"><span>修改</span></a></li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="138">
		<thead>
		<tr>
			<th width="60">编号</th>
			<th width="100">分组名</th>
			<th width="100">分组描述</th>
			<th width="100">价格政策</th>
			<th width="100">分组属性</th>
			<th width="100">结算方式</th>
			<th width="100">状态</th>
			<th width="100">添加时间</th>
		</tr>
		</thead>
		<tbody>
		<volist id="vo" name="data">
			<tr target="gid" rel="{$vo['id']}" class="trbg">
				<td>{$vo['id']}</td>
				<td>{$vo['name']}</td>
				<td>{$vo['remark']}</td>
				<td>{$vo.price_group|price_group}</td>
				<td>{$vo.type|crm_group_type}</td>
				<td><if condition="$vo['settlement'] eq '1'">票面价结算<else />底价结算</if></td>
				<td>
					<if condition=" $vo['status'] eq '1' "><img title="可用" src="{$config_siteurl}statics/images/icon/y.png">
                <else />
                <img title="禁止" src="{$config_siteurl}statics/images/icon/x.png"></if>
				</td>
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