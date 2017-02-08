	<!--分页设置-->
	<form id="pagerForm" action="{:U('Crm/Index/userslist')}" method="post">
		<input type="hidden" name="pageNum" value="1" /><!--【必须】value=1可以写死-->
		<input type="hidden" name="numPerPage" value="10" /><!--【可选】每页显示多少条-->
		<input type="hidden" name="id" value="asc" /><!--【可选】升序降序-->
		<input type="hidden" name="groupid" value="{$groupid}" />
		<input type="hidden" name="navTabId" value="{$navTabId}" />
		<input type="hidden" name="cid" value="{$cid}" />
	</form>	
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="{:U('Crm/Index/addusers',array('navTabId'=>$navTabId,'groupid'=>$groupid,'cid'=>$cid));}" target="dialog" width="610" height="500" mask="true"><span>新增员工</span></a></li>
			<li><a class="delete" href="{:U('Crm/Index/deleteusers',array('navTabId'=>$navTabId,'groupid'=>$groupid,'cid'=>$cid));}&id={gid}" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择员工"><span>删除员工</span></a></li>
			<li><a class="edit" href="{:U('Crm/Index/editusers',array('navTabId'=>$navTabId,'groupid'=>$groupid,'cid'=>$cid));}&id={gid}" target="dialog" width="610" height="500" mask="true" warn="请选择员工名称"><span>修改员工</span></a></li>
			<li><a class="delete" href="{:U('Crm/Index/reset_pwd',array('navTabId'=>$navTabId,'groupid'=>$groupid,'cid'=>$cid));}&id={gid}" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要重置密码吗？" warn="请选择员工"><span>重置密码</span></a></li>
		</ul>
	</div>


	<table class="table" width="100%" layoutH="75">
		<thead>
		<tr>
			<th width="60">编号</th>
			<th width="100">客户名称</th>
			<th width="100">用户名</th>
			<th>电话</th>
			<th width="100">状态</th>
			<th width="120">添加时间</th>
		</tr>
		</thead>
		<tbody>
		<volist id="vo" name="data">
			<tr target="gid" rel="{$vo['id']}">
				<td>{$vo['id']}</td>
				<td><a href="{:U('Crm/Index/gdetail',array('id'=>$vo['id']))}" target="dialog">{$vo['nickname']}</a></td>
				<td>{$vo.username}</td>
				<td>{$vo['phone']}</td>
				<td><if condition="$vo['status'] eq '1'">启用<else/>不启用</if></td>
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
