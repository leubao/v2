<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
  <!--工具条 s-->
  <Managetemplate file="Common/Nav"/>
  <!--帮助 说明-->
</div>
<div class="bjui-pageContent tableContent">
	<div>
		<table id="sale_card_window"></table>
	</div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
    </ul>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#sale_card_window').datagrid({
		'columns':[{name:'nickname',width:'150',lable:'姓名'},],
		'data':,
	});
});
</script>