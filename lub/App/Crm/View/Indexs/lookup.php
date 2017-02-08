<form id="pagerForm" action="{:U('Crm/Index/lookup')}">
	<input type="hidden" name="pageNum" value="1" />
	<input type="hidden" name="numPerPage" value="10" />
	<input type="hidden" name="name" value="{$name}">
	<input type="hidden" name="navTabId" value="{$navTabId}" />
</form>
<div class="pageHeader">
	<form rel="pagerForm" method="post" action="{:U('Crm/Index/lookup')}" onsubmit="return dwzSearch(this, 'dialog');">
	<div class="searchBar">
		<ul class="searchContent">
			<li>
				<label>销售姓名:</label>
				<input class="textInput" name="name" value="{$nickname}" type="text">
			</li><!--
			<li>	
				<label>电话号码:</label>
				<input class="textInput" name="phone" value="{$phone}" type="text">
			</li>-->	  
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

	<table class="table" layoutH="118" targetType="dialog" width="100%">
		<thead>
			<tr>
				<th orderfield="salesmanname">姓名</th>
				<th width="80">查找带回</th>
			</tr>
		</thead>
		<tbody>
			<volist name="list" id="vo">
				<tr>
					<td>{$vo.nickname}</td>
					<td>
						<a class="btnSelect" href="javascript:$.bringBack({itemid:'{$vo.item_id}',salesman:'{$vo.id}', salesmanname:'{$vo.nickname}'})" title="查找带回">选择</a>
					</td>
				</tr>				
			</volist>
		</tbody>
	</table>

	<div class="panelBar">
		<div class="pages">
			<span>共{$totalCount}条</span>
		</div>
		<div class="pagination" targetType="dialog" totalCount="{$totalCount}" numPerPage="{$numPerPage}" pageNumShown="10" currentPage="{$currentPage}"></div>
	</div>
</div>