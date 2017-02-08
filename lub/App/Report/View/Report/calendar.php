<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader"> 
  <!--工具条 s-->
  <div class="toolBar"> 
    <!--查询条 s-->
    <form id="pagerForm" data-toggle="ajaxsearch" action="table-fixed.html" method="post">
      <input type="hidden" name="pageSize" value="${model.pageSize}">
      <input type="hidden" name="pageCurrent" value="${model.pageCurrent}">
      <input type="hidden" name="orderField" value="${param.orderField}">
      <input type="hidden" name="orderDirection" value="${param.orderDirection}">
      <div class="bjui-searchBar">
        <label>护照号：</label>
        <input type="text" id="customNo" value="" name="code" class="form-control" size="10">
        &nbsp;
        <label>客户姓名：</label>
        <input type="text" value="" name="name" class="form-control" size="8">
        &nbsp;
        <label>所属业务:</label>
        <select name="type" data-toggle="selectpicker">
          <option value="">全部</option>
          <option value="1">联络</option>
          <option value="2">住宿</option>
          <option value="3">餐饮</option>
          <option value="4">交通</option>
        </select>
        &nbsp;
        <input type="checkbox" id="j_table_chk" value="true" data-toggle="icheck" data-label="我的客户">
        &nbsp;
        <button type="button" class="showMoreSearch" data-toggle="moresearch" data-name="custom2"><i class="fa fa-angle-double-down"></i></button>
        <button type="submit" class="btn-default" data-icon="search">查询</button>
        &nbsp; <a class="btn btn-orange" href="javascript:;" onclick="$(this).navtab('reloadForm', true);" data-icon="undo">清空查询</a>
        <div class="pull-right">
          <div class="btn-group">
            <button type="button" class="btn-default dropdown-toggle" data-toggle="dropdown" data-icon="copy">复选框-批量操作<span class="caret"></span></button>
            <ul class="dropdown-menu right" role="menu">
              <li><a href="book1.xlsx" data-toggle="doexport" data-confirm-msg="确定要导出信息吗？">导出<span style="color: green;">全部</span></a></li>
              <li><a href="book1.xlsx" data-toggle="doexportchecked" data-confirm-msg="确定要导出选中项吗？" data-idname="expids" data-group="ids">导出<span style="color: red;">选中</span></a></li>
              <li class="divider"></li>
              <li><a href="ajaxDone2.html" data-toggle="doajaxchecked" data-confirm-msg="确定要删除选中项吗？" data-idname="delids" data-group="ids">删除选中</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="bjui-moreSearch">
        <label>职业：</label>
        <input type="text" value="" name="profession" size="15" />
        <label>&nbsp;性别:</label>
        <select name="sex" data-toggle="selectpicker">
          <option value="">全部</option>
          <option value="true">男</option>
          <option value="false">女</option>
        </select>
        <label>&nbsp;手机:</label>
        <input type="text" value="" name="mobile" size="10">
        <label>&nbsp;渠道商:</label>
        <input type="hidden" name="pid" class="doc_lookup" value="">
        <input type="text" data-toggle="lookup" data-url="{:U('Manage/Index/channel')}" name="name" class="doc_lookup" size="10">
      </div>
    </form>
    <!-- 查询条 e--> 
  </div>
</div>
<div class="bjui-pageContent">
	<!--销售图表 s-->
	<div id="echat">
		<div style="mini-width:400px;height:350px" data-toggle="echarts" data-type="pie,funnel" data-theme="blue" data-url="{:U('Report/Report/wire');}"></div>
	</div>
	<!--销售图表 e-->
	<!--销售日历 s-->
	<div id="calendar" class="calendar"></div>
	<!--销售日历 s-->
</div>
<script>
	//日历初始化
$('#calendar').calendar();
</script>