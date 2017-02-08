<script type="text/javascript">

function closedialog(param) {

	alert(param.msg);

	return true;

}

</script>

	<form id="pagerForm" action="{:U('Crm/Index/grouplist')}" method="post">

		<input type="hidden" name="pageNum" value="1" /><!--【必须】value=1可以写死-->

		<input type="hidden" name="numPerPage" value="10" /><!--【可选】每页显示多少条-->

		<input type="hidden" name="id" value="asc" /><!--【可选】升序降序-->

		<input type="hidden" name="groupid" value="{$groupid}" />

		<input type="hidden" name="navTabId" value="{$navTabId}" />

	</form>	

	<div class="panelBar">

		<ul class="toolBar">

			<li><a class="add" href="{:U('Crm/Index/add_gov',array('navTabId'=>$navTabId,'groupid'=>$groupid));}" target="dialog" rel="{$navTabId}" mask="true"><span>新增客户</span></a></li>

			<li><a class="delete" href="{:U('Crm/Index/delete_gov',array('navTabId'=>$navTabId,'groupid'=>$groupid));}&id={gid}" rel="{$navTabId}" target="ajaxTodo" calback="navTabAjaxMenu" title="你确定要删除吗？" warn="请选择客户名称"><span>删除客户</span></a></li>

			<li><a class="edit" href="{:U('Crm/Index/edit_gov',array('navTabId'=>$navTabId,'groupid'=>$groupid));}&id={gid}" rel="{$navTabId}" target="dialog" mask="true" warn="请选择一条数据"><span>修改客户</span></a></li>

			<!-- <li><a class="icon" href="{:U('Crm/Index/recharge',array('navTabId'=>$navid,'groupid'=>$groupid))}&id={gid}" rel="{$navTabId}" target="dialog" mask="true" warn="请选择一条数据"><span>充值</span></a></li> -->

			<li><a class="icon" href="{:U('Crm/Index/quota',array('navTabId'=>$navTabId,'groupid'=>$groupid))}&id={gid}" rel="{$navTabId}" target="dialog" mask="true" warn="请选择一条数据"><span>管理配额</span></a></li>

		</ul>

	</div>

	<table class="table" width="100%" layoutH="75">

		<thead>

		<tr>

			<th width="30">编号</th>

			<th width="120">姓名</th>

			<!-- <th>地址</th> -->

			<th>联系人电话</th>

			<th>销售配额</th>

			<!-- <th width="100">税号</th>

			<th width="80" orderField="sequence" <if condition="$_REQUEST._order eq 'sequence'">class="{$_REQUEST._sort}"</if>>账号</th>

			<th width="100" orderField="status" <if condition="$_REQUEST._order eq 'status'">class="{$_REQUEST._sort}"</if>>开户行</th> -->

			<th width="30" align="center">状态</th>

			<th>管理员</th>

			<th width="110">添加时间</th>

			<!-- <th>操作</th> -->

		</tr>

		</thead>

		<tbody>

		<volist id="vo" name="data">

			<tr target="gid" rel="{$vo['id']}">

				<td>{$vo['id']}</td>

				<td><a href="{:U('Crm/Index/gdetail',array('id'=>$vo['id'],'type'=>1))}" target="dialog">{$vo['name']}</a></td>

				<!-- <td>{$vo['address']}</td> -->

				<td>{$vo['phone']}</td>

				<td>{$vo['quota']}</td>

				<!-- <td>{$vo['tariff']}</td>

				<td>{$vo['bank_account']}</td>

				<td>{$vo['bank']}</td> -->

				<td><if condition=" $vo['status'] eq '1' "><img title="可用" src="{$config_siteurl}statics/images/icon/y.png">

                <else />

                <img title="禁止" src="{$config_siteurl}statics/images/icon/x.png"></if></td>

				<td>{$vo['salesman']}</td>

				<td>{$vo['create_time']}</td>

				<!-- <td>
				
				<a target="dialog" href="{:U('Crm/Index/userslist',array('navTabId'=>$navid,'groupid'=>$groupid,'cid'=>$vo['id']))}">员工</a> | 
				
					<a target="dialog" href="{:U('Crm/Index/checkcash',array('navTabId'=>$navid,'groupid'=>$groupid,'cid'=>$vo['id']))}">授信查询</a>
				
				</td> -->

			</tr>

		</volist>

		</tbody>

	</table>



	<div class="panelBar">

		<div class="pages">

			<span>共{$totalCount}条</span>

		</div>

		<div class="pagination" rel="jbsxBox" targetType="navTab" totalCount="{$totalCount}" numPerPage="{$numPerPage}" pageNumShown="10" currentPage="{$currentPage}"></div>

	</div>