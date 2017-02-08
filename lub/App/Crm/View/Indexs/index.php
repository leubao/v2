<div class="pageContent">
	<div layoutH="1" style="float:left; display:block; overflow:auto; width:240px; border:solid 1px #CCC; line-height:21px; background:#fff">
	    <ul class="tree treeFolder">
			<li><a href="javascript">客户分组</a>
				<ul>
					<volist name="data" id="vo">
						<li><a href="{:U('Crm/Index/grouplist',array('navTabId'=>$navTabId,'id'=>$vo['id'],'type'=>$vo['type']));}" target="ajax" rel="gloupBox" >{$vo.name}</a></li>
					</volist>
				</ul>
			</li>
	     </ul>
	</div>	
	<div id="gloupBox" class="unitBox" style="margin-left:246px;border-left:solid 1px #CCC;border-right:solid 1px #CCC;">
		<!--#include virtual="list1.html" -->
	</div>
</div>